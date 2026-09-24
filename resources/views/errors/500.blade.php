@extends('layouts.storefront')

@section('title', 'Temporary Atelier Service — Rayka Imitation Jewellery')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 bg-[#FAF7F0]">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="relative inline-block">
            <span class="font-serif-royal text-7xl sm:text-8xl font-black text-[#D4AF6A]/40 tracking-widest block select-none">500</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <img src="{{ asset('images/rayka-logo.png') }}" alt="Rayka Crest" class="w-16 h-16 object-contain drop-shadow">
            </div>
        </div>

        <div class="space-y-2">
            <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Atelier Concierge</span>
            <h1 class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#4A2C1D]">
                Brief Concierge Maintenance
            </h1>
            <p class="text-xs sm:text-sm text-stone-600 leading-relaxed max-w-sm mx-auto">
                Our royal server is momentarily curating pieces. Please refresh the page or connect directly with our WhatsApp concierge.
            </p>
        </div>

        <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center space-x-2 bg-[#4A2C1D] text-[#E7C77B] px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#3A1C0E] transition shadow-md hover:scale-105 active:scale-95 cursor-pointer">
                <span>Reload Showroom</span>
            </a>
            <a href="https://wa.me/918128498531" target="_blank" rel="noopener noreferrer" 
               class="inline-flex items-center space-x-2 bg-[#25D366] text-white px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#20ba5a] transition shadow-md hover:scale-105 active:scale-95 cursor-pointer">
                <span>WhatsApp Concierge</span>
            </a>
        </div>
    </div>
</div>
@endsection
