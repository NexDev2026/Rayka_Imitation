@extends('layouts.storefront')

@section('title', 'Trending Jewellery Masterpieces — Rayka Imitation Jewellery')

@section('content')
<?php /** @var \Illuminate\Pagination\LengthAwarePaginator $products */ ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

    <div class="text-center max-w-xl mx-auto">
        <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Most Desired</span>
        <h1 class="font-serif-royal text-3xl font-bold text-[#4A2C1D] mt-1">
            Trending Masterpieces
        </h1>
        <div class="rangoli-divider">
            <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
        </div>
        <p class="text-xs text-stone-600">
            Hand-curated royal creations capturing the heart of festive celebrations and wedding seasons across India.
        </p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @forelse($products as $prod)
            <x-product-card :product="$prod" />
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-[#D4AF6A]/30 p-8 shadow-xs">
                <p class="text-stone-600 font-serif-royal text-lg font-bold">No Trending Pieces Found</p>
                <p class="text-stone-500 text-xs mt-1">Our master artisans are curating new royal pieces. Please explore our other royal categories.</p>
                <a href="{{ route('home') }}" class="inline-block mt-4 px-6 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#2E180E] transition">
                    Explore Collections
                </a>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection

