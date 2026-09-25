<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\PageView;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Key Metric Cards
        $todayOrdersCount = Order::whereDate('created_at', $today)->count();
        $totalOrdersCount = Order::count();
        $pendingPaymentsCount = Order::where('status', 'Pending Verification')->count();
        $totalRevenue = Order::whereIn('status', ['Confirmed', 'Processing', 'Shipped', 'Delivered'])->sum('total_amount');
        $todayRevenue = Order::whereDate('created_at', $today)->whereIn('status', ['Confirmed', 'Processing', 'Shipped', 'Delivered'])->sum('total_amount');

        // Unique Visitors & Page Views Telemetry (Strictly authentic visitors, excluding dummy loopback seed hits)
        $realPageViews = PageView::whereNotIn('ip_address', ['127.0.0.1', '::1', 'localhost']);

        $todayViews = (clone $realPageViews)->whereDate('viewed_date', $today)->count();
        $todayUniqueVisitors = (clone $realPageViews)->whereDate('viewed_date', $today)->distinct('ip_address')->count('ip_address');

        $monthlyViews = (clone $realPageViews)->whereDate('viewed_date', '>=', $startOfMonth)->count();
        $monthlyUniqueVisitors = (clone $realPageViews)->whereDate('viewed_date', '>=', $startOfMonth)->distinct('ip_address')->count('ip_address');

        // Low stock alerts (< 10 units)
        $lowStockProducts = Product::with('category')
            ->where('stock_quantity', '<=', 10)
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->take(5)
            ->get();

        // Recent Orders
        $recentOrders = Order::with(['user', 'payment', 'items'])
            ->latest()
            ->take(6)
            ->get();

        // Pending items counter
        $pendingReviewsCount = Review::where('status', 'pending')->count();
        $newInquiriesCount = Inquiry::where('status', 'new')->count();

        // Top Selling Products
        $topProducts = Product::with('images')
            ->where('is_featured', true)
            ->take(5)
            ->get();

        // 7-Day Chart Data
        $chartData = $this->buildTelemetryData(7);

        return view('admin.dashboard', compact(
            'todayOrdersCount',
            'totalOrdersCount',
            'pendingPaymentsCount',
            'totalRevenue',
            'todayRevenue',
            'todayViews',
            'todayUniqueVisitors',
            'monthlyViews',
            'monthlyUniqueVisitors',
            'lowStockProducts',
            'recentOrders',
            'pendingReviewsCount',
            'newInquiriesCount',
            'topProducts'
        ))->with($chartData);
    }

    public function telemetry(Request $request)
    {
        $days = (int) $request->query('days', 7);
        if (! in_array($days, [7, 14, 30])) {
            $days = 7;
        }

        $data = $this->buildTelemetryData($days);

        return response()->json([
            'success' => true,
            'days' => $days,
            'labels' => $data['chartLabels'],
            'reachData' => $data['reachData'],
            'uniqueData' => $data['uniqueData'],
            'orderData' => $data['orderData'],
            'revenueData' => $data['revenueData'],
            'stats' => [
                'totalViews' => array_sum($data['reachData']),
                'totalUnique' => array_sum($data['uniqueData']),
                'totalOrders' => array_sum($data['orderData']),
                'totalRevenue' => array_sum($data['revenueData']),
                'todayViews' => end($data['reachData']) ?: 0,
                'todayUnique' => end($data['uniqueData']) ?: 0,
            ],
            'timestamp' => Carbon::now()->toIso8601String(),
        ]);
    }

    protected function buildTelemetryData(int $days): array
    {
        $chartLabels = [];
        $reachData = [];
        $uniqueData = [];
        $orderData = [];
        $revenueData = [];

        $realPageViews = PageView::whereNotIn('ip_address', ['127.0.0.1', '::1', 'localhost']);

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->toDateString();
            $displayLabel = $date->format('d M');

            $chartLabels[] = $displayLabel;

            // Real Page Views count
            $visits = (clone $realPageViews)->whereDate('viewed_date', $dateString)->count();
            // Distinct Unique Visitors
            $unique = (clone $realPageViews)->whereDate('viewed_date', $dateString)->distinct('ip_address')->count('ip_address');

            $reachData[] = $visits;
            $uniqueData[] = $unique;

            // Daily Orders
            $orders = Order::whereDate('created_at', $dateString)->count();
            $orderData[] = $orders;

            // Daily Revenue
            $rev = Order::whereDate('created_at', $dateString)
                ->whereIn('status', ['Confirmed', 'Processing', 'Shipped', 'Delivered'])
                ->sum('total_amount');
            $revenueData[] = (float) $rev;
        }

        return compact('chartLabels', 'reachData', 'uniqueData', 'orderData', 'revenueData');
    }
}
