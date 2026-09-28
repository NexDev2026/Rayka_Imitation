@extends('layouts.storefront')

@section('title', '7-Day Delivery & Authentic Quality Warranty Policy — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="text-center space-y-2">
        <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Patron Protection</span>
        <h1 class="font-serif-royal text-3xl sm:text-4xl font-bold text-[#4A2C1D]">7-Day Delivery & Authentic Warranty Policy</h1>
        <div class="rangoli-divider"><img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli"></div>
    </div>

    <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-8 sm:p-12 shadow-xs space-y-5 text-stone-700 text-xs sm:text-sm leading-relaxed">
        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">1. 7-Day Express Delivery Guarantee Across India</h3>
        <p>We are committed to prompt fulfillment. Every confirmed order is meticulously packed in tamper-proof heritage packaging and dispatched through our premier express courier partners within 24 to 48 hours, reaching your doorstep across India within 7 days.</p>

        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">2. Authentic Quality Warranty & Transit Protection</h3>
        <p>Every Rayka jewellery piece is backed by our Authentic Quality Warranty. In the rare event of transit damage or missing stone upon delivery, simply message our WhatsApp concierge@if(!empty($storeSettings['store_whatsapp'])) at <strong>{{ $storeSettings['store_whatsapp'] }}</strong>@endif or email <strong>{{ $storeSettings['store_email'] ?? 'care@raykajewellery.com' }}</strong> with your Order Number and a 30-second continuous parcel unboxing video within 7 days of package arrival. Our concierge team will provide priority resolution promptly.</p>

        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">3. Hygiene & Care Standards</h3>
        <p>Due to personal hygiene and specialized jewellery craftsmanship, items showing signs of physical wear, chemical/perfume exposure, alteration, or missing authentic packaging are not eligible for warranty claims.</p>
    </div>
</div>
@endsection
