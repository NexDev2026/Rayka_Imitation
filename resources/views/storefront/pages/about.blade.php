@extends('layouts.storefront')

@section('title', 'About Us — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div class="text-center space-y-2">
        <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">The Heritage Legacy</span>
        <h1 class="font-serif-royal text-3xl sm:text-4xl font-bold text-[#4A2C1D]">About Rayka Imitation Jewellery</h1>
        <div class="rangoli-divider"><img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli"></div>
    </div>

    <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-8 sm:p-12 shadow-xs space-y-6 text-stone-700 text-xs sm:text-sm leading-relaxed">
        <p>
            Established in the heart of the gemstone capital of the world — Jaipur, Rajasthan — <strong>Rayka Imitation Jewellery</strong> was conceived with a singular sovereign vision: to bring the uncompromised grandeur, regal grace, and timeless beauty of royal Indian heirloom jewellery into the modern patron's collection.
        </p>
        <p>
            Traditional fine jewellery carries astronomical prices and security anxieties. Rayka bridges this gap through master craftsmanship. Every Kundan choker, peacock jhumka, and 1 gram micro gold chain is hand-assembled by artisans with decades of experience preserving Rajputana, Mughal, and Temple goldsmith traditions.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-[#D4AF6A]/30 text-center">
            <div class="p-4 bg-[#FAF7F0] rounded-xl border border-[#D4AF6A]/30 flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-white border border-[#D4AF6A]/40 flex items-center justify-center text-[#996E2E] shadow-2xs mb-1">
                    <svg class="w-5 h-5 text-[#996E2E]" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                </div>
                <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D] mt-1">Royal Heritage</h4>
                <p class="text-[11px] text-stone-500 mt-0.5">Inspired by Rajputana palace collections</p>
            </div>
            <div class="p-4 bg-[#FAF7F0] rounded-xl border border-[#D4AF6A]/30 flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-white border border-[#D4AF6A]/40 flex items-center justify-center text-[#996E2E] shadow-2xs mb-1">
                    <svg class="w-5 h-5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D] mt-1">1 Gram Micro Gold</h4>
                <p class="text-[11px] text-stone-500 mt-0.5">Triple micron plating with anti-tarnish coating</p>
            </div>
            <div class="p-4 bg-[#FAF7F0] rounded-xl border border-[#D4AF6A]/30 flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl bg-white border border-[#D4AF6A]/40 flex items-center justify-center text-[#996E2E] shadow-2xs mb-1">
                    <svg class="w-5 h-5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D] mt-1">100% Guaranteed</h4>
                <p class="text-[11px] text-stone-500 mt-0.5">Easy 7-day transit replacement guarantee</p>
            </div>
        </div>

        <div class="pt-6 border-t border-[#D4AF6A]/30 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
            <div>
                <strong class="block text-[#4A2C1D] font-serif-royal text-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Visit Our Boutique Store</span>
                </strong>
                <p class="text-stone-600 mt-0.5">Shop No. 29, Shreeji Bapa Complex, Near Rita Nagar Bus Stand, Vastral Road, Amraiwadi, Ahmedabad, Gujarat 380026</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="https://share.google/vaohJv28SH29hBV8j" target="_blank" rel="noopener noreferrer" class="px-3.5 py-1.5 bg-[#FAF7F0] border border-[#D4AF6A] rounded-lg text-[#996E2E] font-semibold hover:bg-white transition inline-flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    <span>Google Maps</span>
                </a>
                <a href="https://www.instagram.com/rayka_imitation_amdavad/?hl=en" target="_blank" rel="noopener noreferrer" class="px-3.5 py-1.5 bg-gradient-to-r from-purple-600 via-pink-600 to-amber-600 text-white rounded-lg font-semibold hover:opacity-90 transition inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    <span>@rayka_imitation_amdavad</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

