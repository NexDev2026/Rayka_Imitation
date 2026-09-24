<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Services\GuestSessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $guestToken = GuestSessionService::getGuestToken();

        $query = Wishlist::with(['product.images', 'product.category']);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('guest_token', $guestToken);
        }

        $wishlists = $query->latest()->get();

        return view('storefront.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->product_id;
        $userId = Auth::id();
        $guestToken = GuestSessionService::getGuestToken();

        $query = Wishlist::where('product_id', $productId);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('guest_token', $guestToken);
        }

        $existing = $query->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
            $message = 'Removed from your royal wishlist';
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'guest_token' => $userId ? null : $guestToken,
                'product_id' => $productId,
            ]);
            $status = 'added';
            $message = 'Added to your royal wishlist!';
        }

        $count = $this->getCurrentCount();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => $status,
                'message' => $message,
                'count' => $count,
            ]);
        }

        return back()->with('success', $message);
    }

    public function remove($id)
    {
        $userId = Auth::id();
        $guestToken = GuestSessionService::getGuestToken();

        $item = Wishlist::where('id', $id)
            ->where(function ($q) use ($userId, $guestToken) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('guest_token', $guestToken);
                }
            })->firstOrFail();

        $item->delete();

        return back()->with('success', 'Item removed from wishlist.');
    }

    public function getCounts()
    {
        $wishlistCount = $this->getCurrentCount();

        // Use a read-only lookup — never firstOrCreate — so we don't mint ghost cart rows
        // on every badge refresh call from a new guest browser.
        $userId = Auth::id();
        $guestToken = GuestSessionService::getGuestToken();

        $cartQuery = Cart::with('items');
        if ($userId) {
            $cartQuery->where('user_id', $userId);
        } else {
            $cartQuery->where('guest_token', $guestToken);
        }
        $cart = $cartQuery->first();

        $cartCount = 0;
        $cartItems = [];
        if ($cart) {
            foreach ($cart->items as $ci) {
                $pid = (int) $ci->product_id;
                $cartItems[$pid] = ($cartItems[$pid] ?? 0) + (int) $ci->quantity;
                $cartCount += (int) $ci->quantity;
            }
        }

        $wishlistItems = Wishlist::where(function ($q) use ($userId, $guestToken) {
            if ($userId) {
                $q->where('user_id', $userId);
            } else {
                $q->where('guest_token', $guestToken);
            }
        })->pluck('product_id')->map(fn ($id) => (int) $id)->toArray();

        return response()->json([
            'wishlist_count' => $wishlistCount,
            'cart_count' => $cartCount,
            'cart_items' => (object) $cartItems,
            'wishlist_items' => $wishlistItems,
        ]);
    }

    private function getCurrentCount(): int
    {
        $userId = Auth::id();
        $guestToken = GuestSessionService::getGuestToken();

        if ($userId) {
            return Wishlist::where('user_id', $userId)->count();
        }

        return Wishlist::where('guest_token', $guestToken)->count();
    }
}
