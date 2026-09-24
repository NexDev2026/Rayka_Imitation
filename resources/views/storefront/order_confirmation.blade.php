@extends('layouts.storefront')

@section('title', "Order Confirmation — #{$order->order_number}")

@section('content')
<div class="max-w-4xl mx-auto px-2.5 sm:px-6 lg:px-8 py-6 sm:py-12">

    <!-- Success Greeting Card -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border-2 border-[#D4AF6A] p-4 sm:p-10 shadow-lg text-center relative overflow-hidden space-y-5 sm:space-y-6">
        
        <!-- Rangoli Accent -->
        <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" alt="Motif" class="w-32 h-32 opacity-10 absolute -top-8 -right-8 pointer-events-none">
        
        @php
            $isRejected = in_array($order->status, ['Rejected', 'Cancelled']);
            $step = $order->step_index; // 0 for Rejected, 1 for Pending, 2 for Confirmed, 3 for Processing, 4 for Shipped, 5 for Delivered
        @endphp

        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full {{ $isRejected ? 'bg-rose-50 border-2 border-rose-300 text-rose-700' : 'bg-[#FAF7F0] border-2 border-[#D4AF6A] text-[#996E2E]' }} flex items-center justify-center mx-auto shadow-sm">
            @if($isRejected)
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            @else
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-[#996E2E]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/>
                </svg>
            @endif
        </div>

        <div class="space-y-2">
            <span class="{{ $order->status_badge_class }} font-bold text-xs uppercase tracking-wider px-3.5 py-1 rounded-full inline-block border">
                {{ $order->status }}
            </span>
            @if($isRejected)
                <h1 class="font-serif-royal text-xl sm:text-3xl lg:text-4xl font-bold text-rose-900 leading-tight">
                    Payment Verification Unsuccessful
                </h1>
                <p class="text-xs sm:text-sm text-stone-600 max-w-lg mx-auto">
                    We could not verify the UPI payment proof for order <strong>#{{ $order->order_number }}</strong>.
                </p>
                @if($order->payment?->admin_note)
                    <div class="max-w-lg mx-auto p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                        <strong>Reason from Atelier:</strong> {{ $order->payment->admin_note }}
                    </div>
                @endif
            @elseif($order->isConfirmed())
                <h1 class="font-serif-royal text-xl sm:text-3xl lg:text-4xl font-bold text-[#4A2C1D] leading-tight">
                    Payment Verified & Order Confirmed!
                </h1>
                <p class="text-xs sm:text-sm text-stone-600 max-w-lg mx-auto">
                    Your order <strong>#{{ $order->order_number }}</strong> is confirmed and being prepared in our Rajasthan atelier for dispatch.
                </p>
            @else
                <h1 class="font-serif-royal text-xl sm:text-3xl lg:text-4xl font-bold text-[#4A2C1D] leading-tight">
                    Thank You for Your Royal Patronage!
                </h1>
                <p class="text-xs sm:text-sm text-stone-600 max-w-lg mx-auto">
                    Your order <strong>#{{ $order->order_number }}</strong> has been registered. Our administration team is reviewing your UPI payment screenshot proof.
                </p>
            @endif
        </div>

        <!-- Workflow Status Tracker -->
        <div class="py-5 sm:py-6 border-y border-[#D4AF6A]/30">
            <p class="text-xs font-bold uppercase tracking-wider text-[#996E2E] mb-3 sm:mb-4">Order Progress Status</p>
            @if($isRejected)
                <div class="p-3.5 sm:p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm max-w-xl mx-auto text-left">
                    <div class="flex items-center space-x-3">
                        <span class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-rose-600 text-white flex items-center justify-center font-bold shrink-0">✕</span>
                        <div>
                            <p class="font-bold text-rose-900">Payment Verification Failed</p>
                            <p class="text-xs text-rose-700 mt-0.5">Please share correct payment screenshot or UTR number with our concierge team on WhatsApp.</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 text-xs">
                    <div class="p-2.5 sm:p-3 rounded-xl {{ $step >= 1 ? ($step > 1 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-amber-50 border-amber-300 text-amber-900 font-bold') : 'bg-stone-50 border-stone-200 text-stone-400' }} border flex flex-col items-center justify-center text-center space-y-1">
                        @if($step > 1)
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @else
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                        <span class="text-[11px] sm:text-xs">1. Verification</span>
                    </div>
                    <div class="p-2.5 sm:p-3 rounded-xl {{ $step >= 2 ? ($step > 2 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-blue-50 border-blue-300 text-blue-900 font-bold animate-pulse') : 'bg-stone-50 border-stone-200 text-stone-400' }} border flex flex-col items-center justify-center text-center space-y-1">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span class="text-[11px] sm:text-xs">2. Pack</span>
                    </div>
                    <div class="p-2.5 sm:p-3 rounded-xl {{ $step >= 4 ? ($step > 4 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-indigo-50 border-indigo-300 text-indigo-900 font-bold animate-pulse') : 'bg-stone-50 border-stone-200 text-stone-400' }} border flex flex-col items-center justify-center text-center space-y-1">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                        <span class="text-[11px] sm:text-xs">3. Shipped</span>
                    </div>
                    <div class="p-2.5 sm:p-3 rounded-xl {{ $step >= 5 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-stone-50 border-stone-200 text-stone-400' }} border flex flex-col items-center justify-center text-center space-y-1">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V4m0 4l-4-4m4 4l4-4M3 8h18v13a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                        <span class="text-[11px] sm:text-xs">4. Delivered</span>
                    </div>
                </div>
                <p class="text-[11px] text-stone-500 mt-3">
                    Upon confirmation by our staff, your official <strong>Downloadable PDF Invoice</strong> will be unlocked in your account area.
                </p>
            @endif
        </div>

        <!-- Order Summary & Address Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 text-left text-xs bg-[#FAF7F0] p-4 sm:p-6 rounded-xl sm:rounded-2xl border border-[#D4AF6A]/30 w-full">
            <div class="space-y-1 min-w-0">
                <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D] mb-2">Delivery Address:</h4>
                <p class="font-semibold text-stone-800">{{ $order->address?->name }}</p>
                <p class="text-stone-600 leading-relaxed break-words">{{ $order->address?->formatted_address }}</p>
                <p class="text-stone-600 mt-1">Mobile: <strong class="text-stone-800">{{ $order->address?->mobile }}</strong></p>
                <p class="text-stone-600 break-all">Email: {{ $order->address?->email }}</p>
            </div>

            <div class="space-y-1 pt-3 sm:pt-0 border-t sm:border-t-0 border-[#D4AF6A]/20">
                <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D] mb-2">Payment Breakdown:</h4>
                <div class="space-y-1.5 text-stone-600">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span class="font-semibold">₹{{ number_format($order->subtotal) }}</span>
                    </div>
                    @if($order->coupon_discount > 0)
                        <div class="flex justify-between text-emerald-800">
                            <span>Discount ({{ $order->coupon_code }}):</span>
                            <span>− ₹{{ number_format($order->coupon_discount) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span>Shipping:</span>
                        <span>{{ $order->shipping_fee == 0 ? 'FREE' : '₹' . number_format($order->shipping_fee) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-sm text-[#4A2C1D] pt-2 border-t border-[#D4AF6A]/30">
                        <span>Total Paid:</span>
                        <span class="font-serif-royal text-base">₹{{ number_format($order->total_amount) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Links -->
        <div class="pt-3 flex flex-col sm:flex-row items-center justify-center gap-3 w-full">
            @if($isRejected)
                @php
                    $waNumber = preg_replace('/[^0-9]/', '', \App\Models\StoreSetting::get('whatsapp_number', '919876543210'));
                @endphp
                <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode("Hello Rayka Concierge, my payment proof for order #{$order->order_number} needs verification. Here is my transaction detail:") }}" target="_blank" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider transition shadow-md cursor-pointer">
                    <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Contact Concierge on WhatsApp</span>
                </a>
                <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-[#4A2C1D] text-[#E7C77B] px-6 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider hover:bg-[#2E180E] transition shadow-md cursor-pointer">
                    <span>View Order Details</span>
                </a>
            @else
                <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-[#4A2C1D] text-[#E7C77B] px-6 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider hover:bg-[#2E180E] transition shadow-md cursor-pointer">
                    <svg class="w-4 h-4 text-[#E7C77B] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Track Live Consignment</span>
                    <span>→</span>
                </a>
            @endif
            <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-white border border-[#D4AF6A] text-[#4A2C1D] px-6 py-3.5 rounded-full font-bold text-xs uppercase tracking-wider hover:bg-[#FAF7F0] transition cursor-pointer">
                <span>Continue Shopping</span>
            </a>
        </div>

    </div>

</div>
@endsection

