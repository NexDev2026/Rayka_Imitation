<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // 1. Initial Historical Backfill if logs table is empty
        $this->ensureHistoricalLogsBootstrapped();

        $query = ActivityLog::query()->latest('created_at');

        // Filter: Category
        $category = $request->query('category', 'all');
        if ($category === 'deletions') {
            $query->where('action', 'like', '%DELETE%');
        } elseif ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        // Filter: Actor Type (customer vs admin)
        $actorType = $request->query('actor_type', 'all');
        if ($actorType && $actorType !== 'all') {
            $query->where('actor_type', $actorType);
        }

        // Filter: Date Range
        $dateRange = $request->query('date_range', 'all');
        if ($dateRange === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === '7d') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($dateRange === '30d') {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        }

        // Filter: Search
        $search = $request->query('search');
        if (! empty($search)) {
            $query->search($search);
        }

        // Stats for KPI summary cards
        $totalEvents = ActivityLog::count();
        $inboundOrdersCount = ActivityLog::where('action', 'ORDER_PLACED')->count();
        $restoredUnitsCount = ActivityLog::where('action', 'INVENTORY_RESTORED')->count();
        $cancelledOrRejectedCount = ActivityLog::whereIn('action', ['ORDER_CANCELLED', 'PAYMENT_REJECTED'])->count();
        $deletedOrdersCount = ActivityLog::where('action', 'ORDER_DELETED')->count();

        $logs = $query->paginate(25)->withQueryString();

        return view('admin.activity-logs.index', compact(
            'logs',
            'category',
            'actorType',
            'dateRange',
            'search',
            'totalEvents',
            'inboundOrdersCount',
            'restoredUnitsCount',
            'cancelledOrRejectedCount',
            'deletedOrdersCount'
        ));
    }

    /**
     * Seeds initial real-world audit logs from existing orders and user accounts
     * so that the audit trail is immediately populated with historical truth.
     */
    protected function ensureHistoricalLogsBootstrapped(): void
    {
        if (ActivityLog::count() > 0) {
            return;
        }

        // Bootstrap from Orders
        $orders = Order::with(['items.product', 'payment', 'user', 'address'])->latest()->get();

        foreach ($orders as $order) {
            $customerName = $order->address?->full_name ?? ($order->user?->name ?? 'Customer');
            $customerEmail = $order->address?->email ?? ($order->user?->email ?? null);
            $orderDate = $order->created_at ?? now();

            // 1. Order Placed event
            ActivityLog::create([
                'actor_type' => 'customer',
                'actor_id' => $order->user_id,
                'actor_name' => $customerName,
                'actor_email' => $customerEmail,
                'action' => 'ORDER_PLACED',
                'category' => 'orders',
                'description' => "Customer {$customerName} placed Order #{$order->order_number} for ₹" . number_format($order->total_amount, 2) . " ({$order->items->count()} items).",
                'subject_type' => 'Order',
                'subject_id' => (string) $order->id,
                'subject_ref' => $order->order_number,
                'metadata' => [
                    'total_amount' => (float) $order->total_amount,
                    'items_count' => $order->items->count(),
                    'payment_method' => $order->payment?->payment_method ?? 'Static QR / UPI',
                ],
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // 2. Cancellation / Rejection events
            if ($order->isCancelled() || $order->isRejected()) {
                $isByAdmin = $order->isCancelledByAdmin() || $order->isRejected();
                $actorType = $isByAdmin ? 'admin' : 'customer';
                $actorName = $isByAdmin ? 'Rayka Administrator' : $customerName;
                $actionName = $order->isRejected() ? 'PAYMENT_REJECTED' : 'ORDER_CANCELLED';
                $actionDate = $order->cancelled_at ?? ($order->updated_at ?? $orderDate->addMinutes(15));
                $reason = $order->cancellation_reason ?: ($order->notes ?? 'Order closed');

                ActivityLog::create([
                    'actor_type' => $actorType,
                    'actor_id' => $isByAdmin ? 1 : $order->user_id,
                    'actor_name' => $actorName,
                    'actor_email' => $isByAdmin ? 'admin@raykajewellery.com' : $customerEmail,
                    'action' => $actionName,
                    'category' => 'orders',
                    'description' => ($isByAdmin ? "Admin marked Order #{$order->order_number} as {$order->status}." : "Customer {$customerName} requested order cancellation.") . " Reason: {$reason}",
                    'subject_type' => 'Order',
                    'subject_id' => (string) $order->id,
                    'subject_ref' => $order->order_number,
                    'metadata' => [
                        'status' => $order->status,
                        'reason' => $reason,
                    ],
                    'created_at' => $actionDate,
                    'updated_at' => $actionDate,
                ]);

                // 3. Inventory Restored events for each item
                foreach ($order->items as $item) {
                    $qty = max(1, (int) $item->quantity);
                    ActivityLog::create([
                        'actor_type' => $actorType,
                        'actor_id' => $isByAdmin ? 1 : $order->user_id,
                        'actor_name' => $actorName,
                        'actor_email' => $isByAdmin ? 'admin@raykajewellery.com' : $customerEmail,
                        'action' => 'INVENTORY_RESTORED',
                        'category' => 'inventory',
                        'description' => "Restored +{$qty} unit(s) of '{$item->product_name}' to inventory for Order #{$order->order_number}.",
                        'subject_type' => 'Product',
                        'subject_id' => (string) $item->product_id,
                        'subject_ref' => $item->product_sku ?: $order->order_number,
                        'metadata' => [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product_name,
                            'quantity' => $qty,
                            'variant' => $item->variant_info,
                        ],
                        'created_at' => $actionDate,
                        'updated_at' => $actionDate,
                    ]);
                }
            } elseif ($order->isConfirmed()) {
                // Payment confirmed event
                $confirmDate = $order->payment?->verified_at ?? $orderDate->addMinutes(5);
                ActivityLog::create([
                    'actor_type' => 'admin',
                    'actor_id' => 1,
                    'actor_name' => 'Rayka Administrator',
                    'actor_email' => 'admin@raykajewellery.com',
                    'action' => 'PAYMENT_CONFIRMED',
                    'category' => 'orders',
                    'description' => "Administrator verified UPI payment proof and confirmed Order #{$order->order_number}.",
                    'subject_type' => 'Order',
                    'subject_id' => (string) $order->id,
                    'subject_ref' => $order->order_number,
                    'metadata' => [
                        'status' => $order->status,
                        'total_amount' => (float) $order->total_amount,
                    ],
                    'created_at' => $confirmDate,
                    'updated_at' => $confirmDate,
                ]);
            }
        }

        // Bootstrap from User registrations
        $users = User::latest()->take(10)->get();
        foreach ($users as $user) {
            ActivityLog::create([
                'actor_type' => $user->isAdmin() ? 'admin' : 'customer',
                'actor_id' => $user->id,
                'actor_name' => $user->name,
                'actor_email' => $user->email,
                'action' => $user->isAdmin() ? 'ADMIN_SETUP' : 'USER_REGISTERED',
                'category' => 'auth',
                'description' => ($user->isAdmin() ? "System administrator account initialized: " : "New customer registered: ") . "{$user->name} ({$user->email})",
                'subject_type' => 'User',
                'subject_id' => (string) $user->id,
                'subject_ref' => $user->email,
                'metadata' => [
                    'role' => $user->role,
                    'mobile' => $user->mobile,
                ],
                'created_at' => $user->created_at ?? now(),
                'updated_at' => $user->created_at ?? now(),
            ]);
        }
    }
}
