@extends('layouts.storefront')

@section('title', 'My Royal Orders — Rayka Imitation Jewellery')

@section('content')
<div class="bg-stone-50 min-h-screen pt-8 pb-24 sm:py-16" 
     x-data="{
         cancelModal: false,
         cancelOrderNumber: '',
         cancelActionUrl: '',
         selectedReason: 'Ordered by mistake / Change of mind',
         customComment: '',
         isSubmitting: false,
         openCancelModal(orderNumber, actionUrl) {
             this.cancelOrderNumber = orderNumber;
             this.cancelActionUrl = actionUrl;
             this.selectedReason = 'Ordered by mistake / Change of mind';
             this.customComment = '';
             this.isSubmitting = false;
             this.cancelModal = true;
         }
     }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <div class="inline-flex items-center space-x-2 bg-amber-100/50 border border-amber-200 text-amber-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Royal Patron Portal</span>
                </div>
                <h1 class="font-serif-royal text-3xl sm:text-4xl font-bold text-[#4A2C1D]">
                    My Orders
                </h1>
                <p class="text-sm text-stone-500 mt-2 max-w-xl leading-relaxed">
                    Track the journey of your Rayka creations from our atelier to your doorstep. View detailed invoices, manage past purchases, and track live dispatches.
                </p>
            </div>
            
            <div class="bg-white px-5 py-3 rounded-2xl border border-[#D4AF6A]/30 shadow-sm flex items-center space-x-4 shrink-0 max-w-full">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#4A2C1D] to-[#996E2E] flex items-center justify-center text-[#E7C77B] font-serif-royal font-bold text-lg shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-[#4A2C1D] truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-stone-500 font-mono truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-6 sm:gap-8 border-b border-[#D4AF6A]/30 mb-8 overflow-x-auto no-scrollbar scrollbar-hide">
            <a href="{{ route('account.orders') }}" class="pb-3 border-b-2 border-[#996E2E] text-[#4A2C1D] font-bold text-sm tracking-wide whitespace-nowrap">
                My Orders
            </a>
            <a href="{{ route('account.addresses') }}" class="pb-3 border-b-2 border-transparent text-stone-500 hover:text-[#4A2C1D] font-semibold text-sm tracking-wide whitespace-nowrap transition">
                Saved Addresses
            </a>
            <a href="{{ route('account.settings') }}" class="pb-3 border-b-2 border-transparent text-stone-500 hover:text-[#4A2C1D] font-semibold text-sm tracking-wide whitespace-nowrap transition">
                Account Security
            </a>
        </div>

        @if($orders->isEmpty())
            <div class="bg-white rounded-3xl border border-[#D4AF6A]/30 p-12 text-center shadow-sm">
                <div class="w-20 h-20 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center mx-auto mb-5 text-[#996E2E]">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D]">No Royal Orders Yet</h3>
                <p class="text-sm text-stone-500 mt-2 mb-8 max-w-md mx-auto">
                    Your collection is currently empty. Begin your journey and discover our exquisite 1 gram micro gold creations.
                </p>
                <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-[#E7C77B] px-8 py-4 rounded-full text-xs font-bold uppercase tracking-wider hover:shadow-lg hover:scale-105 transition-all duration-300">
                    <span>Explore Collections</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        @else
            <div class="space-y-8">
                @foreach($orders as $order)
                    @php
                        // Status styling logic
                        $statusColor = match($order->status) {
                            'Pending Verification' => 'text-amber-700 bg-amber-50 border-amber-200',
                            'Confirmed' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
                            'Processing' => 'text-blue-700 bg-blue-50 border-blue-200',
                            'Shipped' => 'text-indigo-700 bg-indigo-50 border-indigo-200',
                            'Delivered' => 'text-green-700 bg-green-50 border-green-200',
                            'Rejected' => 'text-rose-700 bg-rose-50 border-rose-200',
                            'Cancelled' => $order->isCancelledByCustomer() ? 'text-stone-700 bg-stone-100 border-stone-200' : 'text-rose-700 bg-rose-50 border-rose-200',
                            default => 'text-stone-700 bg-stone-50 border-stone-200',
                        };
                        
                        $icon = match($order->status) {
                            'Pending Verification' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                            'Confirmed', 'Processing' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
                            'Shipped' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>',
                            'Delivered' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>',
                            'Rejected', 'Cancelled' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>',
                            default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                        };
                    @endphp

                    <div class="bg-white rounded-3xl border border-[#D4AF6A]/30 overflow-hidden shadow-xs hover:shadow-md transition-shadow duration-300">
                        
                        <!-- Top Bar (Order Info) -->
                        <div class="bg-[#FAF7F0]/50 border-b border-[#D4AF6A]/20 p-4 sm:p-5 sm:px-8 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                            <div class="grid grid-cols-2 sm:flex sm:flex-wrap sm:items-center gap-x-4 sm:gap-x-8 gap-y-2.5 sm:gap-y-3 w-full sm:w-auto">
                                <div class="col-span-2 sm:col-auto pb-1 sm:pb-0 border-b sm:border-b-0 border-stone-200/50">
                                    <p class="text-[10px] sm:text-xs text-stone-500 uppercase tracking-wider font-semibold mb-0.5">Order Number</p>
                                    <p class="font-serif-royal font-bold text-[#4A2C1D] text-sm sm:text-lg break-all">#{{ $order->order_number }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] sm:text-xs text-stone-500 uppercase tracking-wider font-semibold mb-0.5">Date Placed</p>
                                    <p class="font-bold text-[#4A2C1D] text-xs sm:text-base whitespace-nowrap">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] sm:text-xs text-stone-500 uppercase tracking-wider font-semibold mb-0.5">Total Amount</p>
                                    <p class="font-bold text-[#4A2C1D] text-xs sm:text-base whitespace-nowrap">₹{{ number_format($order->total_amount) }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end gap-2 sm:gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-stone-200/50 w-full sm:w-auto">
                                @if($order->isConfirmed())
                                    <a href="{{ route('account.order.invoice', $order->order_number) }}" class="inline-flex items-center space-x-1.5 px-3 sm:px-3.5 py-1.5 sm:py-2 rounded-full bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] hover:text-[#996E2E] font-bold text-xs transition shadow-2xs shrink-0" title="Download Official Tax Invoice">
                                        <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span class="hidden sm:inline text-[11px] uppercase tracking-wider">Tax Invoice</span>
                                        <span class="sm:hidden text-[11px]">Invoice</span>
                                    </a>
                                @endif
                                
                                @if($order->canBeCancelled())
                                    <button type="button" 
                                            @click="openCancelModal('{{ $order->order_number }}', '{{ route('account.order.cancel', $order->order_number) }}')"
                                            class="inline-flex items-center space-x-1.5 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs uppercase tracking-wider transition cursor-pointer shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        <span class="hidden sm:inline">Cancel</span>
                                    </button>
                                @endif

                                <a href="{{ route('account.order.detail', $order->order_number) }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center space-x-1.5 sm:space-x-2 px-4 sm:px-6 py-2 sm:py-2.5 rounded-full bg-stone-100 hover:bg-stone-200 text-[#4A2C1D] border border-stone-200 font-bold text-xs uppercase tracking-wider transition text-center">
                                    <span>View Details</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Main Content (Status + Items preview) -->
                        <div class="p-5 sm:p-8 flex flex-col lg:flex-row gap-8 lg:items-center">
                            
                            <!-- Left: Status Display -->
                            <div class="lg:w-1/3 shrink-0 flex items-start gap-4 border-b lg:border-b-0 lg:border-r border-stone-100 pb-6 lg:pb-0 lg:pr-6">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border {{ $statusColor }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $icon !!}
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-base sm:text-lg text-[#4A2C1D]">{{ $order->status }}</h3>
                                    
                                    @if($order->status === 'Pending Verification')
                                        <p class="text-xs text-stone-500 mt-1 leading-relaxed">Payment screenshot is being verified by our royal team.</p>
                                    @elseif($order->status === 'Rejected')
                                        <p class="text-xs text-rose-600 mt-1 leading-relaxed font-medium">Payment unverified or rejected by administration. Click View Details to resolve.</p>
                                    @elseif($order->status === 'Cancelled')
                                        @if($order->isCancelledByCustomer())
                                            @php $details = $order->getCancellationDetails(); @endphp
                                            <p class="text-xs text-stone-500 mt-1 leading-relaxed">
                                                Cancelled by you on {{ $order->updated_at->format('M d, Y') }}.
                                                @if(!empty($details['reason']))
                                                    <span class="block text-[11px] text-stone-600 mt-0.5 font-medium italic">"{{ $details['reason'] }}"</span>
                                                @endif
                                            </p>
                                        @else
                                            <p class="text-xs text-rose-600 mt-1 leading-relaxed">Cancelled by admin. Click View Details for more information.</p>
                                        @endif
                                    @elseif($order->tracking_number && in_array($order->status, ['Processing', 'Shipped', 'Delivered']) && !$order->isCancelled() && !$order->isRejected())
                                        <div class="mt-2 text-xs">
                                            <p class="text-stone-500">Shipped via <span class="font-bold text-[#4A2C1D]">{{ $order->tracking_carrier ?: 'Express Logistics' }}</span></p>
                                            <p class="text-stone-500 mt-0.5">Tracking ID: <span class="font-bold font-mono text-[#4A2C1D] bg-stone-100 px-1.5 py-0.5 rounded">{{ $order->tracking_number }}</span></p>
                                        </div>
                                    @elseif($order->status === 'Confirmed')
                                        <p class="text-xs text-stone-500 mt-1 leading-relaxed">Payment confirmed. Order is being prepared for dispatch.</p>
                                    @elseif($order->status === 'Pending Verification')
                                        <p class="text-xs text-stone-500 mt-1 leading-relaxed">Payment screenshot under verification.</p>
                                    @else
                                        <p class="text-xs text-stone-500 mt-1 leading-relaxed">Order is being processed for dispatch.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Items Preview -->
                            <div class="lg:w-2/3">
                                <div class="flex items-center gap-3.5 overflow-x-auto pt-3.5 pb-3 px-2 no-scrollbar scrollbar-hide">
                                    @foreach($order->items->take(4) as $item)
                                        @php
                                            $productUrl = $item->product ? route('product.show', $item->product->slug) : null;
                                        @endphp
                                        <div class="relative shrink-0 group">
                                            @if($productUrl)
                                                <a href="{{ $productUrl }}" class="block w-20 h-20 sm:w-24 sm:h-24 rounded-xl border border-[#D4AF6A]/30 overflow-hidden bg-[#FAF7F0] shadow-2xs group-hover:border-[#996E2E] group-hover:shadow-md transition duration-300" title="{{ $item->product_name }} — Click to view product">
                                                    <img src="{{ $item->product_image ?: asset('images/products/p1-a.svg') }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                                </a>
                                            @else
                                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl border border-[#D4AF6A]/30 overflow-hidden bg-[#FAF7F0] shadow-2xs" title="{{ $item->product_name }}">
                                                    <img src="{{ $item->product_image ?: asset('images/products/p1-a.svg') }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                </div>
                                            @endif
                                            <div class="absolute -top-2 -right-2 z-10 min-w-6 h-6 px-1 rounded-full bg-[#4A2C1D] text-[#E7C77B] text-[11px] font-bold flex items-center justify-center shadow-md border-2 border-white ring-1 ring-[#D4AF6A]/40 pointer-events-none">
                                                {{ $item->quantity }}
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if($order->items->count() > 4)
                                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl border-2 border-dashed border-[#D4AF6A]/70 flex flex-col items-center justify-center bg-[#FAF7F0]/60 text-[#996E2E] shrink-0 hover:bg-[#FAF7F0] transition">
                                            <span class="text-base sm:text-lg font-bold">+{{ $order->items->count() - 4 }}</span>
                                            <span class="text-[10px] uppercase tracking-wider font-semibold">More</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination (if applicable) -->
            @if($orders->hasPages())
                <div class="mt-10">
                    {{ $orders->links() }}
                </div>
            @endif
        @endif

    </div>

    <!-- Interactive Cancellation Modal (Responsive Mobile First) -->
    <div x-show="cancelModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         role="dialog" 
         aria-modal="true">
        <!-- Backdrop -->
        <div x-show="cancelModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" 
             @click="cancelModal = false"></div>

        <div class="flex min-h-full items-end sm:items-center justify-center p-3 sm:p-4 text-center">
            <div x-show="cancelModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl sm:rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-md sm:max-w-lg border border-[#D4AF6A]/40 max-h-[90vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="bg-[#FAF7F0] px-5 py-4 sm:px-6 sm:py-5 border-b border-[#D4AF6A]/20 flex items-center justify-between shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <h3 class="font-serif-royal font-bold text-lg text-[#4A2C1D]">Cancel Order</h3>
                            <p class="text-xs text-stone-500 font-mono">#<span x-text="cancelOrderNumber"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="cancelModal = false" class="text-stone-400 hover:text-stone-700 transition p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable for small mobile screens) -->
                <form :action="cancelActionUrl" method="POST" @submit="isSubmitting = true" class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-stone-800 text-sm mb-1">
                            Reason for Cancellation <span class="text-rose-600">*</span>
                        </label>
                        <p class="text-[11px] text-stone-500 mb-3">
                            Please select a reason so we can help you better:
                        </p>

                        <div class="space-y-2">
                            @php
                                $orderReasons = [
                                    'Ordered by mistake / Change of mind',
                                    'Found a better price or alternative design',
                                    'Incorrect delivery address or phone number',
                                    'Delivery time is too long / Need it urgently',
                                    'Payment issue / Want to change payment method',
                                    'Other reason (please specify below)',
                                ];
                            @endphp

                            @foreach($orderReasons as $r)
                                <label class="flex items-center space-x-3 p-3 rounded-xl border cursor-pointer transition text-left"
                                       :class="selectedReason === '{{ $r }}' ? 'border-[#996E2E] bg-[#FAF7F0] ring-1 ring-[#D4AF6A]' : 'border-stone-200 hover:border-stone-300 bg-white'">
                                    <input type="radio" name="cancellation_reason" value="{{ $r }}" x-model="selectedReason" class="text-[#996E2E] focus:ring-[#D4AF6A] accent-[#996E2E] shrink-0">
                                    <span class="font-medium text-stone-800 text-xs">{{ $r }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">
                            Additional Details (Optional):
                        </label>
                        <textarea name="cancellation_comment" x-model="customComment" rows="2" placeholder="Tell us more about the reason..." class="w-full border border-stone-300 rounded-xl p-2.5 text-xs focus:ring-[#D4AF6A] focus:border-[#996E2E] outline-hidden"></textarea>
                    </div>

                    <!-- Warning Callout -->
                    <div class="p-3 bg-rose-50 rounded-xl border border-rose-200 text-rose-800 text-[11px] flex items-start space-x-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Once cancelled, reserved creations will be immediately released back to our inventory. This action cannot be undone.</span>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-3 border-t border-stone-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 shrink-0">
                        <button type="button" @click="cancelModal = false" class="w-full sm:w-auto px-5 py-2.5 rounded-full border border-stone-300 text-stone-700 hover:bg-stone-100 font-bold text-xs transition text-center cursor-pointer">
                            Keep My Order
                        </button>
                        <button type="submit" :disabled="isSubmitting" class="w-full sm:w-auto px-6 py-2.5 rounded-full bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center space-x-1.5 cursor-pointer">
                            <span x-show="!isSubmitting">Confirm Cancellation</span>
                            <span x-show="isSubmitting" style="display:none;">Cancelling...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

