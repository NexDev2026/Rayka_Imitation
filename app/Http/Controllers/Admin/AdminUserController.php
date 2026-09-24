<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['orders', 'reviews'])
            ->withSum('orders', 'total_amount');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('mobile', 'like', "%{$term}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total'    => User::count(),
            'customers' => User::where('role', 'customer')->count(),
            'admins'   => User::whereIn('role', ['admin', 'super-admin'])->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function show(int $id)
    {
        $user = User::withCount(['orders', 'reviews'])
            ->withSum('orders', 'total_amount')
            ->findOrFail($id);

        $orders = Order::with(['items', 'payment'])
            ->where('user_id', $id)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.users.show', compact('user', 'orders'));
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return back()->with('error', 'Admin accounts cannot be deleted via this panel.');
        }

        // Prevent deletion if they have active/pending orders
        $activeOrders = $user->orders()
            ->whereNotIn('status', ['Delivered', 'Cancelled', 'Rejected'])
            ->count();

        if ($activeOrders > 0) {
            return back()->with('error', "Cannot delete: this user has {$activeOrders} active order(s) in progress.");
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User #{$id} ({$user->name}) deleted successfully.");
    }
}
