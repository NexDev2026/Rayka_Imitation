@extends('admin.layouts.admin')

@section('title', 'Orders Management')
@section('page_title', 'Orders & Payment Proof Verification')

@section('content')
<div class="space-y-5">

    <!-- Top Filter Header: Search Bar & Quick Status Pills -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-1">
        <!-- Filter Tabs by Status -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
            <a href="{{ route('admin.orders.index', request()->only('search')) }}" 
               class="px-3.5 py-2 rounded-xl shrink-0 whitespace-nowrap transition-all duration-150 {{ !request('status') ? 'bg-[#4A2C1D] text-[#E7C77B] font-bold shadow-xs' : 'bg-white text-stone-700 border border-stone-200 hover:bg-[#FAF7F0] hover:text-[#4A2C1D]' }}">
                <span>All Orders</span>
                @if(isset($totalOrdersCount))
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] {{ !request('status') ? 'bg-[#E7C77B]/20 text-[#E7C77B]' : 'bg-stone-100 text-stone-500' }}">{{ $totalOrdersCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->only('search'), ['status' => 'Pending Verification'])) }}" 
               class="px-3.5 py-2 rounded-xl shrink-0 whitespace-nowrap transition-all duration-150 flex items-center gap-1.5 {{ request('status') === 'Pending Verification' ? 'bg-amber-600 text-white font-bold shadow-xs ring-2 ring-amber-300/50' : 'bg-white text-amber-900 border border-amber-300 hover:bg-amber-50' }}">
                <span>Pending Proofs</span>
                @if($pendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full font-extrabold text-[10px] {{ request('status') === 'Pending Verification' ? 'bg-white text-amber-700' : 'bg-amber-200 text-amber-900' }} animate-pulse">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->only('search'), ['status' => 'Confirmed'])) }}" 
               class="px-3.5 py-2 rounded-xl shrink-0 whitespace-nowrap transition-all duration-150 {{ request('status') === 'Confirmed' ? 'bg-emerald-700 text-white font-bold shadow-xs' : 'bg-white text-emerald-900 border border-emerald-300 hover:bg-emerald-50' }}">
                Confirmed
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->only('search'), ['status' => 'Processing'])) }}" 
               class="px-3.5 py-2 rounded-xl shrink-0 whitespace-nowrap transition-all duration-150 {{ request('status') === 'Processing' ? 'bg-blue-700 text-white font-bold shadow-xs' : 'bg-white text-stone-700 border border-stone-200 hover:bg-blue-50 hover:text-blue-900' }}">
                Processing
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->only('search'), ['status' => 'Shipped'])) }}" 
               class="px-3.5 py-2 rounded-xl shrink-0 whitespace-nowrap transition-all duration-150 {{ request('status') === 'Shipped' ? 'bg-indigo-700 text-white font-bold shadow-xs' : 'bg-white text-stone-700 border border-stone-200 hover:bg-indigo-50 hover:text-indigo-900' }}">
                Shipped
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->only('search'), ['status' => 'Delivered'])) }}" 
               class="px-3.5 py-2 rounded-xl shrink-0 whitespace-nowrap transition-all duration-150 {{ request('status') === 'Delivered' ? 'bg-green-700 text-white font-bold shadow-xs' : 'bg-white text-stone-700 border border-stone-200 hover:bg-green-50 hover:text-green-900' }}">
                Delivered
            </a>

            <a href="{{ route('admin.orders.index', array_merge(request()->only('search'), ['status' => 'Rejected'])) }}" 
               class="px-3.5 py-2 rounded-xl shrink-0 whitespace-nowrap transition-all duration-150 {{ request('status') === 'Rejected' ? 'bg-rose-700 text-white font-bold shadow-xs' : 'bg-white text-rose-900 border border-rose-300 hover:bg-rose-50' }}">
                Rejected
            </a>
        </div>

        <!-- Search Bar -->
        <form action="{{ route('admin.orders.index') }}" method="GET" class="relative shrink-0 w-full lg:w-72">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Search Order #, Name, Phone..." 
                   class="w-full pl-9 pr-8 py-2 rounded-xl border border-stone-200 bg-white text-xs text-stone-800 placeholder-stone-400 focus:outline-hidden focus:border-[#D4AF6A] focus:ring-1 focus:ring-[#D4AF6A] shadow-2xs">
            <svg class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            @if(request('search'))
                <a href="{{ route('admin.orders.index', request()->only('status')) }}" class="absolute right-2.5 top-2.5 text-stone-400 hover:text-stone-700 text-xs">
                    ✕
                </a>
            @endif
        </form>
    </div>

    <!-- Orders Container -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        
        <!-- 1. MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($orders as $ord)
                @php
                    $customerName = $ord->user?->name ?: ($ord->address?->name ?: 'Guest Patron');
                    $customerMobile = $ord->user?->mobile ?: ($ord->address?->mobile ?: 'N/A');
                @endphp
                <div class="p-4 space-y-3.5 hover:bg-[#FAF7F0]/40 transition">
                    <!-- Card Top: Order #, Status & Date -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="{{ route('admin.orders.show', $ord->id) }}" class="font-mono font-bold text-sm text-[#4A2C1D] hover:text-[#996E2E]">
                                    {{ $ord->order_number }}
                                </a>
                                @if($ord->isCod())
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300">
                                        COD ({{ $ord->advance_percentage }}%)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Prepaid
                                    </span>
                                @endif
                            </div>
                            <span class="text-[11px] text-stone-400 block mt-0.5 font-mono">
                                {{ $ord->created_at->format('d M, Y • h:i A') }}
                            </span>
                        </div>
                        @if($ord->isCancelledByCustomer())
                            <div class="text-right space-y-0.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border shrink-0 bg-stone-100 text-stone-700 border-stone-300 inline-block">
                                    Cancelled (User)
                                </span>
                                @if($ord->isRefunded())
                                    <span class="block px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 ml-auto w-fit">
                                        Refunded: ₹{{ number_format($ord->refund_amount) }}
                                    </span>
                                @elseif($ord->isRefundPending())
                                    <span class="block px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-100 text-amber-900 border border-amber-300 animate-pulse ml-auto w-fit">
                                        Refund Due: ₹{{ number_format($ord->getRefundableAmount()) }}
                                    </span>
                                @endif
                            </div>
                        @elseif($ord->isCancelledByAdmin())
                            <div class="text-right space-y-0.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border shrink-0 bg-rose-50 text-rose-700 border-rose-200 inline-block">
                                    Cancelled (Admin)
                                </span>
                                @if($ord->isRefunded())
                                    <span class="block px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 ml-auto w-fit">
                                        Refunded: ₹{{ number_format($ord->refund_amount) }}
                                    </span>
                                @elseif($ord->isRefundPending())
                                    <span class="block px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-100 text-amber-900 border border-amber-300 animate-pulse ml-auto w-fit">
                                        Refund Due: ₹{{ number_format($ord->getRefundableAmount()) }}
                                    </span>
                                @endif
                            </div>
                        @elseif($ord->isRejected())
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border shrink-0 bg-rose-100 text-rose-800 border-rose-300">
                                Rejected
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border shrink-0 {{ $ord->status_badge_class }}">
                                {{ $ord->status }}
                            </span>
                        @endif
                    </div>

                    <!-- Customer & Amount Row -->
                    <div class="flex items-center justify-between text-xs pt-1 border-t border-stone-100">
                        <div>
                            <strong class="text-stone-900 font-bold block">{{ $customerName }}</strong>
                            <a href="tel:{{ $customerMobile }}" class="text-stone-500 hover:text-[#996E2E] text-[11px] font-mono">
                                {{ $customerMobile }}
                            </a>
                        </div>
                        <div class="text-right">
                            <strong class="text-[#4A2C1D] text-sm font-serif-royal block">₹{{ number_format($ord->total_amount) }}</strong>
                            <span class="text-stone-400 text-[10px]">{{ $ord->items->count() }} items</span>
                        </div>
                    </div>

                    @if($ord->isCod())
                        <div class="p-2.5 rounded-xl bg-[#FAF7F0] border border-[#D4AF6A]/30 text-xs space-y-1">
                            <div class="flex justify-between text-emerald-800 font-semibold">
                                <span>Advance ({{ $ord->advance_percentage }}%):</span>
                                <span class="font-mono font-bold">₹{{ number_format($ord->advance_amount) }}</span>
                            </div>
                            <div class="flex justify-between text-[#4A2C1D] font-bold">
                                <span>Doorstep Balance:</span>
                                <span class="font-mono">₹{{ number_format($ord->balance_amount) }}</span>
                            </div>
                            <div class="flex items-center justify-between pt-1 border-t border-[#D4AF6A]/20 text-[11px]">
                                <span class="text-stone-600">Balance Status:</span>
                                <span class="font-bold px-2 py-0.2 rounded border {{ $ord->balance_status_badge_class }}">
                                    {{ $ord->balance_status }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Payment Proof Indicators -->
                    <div class="flex items-center justify-between text-xs pt-1 flex-wrap gap-1.5">
                        <span class="text-stone-500 text-[11px] font-semibold">Proofs:</span>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @if($ord->payment && $ord->payment->screenshot_path)
                                <a href="{{ asset($ord->payment->screenshot_path) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-300 text-amber-900 font-bold text-[11px] hover:bg-amber-100">
                                    <span>Advance</span>
                                </a>
                            @else
                                <span class="text-stone-400 italic text-[11px]">No Advance Proof</span>
                            @endif

                            @if($ord->delivery_payment_screenshot)
                                <a href="{{ asset($ord->delivery_payment_screenshot) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-300 text-emerald-900 font-bold text-[11px] hover:bg-emerald-100">
                                    <span>Bal Proof</span>
                                </a>
                            @endif

                            @if($ord->refund_screenshot)
                                <a href="{{ asset($ord->refund_screenshot) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-300 text-blue-900 font-bold text-[11px] hover:bg-blue-100">
                                    <span>Refund Proof</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-2 border-t border-stone-100">
                        @if($ord->isConfirmed() || $ord->isBalanceSettled())
                            <a href="{{ route('admin.orders.invoice', $ord->id) }}" target="_blank" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-semibold text-xs transition text-center shrink-0">
                                Invoice
                            </a>
                        @endif
                        <a href="{{ route('admin.orders.show', $ord->id) }}" class="flex-1 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-xl font-bold text-xs text-center hover:bg-[#2E180E] transition shadow-2xs">
                            Inspect & Verify →
                        </a>
                        @if($ord->canBeDeleted())
                            <form action="{{ route('admin.orders.destroy', $ord->id) }}" method="POST" onsubmit="return confirm('Permanently delete Order #{{ $ord->order_number }}? This action cannot be undone.');" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl border border-rose-200 transition cursor-pointer" title="Delete Cancelled/Rejected Order">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-stone-400 text-xs">
                    No orders found matching status.
                </div>
            @endforelse
        </div>

        <!-- 2. DESKTOP TABULAR VIEW (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-[#FAF7F0] text-[#4A2C1D] uppercase text-[10px] font-bold tracking-wider border-b border-[#D4AF6A]/30">
                    <tr>
                        <th class="py-3.5 px-4 font-bold min-w-[160px]">Order & Type</th>
                        <th class="py-3.5 px-4 font-bold min-w-[120px]">Placed At</th>
                        <th class="py-3.5 px-4 font-bold min-w-[200px]">Customer & Contact</th>
                        <th class="py-3.5 px-4 font-bold min-w-[170px]">Total & Breakdown</th>
                        <th class="py-3.5 px-4 font-bold min-w-[160px]">Proof Attachments</th>
                        <th class="py-3.5 px-4 font-bold min-w-[160px]">Workflow Status</th>
                        <th class="py-3.5 px-4 font-bold text-right min-w-[180px]">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 bg-white">
                    @forelse($orders as $ord)
                        @php
                            $customerName = $ord->user?->name ?: ($ord->address?->name ?: 'Guest Patron');
                            $customerMobile = $ord->user?->mobile ?: ($ord->address?->mobile ?: 'N/A');
                            $customerInitial = strtoupper(substr(trim($customerName), 0, 1) ?: 'P');
                        @endphp
                        <tr class="hover:bg-[#FAF7F0]/40 transition duration-150">
                            <!-- 1. Order # & Type Badge -->
                            <td class="py-4 px-4 align-top">
                                <div class="space-y-1.5">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" 
                                       class="font-mono font-bold text-sm text-[#4A2C1D] hover:text-[#996E2E] transition flex items-center gap-1.5 group">
                                        <span>{{ $ord->order_number }}</span>
                                        <svg class="w-3.5 h-3.5 text-stone-400 group-hover:text-[#996E2E] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    @if($ord->isCod())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-900 border border-amber-300 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                            <span>COD ({{ $ord->advance_percentage }}%)</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            <span>Prepaid Online</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 2. Placed At Date & Time -->
                            <td class="py-4 px-4 align-top whitespace-nowrap">
                                <div class="text-xs font-semibold text-stone-800">
                                    {{ $ord->created_at->format('d M, Y') }}
                                </div>
                                <div class="text-[11px] font-mono text-stone-400 mt-0.5">
                                    {{ $ord->created_at->format('h:i A') }} IST
                                </div>
                            </td>

                            <!-- 3. Customer & Contact (No wrapping bug!) -->
                            <td class="py-4 px-4 align-top min-w-[200px]">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/60 text-[#4A2C1D] font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                        {{ $customerInitial }}
                                    </div>
                                    <div class="min-w-0">
                                        <strong class="text-stone-900 font-bold text-xs block truncate max-w-[170px]" title="{{ $customerName }}">
                                            {{ $customerName }}
                                        </strong>
                                        <a href="tel:{{ $customerMobile }}" class="text-stone-500 hover:text-[#996E2E] text-[11px] font-mono mt-0.5 inline-flex items-center gap-1 transition">
                                            <svg class="w-3 h-3 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span>{{ $customerMobile }}</span>
                                        </a>
                                    </div>
                                </div>
                            </td>

                            <!-- 4. Total & Financial Breakdown -->
                            <td class="py-4 px-4 align-top whitespace-nowrap">
                                <div class="font-serif-royal font-bold text-sm text-[#4A2C1D] leading-none">
                                    ₹{{ number_format($ord->total_amount) }}
                                </div>
                                <span class="text-[11px] text-stone-400 block mt-1">
                                    {{ $ord->items->count() }} {{ Str::plural('item', $ord->items->count()) }}
                                </span>
                                @if($ord->isCod())
                                    <div class="mt-2 p-2 rounded-xl bg-[#FAF7F0] border border-[#D4AF6A]/30 text-[10px] space-y-1">
                                        <div class="flex items-center justify-between gap-2 text-emerald-800 font-semibold">
                                            <span>Adv ({{ $ord->advance_percentage }}%):</span>
                                            <span class="font-mono font-bold">₹{{ number_format($ord->advance_amount) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2 text-[#4A2C1D] font-bold">
                                            <span>Doorstep:</span>
                                            <span class="font-mono">₹{{ number_format($ord->balance_amount) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2 pt-1 border-t border-[#D4AF6A]/20">
                                            <span class="text-stone-500">Balance Status:</span>
                                            <span class="font-bold px-1.5 py-0.2 rounded border {{ $ord->balance_status_badge_class }}">
                                                {{ $ord->balance_status }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- 5. Proof Attachments -->
                            <td class="py-4 px-4 align-top whitespace-nowrap">
                                <div class="space-y-1.5">
                                    @if($ord->payment && $ord->payment->screenshot_path)
                                        <a href="{{ asset($ord->payment->screenshot_path) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-bold text-[11px] transition shadow-2xs block w-fit">
                                            <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>{{ $ord->isCod() ? 'Advance Proof' : 'Payment Proof' }}</span>
                                        </a>
                                    @else
                                        <span class="text-stone-400 italic text-[11px] block">No Advance Proof</span>
                                    @endif

                                    @if($ord->delivery_payment_screenshot)
                                        <a href="{{ asset($ord->delivery_payment_screenshot) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold text-[11px] transition shadow-2xs block w-fit">
                                            <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Delivery Slip</span>
                                        </a>
                                    @endif

                                    @if($ord->refund_screenshot)
                                        <a href="{{ asset($ord->refund_screenshot) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-300 font-bold text-[11px] transition shadow-2xs block w-fit">
                                            <svg class="w-3.5 h-3.5 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                            <span>Refund Slip</span>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- 6. Workflow Status -->
                            <td class="py-4 px-4 align-top whitespace-nowrap">
                                <div class="space-y-1.5">
                                    @if($ord->isCancelledByCustomer())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-stone-100 text-stone-700 border-stone-300 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-stone-500"></span>
                                            <span>Cancelled (User)</span>
                                        </span>
                                        @if($ord->isRefunded())
                                            <span class="block px-2.5 py-0.5 rounded-md text-[9px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 w-fit">
                                                Refunded: ₹{{ number_format($ord->refund_amount) }}
                                            </span>
                                        @elseif($ord->isRefundPending())
                                            <span class="block px-2.5 py-0.5 rounded-md text-[9px] font-bold uppercase bg-amber-100 text-amber-900 border border-amber-300 animate-pulse w-fit">
                                                Refund Due: ₹{{ number_format($ord->getRefundableAmount()) }}
                                            </span>
                                        @endif
                                        @php $cReason = $ord->getCancellationDetails()['reason'] ?? null; @endphp
                                        @if($cReason)
                                            <span class="block text-[10px] text-stone-500 italic truncate max-w-[160px]" title="{{ $cReason }}">
                                                {{ Str::limit($cReason, 24) }}
                                            </span>
                                        @endif
                                    @elseif($ord->isCancelledByAdmin())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-rose-50 text-rose-700 border-rose-200 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            <span>Cancelled (Admin)</span>
                                        </span>
                                        @if($ord->isRefunded())
                                            <span class="block px-2.5 py-0.5 rounded-md text-[9px] font-bold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300 w-fit">
                                                Refunded: ₹{{ number_format($ord->refund_amount) }}
                                            </span>
                                        @elseif($ord->isRefundPending())
                                            <span class="block px-2.5 py-0.5 rounded-md text-[9px] font-bold uppercase bg-amber-100 text-amber-900 border border-amber-300 animate-pulse w-fit">
                                                Refund Due: ₹{{ number_format($ord->getRefundableAmount()) }}
                                            </span>
                                        @endif
                                    @elseif($ord->isRejected())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border bg-rose-100 text-rose-800 border-rose-300 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-700"></span>
                                            <span>Rejected</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $ord->status_badge_class }} shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            <span>{{ $ord->status }}</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 7. Action Buttons -->
                            <td class="py-4 px-4 align-top text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($ord->isConfirmed() || $ord->isBalanceSettled())
                                        <a href="{{ route('admin.orders.invoice', $ord->id) }}" 
                                           target="_blank"
                                           class="px-2.5 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 hover:text-stone-900 rounded-lg text-xs font-semibold inline-flex items-center gap-1 transition shadow-2xs" 
                                           title="Download Official Tax Invoice">
                                            <svg class="w-3.5 h-3.5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>Invoice</span>
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.orders.show', $ord->id) }}" 
                                       class="px-3 py-1.5 bg-[#4A2C1D] hover:bg-[#2E180E] text-[#E7C77B] rounded-lg text-xs font-bold inline-flex items-center gap-1 transition shadow-xs hover:shadow">
                                        <span>Inspect & Verify</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>

                                    @if($ord->canBeDeleted())
                                        <form action="{{ route('admin.orders.destroy', $ord->id) }}" method="POST" onsubmit="return confirm('Permanently delete Order #{{ $ord->order_number }}? This action cannot be undone.');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer" title="Delete Cancelled/Rejected Order">
                                                <svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" onclick="alert('Order #{{ $ord->order_number }} is active ({{ $ord->status }}). For security and accounting integrity, only Cancelled or Rejected orders can be permanently deleted.');" class="p-1.5 text-stone-300 hover:text-stone-500 hover:bg-stone-100 rounded-lg border border-transparent transition cursor-pointer inline-block" title="Protected: Order must be Cancelled or Rejected before deletion">
                                            <svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center text-stone-400">
                                <div class="w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-stone-600">No orders found matching this filter.</p>
                                <p class="text-xs text-stone-400 mt-1">Try selecting a different status filter or clearing your search query.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $orders->links() }}
    </div>

</div>
@endsection
