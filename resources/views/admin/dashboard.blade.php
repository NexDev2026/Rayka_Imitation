@extends('admin.layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Operational Overview & Traffic Analytics')

@section('content')
<div class="space-y-8">

    <!-- 1. KEY METRICS ROW (5 Cards Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Total Revenue -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-stone-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Total Revenue</span>
                <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D] mt-1">₹{{ number_format($totalRevenue) }}</h3>
                <p class="text-[11px] text-emerald-700 font-medium mt-1">Today: ₹{{ number_format($todayRevenue) }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-[#D4AF6A] flex items-center justify-center text-xl font-bold shrink-0">
                ₹
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-stone-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Orders</span>
                <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D] mt-1">{{ $totalOrdersCount }}</h3>
                <p class="text-[11px] text-stone-500 font-medium mt-1">Today: {{ $todayOrdersCount }} placed</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-xl shrink-0">
                <svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
            </div>
        </div>

        <!-- Unique Store Visitors -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-stone-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Unique Visitors</span>
                <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D] mt-1" id="kpiTodayUnique">{{ number_format($todayUniqueVisitors) }}</h3>
                <p class="text-[11px] text-[#996E2E] font-medium mt-1"><span id="kpiTodayViews">{{ number_format($todayViews) }}</span> Views • {{ number_format($monthlyUniqueVisitors) }} Mo.</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl shrink-0 relative">
                <svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
            </div>
        </div>

        <!-- Pending Payment Verification (Crucial Workflow Metric) -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border {{ $pendingPaymentsCount > 0 ? 'border-amber-400 ring-2 ring-amber-200' : 'border-stone-200' }} shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-amber-900 uppercase tracking-wider">Pending QR</span>
                <h3 class="font-serif-royal text-2xl font-bold text-amber-900 mt-1">{{ $pendingPaymentsCount }}</h3>
                <a href="{{ route('admin.orders.index', ['status' => 'Pending Verification']) }}" class="text-[11px] text-amber-800 underline font-medium mt-1 block">
                    Review proofs →
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shrink-0">
                <svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-stone-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-rose-800 uppercase tracking-wider">Low Stock</span>
                <h3 class="font-serif-royal text-2xl font-bold text-rose-800 mt-1">{{ $lowStockProducts->count() }}</h3>
                <span class="text-[11px] text-stone-500">Less than 10 units</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center text-xl shrink-0">
                <svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
        </div>

    </div>

    <!-- 2. ANALYTICS CHARTS (DAILY USER REACH & DAILY ORDERS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8" id="telemetrySection" x-data="dashboardTelemetry()">
        
        <!-- User Reach & Unique Visitors Chart (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-5 sm:p-6 border border-stone-200 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] flex items-center gap-2">
                        <span>Daily Reach & Unique Visitors</span>
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Pulse
                        </span>
                    </h3>
                    <p class="text-xs text-stone-500">Dual-stream telemetry: Total Impressions vs Unique Visitors.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="inline-flex bg-stone-100 p-0.5 rounded-lg text-xs font-semibold">
                        <button type="button" @click="fetchData(7)" :class="days === 7 ? 'bg-white text-[#4A2C1D] shadow-xs' : 'text-stone-500 hover:text-stone-800'" class="px-2.5 py-1 rounded-md transition cursor-pointer">7D</button>
                        <button type="button" @click="fetchData(14)" :class="days === 14 ? 'bg-white text-[#4A2C1D] shadow-xs' : 'text-stone-500 hover:text-stone-800'" class="px-2.5 py-1 rounded-md transition cursor-pointer">14D</button>
                        <button type="button" @click="fetchData(30)" :class="days === 30 ? 'bg-white text-[#4A2C1D] shadow-xs' : 'text-stone-500 hover:text-stone-800'" class="px-2.5 py-1 rounded-md transition cursor-pointer">30D</button>
                    </div>
                    <button type="button" @click="fetchData(days)" :disabled="loading" class="p-1.5 text-stone-500 hover:text-[#996E2E] bg-stone-50 hover:bg-stone-100 rounded-lg border border-stone-200 transition cursor-pointer" title="Refresh Telemetry">
                        <svg class="w-4 h-4" :class="{'animate-spin text-[#996E2E]': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                </div>
            </div>
            <div class="h-64 relative">
                <canvas id="userReachChart"></canvas>
            </div>
        </div>

        <!-- Daily Orders & Revenue Chart (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl p-5 sm:p-6 border border-stone-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                        Daily Orders Trend
                    </h3>
                    <p class="text-xs text-stone-500">Order count distribution over selected period.</p>
                </div>
            </div>
            <div class="h-64 relative">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>

    </div>

    <!-- 3. RECENT ORDERS & PAYMENT VERIFICATION SHORTLIST -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-stone-200 flex items-center justify-between">
            <div>
                <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                    Recent Orders & Payment Verification Status
                </h3>
                <p class="text-xs text-stone-500">Inspect customer screenshot proofs and confirm orders.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-[#996E2E] hover:underline">
                View All Orders →
            </a>
        </div>

        <!-- 1. MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($recentOrders as $ord)
                <div class="p-4 space-y-2.5 hover:bg-stone-50/60 transition text-xs">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-mono font-bold text-sm text-[#4A2C1D] block">{{ $ord->order_number }}</span>
                            <span class="text-[11px] text-stone-500 font-medium">{{ $ord->user?->name ?: 'Guest Customer' }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border shrink-0 {{ $ord->status_badge_class }}">
                            {{ $ord->status }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-stone-100">
                        <div>
                            <span class="text-stone-400 text-[10px]">Total Amount:</span>
                            <span class="font-bold text-stone-900 block font-serif-royal text-sm">₹{{ number_format($ord->total_amount) }}</span>
                        </div>
                        <div>
                            @if($ord->payment && $ord->payment->screenshot_path)
                                <a href="{{ asset($ord->payment->screenshot_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[#996E2E] hover:underline font-medium text-[11px]">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>Proof</span>
                                </a>
                            @else
                                <span class="text-stone-400 italic text-[11px]">No proof</span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-stone-100">
                        <a href="{{ route('admin.orders.show', $ord->id) }}" class="block w-full py-2 bg-[#4A2C1D] text-[#E7C77B] text-center rounded-xl font-bold text-xs hover:bg-[#2E180E] transition">
                            Review & Verify →
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-stone-400 text-xs">
                    No orders registered yet.
                </div>
            @endforelse
        </div>

        <!-- 2. DESKTOP TABULAR VIEW (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 uppercase border-b border-stone-200 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4">Order #</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Amount</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Payment Proof</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($recentOrders as $ord)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 font-mono font-bold text-[#4A2C1D] whitespace-nowrap">
                                {{ $ord->order_number }}
                            </td>
                            <td class="p-4">
                                <p class="font-semibold text-stone-900">{{ $ord->user?->name ?: 'Guest Customer' }}</p>
                                <p class="text-[11px] text-stone-500">{{ $ord->user?->mobile ?: $ord->user?->email }}</p>
                            </td>
                            <td class="p-4 font-bold whitespace-nowrap">
                                ₹{{ number_format($ord->total_amount) }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $ord->status_badge_class }}">
                                    {{ $ord->status }}
                                </span>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if($ord->payment && $ord->payment->screenshot_path)
                                    <a href="{{ asset($ord->payment->screenshot_path) }}" target="_blank" class="inline-flex items-center space-x-1 text-[#996E2E] hover:underline font-medium">
                                        <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>View Proof</span>
                                    </a>
                                @else
                                    <span class="text-stone-400 italic">No proof</span>
                                @endif
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $ord->id) }}" class="px-3 py-1.5 bg-[#4A2C1D] text-[#E7C77B] rounded-lg font-semibold hover:bg-[#2E180E] transition">
                                    Review →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-stone-400">No orders registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. LOW STOCK ALERTS SECTION -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-200 flex items-center justify-between">
            <h3 class="font-serif-royal text-base font-bold text-rose-900 flex items-center space-x-2">
                <span><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg></span>
                <span>Low Inventory Watchlist</span>
            </h3>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-[#996E2E] hover:underline">
                Manage Stock in Catalog →
            </a>
        </div>
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @forelse($lowStockProducts as $lsp)
                <div class="p-3.5 rounded-xl border border-rose-200 bg-rose-50/50 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-stone-500 uppercase">{{ $lsp->category?->name }}</span>
                        <h4 class="font-serif-royal text-xs font-bold text-[#4A2C1D] line-clamp-2 mt-0.5">{{ $lsp->name }}</h4>
                    </div>
                    <div class="mt-3 pt-2 border-t border-rose-200 flex items-center justify-between">
                        <span class="text-[10px] text-stone-500">Live Stock:</span>
                        <span class="bg-rose-600 text-white font-bold text-xs px-2 py-0.5 rounded-full">{{ $lsp->stock_quantity }} units</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-4 text-xs text-stone-500">
                    All jewellery inventory levels are healthy!
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
let reachChartInstance = null;
let ordersChartInstance = null;

function dashboardTelemetry() {
    return {
        days: 7,
        loading: false,
        pollTimer: null,
        init() {
            this.initCharts();
            this.pollTimer = setInterval(() => {
                if (!document.hidden) {
                    this.fetchData(this.days, true);
                }
            }, 30000);
        },
        destroy() {
            if (this.pollTimer) clearInterval(this.pollTimer);
        },
        initCharts() {
            const initialLabels = @json($chartLabels);
            const initialReach = @json($reachData);
            const initialUnique = @json($uniqueData);
            const initialOrders = @json($orderData);

            const ctxReach = document.getElementById('userReachChart');
            if (ctxReach) {
                reachChartInstance = new Chart(ctxReach, {
                    type: 'line',
                    data: {
                        labels: initialLabels,
                        datasets: [
                            {
                                label: 'Total Page Views',
                                data: initialReach,
                                borderColor: '#D4AF6A',
                                backgroundColor: 'rgba(212, 175, 106, 0.12)',
                                borderWidth: 2.5,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#D4AF6A',
                                pointRadius: 3,
                                pointHoverRadius: 5
                            },
                            {
                                label: 'Unique Visitors',
                                data: initialUnique,
                                borderColor: '#4A2C1D',
                                backgroundColor: 'rgba(74, 44, 29, 0.05)',
                                borderWidth: 2.5,
                                borderDash: [4, 4],
                                fill: false,
                                tension: 0.35,
                                pointBackgroundColor: '#4A2C1D',
                                pointRadius: 4,
                                pointHoverRadius: 6
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
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    font: { family: 'Poppins', size: 11, weight: '500' },
                                    color: '#4A2C1D'
                                }
                            },
                            tooltip: {
                                backgroundColor: '#261007',
                                titleColor: '#E7C77B',
                                bodyColor: '#FAF7F0',
                                padding: 10,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.04)' },
                                ticks: { font: { family: 'Poppins', size: 10 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Poppins', size: 10 } }
                            }
                        }
                    }
                });
            }

            const ctxOrders = document.getElementById('ordersChart');
            if (ctxOrders) {
                ordersChartInstance = new Chart(ctxOrders, {
                    type: 'bar',
                    data: {
                        labels: initialLabels,
                        datasets: [{
                            label: 'Daily Orders',
                            data: initialOrders,
                            backgroundColor: '#4A2C1D',
                            borderRadius: 6,
                            hoverBackgroundColor: '#D4AF6A'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#261007',
                                titleColor: '#E7C77B',
                                bodyColor: '#FAF7F0',
                                padding: 10,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, font: { family: 'Poppins', size: 10 } },
                                grid: { color: 'rgba(0,0,0,0.04)' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Poppins', size: 10 } }
                            }
                        }
                    }
                });
            }
        },
        async fetchData(daysCount, silent = false) {
            this.days = daysCount;
            if (!silent) this.loading = true;

            try {
                const res = await fetch(`{{ route('admin.analytics.telemetry') }}?days=${this.days}`);
                if (res.ok) {
                    const data = await res.json();
                    if (data.success) {
                        if (reachChartInstance) {
                            reachChartInstance.data.labels = data.labels;
                            reachChartInstance.data.datasets[0].data = data.reachData;
                            reachChartInstance.data.datasets[1].data = data.uniqueData;
                            reachChartInstance.update();
                        }
                        if (ordersChartInstance) {
                            ordersChartInstance.data.labels = data.labels;
                            ordersChartInstance.data.datasets[0].data = data.orderData;
                            ordersChartInstance.update();
                        }
                        if (data.stats) {
                            const kpiU = document.getElementById('kpiTodayUnique');
                            const kpiV = document.getElementById('kpiTodayViews');
                            if (kpiU && data.stats.todayUnique !== undefined) kpiU.innerText = Number(data.stats.todayUnique).toLocaleString();
                            if (kpiV && data.stats.todayViews !== undefined) kpiV.innerText = Number(data.stats.todayViews).toLocaleString();
                        }
                    }
                }
            } catch (e) {
                console.error('Telemetry fetch error', e);
            } finally {
                if (!silent) this.loading = false;
            }
        }
    };
}
</script>
@endpush
