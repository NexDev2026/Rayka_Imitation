@extends('layouts.storefront')

@section('title', $order ? "Track Consignment #{$order->order_number} — Rayka Imitation Jewellery" : 'Track Your Order — Rayka Imitation Jewellery')

@section('content')
<div class="min-h-[75vh] py-10 sm:py-16 bg-[#FAF7F0]/60 relative overflow-hidden">
    <!-- Subtle Background Glows & Motifs -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-[#D4AF6A]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#996E2E]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8 relative z-10 space-y-8 sm:space-y-10">

        <!-- Header -->
        <div class="text-center space-y-2.5 sm:space-y-3">
            <div class="inline-flex items-center space-x-2 px-3.5 py-1 rounded-full bg-white border border-[#D4AF6A]/60 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-serif-royal text-[10px] sm:text-[11px] font-bold tracking-widest uppercase text-[#996E2E]">Real-Time Consignment Tracker</span>
            </div>
            <h1 class="font-serif-royal text-2xl sm:text-4xl lg:text-5xl font-bold text-[#4A2C1D]">
                Track Your Royal Consignment
            </h1>
            <p class="text-xs sm:text-sm text-stone-600 max-w-xl mx-auto px-2">
                Follow your handcrafted 1-gram micro gold creations step-by-step from our atelier to your doorstep.
            </p>
        </div>

        <!-- Search Card -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl sm:rounded-3xl border-2 border-[#D4AF6A]/40 p-4 sm:p-8 shadow-xl relative backdrop-blur-xs">
            <form action="{{ route('order.track.search') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#4A2C1D] uppercase tracking-wider mb-1.5">
                            Order Number *
                        </label>
                        <div>
                            <input type="text" name="order_number" value="{{ old('order_number', $orderNumber ?? ($order->order_number ?? '')) }}" required
                                   placeholder="e.g. RAY-20260917-ABC1"
                                   class="w-full bg-[#FAF7F0]/50 border border-[#D4AF6A]/60 rounded-xl px-4 py-3 text-xs sm:text-sm font-mono uppercase font-bold text-stone-800 placeholder:text-stone-400 focus:outline-hidden focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition shadow-2xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#4A2C1D] uppercase tracking-wider mb-1.5">
                            Mobile No. or Email *
                        </label>
                        <div>
                            <input type="text" name="identifier" value="{{ old('identifier') }}" required
                                   placeholder="e.g. 9876543210 or email@domain.com"
                                   class="w-full bg-[#FAF7F0]/50 border border-[#D4AF6A]/60 rounded-xl px-4 py-3 text-xs sm:text-sm text-stone-800 placeholder:text-stone-400 focus:outline-hidden focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition shadow-2xs">
                        </div>
                    </div>
                </div>

                <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-[11px] text-stone-500">
                        * Required for order privacy and verified customer access.
                    </p>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-gradient-to-r from-[#4A2C1D] to-[#2E180E] hover:from-[#3D2316] hover:to-[#1F1009] text-[#E7C77B] px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition transform active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4 text-[#E7C77B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Track Consignment</span>
                    </button>
                </div>
            </form>
        </div>

        @if($order)
            <!-- ================= ORDER DETAILS DASHBOARD ================= -->
            <div class="bg-white rounded-3xl border border-[#D4AF6A]/50 shadow-xl overflow-hidden divide-y divide-[#D4AF6A]/20" x-data="{ copiedAwb: false }">

                <!-- 1. Consignment Overview Header -->
                <div class="p-4 sm:p-8 bg-gradient-to-b from-[#FAF7F0] to-white flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6">
                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Consignment Number:</span>
                            <span class="font-mono text-base sm:text-lg font-bold text-[#4A2C1D] bg-white px-3 py-1 rounded-lg border border-[#D4AF6A]/40 shadow-2xs">
                                {{ $order->order_number }}
                            </span>
                            <button type="button" @click="navigator.clipboard.writeText('{{ $order->order_number }}'); copiedAwb = true; setTimeout(() => copiedAwb = false, 2000)" class="text-[11px] font-semibold text-[#996E2E] hover:underline cursor-pointer">
                                <span x-show="!copiedAwb">Copy #</span>
                                <span x-show="copiedAwb" class="text-emerald-700 font-bold inline-flex items-center gap-1" style="display:none;">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Copied
                                </span>
                            </button>
                        </div>
                        <p class="text-xs text-stone-500">
                            Booked on {{ $order->created_at->format('D, d M Y \a\t h:i A') }} IST • Destination: <strong class="text-stone-700">{{ $order->address?->city }}, {{ $order->address?->state }}</strong>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="px-4 py-1.5 rounded-full border text-xs font-bold uppercase tracking-wider shadow-2xs {{ $order->status_badge_class }}">
                            {{ $order->status }}
                        </div>

                        @if($order->isConfirmed())
                            <a href="{{ route('order.track.invoice', $order->order_number) }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-full bg-white border border-[#D4AF6A] text-[#4A2C1D] hover:bg-[#FAF7F0] text-xs font-bold shadow-xs transition">
                                <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Official Tax Invoice (PDF)</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 2. SaaS 5-Step Visual Delivery Journey -->
                <div class="p-4 sm:p-10 bg-white space-y-6 sm:space-y-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-4 border-b border-stone-100">
                        <h3 class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D] flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#996E2E] shrink-0"></span>
                            <span>Live Consignment Journey</span>
                        </h3>
                        <div class="inline-flex items-center gap-1.5 text-xs text-stone-600 bg-[#FAF7F0] px-3 py-1.5 rounded-full border border-[#D4AF6A]/40 self-start sm:self-auto shadow-2xs">
                            <span class="text-stone-500 font-medium">Current Stage:</span>
                            <strong class="text-[#4A2C1D] font-bold">{{ $order->status }}</strong>
                        </div>
                    </div>

                    @php
                        $step = $order->step_index; // 1: Pending, 2: Confirmed, 3: Processing, 4: Shipped, 5: Delivered
                        $isRejected = $order->status === 'Rejected';
                        $isCancelled = $order->status === 'Cancelled';
                        $cancelDetails = $order->getCancellationDetails();
                        $waNumber = $storeSettings['clean_whatsapp'] ?? '';
                    @endphp

                    @if($isRejected)
                        <div class="p-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs sm:text-sm shadow-xs">
                            <div class="flex items-start space-x-3.5">
                                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold shrink-0 shadow-xs">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <p class="font-bold text-base text-rose-900">Order Rejected / Payment Unverified</p>
                                    <p class="text-xs text-rose-700 leading-relaxed">
                                        {{ $order->payment?->admin_note ?: 'This order has been marked as rejected because payment proof could not be verified in our bank account.' }}
                                    </p>
                                </div>
                            </div>
                            @if(!empty($waNumber))
                            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode("Hello Rayka Support, my order #{$order->order_number} is showing Rejected. Please help verify:") }}" target="_blank" class="shrink-0 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>Resolve via WhatsApp Concierge</span>
                                <span>→</span>
                            </a>
                            @endif
                        </div>
                    @elseif($isCancelled)
                        <div class="space-y-4">
                            @if($cancelDetails['by_customer'])
                                <div class="max-w-2xl mx-auto p-4 sm:p-6 rounded-2xl bg-stone-50 border border-stone-300 text-stone-800 shadow-xs space-y-3">
                                    <div class="flex items-start gap-3 sm:gap-4">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-stone-200 text-stone-700 flex items-center justify-center font-bold text-base sm:text-lg shrink-0 shadow-xs">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                        <div class="space-y-1.5 flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                                <h4 class="font-serif-royal font-bold text-base sm:text-lg text-stone-900 leading-snug">Order Cancelled by Customer</h4>
                                                <span class="text-[9.5px] sm:text-[10px] uppercase font-bold tracking-wider bg-stone-200 text-stone-700 px-2 py-0.5 rounded-full inline-block">User Cancelled</span>
                                            </div>
                                            <p class="text-xs text-stone-600 leading-relaxed">
                                                This order was cancelled on <strong class="text-stone-800">{{ $cancelDetails['cancelled_at']->format('l, F j, Y \a\t h:i A') }} IST</strong>. Reserved creations were returned to available inventory.
                                            </p>
                                            @if(!empty($cancelDetails['reason']))
                                                <div class="mt-2 p-2.5 sm:p-3 bg-white rounded-xl border border-stone-200 text-xs">
                                                    <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-bold text-stone-400 block mb-0.5">Cancellation Reason:</span>
                                                    <span class="font-semibold text-stone-800 break-words leading-relaxed">"{{ $cancelDetails['reason'] }}"</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="max-w-2xl mx-auto p-4 sm:p-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-xs space-y-3">
                                    <div class="flex items-start gap-3 sm:gap-4">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-rose-600 text-white flex items-center justify-center font-bold text-base sm:text-lg shrink-0 shadow-xs">
                                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                        <div class="space-y-1.5 flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                                <h4 class="font-serif-royal font-bold text-base sm:text-lg text-rose-950 leading-snug">Order Cancelled by Boutique Admin</h4>
                                                <span class="text-[9.5px] sm:text-[10px] uppercase font-bold tracking-wider bg-rose-200/80 text-rose-800 px-2 py-0.5 rounded-full inline-block">Admin Decision</span>
                                            </div>
                                            <p class="text-xs text-rose-700 leading-relaxed">
                                                This order was cancelled by administrator on <strong class="text-rose-900">{{ $cancelDetails['cancelled_at']->format('l, F j, Y \a\t h:i A') }} IST</strong>.
                                            </p>
                                            @if(!empty($cancelDetails['reason']))
                                                <div class="mt-2 p-2.5 sm:p-3 bg-white rounded-xl border border-rose-200 text-xs">
                                                    <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-bold text-rose-500 block mb-0.5">Cancellation Reason:</span>
                                                    <span class="font-semibold text-rose-950 break-words leading-relaxed">"{{ $cancelDetails['reason'] }}"</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- REASSURING REFUND STATUS CARD -->
                            @if($order->isRefunded())
                                <div class="max-w-2xl mx-auto rounded-2xl border border-emerald-300 bg-gradient-to-r from-emerald-50/90 via-white to-emerald-50/90 p-4 sm:p-5 shadow-xs transition">
                                    <div class="space-y-2">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 pb-2 border-b border-emerald-200/70">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 shrink-0"></span>
                                                <h4 class="font-serif-royal font-bold text-sm sm:text-base text-emerald-950">
                                                    Refund Processed & Returned (₹{{ number_format($order->refund_amount > 0 ? $order->refund_amount : $order->getRefundableAmount(), 2) }})
                                                </h4>
                                            </div>
                                            <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 self-start sm:self-auto">
                                                Refund Completed
                                            </span>
                                        </div>

                                        <p class="text-xs text-emerald-900 leading-relaxed">
                                            Your {{ $order->isCod() ? 'advance booking token' : 'prepaid payment' }} of <strong>₹{{ number_format($order->refund_amount > 0 ? $order->refund_amount : $order->getRefundableAmount(), 2) }}</strong> was successfully refunded on <strong class="text-emerald-950">{{ $order->refunded_at?->format('d M, Y \a\t h:i A') ?? 'Recently' }}</strong>.
                                        </p>

                                        <div class="p-3 bg-white/95 rounded-xl border border-emerald-200 text-xs text-stone-700 space-y-1.5 mt-2">
                                            <div class="flex flex-wrap items-center justify-between gap-1">
                                                <span class="text-stone-500">Refund Method:</span>
                                                <strong class="text-stone-900">{{ $order->refund_method ?: 'UPI / Bank Reversal' }}</strong>
                                            </div>
                                            @if($order->refund_transaction_reference)
                                                <div class="flex flex-wrap items-center justify-between gap-1 pt-1 border-t border-emerald-100">
                                                    <span class="text-stone-500">Bank / UPI Ref (UTR):</span>
                                                    <span class="font-mono font-bold text-emerald-950">{{ $order->refund_transaction_reference }}</span>
                                                </div>
                                            @endif
                                            @if($order->refund_note)
                                                <div class="pt-1 text-[11px] text-stone-600 italic">
                                                    Note: "{{ $order->refund_note }}"
                                                </div>
                                            @endif
                                        </div>

                                        @if($order->refund_screenshot)
                                            <div class="pt-2">
                                                <a href="{{ asset($order->refund_screenshot) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] rounded-lg shadow-2xs transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>View Official Bank Refund Receipt</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif($order->refund_status === 'Not Applicable')
                                <div class="max-w-2xl mx-auto rounded-2xl border border-stone-300 bg-stone-50/90 p-4 sm:p-5 shadow-xs transition">
                                    <div class="space-y-1.5">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 pb-2 border-b border-stone-200">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-stone-400 shrink-0"></span>
                                                <h4 class="font-serif-royal font-bold text-sm sm:text-base text-stone-800">
                                                    Refund Settlement: Not Applicable
                                                </h4>
                                            </div>
                                            <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-stone-200/80 text-stone-700 border border-stone-300 self-start sm:self-auto">
                                                No Refund Due
                                            </span>
                                        </div>
                                        <p class="text-xs text-stone-600 leading-relaxed">
                                            No refund is applicable for this cancelled order.
                                        </p>
                                        @if($order->refund_note)
                                            <div class="p-2.5 bg-white rounded-xl border border-stone-200 text-xs text-stone-700">
                                                <span class="text-stone-400 text-[10px] uppercase font-bold block mb-0.5">Accounts Note:</span>
                                                <span>"{{ $order->refund_note }}"</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @elseif($order->isRefundPending())
                                <div class="max-w-2xl mx-auto rounded-2xl border border-amber-300 bg-gradient-to-r from-amber-50/90 via-white to-amber-50/90 p-4 sm:p-5 shadow-xs transition">
                                    <div class="space-y-1.5">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 pb-2 border-b border-amber-200">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse shrink-0"></span>
                                                <h4 class="font-serif-royal font-bold text-sm sm:text-base text-[#4A2C1D]">
                                                    Refund In Process (₹{{ number_format($order->getRefundableAmount(), 2) }})
                                                </h4>
                                            </div>
                                            <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 self-start sm:self-auto">
                                                Refund Queued
                                            </span>
                                        </div>
                                        <p class="text-xs text-amber-950 leading-relaxed">
                                            Your {{ $order->isCod() ? 'advance booking token' : 'prepaid payment' }} of <strong>₹{{ number_format($order->getRefundableAmount(), 2) }}</strong> is in queue for refund. Our accounts team typically processes reversals to your source UPI or bank account within 24 to 48 business hours.
                                        </p>
                                        <p class="text-[11px] text-stone-500">
                                            Once processed, your bank transaction reference number (UTR) and refund receipt will be updated right here.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Progress Stepper (Desktop Horizontal / Mobile Vertical) -->
                        <div class="relative">
                            <!-- Desktop Horizontal Bar -->
                            <div class="hidden md:block absolute top-6 left-12 right-12 h-1 bg-stone-200 rounded-full z-0">
                                <div class="h-full bg-gradient-to-r from-[#996E2E] to-emerald-500 rounded-full transition-all duration-700"
                                     style="width: {{ $step === 1 ? '5%' : ($step === 2 ? '28%' : ($step === 3 ? '52%' : ($step === 4 ? '76%' : '100%'))) }}"></div>
                            </div>

                            <!-- Mobile Vertical Timeline Bar -->
                            <div class="md:hidden absolute top-6 bottom-6 left-6 w-0.5 bg-stone-200 -translate-x-1/2 z-0">
                                <div class="w-full bg-gradient-to-b from-[#996E2E] to-emerald-500 transition-all duration-700"
                                     style="height: {{ $step === 1 ? '10%' : ($step === 2 ? '32%' : ($step === 3 ? '56%' : ($step === 4 ? '80%' : '100%'))) }}"></div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 md:gap-2 relative z-10">
                                <!-- Step 1: Placed -->
                                <div class="flex md:flex-col items-start md:items-center md:text-center space-x-4 md:space-x-0 group">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm transition-all duration-300 shadow-md shrink-0 z-10 bg-white {{ $step >= 1 ? 'bg-gradient-to-br from-[#4A2C1D] to-[#996E2E] text-[#E7C77B] ring-4 ring-[#D4AF6A]/30' : 'bg-stone-100 text-stone-400 border border-stone-300' }}">
                                        @if($step > 1)
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @else
                                            <span>01</span>
                                        @endif
                                    </div>
                                    <div class="mt-0 md:mt-3 flex-1 min-w-0">
                                        <p class="text-xs font-bold text-[#4A2C1D]">1. Order Placed</p>
                                        <p class="text-[11px] text-stone-500 mt-0.5">Payment proof recorded</p>
                                        <span class="text-[10px] text-stone-400 font-mono">{{ $order->created_at->format('d M, h:i A') }} IST</span>
                                    </div>
                                </div>

                                <!-- Step 2: Payment Verified -->
                                <div class="flex md:flex-col items-start md:items-center md:text-center space-x-4 md:space-x-0 group">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm transition-all duration-300 shadow-md shrink-0 z-10 bg-white {{ $step >= 2 ? 'bg-gradient-to-br from-[#4A2C1D] to-[#996E2E] text-[#E7C77B] ring-4 ring-[#D4AF6A]/30' : ($step === 1 ? 'bg-amber-100 text-amber-800 border-2 border-amber-400 animate-pulse' : 'bg-stone-100 text-stone-400 border border-stone-300') }}">
                                        @if($step > 2)
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @elseif($step === 1)
                                            <svg class="w-6 h-6 text-amber-700 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @else
                                            <span>02</span>
                                        @endif
                                    </div>
                                    <div class="mt-0 md:mt-3 flex-1 min-w-0">
                                        <p class="text-xs font-bold {{ $step >= 2 ? 'text-[#4A2C1D]' : 'text-stone-500' }}">2. Payment Verified</p>
                                        <p class="text-[11px] text-stone-500 mt-0.5">Admin confirmed UPI proof</p>
                                        @if($order->payment?->verified_at)
                                            <span class="text-[10px] text-stone-400 font-mono">{{ $order->payment->verified_at->format('d M, h:i A') }} IST</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Step 3: Processing & Atelier Pack -->
                                <div class="flex md:flex-col items-start md:items-center md:text-center space-x-4 md:space-x-0 group">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm transition-all duration-300 shadow-md shrink-0 z-10 bg-white {{ $step >= 3 ? 'bg-gradient-to-br from-[#4A2C1D] to-[#996E2E] text-[#E7C77B] ring-4 ring-[#D4AF6A]/30' : ($step === 2 ? 'bg-blue-100 text-blue-800 border-2 border-blue-400 animate-pulse' : 'bg-stone-100 text-stone-400 border border-stone-300') }}">
                                        @if($step > 3)
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @else
                                            <span>03</span>
                                        @endif
                                    </div>
                                    <div class="mt-0 md:mt-3 flex-1 min-w-0">
                                        <p class="text-xs font-bold {{ $step >= 3 ? 'text-[#4A2C1D]' : 'text-stone-500' }}">3. Handcrafted & Packed</p>
                                        <p class="text-[11px] text-stone-500 mt-0.5">QC inspect & velvet packaging</p>
                                    </div>
                                </div>

                                <!-- Step 4: Shipped & In Transit -->
                                <div class="flex md:flex-col items-start md:items-center md:text-center space-x-4 md:space-x-0 group">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm transition-all duration-300 shadow-md shrink-0 z-10 bg-white {{ $step >= 4 ? 'bg-gradient-to-br from-indigo-700 to-indigo-900 text-white ring-4 ring-indigo-200' : ($step === 3 ? 'bg-indigo-50 text-indigo-700 border-2 border-indigo-400 animate-pulse' : 'bg-stone-100 text-stone-400 border border-stone-300') }}">
                                        @if($step > 4)
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                        @endif
                                    </div>
                                    <div class="mt-0 md:mt-3 flex-1 min-w-0">
                                        <p class="text-xs font-bold {{ $step >= 4 ? 'text-[#4A2C1D]' : 'text-stone-500' }}">4. Shipped / In Transit</p>
                                        <p class="text-[11px] text-stone-500 mt-0.5">Handed to courier partner</p>
                                        @if($order->tracking_carrier)
                                            <span class="text-[10px] text-indigo-700 font-semibold">{{ $order->tracking_carrier }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Step 5: Delivered -->
                                <div class="flex md:flex-col items-start md:items-center md:text-center space-x-4 md:space-x-0 group">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm transition-all duration-300 shadow-md shrink-0 z-10 bg-white {{ $step >= 5 ? 'bg-gradient-to-br from-emerald-600 to-green-700 text-white ring-4 ring-emerald-200' : 'bg-stone-100 text-stone-400 border border-stone-300' }}">
                                        @if($step >= 5)
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        @else
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7"/></svg>
                                        @endif
                                    </div>
                                    <div class="mt-0 md:mt-3 flex-1 min-w-0">
                                        <p class="text-xs font-bold {{ $step >= 5 ? 'text-emerald-800' : 'text-stone-500' }}">5. Royal Delivery</p>
                                        <p class="text-[11px] text-stone-500 mt-0.5">Delivered to your hands</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Courier & AWB Box (when processing, shipped, or delivered) -->
                    @if($order->tracking_number && in_array($order->status, ['Processing', 'Shipped', 'Delivered']) && !$order->isCancelled() && !$order->isRejected())
                        <div class="p-5 bg-gradient-to-r from-indigo-50/70 via-white to-indigo-50/70 rounded-2xl border border-indigo-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-indigo-700">Official Courier Logistics Partner</span>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="font-serif-royal text-base font-bold text-[#4A2C1D]">{{ $order->tracking_carrier ?: 'Express Courier' }}</span>
                                    <span class="text-xs font-mono font-bold bg-white px-3 py-1 rounded border border-indigo-200 text-indigo-950 shadow-2xs">
                                        AWB: {{ $order->tracking_number }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button" @click="navigator.clipboard.writeText('{{ $order->tracking_number }}'); copiedAwb = true; setTimeout(() => copiedAwb = false, 2000)" class="px-4 py-2 bg-white hover:bg-stone-50 text-indigo-900 border border-indigo-200 rounded-xl font-bold text-xs shadow-2xs transition cursor-pointer">
                                    <span x-show="!copiedAwb">Copy AWB</span>
                                    <span x-show="copiedAwb" class="text-emerald-700 font-bold inline-flex items-center gap-1" style="display:none;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Copied
                                    </span>
                                </button>

                                @if($order->tracking_url)
                                    <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center space-x-1.5 px-5 py-2 bg-indigo-900 hover:bg-indigo-950 text-white rounded-xl font-bold text-xs shadow-sm transition">
                                        <span>Direct Courier Tracking</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- 3. Consignment Items & Summary -->
                <div class="p-4 sm:p-8 bg-[#FAF7F0]/30 space-y-5 sm:space-y-6">
                    <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                        Artisanal Jewels in This Consignment ({{ $order->items->count() }})
                    </h3>

                    <div class="divide-y divide-stone-200 bg-white rounded-2xl border border-[#D4AF6A]/30 overflow-hidden shadow-xs">
                        @foreach($order->items as $item)
                            <div class="p-3.5 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
                                <div class="flex items-center space-x-3.5 sm:space-x-4">
                                    <img src="{{ $item->product_image ?: asset('images/products/p1-a.svg') }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="w-14 h-14 sm:w-20 sm:h-20 rounded-xl object-cover bg-[#FAF7F0] border border-[#D4AF6A]/30 shadow-2xs shrink-0">
                                    <div class="space-y-0.5 sm:space-y-1">
                                        <h4 class="font-serif-royal font-bold text-sm sm:text-base text-[#4A2C1D]">
                                            @if($item->product)
                                                <a href="{{ route('product.show', $item->product->slug) }}" class="hover:text-[#996E2E] transition">
                                                    {{ $item->product_name }}
                                                </a>
                                            @else
                                                {{ $item->product_name }}
                                            @endif
                                        </h4>
                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] text-stone-500">
                                            <span class="font-mono font-semibold">SKU: {{ $item->product_sku ?: 'RAY-JEWEL' }}</span>
                                            @if($item->variant_info)
                                                <span>•</span>
                                                <span class="bg-[#FAF7F0] text-[#996E2E] px-2 py-0.5 rounded font-medium border border-[#D4AF6A]/30">{{ $item->variant_info }}</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-stone-500 font-mono">
                                            {{ $item->quantity }} unit(s) × ₹{{ number_format($item->unit_price) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right border-t sm:border-t-0 pt-2 sm:pt-0">
                                    <span class="block text-[11px] sm:text-xs text-stone-400 font-medium">Subtotal</span>
                                    <span class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                                        ₹{{ number_format($item->subtotal) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 4. Shipping & Payment Overview Cards -->
                <div class="p-4 sm:p-8 bg-white grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-8">
                    <!-- Shipping Destination -->
                    <div class="space-y-2.5 sm:space-y-3">
                        <div class="flex items-center space-x-2 text-[#996E2E]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D]">Shipping Address</h4>
                        </div>
                        <div class="bg-[#FAF7F0] p-3.5 sm:p-4 rounded-xl border border-[#D4AF6A]/30 text-xs text-stone-700 space-y-1 min-w-0">
                            <p class="font-bold text-stone-900">{{ $order->address?->name }}</p>
                            <p class="text-stone-600 leading-relaxed break-words">{{ $order->address?->formatted_address }}</p>
                            <p class="pt-2 text-stone-500 font-mono">Mobile: <strong class="text-stone-800">{{ $order->address?->mobile }}</strong></p>
                            <p class="text-stone-500 break-all">Email: <strong class="text-stone-800 break-all">{{ $order->address?->email }}</strong></p>
                        </div>
                    </div>

                    <!-- Payment Breakdown -->
                    <div class="space-y-2.5 sm:space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2 text-[#996E2E]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D]">Payment Details</h4>
                            </div>
                            @if($order->isCod())
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 uppercase">
                                    Cash on Delivery
                                </span>
                            @else
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 uppercase">
                                    Full Online UPI
                                </span>
                            @endif
                        </div>
                        <div class="bg-[#FAF7F0] p-3.5 sm:p-4 rounded-xl border border-[#D4AF6A]/30 text-xs text-stone-700 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-stone-500">Subtotal:</span>
                                <span class="font-semibold">₹{{ number_format($order->subtotal) }}</span>
                            </div>
                            @if($order->coupon_discount > 0)
                                <div class="flex justify-between text-emerald-800 font-medium">
                                    <span>Discount ({{ $order->coupon_code }}):</span>
                                    <span>− ₹{{ number_format($order->coupon_discount) }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-stone-500">Royal Insured Shipping:</span>
                                <span class="font-semibold whitespace-nowrap shrink-0 {{ $order->shipping_fee == 0 ? 'text-emerald-600' : 'text-stone-800' }}">{{ $order->shipping_fee == 0 ? 'FREE' : '₹' . number_format($order->shipping_fee) }}</span>
                            </div>
                            @if($order->cod_extra_charge > 0)
                                <div class="flex items-center justify-between gap-2 text-[#996E2E] font-medium">
                                    <span>COD Handling Fee:</span>
                                    <span class="font-semibold whitespace-nowrap shrink-0">+ ₹{{ number_format($order->cod_extra_charge) }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between gap-2 text-sm font-bold text-[#4A2C1D] pt-2 border-t border-[#D4AF6A]/30">
                                <span>Total Order Amount:</span>
                                <span class="font-serif-royal text-base whitespace-nowrap shrink-0">₹{{ number_format($order->total_amount) }}</span>
                            </div>

                            @if($order->isCod())
                                <div class="p-2.5 rounded-lg bg-amber-50/80 border border-amber-200/80 space-y-1.5 text-xs mt-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-emerald-800 font-bold">1. Advance Paid ({{ $order->advance_percentage }}%):</span>
                                        <span class="font-bold text-emerald-800 font-serif-royal whitespace-nowrap shrink-0">₹{{ number_format($order->advance_amount) }}</span>
                                    </div>
                                    @if($order->isCancelled())
                                        <div class="flex items-center justify-between gap-2 text-stone-600">
                                            <span>2. Balance on Delivery:</span>
                                            <span class="font-bold font-serif-royal text-stone-500 whitespace-nowrap shrink-0">
                                                <span class="line-through">₹{{ number_format($order->balance_amount) }}</span>
                                                <span class="text-[10px] font-sans font-semibold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200 ml-1">Voided</span>
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-amber-200/60 text-[11px]">
                                            <span class="text-stone-600">Advance Refund Status:</span>
                                            <span class="font-bold px-2.5 py-0.5 rounded-full border whitespace-nowrap shrink-0 {{ $order->refund_status_badge_class }}">
                                                {{ $order->refund_status === 'Refunded' ? 'Refunded' : ($order->refund_status === 'Not Applicable' ? 'Not Applicable' : ($order->isRefundPending() ? 'Refund Pending' : $order->refund_status)) }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[#4A2C1D] font-bold">2. Balance Due on Delivery:</span>
                                            <span class="font-bold text-[#4A2C1D] font-serif-royal whitespace-nowrap shrink-0">₹{{ number_format($order->balance_amount) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-amber-200/60 text-[11px]">
                                            <span class="text-stone-600">Balance Collection Status:</span>
                                            <span class="font-bold px-2.5 py-0.5 rounded-full border whitespace-nowrap shrink-0 {{ $order->balance_status_badge_class }}">
                                                {{ $order->balance_status }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="pt-2 text-[11px] text-stone-500 border-t border-stone-200">
                                <span>Method: <strong>{{ $order->payment_method ?: ($order->isCod() ? 'Cash on Delivery (Advance Paid)' : 'Direct UPI / QR Code') }}</strong></span> • 
                                <span>Status: <strong class="text-stone-800">{{ $order->payment?->status ?: $order->status }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($order->isCod() && (float)$order->balance_amount > 0 && !$order->isCancelled() && !$order->isRejected() && !$order->isBalanceSettled())
                    @if($order->delivery_payment_screenshot)
                        <!-- UNDER REVIEW STATE: Proof submitted, awaiting admin verification (No QR, No upload form) -->
                        <div class="p-4 sm:p-6 bg-gradient-to-r from-amber-50/60 via-white to-amber-50/60 border-t border-amber-200">
                            <div class="max-w-2xl mx-auto p-4 sm:p-5 rounded-2xl bg-white border border-amber-300 shadow-2xs space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2.5 border-b border-amber-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-base shrink-0 shadow-xs">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-serif-royal font-bold text-sm sm:text-base text-[#4A2C1D]">Delivery Balance Proof Uploaded (Under Review)</h4>
                                            <p class="text-[11px] text-stone-500">Remaining Balance: <strong>₹{{ number_format($order->balance_amount) }}</strong> • Our team is currently verifying this proof.</p>
                                        </div>
                                    </div>
                                    <span class="self-start sm:self-auto px-3 py-1 bg-amber-100 text-amber-900 border border-amber-300 font-bold text-[11px] rounded-full">
                                        Verification Pending
                                    </span>
                                </div>
                                <div class="flex items-center gap-3.5 pt-1">
                                    <img src="{{ asset($order->delivery_payment_screenshot) }}" alt="Submitted Proof" class="w-16 h-16 rounded-xl object-contain border border-amber-200 bg-white p-1 shadow-2xs shrink-0">
                                    <div class="text-xs text-stone-600 space-y-0.5">
                                        @if($order->delivery_transaction_reference)
                                            <p>UTR: <strong class="font-mono text-stone-900">{{ $order->delivery_transaction_reference }}</strong></p>
                                        @endif
                                        <p class="text-[11px] text-stone-500">Submitted: {{ $order->delivery_paid_at?->format('d M, Y \a\t h:i A') ?? 'Recently' }}</p>
                                        <p class="text-[11px] text-stone-400">Our accounts team will update your status shortly.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- UNPAID / PENDING: QR Code and Proof Upload Form -->
                        @php
                            $storeSettings = $storeSettings ?? \App\Models\StoreSetting::getAll();
                            $deliveryQr = !empty($storeSettings['cod_delivery_qr_image']) ? $storeSettings['cod_delivery_qr_image'] : ($storeSettings['qr_code_image'] ?? '/images/qr/sample-upi-qr.svg');
                            $deliveryUpi = !empty($storeSettings['cod_delivery_upi_id']) ? $storeSettings['cod_delivery_upi_id'] : ($storeSettings['upi_id'] ?? 'raykajewellery@icici');
                            $deliveryPayee = !empty($storeSettings['cod_delivery_payee_name']) ? $storeSettings['cod_delivery_payee_name'] : ($storeSettings['upi_payee_name'] ?? 'Rayka Imitation Jewellery');
                        @endphp
                        <!-- 4.5. AFTER-DELIVERY FINAL BALANCE PAYMENT CARD (COD) -->
                        <div class="p-4 sm:p-8 bg-gradient-to-br from-[#FAF7F0] via-white to-amber-50/40 border-t border-[#D4AF6A]/30"
                             x-data="{
                                deliveryProofPreview: null,
                                copiedDeliveryUpi: false,
                                previewDeliveryFile(e) {
                                    let file = e.target.files[0];
                                    if (file) {
                                        if (file.size > 10 * 1024 * 1024) {
                                            alert('File exceeds 10MB limit.');
                                            e.target.value = '';
                                            this.deliveryProofPreview = null;
                                            return;
                                        }
                                        let reader = new FileReader();
                                        reader.onload = (re) => { this.deliveryProofPreview = re.target.result; };
                                        reader.readAsDataURL(file);
                                    }
                                }
                             }">
                            <div class="max-w-2xl mx-auto rounded-2xl border-2 border-[#D4AF6A] bg-white p-4 sm:p-6 shadow-sm space-y-4">
                                <div class="border-b border-[#D4AF6A]/30 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <h4 class="font-serif-royal font-bold text-base text-[#4A2C1D] flex items-center gap-2">
                                            <svg class="w-5 h-5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                            <span>Pay Remaining Balance Online (After Delivery)</span>
                                        </h4>
                                        <p class="text-[11px] text-stone-500 mt-0.5">
                                            Pay courier agent in cash or scan the dedicated delivery QR code to pay online.
                                        </p>
                                    </div>
                                    <span class="self-start sm:self-auto text-xs font-bold text-emerald-800 bg-emerald-100 border border-emerald-300 px-3 py-1 rounded-full">
                                        ₹{{ number_format($order->balance_amount) }} Due
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                                    <!-- QR Display Box -->
                                    <div class="text-center p-3 bg-[#FAF7F0]/60 rounded-xl border border-[#D4AF6A]/40">
                                        <img src="{{ asset($deliveryQr) }}" alt="Delivery Payment QR" class="w-36 h-36 object-contain mx-auto border rounded-lg bg-white p-1 shadow-2xs">
                                        <div class="mt-2.5 inline-flex items-center space-x-2 bg-white px-3 py-1 rounded-full border border-[#D4AF6A]/40 text-xs shadow-2xs">
                                            <span class="font-mono text-stone-700 select-all">{{ $deliveryUpi }}</span>
                                            <button type="button" @click="navigator.clipboard.writeText('{{ $deliveryUpi }}'); copiedDeliveryUpi = true; setTimeout(() => copiedDeliveryUpi = false, 2000)" class="font-bold text-[#996E2E] hover:underline cursor-pointer">
                                                <span x-show="!copiedDeliveryUpi">Copy</span>
                                                <span x-show="copiedDeliveryUpi" class="text-emerald-700 font-bold inline-flex items-center gap-1" style="display:none;">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Copied
                                                </span>
                                            </button>
                                        </div>
                                        <p class="text-[10px] text-stone-400 mt-1">Payee: {{ $deliveryPayee }}</p>
                                    </div>

                                    <!-- Upload Form -->
                                    <div>
                                        <form action="{{ route('order.track.delivery_payment', $order->order_number) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                                            @csrf
                                            <div>
                                                <label class="block font-semibold text-stone-700 mb-1">
                                                    Upload Balance Payment Screenshot *
                                                </label>
                                                <div class="flex flex-col items-center justify-center border-2 border-dashed border-[#D4AF6A] bg-stone-50/50 hover:bg-[#FAF7F0] rounded-xl p-3 text-center cursor-pointer relative transition">
                                                    <input type="file" 
                                                           name="delivery_payment_screenshot" 
                                                           accept="image/*,.heic,.heif" 
                                                           required
                                                           @change="previewDeliveryFile($event)"
                                                           class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">

                                                    <div x-show="!deliveryProofPreview" class="pointer-events-none space-y-1">
                                                        <svg class="w-6 h-6 text-[#996E2E] mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        <p class="text-[11px] font-bold text-stone-700">Attach screenshot proof</p>
                                                        <p class="text-[9px] text-stone-400">JPG, PNG, WebP up to 10MB</p>
                                                    </div>

                                                    <div x-show="deliveryProofPreview" class="pointer-events-none space-y-1" style="display: none;">
                                                        <img :src="deliveryProofPreview" class="max-h-20 rounded border mx-auto">
                                                        <p class="text-[10px] text-emerald-700 font-bold">Screenshot selected (Click to replace)</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block font-semibold text-stone-700 mb-1">UPI Ref / UTR (Optional)</label>
                                                <input type="text" name="delivery_transaction_reference" placeholder="e.g. 428901839210" class="w-full border rounded-lg p-2 text-xs font-mono">
                                            </div>

                                            <button type="submit" class="w-full py-2.5 bg-[#4A2C1D] hover:bg-[#2E180E] text-[#E7C77B] rounded-xl font-bold uppercase tracking-wider text-[11px] transition shadow-xs flex items-center justify-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-[#E7C77B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                <span>Submit Proof (₹{{ number_format($order->balance_amount) }})</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- 5. Royal Concierge Assistance Bar -->
                <div class="p-4 sm:p-8 bg-gradient-to-r from-[#4A2C1D] to-[#2E180E] text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h4 class="font-serif-royal text-base font-bold text-[#E7C77B]">Need Assistance with This Consignment?</h4>
                        <p class="text-xs text-stone-300">Our Royal Customer Care team is standing by to help with any delivery inquiries.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        @if(!empty($storeSettings['clean_whatsapp']))
                        <a href="https://wa.me/{{ $storeSettings['clean_whatsapp'] }}?text=Hello%20Rayka%20Jewellery,%20I%20need%20assistance%20regarding%20my%20Order%20%23{{ $order->order_number }}" 
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center space-x-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full font-bold text-xs shadow-md transition">
                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            <span>WhatsApp Concierge</span>
                        </a>
                        @endif

                        <a href="{{ route('contact') }}" class="inline-flex items-center space-x-1.5 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-full font-bold text-xs border border-white/20 transition">
                            <span>Contact Support</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

