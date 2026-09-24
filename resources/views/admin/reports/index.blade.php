@extends('admin.layouts.admin')

@section('title', 'Reports & Analytics')
@section('page_title', 'Performance & Traffic Analytics')

@section('content')
<div class="space-y-8">

    <!-- Monthly Summary Metric Cards (4 Cards Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
        <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-xs">
            <span class="font-semibold text-stone-500 uppercase">Monthly Revenue</span>
            <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D] mt-1">₹{{ number_format($monthlyRevenue) }}</h3>
            <p class="text-emerald-700 text-[11px] mt-1">Month to date earnings</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-xs">
            <span class="font-semibold text-stone-500 uppercase">Monthly Orders</span>
            <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D] mt-1">{{ $monthlyOrders }}</h3>
            <p class="text-stone-500 text-[11px] mt-1">Total patron transactions</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-xs">
            <span class="font-semibold text-stone-500 uppercase">Total Impressions</span>
            <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D] mt-1">{{ number_format($monthlyVisits) }}</h3>
            <p class="text-[#996E2E] text-[11px] mt-1">Total catalogue hits</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200 shadow-xs">
            <span class="font-semibold text-stone-500 uppercase">Unique Store Visitors</span>
            <h3 class="font-serif-royal text-2xl font-bold text-emerald-800 mt-1">{{ number_format($monthlyUniqueVisitors) }}</h3>
            <p class="text-emerald-700 text-[11px] mt-1">Distinct verified audience</p>
        </div>
    </div>

    <!-- 30 Days Traffic & Orders Trend Chart -->
    <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                    30-Day Traffic Reach & Orders Trajectory
                </h3>
                <p class="text-xs text-stone-500">Correlation between daily store visits and completed customer purchases.</p>
            </div>
        </div>
        <div class="h-72">
            <canvas id="monthlyTrendChart"></canvas>
        </div>
    </div>

    <!-- Top Selling Products Leaderboard -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-200">
            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                Top Selling Jewellery Creations
            </h3>
        </div>
        <!-- 1. MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($bestSellers as $idx => $bs)
                <div class="p-4 flex items-center justify-between gap-3 text-xs hover:bg-stone-50/60 transition">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-7 h-7 rounded-lg bg-amber-50 border border-amber-200 text-[#996E2E] font-bold text-xs flex items-center justify-center shrink-0">
                            #{{ $idx + 1 }}
                        </span>
                        <div class="min-w-0">
                            <strong class="font-semibold text-stone-900 block truncate">{{ $bs->product_name }}</strong>
                            <span class="text-stone-400 text-[11px]">{{ $bs->total_qty }} units sold</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="font-serif-royal font-bold text-sm text-[#4A2C1D] block">₹{{ number_format($bs->total_sales) }}</span>
                        <span class="text-stone-400 text-[10px]">Revenue</span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-stone-400 text-xs">
                    No sales records registered yet.
                </div>
            @endforelse
        </div>

        <!-- 2. DESKTOP TABULAR VIEW (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 uppercase border-b border-stone-200 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4">Rank</th>
                        <th class="p-4">Product Name</th>
                        <th class="p-4">Units Sold</th>
                        <th class="p-4 text-right">Total Revenue (₹)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($bestSellers as $idx => $bs)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 font-bold text-[#996E2E] whitespace-nowrap">#{{ $idx + 1 }}</td>
                            <td class="p-4 font-semibold text-stone-800">{{ $bs->product_name }}</td>
                            <td class="p-4 font-bold whitespace-nowrap">{{ $bs->total_qty }} units</td>
                            <td class="p-4 text-right font-serif-royal font-bold text-sm text-[#4A2C1D] whitespace-nowrap">₹{{ number_format($bs->total_sales) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-stone-400">No sales records registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dates = @json($dates);
    const views = @json($viewsTrend);
    const unique = @json($uniqueTrend);
    const orders = @json($ordersTrend);

    const ctx = document.getElementById('monthlyTrendChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Total Page Views',
                        data: views,
                        borderColor: '#D4AF6A',
                        backgroundColor: 'rgba(212, 175, 106, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Unique Visitors',
                        data: unique,
                        borderColor: '#4A2C1D',
                        backgroundColor: 'rgba(74, 44, 29, 0.05)',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        tension: 0.3,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Orders Placed',
                        data: orders,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: { type: 'linear', display: true, position: 'left', beginAtZero: true, title: { display: true, text: 'Traffic (Views / Visitors)', font: { size: 10 } } },
                    y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false }, beginAtZero: true, title: { display: true, text: 'Orders', font: { size: 10 } } }
                }
            }
        });
    }
});
</script>
@endpush

