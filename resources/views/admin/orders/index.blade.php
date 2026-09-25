@extends('admin.layouts.admin')

@section('title', 'Orders Management')
@section('page_title', 'Orders & Payment Proof Verification')

@section('content')
<div class="space-y-6">

    <!-- Filter Tabs by Status -->
    <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-2 border-b border-stone-200 text-xs no-scrollbar">
        <a href="{{ route('admin.orders.index') }}" class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ !request('status') ? 'bg-[#4A2C1D] text-[#E7C77B] font-bold shadow-xs' : 'bg-white text-stone-700 border hover:bg-stone-50' }}">
            All Orders
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'Pending Verification']) }}" class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ request('status') === 'Pending Verification' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-white text-amber-900 border border-amber-300 hover:bg-amber-50' }} flex items-center space-x-1.5">
            <span>Pending Proofs</span>
            @if($pendingCount > 0)
                <span class="bg-amber-200 text-black px-1.5 py-0.2 rounded-full font-bold text-[10px]">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'Confirmed']) }}" class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ request('status') === 'Confirmed' ? 'bg-emerald-700 text-white font-bold shadow-xs' : 'bg-white text-emerald-900 border border-emerald-300 hover:bg-emerald-50' }}">
            Confirmed
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'Processing']) }}" class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ request('status') === 'Processing' ? 'bg-blue-700 text-white font-bold shadow-xs' : 'bg-white text-stone-700 border hover:bg-stone-50' }}">
            Processing
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'Shipped']) }}" class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ request('status') === 'Shipped' ? 'bg-indigo-700 text-white font-bold shadow-xs' : 'bg-white text-stone-700 border hover:bg-stone-50' }}">
            Shipped
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'Delivered']) }}" class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ request('status') === 'Delivered' ? 'bg-green-700 text-white font-bold shadow-xs' : 'bg-white text-stone-700 border hover:bg-stone-50' }}">
            Delivered
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'Rejected']) }}" class="px-3 py-1.5 rounded-lg shrink-0 whitespace-nowrap {{ request('status') === 'Rejected' ? 'bg-rose-700 text-white font-bold shadow-xs' : 'bg-white text-stone-700 border hover:bg-stone-50' }}">
            Rejected
        </a>
    </div>

    <!-- Orders Container -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        
        <!-- 1. MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($orders as $ord)
                <div class="p-4 space-y-3 hover:bg-stone-50/60 transition">
                    <!-- Card Top: Order #, Status & Date -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-mono font-bold text-sm text-[#4A2C1D] block">
                                {{ $ord->order_number }}
                            </span>
                            <span class="text-[11px] text-stone-400">
                                {{ $ord->created_at->format('d M, Y h:i A') }}
                            </span>
                        </div>
                        @if($ord->isCancelledByCustomer())
                            <div class="text-right">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border shrink-0 bg-stone-100 text-stone-700 border-stone-300 inline-block">
                                    Cancelled (User)
                                </span>
                                @php $cReason = $ord->getCancellationDetails()['reason'] ?? null; @endphp
                                @if($cReason)
                                    <span class="block text-[10px] text-stone-500 italic truncate max-w-[150px] mt-0.5" title="{{ $cReason }}">
                                        {{ Str::limit($cReason, 28) }}
                                    </span>
                                @endif
                            </div>
                        @elseif($ord->isCancelledByAdmin())
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border shrink-0 bg-rose-50 text-rose-700 border-rose-200">
                                Cancelled (Admin)
                            </span>
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
                            <strong class="text-stone-900 block">{{ $ord->user?->name ?: $ord->address?->name }}</strong>
                            <a href="tel:{{ $ord->user?->mobile ?: $ord->address?->mobile }}" class="text-[#996E2E] text-[11px]">
                                {{ $ord->user?->mobile ?: $ord->address?->mobile }}
                            </a>
                        </div>
                        <div class="text-right">
                            <strong class="text-stone-900 text-sm font-serif-royal block">₹{{ number_format($ord->total_amount) }}</strong>
                            <span class="text-stone-400 text-[10px]">{{ $ord->items->count() }} items</span>
                        </div>
                    </div>

                    <!-- Payment Proof Indicator -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <span class="text-stone-500 text-[11px]">Payment Proof:</span>
                        @if($ord->payment && $ord->payment->screenshot_path)
                            <a href="{{ asset($ord->payment->screenshot_path) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-300 text-amber-900 font-bold text-[11px] hover:bg-amber-100">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                                <span>View Proof</span>
                            </a>
                        @else
                            <span class="text-stone-400 italic text-[11px]">No Screenshot</span>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-2 border-t border-stone-100">
                        @if($ord->isConfirmed())
                            <a href="{{ route('admin.orders.invoice', $ord->id) }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-semibold text-xs transition text-center shrink-0">
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
                                <button type="submit" class="p-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl border border-rose-200 transition cursor-pointer" title="Delete Order (Cancelled/Rejected)">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        @else
                            <button type="button" onclick="alert('Order #{{ $ord->order_number }} is active ({{ $ord->status }}). For security and accounting integrity, only Cancelled or Rejected orders can be permanently deleted.');" class="p-2.5 bg-stone-100 text-stone-400 hover:text-stone-600 hover:bg-stone-200 rounded-xl border border-stone-200 transition cursor-pointer shrink-0" title="Protected: Order must be Cancelled or Rejected before deletion">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </button>
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
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 uppercase border-b border-stone-200 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4">Order #</th>
                        <th class="p-4">Date</th>
                        <th class="p-4">Customer & Contact</th>
                        <th class="p-4">Items / Total</th>
                        <th class="p-4">Payment Screenshot</th>
                        <th class="p-4">Workflow Status</th>
                        <th class="p-4 text-right">Review Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 font-mono font-bold text-[#4A2C1D]">
                                {{ $ord->order_number }}
                            </td>
                            <td class="p-4 text-stone-500 whitespace-nowrap">
                                {{ $ord->created_at->format('d M, Y h:i A') }}
                            </td>
                            <td class="p-4">
                                <strong class="text-stone-900 block">{{ $ord->user?->name ?: $ord->address?->name }}</strong>
                                <span class="text-stone-400 text-[11px]">{{ $ord->user?->mobile ?: $ord->address?->mobile }}</span>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <strong class="text-stone-900">₹{{ number_format($ord->total_amount) }}</strong>
                                <span class="text-stone-400 block text-[11px]">{{ $ord->items->count() }} items</span>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if($ord->payment && $ord->payment->screenshot_path)
                                    <a href="{{ asset($ord->payment->screenshot_path) }}" target="_blank" class="inline-flex items-center space-x-1 px-2.5 py-1 rounded bg-amber-50 border border-amber-300 text-amber-900 font-bold hover:bg-amber-100">
                                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" /></svg>
                                        <span>View Proof</span>
                                    </a>
                                @else
                                    <span class="text-stone-400 italic">No Screenshot</span>
                                @endif
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                @if($ord->isCancelledByCustomer())
                                    <div class="space-y-0.5">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border bg-stone-100 text-stone-700 border-stone-300">
                                            Cancelled (User)
                                        </span>
                                        @php $cReason = $ord->getCancellationDetails()['reason'] ?? null; @endphp
                                        @if($cReason)
                                            <span class="block text-[10px] text-stone-500 truncate max-w-[140px]" title="{{ $cReason }}">
                                                {{ Str::limit($cReason, 24) }}
                                            </span>
                                        @endif
                                    </div>
                                @elseif($ord->isCancelledByAdmin())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border bg-rose-50 text-rose-700 border-rose-200">
                                        Cancelled (Admin)
                                    </span>
                                @elseif($ord->isRejected())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border bg-rose-100 text-rose-800 border-rose-300">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $ord->status_badge_class }}">
                                        {{ $ord->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right whitespace-nowrap space-x-1.5">
                                @if($ord->isConfirmed())
                                    <a href="{{ route('admin.orders.invoice', $ord->id) }}" class="text-stone-500 hover:text-stone-800 font-medium mr-1.5">Invoice</a>
                                @endif
                                <a href="{{ route('admin.orders.show', $ord->id) }}" class="inline-flex items-center px-3.5 py-1.5 bg-[#4A2C1D] text-[#E7C77B] rounded-lg font-bold hover:bg-[#2E180E] transition text-xs">
                                    Inspect & Verify →
                                </a>
                                @if($ord->canBeDeleted())
                                    <form action="{{ route('admin.orders.destroy', $ord->id) }}" method="POST" onsubmit="return confirm('Permanently delete Order #{{ $ord->order_number }}? This action cannot be undone.');" class="inline-block align-middle">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition cursor-pointer" title="Delete Order (Cancelled/Rejected)">
                                            <svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" onclick="alert('Order #{{ $ord->order_number }} is active ({{ $ord->status }}). For security and accounting integrity, only Cancelled or Rejected orders can be permanently deleted.');" class="p-1.5 text-stone-300 hover:text-stone-500 hover:bg-stone-100 rounded-lg border border-transparent transition cursor-pointer inline-block align-middle" title="Protected: Order must be Cancelled or Rejected before deletion">
                                        <svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-stone-400">No orders found matching status.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <div>
        {{ $orders->links() }}
    </div>

</div>
@endsection
