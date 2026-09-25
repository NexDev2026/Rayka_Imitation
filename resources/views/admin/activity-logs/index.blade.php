@extends('admin.layouts.admin')

@section('title', 'Activity & Audit Logs')
@section('page_title', 'Real-time Activity & Audit Trail')

@section('content')
<div class="space-y-6">

    <!-- 1. TOP METRIC KPI CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Events -->
        <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500">Total Audit Logs</span>
                <span class="w-8 h-8 rounded-xl bg-stone-100 text-stone-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-serif-royal text-2xl font-bold text-[#4A2C1D]">{{ number_format($totalEvents) }}</span>
                <span class="text-[11px] text-stone-400 font-medium">Recorded</span>
            </div>
        </div>

        <!-- Inbound Orders -->
        <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Orders Received</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-serif-royal text-2xl font-bold text-blue-900">{{ number_format($inboundOrdersCount) }}</span>
                <span class="text-[11px] text-blue-600 font-medium">Inbound</span>
            </div>
        </div>

        <!-- Stock Restored -->
        <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Stock Restorations</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-serif-royal text-2xl font-bold text-emerald-900">+{{ number_format($restoredUnitsCount) }}</span>
                <span class="text-[11px] text-emerald-600 font-medium">Returned</span>
            </div>
        </div>

        <!-- Cancellations & Deletions -->
        <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700">Cancellations & Purges</span>
                <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </span>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-serif-royal text-2xl font-bold text-rose-900">{{ number_format($cancelledOrRejectedCount + $deletedOrdersCount) }}</span>
                <span class="text-[11px] text-rose-600 font-medium">({{ $deletedOrdersCount }} Deleted)</span>
            </div>
        </div>
    </div>

    <!-- 2. FILTER & SEARCH CONTROL BAR -->
    <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-2xs space-y-3">
        <!-- Category Tabs -->
        <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 text-xs no-scrollbar">
            <a href="{{ route('admin.activity_logs.index', array_merge(request()->except('page', 'category'), ['category' => 'all'])) }}"
               class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ ($category === 'all' || !$category) ? 'bg-[#4A2C1D] text-[#E7C77B] font-bold shadow-xs' : 'bg-stone-50 text-stone-700 hover:bg-stone-100' }}">
                All Activities
            </a>
            <a href="{{ route('admin.activity_logs.index', array_merge(request()->except('page', 'category'), ['category' => 'orders'])) }}"
               class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ $category === 'orders' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-stone-50 text-stone-700 hover:bg-stone-100' }}">
                Orders & Payments
            </a>
            <a href="{{ route('admin.activity_logs.index', array_merge(request()->except('page', 'category'), ['category' => 'inventory'])) }}"
               class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ $category === 'inventory' ? 'bg-emerald-700 text-white font-bold shadow-xs' : 'bg-stone-50 text-stone-700 hover:bg-stone-100' }}">
                Inventory & Stock (+Units)
            </a>
            <a href="{{ route('admin.activity_logs.index', array_merge(request()->except('page', 'category'), ['category' => 'deletions'])) }}"
               class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ $category === 'deletions' ? 'bg-rose-700 text-white font-bold shadow-xs' : 'bg-stone-50 text-stone-700 hover:bg-stone-100' }}">
                Deletions & Purges
            </a>
            <a href="{{ route('admin.activity_logs.index', array_merge(request()->except('page', 'category'), ['category' => 'auth'])) }}"
               class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ $category === 'auth' ? 'bg-blue-700 text-white font-bold shadow-xs' : 'bg-stone-50 text-stone-700 hover:bg-stone-100' }}">
                User Registrations
            </a>
        </div>

        <!-- Search & Secondary Filters Form -->
        <form method="GET" action="{{ route('admin.activity_logs.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 pt-2 border-t border-stone-100 text-xs">
            <input type="hidden" name="category" value="{{ $category }}">

            <!-- Search Field -->
            <div class="sm:col-span-6 relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by Order #, Customer, Product, SKU or Action..."
                       class="w-full pl-9 pr-3 py-2 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 placeholder-stone-400 focus:outline-none focus:border-[#D4AF6A] focus:bg-white transition text-xs">
            </div>

            <!-- Actor Type Selector -->
            <div class="sm:col-span-3">
                <select name="actor_type" onchange="this.form.submit()" class="w-full py-2 px-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-700 focus:outline-none focus:border-[#D4AF6A] transition text-xs">
                    <option value="all" {{ $actorType === 'all' ? 'selected' : '' }}>All Actors (Admin & Users)</option>
                    <option value="customer" {{ $actorType === 'customer' ? 'selected' : '' }}>Customers / Users Only</option>
                    <option value="admin" {{ $actorType === 'admin' ? 'selected' : '' }}>Administrators Only</option>
                </select>
            </div>

            <!-- Date Range Selector -->
            <div class="sm:col-span-3 flex items-center gap-2">
                <select name="date_range" onchange="this.form.submit()" class="w-full py-2 px-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-700 focus:outline-none focus:border-[#D4AF6A] transition text-xs">
                    <option value="all" {{ $dateRange === 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ $dateRange === 'today' ? 'selected' : '' }}>Today Only</option>
                    <option value="7d" {{ $dateRange === '7d' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30d" {{ $dateRange === '30d' ? 'selected' : '' }}>Last 30 Days</option>
                </select>

                <button type="submit" class="px-3.5 py-2 bg-[#4A2C1D] text-[#E7C77B] rounded-xl font-bold shrink-0 hover:bg-[#2E180E] transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- 3. ACTIVITY LOGS FEED CONTAINER -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-2xs overflow-hidden">
        
        <!-- MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($logs as $log)
                <div class="p-4 space-y-2.5 hover:bg-stone-50/70 transition" x-data="{ expanded: false }">
                    <!-- Top: Badge, Actor, Time -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $log->badge_class }}">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                            <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold {{ $log->actor_type === 'admin' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">
                                {{ $log->actor_type === 'admin' ? 'Admin' : 'Customer' }}
                            </span>
                        </div>
                        <span class="text-[10px] text-stone-400 font-medium shrink-0">
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <!-- Actor Info & Subject Reference -->
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <div class="flex items-center gap-1.5 font-bold text-stone-800">
                            <span class="w-5 h-5 rounded-full bg-[#4A2C1D] text-[#E7C77B] text-[10px] flex items-center justify-center uppercase font-bold shrink-0">
                                {{ substr($log->actor_name ?: 'U', 0, 1) }}
                            </span>
                            <span class="truncate max-w-[170px]">{{ $log->actor_name ?: 'System' }}</span>
                        </div>
                        @if($log->subject_ref)
                            <span class="font-mono text-[11px] font-semibold text-[#996E2E] bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                {{ $log->subject_ref }}
                            </span>
                        @endif
                    </div>

                    <!-- Description -->
                    <p class="text-xs text-stone-700 leading-relaxed font-medium">
                        {{ $log->description }}
                    </p>

                    <!-- Expandable Metadata Toggle -->
                    @if(!empty($log->metadata) || $log->ip_address)
                        <div class="pt-1">
                            <button @click="expanded = !expanded" class="text-[11px] text-[#996E2E] hover:underline font-semibold flex items-center gap-1 cursor-pointer">
                                <span x-text="expanded ? 'Hide Details' : 'View Audit Context & Metadata ↓'"></span>
                            </button>
                            <div x-show="expanded" x-cloak class="mt-2 p-2.5 bg-stone-50 rounded-xl border border-stone-200 text-[11px] font-mono text-stone-600 space-y-1 overflow-x-auto">
                                @if($log->ip_address)
                                    <div><span class="text-stone-400">IP:</span> {{ $log->ip_address }}</div>
                                @endif
                                @if(!empty($log->metadata))
                                    <div><span class="text-stone-400">Payload:</span> {{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
                                @endif
                                <div><span class="text-stone-400">Timestamp:</span> {{ $log->created_at->format('d M, Y \a\t h:i:s A') }} IST</div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-stone-400 text-xs">
                    No activity records found matching filters.
                </div>
            @endforelse
        </div>

        <!-- DESKTOP TABULAR VIEW (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 uppercase border-b border-stone-200 font-semibold tracking-wider text-[11px]">
                    <tr>
                        <th class="p-4 w-40">Date & Time</th>
                        <th class="p-4 w-48">Actor</th>
                        <th class="p-4 w-40">Action</th>
                        <th class="p-4">Description & Audit Context</th>
                        <th class="p-4 w-36 text-right">Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-stone-50/70 transition">
                            <!-- Date & Time -->
                            <td class="p-4 whitespace-nowrap text-stone-600">
                                <div class="font-bold text-stone-800">{{ $log->created_at->format('d M, Y') }}</div>
                                <div class="text-[11px] text-stone-400 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $log->created_at->format('h:i A') }}</span>
                                    <span>•</span>
                                    <span class="text-amber-800 font-medium">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </td>

                            <!-- Actor (User or Admin) -->
                            <td class="p-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full bg-[#4A2C1D] text-[#E7C77B] text-xs flex items-center justify-center uppercase font-bold shrink-0 shadow-2xs">
                                        {{ substr($log->actor_name ?: 'U', 0, 1) }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="font-bold text-stone-800 truncate max-w-[140px]">{{ $log->actor_name ?: 'System' }}</div>
                                        <span class="inline-block text-[9px] px-1.5 py-0.2 rounded font-bold uppercase {{ $log->actor_type === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-sky-100 text-sky-800' }}">
                                            {{ $log->actor_type }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Action Badge -->
                            <td class="p-4 whitespace-nowrap">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $log->badge_class }}">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>

                            <!-- Description & Payload Details -->
                            <td class="p-4">
                                <p class="text-stone-800 font-medium leading-relaxed">
                                    {{ $log->description }}
                                </p>
                                @if(!empty($log->metadata) || $log->ip_address)
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-[10px] text-stone-500 font-mono">
                                        @if($log->ip_address)
                                            <span class="bg-stone-100 px-1.5 py-0.5 rounded border border-stone-200">IP: {{ $log->ip_address }}</span>
                                        @endif
                                        @if(!empty($log->metadata['quantity']))
                                            <span class="bg-emerald-50 text-emerald-800 px-1.5 py-0.5 rounded border border-emerald-200 font-bold">Qty: {{ $log->metadata['quantity'] }}</span>
                                        @endif
                                        @if(!empty($log->metadata['total_amount']))
                                            <span class="bg-amber-50 text-amber-900 px-1.5 py-0.5 rounded border border-amber-200 font-bold">₹{{ number_format((float)$log->metadata['total_amount'], 2) }}</span>
                                        @endif
                                        @if(!empty($log->metadata['reason']))
                                            <span class="bg-rose-50 text-rose-800 px-1.5 py-0.5 rounded border border-rose-200 truncate max-w-xs" title="{{ $log->metadata['reason'] }}">Reason: {{ $log->metadata['reason'] }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Subject Reference -->
                            <td class="p-4 text-right whitespace-nowrap">
                                @if($log->subject_ref)
                                    @if(str_starts_with($log->subject_ref, 'RAY-') && $log->subject_id)
                                        <a href="{{ route('admin.orders.show', $log->subject_id) }}" class="inline-block font-mono font-bold text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-300 px-2.5 py-1 rounded-lg transition" title="Inspect Order Details">
                                            {{ $log->subject_ref }} →
                                        </a>
                                    @else
                                        <span class="inline-block font-mono font-bold text-stone-700 bg-stone-100 border border-stone-200 px-2 py-0.5 rounded-md">
                                            {{ $log->subject_ref }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-stone-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-stone-400">
                                No activity records found matching filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- 4. PAGINATION -->
    <div>
        {{ $logs->links() }}
    </div>

</div>
@endsection
