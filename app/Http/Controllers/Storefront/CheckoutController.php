<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Mail\NewOrderAdminMail;
use App\Mail\OrderPlacedCustomerMail;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Models\User;
use App\Services\GuestSessionService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = GuestSessionService::getCart();
        $cart->load(['items.product.images', 'items.variant']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your shopping bag is empty. Please add items before checkout.');
        }

        // Live inventory validation before loading checkout page
        foreach ($cart->items as $item) {
            $stock = $item->product ? $item->product->stock_quantity : 0;
            if ($item->variant) {
                $stock = min($stock, $item->variant->stock_quantity);
            }
            if ($stock < $item->quantity) {
                if ($stock <= 0) {
                    return redirect()->route('cart')->with('error', "Sorry! '{$item->product->name}' was just purchased by another customer and is now out of stock. Please adjust your bag.");
                } else {
                    return redirect()->route('cart')->with('error', "Sorry! Only {$stock} unit(s) of '{$item->product->name}' remain in stock. Please adjust your bag quantity to proceed.");
                }
            }
        }

        $subtotal = $cart->subtotal;
        $freeShippingMin = (float) StoreSetting::get('free_shipping_min', '999');
        $shippingFee = ($subtotal >= $freeShippingMin) ? 0.0 : (float) StoreSetting::get('shipping_flat_fee', '99');

        $couponCode = session('applied_coupon');
        $discount = 0.0;
        $coupon = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
            if ($coupon && $coupon->isValidForAmount($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }

        $total = max(0, $subtotal - $discount + $shippingFee);

        $savedAddresses = collect();
        $savedAddress = null;
        if (Auth::check()) {
            $allAddresses = Auth::user()->addresses()->orderByDesc('is_default')->latest()->get();
            $uniqueAddresses = collect();
            $seenKeys = [];
            foreach ($allAddresses as $addr) {
                $normKey = Address::normalizeKey($addr);
                if (! in_array($normKey, $seenKeys, true)) {
                    $seenKeys[] = $normKey;
                    $uniqueAddresses->push($addr);
                }
            }
            $savedAddresses = $uniqueAddresses;
            $savedAddress = $savedAddresses->firstWhere('is_default', true) ?: $savedAddresses->first();
        }

        $qrCodeImage = StoreSetting::get('qr_code_image', '/images/qr/sample-upi-qr.svg');
        $upiId = StoreSetting::get('upi_id', 'raykajewellery@icici');
        $upiPayeeName = StoreSetting::get('upi_payee_name', 'Rayka Imitation Jewellery');

        return view('storefront.checkout', compact(
            'cart',
            'subtotal',
            'shippingFee',
            'coupon',
            'discount',
            'total',
            'savedAddress',
            'savedAddresses',
            'qrCodeImage',
            'upiId',
            'upiPayeeName'
        ));
    }

    public function process(Request $request)
    {
        // 1. Fail-safe base64 decoding if multipart upload was dropped or failed by PHP
        $uploaded = $request->file('payment_screenshot');
        if ((! $uploaded || ! $uploaded->isValid()) && $request->filled('payment_screenshot_base64')) {
            $base64Data = $request->input('payment_screenshot_base64');
            if (preg_match('/^data:image\/(\w+);base64,(.+)$/s', $base64Data, $matches)) {
                $ext = strtolower($matches[1]);
                if ($ext === 'jpeg') {
                    $ext = 'jpg';
                }
                $decoded = base64_decode($matches[2]);
                if ($decoded !== false && strlen($decoded) <= 15 * 1024 * 1024) {
                    $tmpDir = storage_path('app/temp');
                    if (! file_exists($tmpDir)) {
                        mkdir($tmpDir, 0777, true);
                    }
                    $tmpFilePath = $tmpDir.'/pay_b64_'.uniqid().'.'.$ext;
                    file_put_contents($tmpFilePath, $decoded);
                    $mime = 'image/'.($ext === 'jpg' ? 'jpeg' : $ext);
                    $fallbackFile = new UploadedFile(
                        $tmpFilePath,
                        'payment_screenshot.'.$ext,
                        $mime,
                        null,
                        true // test mode enables isValid() without is_uploaded_file check
                    );
                    $request->files->set('payment_screenshot', $fallbackFile);
                    $uploaded = $fallbackFile;
                }
            }
        }

        Log::info('CheckoutController::process request', [
            'has_file' => $request->hasFile('payment_screenshot'),
            'file_name' => $uploaded ? $uploaded->getClientOriginalName() : null,
            'file_size' => $uploaded ? $uploaded->getSize() : null,
            'file_is_valid' => $uploaded ? $uploaded->isValid() : null,
            'file_error_code' => $uploaded ? $uploaded->getError() : null,
        ]);

        $isUsingSavedAddress = Auth::check() && $request->filled('selected_address_id') && $request->selected_address_id !== 'new';

        $rules = [
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|digits:10',
            'email' => 'required|email:rfc,filter|max:255',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'payment_screenshot' => [
                'required',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    if ($value instanceof UploadedFile) {
                        $mime = strtolower($value->getMimeType() ?: '');
                        $ext = strtolower($value->getClientOriginalExtension() ?: '');
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/jfif', 'image/heic', 'image/heif', 'image/bmp', 'image/svg+xml', 'image/gif', 'image/pjpeg', 'image/x-png', 'application/octet-stream'];
                        $allowedExts = ['jpeg', 'png', 'jpg', 'webp', 'jfif', 'heic', 'heif', 'bmp', 'svg', 'gif'];
                        if (! str_starts_with($mime, 'image/') && ! in_array($ext, $allowedExts) && ! in_array($mime, $allowedMimes)) {
                            $fail('The payment screenshot must be a valid image file (JPG, PNG, WebP, HEIC).');
                        }
                    }
                },
            ],
        ];

        if ($isUsingSavedAddress) {
            $rules['selected_address_id'] = [
                'required',
                function ($attribute, $value, $fail) {
                    if (! Auth::user()->addresses()->where('id', $value)->exists()) {
                        $fail('The selected delivery address does not belong to your account.');
                    }
                },
            ];
        } else {
            $rules['address_line'] = 'required|string|max:255';
            $rules['street'] = 'nullable|string|max:255';
            $rules['road'] = 'nullable|string|max:255';
            $rules['landmark'] = 'nullable|string|max:255';
            $rules['city'] = 'required|string|max:100';
            $rules['state'] = 'required|string|max:100';
            $rules['pincode'] = 'required|string|digits:6';
            $rules['address_type'] = 'nullable|in:Home,Office';
            $rules['save_address'] = 'nullable';
        }

        $validator = Validator::make(
            array_merge($request->all(), ['payment_screenshot' => $uploaded]),
            $rules,
            [
                'name.required' => 'Please provide your full name for order dispatch.',
                'mobile.required' => 'A valid 10-digit mobile number is required for courier delivery updates.',
                'mobile.digits' => 'Mobile number must be exactly 10 digits.',
                'email.required' => 'Please provide your email address to receive your invoice & tracking details.',
                'selected_address_id.required' => 'Please select a delivery address or enter a new one.',
                'address_line.required' => 'Please enter your flat, house number, building or apartment name.',
                'city.required' => 'City name is required.',
                'state.required' => 'State is required.',
                'pincode.required' => 'A valid 6-digit postal PIN code is required.',
                'payment_screenshot.required' => 'Please upload your UPI payment confirmation screenshot (proof).',
                'payment_screenshot.max' => 'Payment screenshot must not exceed 10MB in size.',
                'payment_screenshot.uploaded' => 'The payment screenshot failed to upload. Please ensure the image is under 10MB.',
            ]
        );
        $validator->validate();

        $cart = GuestSessionService::getCart();
        $cart->load(['items.product', 'items.variant']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your shopping bag is empty.');
        }

        // Live stock check before placing order
        foreach ($cart->items as $item) {
            $stock = $item->product->stock_quantity;
            if ($item->variant) {
                $stock = min($stock, $item->variant->stock_quantity);
            }
            if ($stock < $item->quantity) {
                return redirect()->route('cart')->with('error', "Sorry, '{$item->product->name}' only has {$stock} items left in stock.");
            }
        }

        // Handle Payment Screenshot Upload (Up to 10MB)
        $screenshotFile = $uploaded ?: $request->file('payment_screenshot');
        $uploadDir = public_path('uploads/payments');
        if (! file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $rawExt = strtolower($screenshotFile->getClientOriginalExtension() ?: 'jpg');
        if ($rawExt === 'jpeg') {
            $rawExt = 'jpg';
        }
        $filename = 'pay_'.time().'_'.Str::random(10).'.'.$rawExt;
        $screenshotFile->move($uploadDir, $filename);
        $screenshotPath = '/uploads/payments/'.$filename;

        // DB Transaction with row-level pessimistic locking (high-concurrency ACID protection)
        try {
            $order = DB::transaction(function () use ($request, $cart, $screenshotPath, $isUsingSavedAddress) {
                // 1. Lock all products and variants for update to prevent concurrent double-selling
                $lockedProducts = [];
                $lockedVariants = [];

                foreach ($cart->items as $item) {
                    /** @var Product|null $prod */
                    $prod = Product::where('id', $item->product_id)->lockForUpdate()->first();
                    if (! $prod) {
                        throw new \Exception("The item '{$item->product_name}' is no longer available.");
                    }

                    $stock = (int) $prod->stock_quantity;
                    $variant = null;

                    if ($item->product_variant_id) {
                        /** @var ProductVariant|null $variant */
                        $variant = ProductVariant::where('id', $item->product_variant_id)->lockForUpdate()->first();
                        if ($variant) {
                            $stock = min($stock, (int) $variant->stock_quantity);
                        }
                    }

                    if ($stock < $item->quantity) {
                        if ($stock <= 0) {
                            throw new \Exception("Sorry! '{$prod->name}' was just purchased by another customer and is now sold out.");
                        } else {
                            throw new \Exception("Sorry! Only {$stock} unit(s) of '{$prod->name}' remain. Another customer may have just completed their checkout.");
                        }
                    }

                    $lockedProducts[$item->id] = $prod;
                    $lockedVariants[$item->id] = $variant;
                }

                $user = Auth::user();
                $userId = $user ? $user->id : null;

                if ($isUsingSavedAddress) {
                    /** @var Address|null $address */
                    $address = $user->addresses()->where('id', $request->selected_address_id)->first();
                    if (! $address) {
                        throw new \Exception('The selected delivery address could not be found.');
                    }
                } else {
                    // Format address line with type, avoiding duplicate (Home)/(Office)
                    $formattedAddressLine = trim($request->address_line);
                    if ($request->filled('address_type')) {
                        $typeTag = '(' . ucfirst(trim($request->address_type)) . ')';
                        if (! str_contains($formattedAddressLine, $typeTag)) {
                            $formattedAddressLine .= ' ' . $typeTag;
                        }
                    }

                    $addressData = [
                        'name' => $request->name,
                        'mobile' => $request->mobile,
                        'email' => $request->email,
                        'address_line' => $formattedAddressLine,
                        'street' => $request->street,
                        'road' => $request->road ?: $request->street,
                        'landmark' => $request->landmark,
                        'city' => $request->city,
                        'state' => $request->state,
                        'pincode' => $request->pincode,
                    ];

                    if ($userId) {
                        $shouldSave = $request->has('save_address') ? $request->boolean('save_address') : true;
                        if ($shouldSave) {
                            $address = Address::findOrCreateOrUpdate($userId, $addressData);
                        } else {
                            $existing = Address::findMatchingForUser($userId, $addressData);
                            $address = $existing ?: Address::create(array_merge($addressData, ['user_id' => null, 'is_default' => false]));
                        }
                    } else {
                        $address = Address::create(array_merge($addressData, ['user_id' => null, 'is_default' => false]));
                    }
                }

                // Calculate Totals
                $subtotal = $cart->subtotal;
                $freeShippingMin = (float) StoreSetting::get('free_shipping_min', '999');
                $shippingFee = ($subtotal >= $freeShippingMin) ? 0.0 : (float) StoreSetting::get('shipping_flat_fee', '99');

                $couponCode = session('applied_coupon');
                $discount = 0.0;
                if ($couponCode) {
                    $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
                    if ($coupon && $coupon->isValidForAmount($subtotal)) {
                        $discount = $coupon->calculateDiscount($subtotal);
                        $coupon->increment('used_count');
                    }
                }

                $total = max(0, $subtotal - $discount + $shippingFee);
                $orderNumber = 'RAY-'.date('Ymd').'-'.strtoupper(Str::random(5));

                // Create Order
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => $userId,
                    'guest_token' => GuestSessionService::getGuestToken(),
                    'address_id' => $address->id,
                    'subtotal' => $subtotal,
                    'coupon_discount' => $discount,
                    'coupon_code' => $couponCode,
                    'shipping_fee' => $shippingFee,
                    'total_amount' => $total,
                    'status' => 'Pending Verification',
                    'notes' => $request->notes,
                ]);

                // Create Order Items & Decrement Stock atomically on locked rows
                foreach ($cart->items as $item) {
                    $variantInfo = $item->variant ? $item->variant->value : null;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'product_sku' => $item->product->sku,
                        'product_image' => $item->product->primaryImage?->image_url ?? $item->product->images->first()?->image_url ?? parse_url($item->product->effective_primary_image, PHP_URL_PATH),
                        'variant_info' => $variantInfo,
                        'unit_price' => $item->price,
                        'quantity' => $item->quantity,
                        'subtotal' => $item->price * $item->quantity,
                    ]);

                    // Decrement locked product inventory
                    $prod = $lockedProducts[$item->id] ?? $item->product;
                    $prod->decrement('stock_quantity', $item->quantity);

                    // Decrement locked variant inventory
                    $var = $lockedVariants[$item->id] ?? $item->variant;
                    if ($var) {
                        $var->decrement('stock_quantity', $item->quantity);
                    }
                }

                // Create Payment Record with Screenshot
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => 'Static QR / UPI',
                    'screenshot_path' => $screenshotPath,
                    'transaction_reference' => $request->transaction_reference ?: 'UPI-PROMPT-SUBMISSION',
                    'status' => 'Pending Verification',
                ]);

                // Clear Cart & Coupon
                $cart->items()->delete();
                session()->forget('applied_coupon');

                return $order;
            });
        } catch (\Throwable $e) {
            // Delete uploaded file if transaction rolled back
            if ($screenshotPath && file_exists(public_path($screenshotPath))) {
                @unlink(public_path($screenshotPath));
            }

            Log::warning('Checkout concurrency exception: '.$e->getMessage());

            return redirect()->route('cart')->with('error', $e->getMessage());
        }

        // Dispatch professional e-commerce order notification emails in the background (zero site latency)
        defer(function () use ($order) {
            try {
                $customerEmail = $order->address?->email ?: ($order->user?->email ?: null);
                if ($customerEmail) {
                    Mail::to($customerEmail)->send(new OrderPlacedCustomerMail($order));
                }

                $adminEmail = StoreSetting::get('admin_email') ?? config('services.brevo.admin_email') ?? StoreSetting::get('store_email', 'admin@raykajewellery.com');
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new NewOrderAdminMail($order));
                }
            } catch (\Throwable $e) {
                Log::warning("Order #{$order->order_number} email dispatch warning: ".$e->getMessage());
            }
        });

        return redirect()->route('order.confirmation', $order->order_number)
            ->with('success', 'Order placed successfully! We are verifying your payment screenshot.');
    }

    public function confirmation($orderNumber)
    {
        $order = Order::with(['items.product', 'address', 'payment'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('storefront.order_confirmation', compact('order'));
    }
}
