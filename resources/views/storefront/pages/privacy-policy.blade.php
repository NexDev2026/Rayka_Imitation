@extends('layouts.storefront')

@section('title', 'Privacy Policy — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="text-center space-y-2">
        <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Data Protection</span>
        <h1 class="font-serif-royal text-3xl sm:text-4xl font-bold text-[#4A2C1D]">Privacy Policy</h1>
        <div class="rangoli-divider"><img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli"></div>
    </div>

    <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-8 sm:p-12 shadow-xs space-y-5 text-stone-700 text-xs sm:text-sm leading-relaxed">
        <p>At Rayka Imitation Jewellery, we honor and respect your privacy. This policy details how we treat customer contact details, delivery addresses, and payment verification proofs.</p>
        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">Information Collected</h3>
        <p>We collect essential shipping credentials (name, mobile number, email, address, pincode) strictly to process your jewelry order and facilitate delivery logistics.</p>
        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">Payment Proof Security</h3>
        <p>Customer payment screenshots uploaded as UPI proof are securely stored in protected directories, accessible solely to authorized administration personnel for manual verification, and are never shared with any 3rd party advertising brokers.</p>
    </div>
</div>
@endsection

