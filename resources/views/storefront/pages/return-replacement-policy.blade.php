@extends('layouts.storefront')

@section('title', 'Return & Replacement Policy — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="text-center space-y-2">
        <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Patron Protection</span>
        <h1 class="font-serif-royal text-3xl sm:text-4xl font-bold text-[#4A2C1D]">Return & Replacement Policy</h1>
        <div class="rangoli-divider"><img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli"></div>
    </div>

    <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-8 sm:p-12 shadow-xs space-y-5 text-stone-700 text-xs sm:text-sm leading-relaxed">
        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">1. 7-Day Hassle-Free Replacement Guarantee</h3>
        <p>Because every jewellery creation is delicate and precious, we offer a dedicated <strong>7-Day Free Replacement Guarantee</strong> in the unlikely event of physical damage during transit, missing stone, or manufacturing defect.</p>

        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">2. How to Request a Replacement</h3>
        <p>Simply message our WhatsApp concierge at <strong>+91 81284 98531</strong> or email <strong>care@raykajewellery.com</strong> with your Order Number and a short unboxing video or photo within 7 days of package delivery. We will arrange a replacement piece or free re-plating promptly.</p>

        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">3. Hygiene & Wear Conditions</h3>
        <p>Due to hygiene and personalized craftsmanship, items that show visible prolonged personal wear, alteration, perfume damage, or missing authentic packaging cannot be returned.</p>
    </div>
</div>
@endsection

