<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Services\GuestSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        $cart = GuestSessionService::getCart();
        $cart->load(['items.product.images', 'items.variant']);

        $subtotal = $cart->subtotal;
        $freeShippingMin = (float) StoreSetting::get('free_shipping_min', '999');
        $shippingFee = ($subtotal >= $freeShippingMin || $subtotal == 0) ? 0.0 : (float) StoreSetting::get('shipping_flat_fee', '99');

        // Check active applied coupon in session
        $couponCode = session('applied_coupon');
        $discount = 0.0;
        $coupon = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
            if ($coupon && $coupon->isValidForAmount($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('applied_coupon');
                $coupon = null;
            }
        }

        $availableCoupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get();

        $total = max(0.0, floatval($subtotal) - floatval($discount) + floatval($shippingFee));

        // Check if any items in bag have stock issues
        $hasOutOfStock = false;
        foreach ($cart->items as $item) {
            $stock = $item->product ? (int) $item->product->stock_quantity : 0;
            if ($item->variant) {
                $stock = min($stock, (int) $item->variant->stock_quantity);
            }
            if ($stock < $item->quantity) {
                $hasOutOfStock = true;
            }
        }

        return view('storefront.cart', compact('cart', 'subtotal', 'shippingFee', 'freeShippingMin', 'coupon', 'discount', 'total', 'availableCoupons', 'hasOutOfStock'));
    }

    public function add(Request $request)
    {
        if ($request->has('variant_id') && ($request->variant_id === '' || $request->variant_id === 'null' || $request->variant_id === '0')) {
            $request->merge(['variant_id' => null]);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        Log::info('CartController::add called!', $request->all());

        $product = Product::findOrFail($request->product_id);
        $quantity = (int) ($request->quantity ?: 1);
        $variantId = $request->variant_id ?: null;

        // Check variant stock or product stock
        $maxStock = $product->stock_quantity;
        $price = $product->price;

        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $maxStock = min($maxStock, $variant->stock_quantity);
                if ($variant->price_override) {
                    $price = $variant->price_override;
                }
            }
        }

        // Apply active category flash sale / offer discount if present
        $offer = $product->active_category_offer;
        if ($offer && $offer->discount_percentage > 0) {
            $discountAmount = ($price * $offer->discount_percentage) / 100;
            $price = round(max(0, $price - $discountAmount), 2);
        }

        if ($maxStock <= 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sorry, this jewellery piece is currently out of stock.']);
            }

            return back()->with('error', 'Sorry, this product is out of stock.');
        }

        $cart = GuestSessionService::getCart();
        /** @var CartItem|null $cartItem */
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        $currentQtyInCart = $cartItem ? $cartItem->quantity : 0;

        if ($currentQtyInCart >= $maxStock) {
            $msg = ($maxStock === 1)
                ? 'Only 1 unit available in stock for this exclusive jewellery piece. You already have it in your bag.'
                : "You already have the maximum available stock ({$maxStock} units) in your shopping bag.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }

            return back()->with('error', $msg);
        }

        $newQty = min($maxStock, $currentQtyInCart + $quantity);

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQty,
                'price' => $price,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => min($quantity, $maxStock),
                'price' => $price,
            ]);
        }

        $cart->unsetRelation('items');
        $cart->load('items');
        $cartCount = (int) $cart->items->sum('quantity');
        $cartSubtotal = (float) $cart->items->sum(fn ($ci) => $ci->price * $ci->quantity);
        $cartItems = [];
        foreach ($cart->items as $ci) {
            $pid = (int) $ci->product_id;
            $cartItems[$pid] = ($cartItems[$pid] ?? 0) + (int) $ci->quantity;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Added to your royal shopping bag!',
                'cart_count' => $cartCount,
                'count' => $cartCount,
                'cart_subtotal' => $cartSubtotal,
                'cart_items' => (object) $cartItems,
            ]);
        }

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout');
        }

        return back()->with('success', 'Added to your royal shopping bag!');
    }

    public function updateProductQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'change' => 'required|integer',
        ]);

        $productId = (int) $request->product_id;
        $change = (int) $request->change;
        $cart = GuestSessionService::getCart();

        /** @var CartItem|null $item */
        $item = $cart->items()->where('product_id', $productId)->first();

        if (! $item) {
            if ($change > 0) {
                return $this->add($request);
            }

            return response()->json(['success' => true, 'cart_count' => $cart->items()->sum('quantity'), 'item_qty' => 0, 'cart_items' => (object) []]);
        }

        $product = Product::findOrFail($productId);
        $maxStock = (int) $product->stock_quantity;
        if ($item->variant) {
            $maxStock = min($maxStock, (int) $item->variant->stock_quantity);
        }

        if ($change > 0 && $item->quantity >= $maxStock) {
            $msg = ($maxStock === 1)
                ? 'Only 1 unit is available in stock for this jewellery piece.'
                : "Maximum available stock ({$maxStock} units) reached.";

            return response()->json([
                'success' => false,
                'message' => $msg,
                'cart_count' => $cart->items()->sum('quantity'),
                'item_qty' => $item->quantity,
            ], 422);
        }

        $newQty = $item->quantity + $change;

        if ($newQty <= 0) {
            $item->delete();
            $msg = 'Removed from shopping bag.';
            $finalQty = 0;
        } else {
            $finalQty = min($maxStock, $newQty);
            $item->update(['quantity' => $finalQty]);
            $msg = ($change > 0) ? 'Quantity increased' : 'Quantity decreased';
        }

        $cart->unsetRelation('items');
        $cart->load('items');
        $cartItems = [];
        $subtotal = 0;
        foreach ($cart->items as $ci) {
            $pid = (int) $ci->product_id;
            $cartItems[$pid] = ($cartItems[$pid] ?? 0) + (int) $ci->quantity;
            $subtotal += ($ci->price * $ci->quantity);
        }

        $freeShippingMin = (float) StoreSetting::get('free_shipping_min', '999');
        $shippingFee = ($subtotal >= $freeShippingMin || $subtotal == 0) ? 0.0 : (float) StoreSetting::get('shipping_flat_fee', '99');

        $couponCode = session('applied_coupon');
        $discount = 0.0;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();
            if ($coupon && $coupon->isValidForAmount($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }

        $total = max(0.0, floatval($subtotal) - floatval($discount) + floatval($shippingFee));

        return response()->json([
            'success' => true,
            'message' => $msg,
            'cart_count' => (int) $cart->items->sum('quantity'),
            'item_qty' => $finalQty,
            'cart_items' => (object) $cartItems,
            'cart_subtotal' => $subtotal,
            'cart_discount' => $discount,
            'cart_total' => $total,
            'cart_shipping' => $shippingFee,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = GuestSessionService::getCart();
        /** @var CartItem $item */
        $item = $cart->items()->where('id', $id)->firstOrFail();

        $maxStock = $item->product->stock_quantity;
        if ($item->variant) {
            $maxStock = min($maxStock, $item->variant->stock_quantity);
        }

        $qty = min($request->quantity, $maxStock);
        $item->update(['quantity' => $qty]);

        return back()->with('success', 'Shopping bag updated.');
    }

    public function remove($id)
    {
        $cart = GuestSessionService::getCart();
        /** @var CartItem $item */
        $item = $cart->items()->where('id', $id)->firstOrFail();
        $item->delete();

        return back()->with('success', 'Item removed from shopping bag.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->code));
        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (! $coupon) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid coupon code. Please enter a valid royal promo code.'], 422);
            }

            return back()->with('coupon_error', 'Invalid coupon code. Please enter a valid royal promo code.');
        }

        $cart = GuestSessionService::getCart();
        $subtotal = $cart->subtotal;

        if (! $coupon->isValidForAmount($subtotal)) {
            $msg = "Coupon {$code} requires a minimum purchase of ₹".number_format($coupon->min_order_value, 2);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->with('coupon_error', $msg);
        }

        session(['applied_coupon' => $coupon->code]);
        $discount = $coupon->calculateDiscount($subtotal);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Coupon '{$coupon->code}' applied successfully!",
                'code' => $coupon->code,
                'discount' => $discount,
            ]);
        }

        return back()->with('coupon_success', "Coupon '{$coupon->code}' applied successfully!");
    }

    public function removeCoupon(Request $request)
    {
        session()->forget('applied_coupon');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Coupon removed.',
            ]);
        }

        return back()->with('success', 'Coupon removed.');
    }
}
