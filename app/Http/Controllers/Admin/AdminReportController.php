<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PageView;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $monthlyRevenue = Order::where('created_at', '>=', $startOfMonth)
            ->whereIn('status', ['Confirmed', 'Processing', 'Shipped', 'Delivered'])
            ->sum('total_amount');

        $monthlyOrders = Order::where('created_at', '>=', $startOfMonth)->count();
        $monthlyVisits = PageView::authentic()->whereDate('viewed_date', '>=', $startOfMonth)->count();
        $monthlyUniqueVisitors = PageView::authentic()->whereDate('viewed_date', '>=', $startOfMonth)->distinct('ip_address')->count('ip_address');

        // 30 Days Trend Data
        $dates = [];
        $viewsTrend = [];
        $uniqueTrend = [];
        $ordersTrend = [];

        for ($i = 29; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i);
            $dStr = $d->toDateString();
            $dates[] = $d->format('d M');

            $views = PageView::authentic()->whereDate('viewed_date', $dStr)->count();
            $unique = PageView::authentic()->whereDate('viewed_date', $dStr)->distinct('ip_address')->count('ip_address');

            $viewsTrend[] = $views;
            $uniqueTrend[] = $unique;

            $ordCount = Order::whereDate('created_at', $dStr)->count();
            $ordersTrend[] = $ordCount;
        }

        // Top Sold Products
        $bestSellers = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(8)
            ->get();

        return view('admin.reports.index', compact(
            'monthlyRevenue',
            'monthlyOrders',
            'monthlyVisits',
            'monthlyUniqueVisitors',
            'dates',
            'viewsTrend',
            'uniqueTrend',
            'ordersTrend',
            'bestSellers'
        ));
    }
}
