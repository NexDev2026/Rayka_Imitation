<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewRegistrationMail;
use App\Mail\LoginOtpMail;
use App\Mail\OrderCancelledByCustomerAdminMail;
use App\Mail\OrderCancelledCustomerMail;
use App\Mail\PasswordChangedAlertMail;
use App\Mail\RegisterOtpMail;
use App\Mail\ResetPasswordOtpMail;
use App\Mail\VerifyNewEmailOtpMail;
use App\Mail\WelcomeUserMail;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\StoreSetting;
use App\Models\User;
use App\Services\GuestSessionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerAccountController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('account.orders');
        }

        return view('storefront.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,filter',
            'password' => 'required|string',
        ], [
            'email.required' => 'Please enter your royal account email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your account password.',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return back()->withInput($request->only('email'))->with('error', 'No royal account found with this email address. Please register a new account.');
        }

        if (! Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))->with('error', 'Incorrect password entered. Please try again or reset your password.');
        }

        Auth::login($user, (bool) $request->filled('remember'));
        GuestSessionService::mergeGuestDataToUser($user);

        return redirect()->intended(route('account.orders'))->with('success', 'Welcome back, '.$user->name.'!');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,filter',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        $otp = (string) random_int(100000, 999999);
        if ($user) {
            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(15),
            ]);

            defer(function () use ($user, $otp) {
                try {
                    Mail::to($user->email)->send(new LoginOtpMail($otp, $user->name));
                } catch (\Throwable $e) {
                    Log::warning("Login OTP email failure for {$user->email}: ".$e->getMessage());
                }
            });
        }

        return response()->json([
            'success' => true,
            'message' => "Verification OTP sent to {$email}!",
        ]);
    }

    public function verifyOtpApi(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,filter',
            'otp' => 'required|string',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if ($user && $user->otp === $request->otp && now()->lessThanOrEqualTo($user->otp_expires_at)) {
            $user->update(['otp' => null, 'otp_expires_at' => null]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 400);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('account.orders');
        }

        return view('storefront.auth.register');
    }

    public function sendRegisterOtp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,filter|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = strtolower(trim($request->email));
        $otp = (string) random_int(100000, 999999);
        
        $name = trim((string) $request->name);
        Cache::put('register_otp_' . $email, $otp, now()->addMinutes(15));
        
        defer(function () use ($email, $otp, $name) {
            try {
                Mail::to($email)->send(new RegisterOtpMail($otp, $name));
            } catch (\Throwable $e) {
                Log::warning("Register OTP email failure for {$email}: ".$e->getMessage());
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent to ' . $email
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,filter|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'otp' => 'required|string',
        ]);

        $email = strtolower(trim($request->email));
        $cachedOtp = Cache::get('register_otp_' . $email);

        if (! $cachedOtp || $cachedOtp !== $request->otp) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired verification code. Please check the code or request a new one.',
                ], 422);
            }
            return back()->withInput($request->except('password', 'password_confirmation', 'otp'))
                         ->with('error', 'Invalid or expired verification code.')
                         ->with('otp_step', true);
        }

        $user = User::create([
            'name' => trim($request->name),
            'email' => $email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        Cache::forget('register_otp_' . $email);

        Auth::login($user);
        GuestSessionService::mergeGuestDataToUser($user);

        defer(function () use ($user, $request) {
            try {
                Mail::to($user->email)->send(new WelcomeUserMail($user));
                $adminEmail = StoreSetting::get('admin_email') ?? config('services.brevo.admin_email') ?? StoreSetting::get('store_email', 'admin@raykajewellery.com');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new AdminNewRegistrationMail($user, $request->ip()));
                }
            } catch (\Throwable $e) {
                Log::warning('Registration email dispatch warning: '.$e->getMessage());
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('account.orders'),
                'message' => 'Your royal account has been created successfully!',
            ]);
        }

        return redirect()->route('account.orders')->with('success', 'Your royal account has been created successfully!');
    }

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('account.orders');
        }

        return view('storefront.auth.forgot_password');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,filter',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'No account registered with this email address.',
            ], 404);
        }

        $otp = (string) random_int(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        defer(function () use ($user, $otp) {
            try {
                Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user->name));
            } catch (\Throwable $e) {
                Log::warning("Reset password OTP email failure for {$user->email}: ".$e->getMessage());
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Password reset OTP sent to {$email}!",
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,filter|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->firstOrFail();

        if (! $user->otp || $request->otp !== $user->otp || now()->greaterThan($user->otp_expires_at)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired verification code. Please request a new OTP.',
                ], 422);
            }
            return back()->withInput()->with('error', 'Invalid or expired verification code. Please request a new OTP.')->with('otp_step', true);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        defer(function () use ($user, $request) {
            try {
                Mail::to($user->email)->send(new PasswordChangedAlertMail($user, $request->ip(), $request->userAgent()));
            } catch (\Throwable $e) {
                Log::warning('Password changed alert dispatch warning: '.$e->getMessage());
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('login'),
                'message' => 'Your password has been reset successfully! Please sign in.',
            ]);
        }

        return redirect()->route('login')->with('success', 'Your password has been reset successfully! Please sign in.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }

    public function addresses()
    {
        $allAddresses = Auth::user()->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $uniqueAddresses = collect();
        $seenKeys = [];
        foreach ($allAddresses as $addr) {
            $normKey = Address::normalizeKey($addr);
            if (! in_array($normKey, $seenKeys, true)) {
                $seenKeys[] = $normKey;
                $uniqueAddresses->push($addr);
            }
        }
        $addresses = $uniqueAddresses;

        return view('storefront.account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
            'email' => 'required|email|max:255',
            'address_line' => 'required|string|max:500',
            'street' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string|regex:/^[0-9]{6}$/',
            'address_type' => 'nullable|string|in:Home,Office,Other',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $isFirst = $user->addresses()->count() === 0;
        $isDefault = $request->boolean('is_default') || $isFirst;

        $addressLine = trim($validated['address_line']);
        if (! empty($validated['address_type'])) {
            $tag = '(' . ucfirst(trim($validated['address_type'])) . ')';
            if (! str_contains($addressLine, $tag)) {
                $addressLine .= ' ' . $tag;
            }
        }

        Address::findOrCreateOrUpdate($user->id, [
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'address_line' => $addressLine,
            'street' => $validated['street'],
            'road' => $validated['street'],
            'landmark' => $validated['landmark'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'pincode' => $validated['pincode'],
        ], $isDefault);

        return back()->with('success', 'Delivery address saved successfully!');
    }

    public function updateAddress(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|regex:/^[0-9]{10}$/',
            'email' => 'required|email|max:255',
            'address_line' => 'required|string|max:500',
            'street' => 'required|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string|regex:/^[0-9]{6}$/',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $address->is_default = true;
        }

        $address->update([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'],
            'address_line' => $validated['address_line'],
            'street' => $validated['street'],
            'road' => $validated['street'],
            'landmark' => $validated['landmark'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'pincode' => $validated['pincode'],
            'is_default' => $address->is_default,
        ]);

        return back()->with('success', 'Delivery address updated successfully!');
    }

    public function destroyAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $wasDefault = $address->is_default;

        // If this address is attached to any orders, unlink it from user rather than deleting
        // to preserve shipping details in order history
        if (Order::where('address_id', $address->id)->exists()) {
            $address->update([
                'user_id' => null,
                'is_default' => false,
            ]);
        } else {
            $address->delete();
        }

        if ($wasDefault) {
            /** @var \App\Models\Address|null $next */
            $next = Auth::user()->addresses()->latest()->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'Delivery address removed successfully.');
    }

    public function setDefaultAddress(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default delivery address updated!');
    }

    public function settings()
    {
        return view('storefront.account.settings');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_email' => 'required|email:rfc,filter|unique:users,email',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'The provided current password does not match our records.');
        }

        $otp = (string) random_int(100000, 999999);
        
        Cache::put('email_change_' . $user->id, [
            'new_email' => strtolower(trim($request->new_email)),
            'otp' => $otp,
        ], now()->addMinutes(15));

        defer(function () use ($request, $otp, $user) {
            try {
                Mail::to(strtolower(trim($request->new_email)))->send(new VerifyNewEmailOtpMail($otp, $user->name));
            } catch (\Throwable $e) {
                Log::warning('Verify new email OTP dispatch warning: '.$e->getMessage());
            }
        });

        return back()->with('email_change_otp_sent', true)->with('success', 'An OTP has been sent to your new email address. Please verify to confirm.')->withFragment('account-security');
    }

    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string',
        ]);

        $user = Auth::user();
        $cacheKey = 'email_change_' . $user->id;
        $cachedData = Cache::get($cacheKey);

        if (! $cachedData || $cachedData['otp'] !== $request->otp) {
            return back()->with('email_change_otp_sent', true)->with('error', 'Invalid or expired OTP code.')->withFragment('account-security');
        }

        $user->update([
            'email' => $cachedData['new_email'],
        ]);

        Cache::forget($cacheKey);

        return back()->with('success', 'Your email address has been updated successfully!')->withFragment('account-security');
    }

    public function orders()
    {
        $orders = Auth::user()->orders()
            ->with(['items.product', 'payment', 'address'])
            ->latest()
            ->paginate(10);

        return view('storefront.account.orders', compact('orders'));
    }

    public function orderDetail($orderNumber)
    {
        $order = Auth::user()->orders()
            ->with(['items.product', 'payment', 'address'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('storefront.account.order_detail', compact('order'));
    }

    public function cancelOrder(Request $request, $orderNumber)
    {
        /** @var \App\Models\Order $order */
        $order = Auth::user()->orders()
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Only allow cancellation if it hasn't been shipped or delivered
        if (! in_array($order->status, ['Pending Verification', 'Processing', 'Confirmed'])) {
            return back()->with('error', 'This order cannot be cancelled as it is already '.$order->status.'.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:255',
            'cancellation_comment' => 'nullable|string|max:500',
        ]);

        $reason = trim($validated['cancellation_reason']);
        $comment = trim($validated['cancellation_comment'] ?? '');
        $fullReason = $reason . ($comment !== '' ? " — Details: {$comment}" : '');

        // Use a transaction to safely cancel order and restore stock
        DB::transaction(function () use ($order, $fullReason) {
            $timestamp = now()->format('d M, Y \a\t h:i A');
            $order->update([
                'status' => 'Cancelled',
                'cancelled_by' => 'customer',
                'cancellation_reason' => $fullReason,
                'cancelled_at' => now(),
                'notes' => trim(($order->notes ?? '') . "\n\nCancelled by user on {$timestamp}. Reason: {$fullReason}"),
            ]);

            if ($order->payment) {
                $order->payment->update([
                    'status' => 'Rejected',
                    'admin_note' => "Order cancelled by user on {$timestamp}. Reason: {$fullReason}",
                ]);
            }

            // Restore product and variant stock
            $order->restoreInventory();
        });

        defer(function () use ($order, $reason, $comment, $fullReason) {
            try {
                $adminEmail = StoreSetting::get('admin_email') ?? config('services.brevo.admin_email') ?? StoreSetting::get('store_email', 'admin@raykajewellery.com');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new OrderCancelledByCustomerAdminMail($order, $reason, $comment));
                }

                $customerEmail = $order->address?->email ?: ($order->user?->email ?: null);
                if ($customerEmail) {
                    Mail::to($customerEmail)->send(new OrderCancelledCustomerMail($order, $fullReason));
                }
            } catch (\Throwable $e) {
                Log::warning("Order #{$order->order_number} cancellation email dispatch warning: ".$e->getMessage());
            }
        });

        return back()->with('success', 'Your order has been successfully cancelled.');
    }

    public function downloadInvoice($orderNumber)
    {
        $order = Order::with(['items.product', 'payment', 'address', 'user'])
            ->where('order_number', $orderNumber)
            ->where(function ($q) {
                if (Auth::check() && ! Auth::user()->isAdmin()) {
                    $q->where('user_id', Auth::id());
                }
            })
            ->firstOrFail();

        if (! $order->isConfirmed()) {
            return back()->with('error', 'Invoice will be available once the order is Confirmed by our team.');
        }

        $storeName = StoreSetting::get('store_name', 'Rayka Imitation Jewellery');
        $storeAddress = StoreSetting::get('store_address');
        $storePhone = StoreSetting::get('store_phone');
        $storeEmail = StoreSetting::get('store_email');
        $upiId = StoreSetting::get('upi_id');

        $pdf = Pdf::loadView('pdf.invoice', compact('order', 'storeName', 'storeAddress', 'storePhone', 'storeEmail', 'upiId'));

        return $pdf->download("Rayka-Invoice-{$order->order_number}.pdf");
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|max:1500',
            'customer_name' => 'required|string|max:255',
        ]);

        // Check if user has purchased this product
        $isVerified = false;
        if (Auth::check()) {
            $hasPurchased = Order::where('user_id', Auth::id())
                ->whereIn('status', ['Confirmed', 'Processing', 'Shipped', 'Delivered'])
                ->whereHas('items', function ($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                })->exists();
            $isVerified = $hasPurchased;
        }

        Review::create([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'customer_email' => Auth::user() ? Auth::user()->email : null,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_verified_purchase' => $isVerified,
            'status' => 'pending', // Pending admin approval
        ]);

        return back()->with('success', 'Thank you! Your royal review has been submitted and is awaiting brief moderation.');
    }
}

