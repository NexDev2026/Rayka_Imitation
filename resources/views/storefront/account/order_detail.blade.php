@extends('layouts.storefront')

@section('title', "Order #$order->order_number Details — Rayka Imitation Jewellery")

@section('content')
<div class="bg-stone-50 min-h-screen pt-8 pb-24 sm:py-16" x-data="{ cancelModal: false, selectedReason: 'Ordered by mistake / Change of mind', customComment: '', isSubmitting: false }">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Navigation and Actions -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <a href="{{ route('account.orders') }}" class="inline-flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-stone-500 hover:text-[#996E2E] transition group">
                <span class="w-8 h-8 rounded-full bg-white border border-stone-200 flex items-center justify-center group-hover:border-[#D4AF6A] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </span>
                <span>Back to Orders</span>
            </a>

            <div class="flex items-center gap-3">
                @if($order->canBeCancelled())
                    <button type="button" @click="cancelModal = true" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-full bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300 font-bold text-xs transition shadow-xs cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Cancel Order</span>
                    </button>
                @endif
                
                @if($order->isConfirmed())
                    <a href="{{ route('account.order.invoice', $order->order_number) }}" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-[#4A2C1D] to-[#2E180E] text-[#E7C77B] hover:shadow-lg hover:scale-105 font-bold text-xs transition shadow-sm">
                        <svg class="w-4 h-4 text-[#D4AF6A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Download Invoice</span>
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center space-x-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 flex items-center space-x-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-[#D4AF6A]/30 shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="bg-[#FAF7F0] p-4 sm:p-8 lg:p-10 border-b border-[#D4AF6A]/20 text-center relative overflow-hidden">
                <!-- Decorative background elements -->
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-[#D4AF6A]/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-[#D4AF6A]/10 rounded-full blur-3xl"></div>
                
                <div class="relative z-10">
                    <span class="inline-block px-3.5 py-1 sm:px-4 sm:py-1.5 rounded-full border text-[11px] sm:text-xs font-bold uppercase tracking-widest mb-3 sm:mb-4 shadow-xs {{ $order->status_badge_class }}">
                        {{ $order->status }}
                    </span>
                    <h1 class="font-serif-royal text-xl sm:text-3xl lg:text-4xl font-bold text-[#4A2C1D] mb-2 break-all sm:break-normal tracking-tight">
                        Order #{{ $order->order_number }}
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-500">Placed on {{ $order->created_at->format('l, F j, Y \a\t h:i A') }} IST</p>
                </div>
            </div>

            <div class="p-3.5 sm:p-8 lg:p-10 space-y-8 sm:space-y-12">
                <!-- Live Consignment Journey (Stepper or Status Card) -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-6 sm:mb-8 text-center">Live Consignment Journey</h3>
                    
                    @php
                        $isRejected = $order->status === 'Rejected';
                        $isCancelled = $order->status === 'Cancelled';
                        $cancelDetails = $order->getCancellationDetails();
                        $waNumber = $storeSettings['clean_whatsapp'] ?? '919638868024';

                        // Determine step completion logic for active orders
                        $step1 = true; // Always verified at least pending
                        $step2 = in_array($order->status, ['Confirmed', 'Processing', 'Shipped', 'Delivered']);
                        $step3 = in_array($order->status, ['Shipped', 'Delivered']);
                        $step4 = $order->status === 'Delivered';
                        
                        function getStepClasses($isCompleted, $isActive, $isCancelled = false) {
                            if ($isCancelled) return 'bg-rose-100 text-rose-500 border-rose-200';
                            if ($isCompleted) return 'bg-[#4A2C1D] text-[#E7C77B] border-[#4A2C1D] shadow-md';
                            if ($isActive) return 'bg-white text-[#996E2E] border-[#D4AF6A] shadow-inner';
                            return 'bg-stone-50 text-stone-300 border-stone-200';
                        }
                    @endphp

                    @if($isRejected)
                        <div class="max-w-2xl mx-auto p-4 sm:p-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-xs space-y-3.5">
                            <div class="flex items-start gap-3 sm:gap-4">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-rose-600 text-white flex items-center justify-center font-bold text-base sm:text-lg shrink-0 shadow-xs">
                                    ✕
                                </div>
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                        <h4 class="font-serif-royal font-bold text-base sm:text-lg text-rose-950 leading-snug">Order Rejected / Payment Unverified</h4>
                                        <span class="text-[9.5px] sm:text-[10px] uppercase font-bold tracking-wider bg-rose-200/80 text-rose-800 px-2 py-0.5 rounded-full inline-block">Payment Unverified</span>
                                    </div>
                                    <p class="text-xs text-rose-700 leading-relaxed break-words">
                                        {{ $order->payment?->admin_note ?: 'This order has been marked as rejected because payment proof could not be verified in our bank account.' }}
                                    </p>
                                </div>
                            </div>
                            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode("Hello Rayka Support, my order #{$order->order_number} was marked as Rejected. Please help verify:") }}" target="_blank" class="w-full py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-2 text-center">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>Resolve via WhatsApp Concierge</span>
                                <span>→</span>
                            </a>
                            <div class="pt-3 border-t border-rose-200/60 text-[11px] text-rose-800">
                                💡 <strong>Need to verify payment?</strong> If money was debited from your bank account, please share your UPI Transaction ID / UTR screenshot with our concierge team on WhatsApp above. We will verify and restore your order.
                            </div>
                        </div>
                    @elseif($isCancelled)
                        @if($cancelDetails['by_customer'])
                            <div class="max-w-2xl mx-auto p-4 sm:p-6 rounded-2xl bg-stone-50 border border-stone-300 text-stone-800 shadow-xs space-y-3.5">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-stone-200 text-stone-700 flex items-center justify-center font-bold text-base sm:text-lg shrink-0 shadow-xs">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                    <div class="space-y-1.5 flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                            <h4 class="font-serif-royal font-bold text-base sm:text-lg text-stone-900 leading-snug">Order Cancelled by You</h4>
                                            <span class="text-[9.5px] sm:text-[10px] uppercase font-bold tracking-wider bg-stone-200 text-stone-700 px-2 py-0.5 rounded-full inline-block">User Cancelled</span>
                                        </div>
                                        <p class="text-xs text-stone-600 leading-relaxed">
                                            You cancelled this order on <strong class="text-stone-800">{{ $cancelDetails['cancelled_at']->format('l, F j, Y \a\t h:i A') }} IST</strong>. Reserved creations were returned to stock.
                                        </p>
                                        @if(!empty($cancelDetails['reason']))
                                            <div class="mt-2 p-2.5 sm:p-3 bg-white rounded-xl border border-stone-200 text-xs">
                                                <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-bold text-stone-400 block mb-0.5">Cancellation Reason:</span>
                                                <span class="font-semibold text-stone-800 break-words leading-relaxed">"{{ $cancelDetails['reason'] }}"</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="pt-3 border-t border-stone-200 text-[11px] text-stone-500 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                    <span class="leading-tight">If you made a payment that requires a refund, our royal concierge team is here to assist.</span>
                                    <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode("Hello Rayka Support, I cancelled my order #{$order->order_number} and need assistance:") }}" target="_blank" class="text-[#996E2E] font-bold hover:underline flex items-center gap-1 shrink-0 self-start sm:self-auto">
                                        <span>Contact Concierge</span>
                                        <span>→</span>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="max-w-2xl mx-auto p-4 sm:p-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-xs space-y-3.5">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-rose-600 text-white flex items-center justify-center font-bold text-base sm:text-lg shrink-0 shadow-xs">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                    <div class="space-y-1.5 flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                            <h4 class="font-serif-royal font-bold text-base sm:text-lg text-rose-950 leading-snug">Order Cancelled by Admin</h4>
                                            <span class="text-[9.5px] sm:text-[10px] uppercase font-bold tracking-wider bg-rose-200/80 text-rose-800 px-2 py-0.5 rounded-full inline-block">Admin Decision</span>
                                        </div>
                                        <p class="text-xs text-rose-700 leading-relaxed">
                                            This order was cancelled by the boutique administrator on <strong class="text-rose-900">{{ $cancelDetails['cancelled_at']->format('l, F j, Y \a\t h:i A') }} IST</strong>.
                                        </p>
                                        @if(!empty($cancelDetails['reason']))
                                            <div class="mt-2 p-2.5 sm:p-3 bg-white rounded-xl border border-rose-200 text-xs">
                                                <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-bold text-rose-500 block mb-0.5">Cancellation Reason:</span>
                                                <span class="font-semibold text-rose-950 break-words leading-relaxed">"{{ $cancelDetails['reason'] }}"</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="pt-3 border-t border-rose-200/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5">
                                    <span class="text-[11px] text-rose-800 leading-tight">Need assistance or clarification about this decision?</span>
                                    <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode("Hello Rayka Support, my order #{$order->order_number} was cancelled by admin. Please help:") }}" target="_blank" class="w-full sm:w-auto shrink-0 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-center">
                                        <span>Contact Concierge</span>
                                        <span>→</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="relative max-w-3xl mx-auto">
                            <!-- Connecting Line -->
                            <div class="absolute top-1/2 left-0 w-full h-1 bg-stone-100 -translate-y-1/2 z-0 hidden sm:block"></div>
                            
                            <!-- Dynamic Progress Line -->
                            <div class="absolute top-1/2 left-0 h-1 bg-[#D4AF6A] -translate-y-1/2 z-0 hidden sm:block transition-all duration-700 ease-in-out" 
                                 style="width: {{ $step4 ? '100%' : ($step3 ? '66%' : ($step2 ? '33%' : '0%')) }}">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 sm:gap-0 relative z-10">
                                <!-- Step 1 -->
                                <div class="flex flex-row sm:flex-col items-center gap-4 sm:gap-3 group">
                                    <div class="w-12 h-12 rounded-full border-2 flex items-center justify-center shrink-0 transition-all duration-300 {{ getStepClasses(true, false, false) }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div class="sm:text-center">
                                        <p class="font-bold text-sm {{ true ? 'text-[#4A2C1D]' : 'text-stone-400' }}">Order Placed</p>
                                        <p class="text-[10px] sm:text-[11px] text-stone-500 mt-0.5">Verification pending</p>
                                    </div>
                                </div>

                                <!-- Step 2 -->
                                <div class="flex flex-row sm:flex-col items-center gap-4 sm:gap-3 group">
                                    <div class="w-12 h-12 rounded-full border-2 flex items-center justify-center shrink-0 transition-all duration-300 {{ getStepClasses($step2, !$step2 && $step1, false) }}">
                                        @if($step2)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        @endif
                                    </div>
                                    <div class="sm:text-center">
                                        <p class="font-bold text-sm {{ $step2 ? 'text-[#4A2C1D]' : 'text-stone-400' }}">Confirmed</p>
                                        <p class="text-[10px] sm:text-[11px] text-stone-500 mt-0.5">Payment verified</p>
                                    </div>
                                </div>

                                <!-- Step 3 -->
                                <div class="flex flex-row sm:flex-col items-center gap-4 sm:gap-3 group">
                                    <div class="w-12 h-12 rounded-full border-2 flex items-center justify-center shrink-0 transition-all duration-300 {{ getStepClasses($step3, !$step3 && $step2, false) }}">
                                        @if($step3)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                        @endif
                                    </div>
                                    <div class="sm:text-center">
                                        <p class="font-bold text-sm {{ $step3 ? 'text-[#4A2C1D]' : 'text-stone-400' }}">Shipped</p>
                                        <p class="text-[10px] sm:text-[11px] text-stone-500 mt-0.5">Dispatched to courier</p>
                                    </div>
                                </div>

                                <!-- Step 4 -->
                                <div class="flex flex-row sm:flex-col items-center gap-4 sm:gap-3 group">
                                    <div class="w-12 h-12 rounded-full border-2 flex items-center justify-center shrink-0 transition-all duration-300 {{ getStepClasses($step4, !$step4 && $step3, false) }}">
                                        @if($step4)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                        @endif
                                    </div>
                                    <div class="sm:text-center">
                                        <p class="font-bold text-sm {{ $step4 ? 'text-[#4A2C1D]' : 'text-stone-400' }}">Delivered</p>
                                        <p class="text-[10px] sm:text-[11px] text-stone-500 mt-0.5">Arrived at destination</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($order->tracking_number && in_array($order->status, ['Processing', 'Shipped', 'Delivered']) && !$order->isCancelled() && !$order->isRejected())
                            <div class="max-w-2xl mx-auto mt-8 p-4 bg-blue-50/50 border border-blue-100 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-blue-800 uppercase font-bold tracking-wider mb-0.5">Tracking Information</p>
                                        <p class="text-sm text-blue-900">Carrier: <strong>{{ $order->tracking_carrier ?: 'Express Logistics' }}</strong></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5">
                                    <div class="bg-white px-4 py-2 rounded-xl border border-blue-200 font-mono font-bold text-blue-900 shadow-sm text-sm sm:text-base">
                                        {{ $order->tracking_number }}
                                    </div>
                                    @if($order->tracking_url)
                                        <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer" class="px-3.5 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-1">
                                            <span>Track Live</span>
                                            <span>↗</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Items -->
                    <div class="lg:col-span-2 space-y-6">
                        <h3 class="font-serif-royal font-bold text-xl text-[#4A2C1D] border-b border-stone-200 pb-3">Purchased Creations</h3>
                        
                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                @php
                                    $productUrl = $item->product ? route('product.show', $item->product->slug) : null;
                                @endphp
                                <div class="flex items-start sm:items-center gap-3 sm:gap-4 p-3.5 sm:p-4 rounded-2xl border border-stone-100 hover:border-[#D4AF6A]/50 bg-white shadow-xs hover:shadow transition group">
                                    @if($productUrl)
                                        <a href="{{ $productUrl }}" title="{{ $item->product_name }} — View Product Specification" class="w-16 h-16 sm:w-24 sm:h-24 rounded-xl border border-stone-200 overflow-hidden bg-[#FAF7F0] shrink-0 block group/img">
                                            <img src="{{ $item->product_image ?: asset('images/products/p1-a.svg') }}" class="w-full h-full object-cover group-hover/img:scale-105 transition duration-500">
                                        </a>
                                    @else
                                        <div class="w-16 h-16 sm:w-24 sm:h-24 rounded-xl border border-stone-200 overflow-hidden bg-[#FAF7F0] shrink-0">
                                            <img src="{{ $item->product_image ?: asset('images/products/p1-a.svg') }}" class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row justify-between sm:items-start gap-1.5 sm:gap-2">
                                            <div class="min-w-0">
                                                <h4 class="font-bold text-[#4A2C1D] text-sm sm:text-base leading-snug break-words">
                                                    @if($productUrl)
                                                        <a href="{{ $productUrl }}" class="hover:text-[#996E2E] transition">{{ $item->product_name }}</a>
                                                    @else
                                                        {{ $item->product_name }}
                                                    @endif
                                                </h4>
                                                @if($item->variant_info)
                                                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">Variant: <span class="text-stone-700 font-medium">{{ $item->variant_info }}</span></p>
                                                @endif
                                                <p class="text-[11px] sm:text-xs font-mono text-stone-400 mt-0.5 uppercase">SKU: {{ $item->product_sku ?? 'N/A' }}</p>
                                            </div>
                                            <div class="sm:text-right shrink-0 mt-1 sm:mt-0">
                                                <p class="font-bold text-[#4A2C1D] text-base sm:text-lg">₹{{ number_format($item->subtotal) }}</p>
                                                <p class="text-[11px] sm:text-xs text-stone-500">₹{{ number_format($item->unit_price) }} × {{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right Column: Address & Summary -->
                    <div class="space-y-6">
                        <div class="bg-[#FAF7F0]/50 p-4 sm:p-6 rounded-2xl border border-[#D4AF6A]/30 overflow-hidden">
                            <h4 class="font-serif-royal font-bold text-lg text-[#4A2C1D] mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#996E2E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>Shipping Details</span>
                            </h4>
                            @php
                                $shipping = $order->shipping_address ?? $order->address;
                            @endphp
                            @if($shipping)
                                <div class="space-y-1 text-sm text-stone-700 min-w-0">
                                    <p class="font-bold text-[#4A2C1D] text-base">{{ $shipping->name }}</p>
                                    <p class="leading-relaxed text-stone-600 mt-2 break-words">{{ $shipping->formatted_address }}</p>
                                    <div class="pt-3 mt-3 border-t border-[#D4AF6A]/20 space-y-2 min-w-0">
                                        @if($shipping->mobile)
                                            <p class="flex items-center gap-2 text-stone-600 min-w-0">
                                                <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                <span class="font-medium text-[#4A2C1D]">{{ $shipping->mobile }}</span>
                                            </p>
                                        @endif
                                        @if($shipping->email)
                                            <p class="flex items-center gap-2 text-stone-600 min-w-0">
                                                <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                <span class="break-all min-w-0 font-medium text-[#4A2C1D]">{{ $shipping->email }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @elseif($order->user)
                                <div class="space-y-1 text-sm text-stone-700 min-w-0">
                                    <p class="font-bold text-[#4A2C1D] text-base">{{ $order->user->name }}</p>
                                    <p class="text-xs text-stone-500 mt-1 italic">Registered Customer Account</p>
                                    <div class="pt-3 mt-3 border-t border-[#D4AF6A]/20 space-y-2 min-w-0">
                                        @if($order->user->mobile)
                                            <p class="flex items-center gap-2 text-stone-600 min-w-0">
                                                <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                <span class="font-medium text-[#4A2C1D]">{{ $order->user->mobile }}</span>
                                            </p>
                                        @endif
                                        <p class="flex items-center gap-2 text-stone-600 min-w-0">
                                            <svg class="w-4 h-4 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            <span class="break-all min-w-0 font-medium text-[#4A2C1D]">{{ $order->user->email }}</span>
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="bg-white p-4 sm:p-6 rounded-2xl border border-[#D4AF6A]/30 shadow-sm">
                            <h4 class="font-serif-royal font-bold text-lg text-[#4A2C1D] mb-4">Payment Summary</h4>
                            <div class="space-y-3 text-sm text-stone-600">
                                <div class="flex justify-between">
                                    <span>Subtotal ({{ $order->items->sum('quantity') }} items)</span>
                                    <span class="font-semibold text-[#4A2C1D]">₹{{ number_format($order->subtotal) }}</span>
                                </div>
                                
                                @if($order->coupon_discount > 0)
                                    <div class="flex justify-between items-center text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                            <span class="font-bold text-xs truncate">{{ $order->coupon_code }}</span>
                                        </div>
                                        <span class="font-bold shrink-0">− ₹{{ number_format($order->coupon_discount) }}</span>
                                    </div>
                                @endif
                                
                                <div class="flex justify-between">
                                    <span>Shipping Fee</span>
                                    <span class="font-semibold {{ $order->shipping_fee == 0 ? 'text-emerald-600' : 'text-[#4A2C1D]' }}">
                                        {{ $order->shipping_fee == 0 ? 'FREE' : '₹' . number_format($order->shipping_fee) }}
                                    </span>
                                </div>
                                
                                <div class="pt-4 border-t border-[#D4AF6A]/30 flex justify-between items-end mt-2">
                                    <div class="min-w-0 pr-2">
                                        <span class="block text-xs uppercase tracking-wider font-bold text-stone-400">Grand Total</span>
                                        <span class="block text-xs text-stone-500 mt-0.5 truncate">Paid via {{ $order->payment?->payment_method ?? 'Static QR/UPI' }}</span>
                                    </div>
                                    <span class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#4A2C1D] shrink-0">₹{{ number_format($order->total_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
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
                            <p class="text-xs text-stone-500 font-mono">#{{ $order->order_number }}</p>
                        </div>
                    </div>
                    <button type="button" @click="cancelModal = false" class="text-stone-400 hover:text-stone-700 transition p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable for small mobile screens) -->
                <form action="{{ route('account.order.cancel', $order->order_number) }}" method="POST" @submit="isSubmitting = true" class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4 text-xs">
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
                                $reasonsList = [
                                    'Ordered by mistake / Change of mind',
                                    'Found a better price or alternative design',
                                    'Incorrect delivery address or phone number',
                                    'Delivery time is too long / Need it urgently',
                                    'Payment issue / Want to change payment method',
                                    'Other reason (please specify below)',
                                ];
                            @endphp

                            @foreach($reasonsList as $r)
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

