@extends('layouts.storefront')

@section('title', "{$navGroup->name} Collection — Rayka Imitation Jewellery")

@section('content')
<?php /** @var \Illuminate\Pagination\LengthAwarePaginator $products */ ?>
<div>

    <!-- Header Banner -->
    <div class="relative min-h-[220px] sm:min-h-[280px] bg-[#2E180E] flex items-center justify-center overflow-hidden border-b-2 border-[#D4AF6A]">
        <img src="{{ asset('images/banners/banner-2.svg') }}" alt="{{ $navGroup->name }}" class="absolute inset-0 w-full h-full object-cover opacity-30">
        
        <div class="relative z-10 text-center px-4 max-w-3xl mx-auto py-10">
            <span class="text-xs uppercase font-bold tracking-[0.3em] text-[#E7C77B]">
                Exclusive Curations
            </span>
            <h1 class="font-serif-royal text-3xl sm:text-5xl font-bold text-[#FAF7F0] mt-2 tracking-wide drop-shadow-md">
                {{ $navGroup->name }} Collection
            </h1>
            <p class="text-xs sm:text-sm text-stone-200 mt-2 font-light max-w-xl mx-auto leading-relaxed">
                Handcrafted treasures for {{ $navGroup->name }}, cast with high-density micron plating and royal Rajputana motifs.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
        
        <!-- Category Tiles Grid under this Nav Group -->
        <div>
            <h3 class="font-serif-royal text-xl font-bold text-[#4A2C1D] mb-6 text-center">
                Explore Categories in {{ $navGroup->name }}
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($categories as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" class="group royal-card p-4 rounded-xl flex flex-col items-center text-center bg-white border border-[#D4AF6A]/40 hover:border-[#D4AF6A]">
                        <div class="w-20 h-20 rounded-full overflow-hidden bg-[#FAF7F0] p-1 border border-[#D4AF6A]/50 mb-3 group-hover:scale-105 transition">
                            <img src="{{ $cat->image ?: asset('images/categories/chains.svg') }}" alt="{{ $cat->name }}" class="w-full h-full object-cover rounded-full">
                        </div>
                        <span class="font-serif-royal text-xs sm:text-sm font-semibold text-[#4A2C1D] group-hover:text-[#996E2E] transition">
                            {{ $cat->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Curated Products Grid -->
        <div>
            <div class="flex items-center justify-between pb-4 border-b border-[#D4AF6A]/30 mb-8">
                <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D]">
                    Featured in {{ $navGroup->name }}
                </h3>
                <span class="text-xs text-stone-500 font-medium">Showing {{ $products->total() }} pieces</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @forelse($products as $prod)
                    <x-product-card :product="$prod" />
                @empty
                    <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-[#D4AF6A]/30 p-8 shadow-xs">
                        <p class="text-stone-600 font-serif-royal text-lg font-bold">No Products Found in {{ $navGroup->name }}</p>
                        <p class="text-stone-500 text-xs mt-1">Our artisans are adding new designs to this royal collection soon.</p>
                        <a href="{{ route('home') }}" class="inline-block mt-4 px-6 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#2E180E] transition">
                            Explore Other Collections
                        </a>
                    </div>
                @endforelse
            </div>

            @if($products->hasPages())
                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

