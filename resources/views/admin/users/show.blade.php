@extends('admin.layouts.admin')

@section('title', 'User Profile — ' . $user->name)

@section('page_title', 'User Profile')

@section('content')
<div class="space-y-5">

    {{-- ── BREADCRUMB ── --}}
    <div class="flex items-center gap-2 text-xs text-stone-500">
        <a href="{{ route('admin.users.index') }}" class="hover:text-[#4A2C1D] transition cursor-pointer">Customer Directory</a>
        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-stone-700 font-semibold truncate">{{ $user->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── LEFT PANEL: User Card ── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Profile Card --}}
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                {{-- Gold top stripe --}}
                <div class="h-1.5 bg-gradient-to-r from-[#996E2E] via-[#D4AF6A] to-[#996E2E]"></div>
                <div class="p-5 sm:p-6 text-center">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-[#4A2C1D] to-[#7A4A2A] flex items-center justify-center mx-auto shadow-lg mb-4">
                        <span class="text-[#E7C77B] font-bold text-2xl sm:text-3xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <h2 class="font-bold text-xl text-stone-800">{{ $user->name }}</h2>
                    <p class="text-xs text-stone-400 mt-0.5">User #{{ $user->id }}</p>

                    <div class="mt-3">
                        @if(in_array($user->role, ['admin', 'super-admin']))
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                {{ ucwords(str_replace('-', ' ', $user->role)) }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                Customer
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Contact Details --}}
                <div class="border-t border-stone-100 divide-y divide-stone-100">
                    <div class="px-5 py-3.5 flex items-center gap-3">
                        <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        <div class="min-w-0">
                            <p class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold">Email</p>
                            <p class="text-sm text-stone-700 font-medium truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                    @if($user->mobile)
                    <div class="px-5 py-3.5 flex items-center gap-3">
                        <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/></svg>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold">Mobile</p>
                            <p class="text-sm text-stone-700 font-medium">{{ $user->mobile }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="px-5 py-3.5 flex items-center gap-3">
                        <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold">Joined</p>
                            <p class="text-sm text-stone-700 font-medium">{{ $user->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                    @if($user->email_verified_at)
                    <div class="px-5 py-3.5 flex items-center gap-3">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold">Email Verified</p>
                            <p class="text-sm text-emerald-700 font-semibold">{{ $user->email_verified_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Stats Mini-Cards --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-white rounded-xl border border-stone-200 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-[#4A2C1D]">{{ $user->orders_count }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold mt-1">Total Orders</p>
                </div>
                <div class="bg-white rounded-xl border border-stone-200 p-4 text-center shadow-sm">
                    <p class="text-lg font-bold text-emerald-700">₹{{ number_format($user->orders_sum_total_amount ?? 0) }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold mt-1">Lifetime Spend</p>
                </div>
                <div class="bg-white rounded-xl border border-stone-200 p-4 text-center shadow-sm">
                    <p class="text-2xl font-bold text-amber-600">{{ $user->reviews_count }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold mt-1">Reviews</p>
                </div>
                <div class="bg-white rounded-xl border border-stone-200 p-4 text-center shadow-sm">
                    @php
                        $completedOrders = $orders->where('status', 'Delivered')->count();
                    @endphp
                    <p class="text-2xl font-bold text-sky-600">{{ $completedOrders }}</p>
                    <p class="text-[10px] uppercase tracking-wider text-stone-400 font-semibold mt-1">Completed</p>
                </div>
            </div>

            {{-- Danger Zone --}}
            @if(!$user->isAdmin())
            <div class="bg-white rounded-2xl border border-rose-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-rose-100 bg-rose-50/60">
                    <p class="text-xs font-bold text-rose-700 uppercase tracking-wider">⚠ Danger Zone</p>
                </div>
                <div class="p-5">
                    <p class="text-xs text-stone-500 mb-3 leading-relaxed">Permanently deletes this account and all associated data. Active orders will block deletion.</p>
                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                          onsubmit="return confirm('PERMANENTLY delete {{ addslashes($user->name) }}? This CANNOT be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl transition cursor-pointer">
                            Delete This Account
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        {{-- ── RIGHT PANEL: Order History ── --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-stone-100 flex items-center justify-between gap-3 flex-wrap">
                    <div>
                        <h3 class="font-bold text-stone-800">Order History</h3>
                        <p class="text-xs text-stone-400 mt-0.5">Last {{ $orders->count() }} orders</p>
                    </div>
                    @if($orders->count() > 0)
                    <span class="text-xs bg-[#FAF7F0] border border-[#D4AF6A]/40 text-[#4A2C1D] px-3 py-1.5 rounded-full font-semibold">
                        {{ $user->orders_count }} total
                    </span>
                    @endif
                </div>

                @forelse($orders as $order)
                <div class="p-3.5 sm:px-6 sm:py-4 border-b border-stone-100 last:border-0 hover:bg-stone-50/50 transition-colors">
                    @php
                        $statusMap = [
                            'Pending Verification' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'Confirmed'            => 'bg-blue-100 text-blue-800 border-blue-200',
                            'Shipped'              => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                            'Out for Delivery'     => 'bg-purple-100 text-purple-800 border-purple-200',
                            'Delivered'            => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'Cancelled'            => 'bg-rose-100 text-rose-800 border-rose-200',
                            'Rejected'             => 'bg-red-100 text-red-800 border-red-200',
                        ];
                        $statusClass = $statusMap[$order->status] ?? 'bg-stone-100 text-stone-700 border-stone-200';
                    @endphp

                    {{-- Top Order Header: Order # + Date on Left, Amount + Badge on Right --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-start gap-2.5 min-w-0">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-[#FAF7F0] border border-[#D4AF6A]/30 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#4A2C1D]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="font-mono font-bold text-xs sm:text-sm text-[#4A2C1D] hover:underline truncate block">
                                    #{{ $order->order_number }}
                                </a>
                                <p class="text-[11px] text-stone-400 mt-0.5">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</p>
                                <p class="text-[11px] text-stone-500 mt-0.5">{{ $order->items->count() }} item(s)</p>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <p class="font-bold text-xs sm:text-sm text-stone-800">₹{{ number_format($order->total_amount, 2) }}</p>
                            <span class="inline-block mt-1 px-2 sm:px-2.5 py-0.5 sm:py-1 text-[9px] sm:text-[10px] font-bold rounded-full border whitespace-nowrap {{ $statusClass }}">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>

                    {{-- Items mini-list --}}
                    @if($order->items->count() > 0)
                    <div class="mt-2.5 sm:ml-11 bg-stone-50/80 sm:bg-transparent p-2.5 sm:p-0 rounded-xl sm:rounded-none space-y-1">
                        @foreach($order->items->take(3) as $item)
                        <div class="flex items-center justify-between text-xs text-stone-600 gap-2">
                            <span class="truncate">{{ $item->product_name }}{{ $item->variant_info ? " ({$item->variant_info})" : '' }}</span>
                            <span class="shrink-0 font-medium text-stone-400">x{{ $item->quantity }}</span>
                        </div>
                        @endforeach
                        @if($order->items->count() > 3)
                        <p class="text-[11px] text-stone-400 italic">+ {{ $order->items->count() - 3 }} more item(s)</p>
                        @endif
                    </div>
                    @endif

                    <div class="mt-2.5 sm:ml-11 flex items-center justify-between sm:justify-start">
                        <a href="{{ route('admin.orders.show', $order->id) }}"
                           class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#4A2C1D] hover:underline cursor-pointer">
                            View Full Order
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-16 text-center">
                    <div class="flex flex-col items-center gap-3 text-stone-400">
                        <svg class="w-12 h-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                        <p class="font-semibold text-sm">No orders placed yet</p>
                        <p class="text-xs">This user hasn't placed any orders.</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-stone-200 hover:border-[#D4AF6A] text-stone-700 hover:text-[#4A2C1D] text-sm font-semibold rounded-xl transition shadow-sm cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back to Directory
        </a>
    </div>
</div>
@endsection
