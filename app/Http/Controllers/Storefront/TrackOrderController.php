<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StoreSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackOrderController extends Controller
{
    /**
     * Show tracking search page or direct order track result.
     */
    public function index(Request $request)
    {
        $orderNumber = trim((string) $request->query('order_number', ''));
        $order = null;

        if ($orderNumber !== '') {
            // If user is authenticated, check if this is their order
            if (Auth::check()) {
                $order = Order::with(['items.product', 'payment', 'address', 'user'])
                    ->where('order_number', $orderNumber)
                    ->where('user_id', Auth::id())
                    ->first();
            }

            // Or if already verified in current session
            if (! $order && session('verified_order_number') === $orderNumber) {
                $order = Order::with(['items.product', 'payment', 'address', 'user'])
                    ->where('order_number', $orderNumber)
                    ->first();
            }
        }

        return view('storefront.track_order', compact('order', 'orderNumber'));
    }

    /**
     * Handle track order search request.
     */
    public function search(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|max:100',
            'identifier' => 'required|string|max:255',
        ], [
            'order_number.required' => 'Please enter your Order Number (e.g. RAY-...).',
            'identifier.required' => 'Please enter the Mobile Number or Email used during checkout.',
        ]);

        $orderNumber = ltrim(strtoupper(trim($request->input('order_number'))), '#');
        $identifier = trim($request->input('identifier'));
        $digitsOnly = preg_replace('/\D/', '', $identifier);
        $cleanEmail = strtolower($identifier);

        $order = Order::with(['items.product', 'payment', 'address', 'user'])
            ->where('order_number', $orderNumber)
            ->first();

        if (! $order) {
            return back()->withInput()->with('error', "No order found matching Order #{$orderNumber}. Please check your order number.");
        }

        // Verify that the identifier matches the order's address or user
        $isMatch = false;

        // 1. Check logged in user
        if (Auth::check() && $order->user_id === Auth::id()) {
            $isMatch = true;
        }

        // 2. Check address email or mobile
        if ($order->address) {
            if ($cleanEmail !== '' && strtolower($order->address->email ?? '') === $cleanEmail) {
                $isMatch = true;
            }
            if ($digitsOnly !== '') {
                $addressDigits = preg_replace('/\D/', '', $order->address->mobile ?? '');
                if ($addressDigits !== '' && (str_ends_with($addressDigits, $digitsOnly) || str_ends_with($digitsOnly, $addressDigits))) {
                    $isMatch = true;
                }
            }
        }

        // 3. Check user email or mobile
        if ($order->user) {
            if ($cleanEmail !== '' && strtolower($order->user->email ?? '') === $cleanEmail) {
                $isMatch = true;
            }
            if ($digitsOnly !== '') {
                $userDigits = preg_replace('/\D/', '', $order->user->mobile ?? '');
                if ($userDigits !== '' && (str_ends_with($userDigits, $digitsOnly) || str_ends_with($digitsOnly, $userDigits))) {
                    $isMatch = true;
                }
            }
        }

        if (! $isMatch) {
            return back()->withInput()->with('error', 'The mobile number or email address does not match this Order ID. Please enter the contact details used at checkout.');
        }

        // Store verification in session so subsequent page refreshes keep the order open
        session(['verified_order_number' => $order->order_number]);

        return view('storefront.track_order', compact('order', 'orderNumber'));
    }

    /**
     * Download Invoice for a verified order.
     */
    public function downloadInvoice(Request $request, $orderNumber)
    {
        $order = Order::with(['items.product', 'payment', 'address', 'user'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Authorization check
        $authorized = false;
        if (Auth::check() && ($order->user_id === Auth::id() || Auth::user()->isAdmin())) {
            $authorized = true;
        } elseif (session('verified_order_number') === $order->order_number) {
            $authorized = true;
        }

        if (! $authorized) {
            return redirect()->route('order.track', ['order_number' => $orderNumber])
                ->with('error', 'Please verify your phone number or email to download this invoice.');
        }

        if (! $order->isConfirmed()) {
            return back()->with('error', 'Official Tax Invoice will be unlocked once payment is confirmed by our administration team.');
        }

        $storeName = StoreSetting::get('store_name', 'Rayka Imitation Jewellery');
        $storeAddress = StoreSetting::get('store_address');
        $storePhone = StoreSetting::get('store_phone');
        $storeEmail = StoreSetting::get('store_email');
        $upiId = StoreSetting::get('upi_id');

        $pdf = Pdf::loadView('pdf.invoice', compact('order', 'storeName', 'storeAddress', 'storePhone', 'storeEmail', 'upiId'));

        return $pdf->download("Rayka-Invoice-{$order->order_number}.pdf");
    }
}
