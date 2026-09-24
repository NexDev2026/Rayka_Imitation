@extends('layouts.storefront')

@section('title', 'Terms & Conditions — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="text-center space-y-2">
        <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Legal Terms</span>
        <h1 class="font-serif-royal text-3xl sm:text-4xl font-bold text-[#4A2C1D]">Terms & Conditions</h1>
        <div class="rangoli-divider"><img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli"></div>
    </div>

    <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-8 sm:p-12 shadow-xs space-y-5 text-stone-700 text-xs sm:text-sm leading-relaxed">
        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">1. Product Representations</h3>
        <p>Rayka specializes in artificial, imitation, and 1 gram micro gold plated jewellery crafted in copper/brass alloys with synthetic zirconia and uncut polki glass. Our creations do not contain solid gold unless explicitly certified.</p>

        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">2. Payment Verification & Order Confirmation</h3>
        <p>Orders are accepted subject to manual review of the static QR code payment screenshot by our admin team. In the case of invalid or unverified transactions, the order may be cancelled or rejected.</p>

        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">3. Jurisdiction</h3>
        <p>All transactions, disputes, and agreements are subject to the exclusive jurisdiction of the competent courts in Jaipur, Rajasthan, India.</p>
    </div>
</div>
@endsection

