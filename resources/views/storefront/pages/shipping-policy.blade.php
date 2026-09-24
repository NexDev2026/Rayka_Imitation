@extends('layouts.storefront')

@section('title', 'Shipping Policy — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="text-center space-y-2">
        <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Dispatch & Courier Guidelines</span>
        <h1 class="font-serif-royal text-3xl sm:text-4xl font-bold text-[#4A2C1D]">Shipping Policy</h1>
        <div class="rangoli-divider"><img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli"></div>
    </div>

    <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-8 sm:p-12 shadow-xs space-y-5 text-stone-700 text-xs sm:text-sm leading-relaxed">
        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">1. Free Express Shipping Across India</h3>
        <p>All orders with subtotal above <strong>₹999</strong> automatically qualify for FREE Express Shipping to over 26,000 pincodes across India. For orders below ₹999, a nominal flat express shipping fee of ₹99 is applied.</p>

        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">2. Payment Verification & Dispatch Timeline</h3>
        <p>Once your static UPI QR code payment screenshot is approved by our admin team, your jewellery undergoes dual inspection, velvet case packing, and is handed over to our premier courier partners (Blue Dart, Delhivery, DTDC, or Speed Post) within 24–48 business hours.</p>

        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">3. Real-Time Tracking Updates</h3>
        <p>A unique airway bill (AWB) and live tracking link will be updated in your <strong>My Orders</strong> dashboard. Delivery typically takes 3–5 business days for metro cities and 4–7 business days for regional locations.</p>
    </div>
</div>
@endsection

