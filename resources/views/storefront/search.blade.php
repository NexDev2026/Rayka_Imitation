@extends('layouts.storefront')

@section('title', "Search Results for '{$q}' — Rayka Imitation Jewellery")

@section('content')
@php /** @var \Illuminate\Pagination\LengthAwarePaginator $products */ @endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <div class="border-b border-[#D4AF6A]/30 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#4A2C1D]">
                Search Results for <span class="text-[#996E2E]">"{{ $q }}"</span>
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Found {{ $products->total() }} matching jewels.
            </p>
        </div>

        <form action="{{ route('search') }}" method="GET" class="relative w-full sm:w-72">
            <input type="text" name="q" value="{{ $q }}" placeholder="Search again..." class="w-full border border-[#D4AF6A]/50 rounded-full pl-9 pr-3 py-1.5 text-xs bg-white focus:outline-hidden focus:border-[#D4AF6A]">
            <button type="submit" class="absolute left-3 top-2 text-[#996E2E]">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            </button>
        </form>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
        @forelse($products as $prod)
            <x-product-card :product="$prod" />
        @empty
            <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-[#D4AF6A]/30 p-8">
                <div class="w-16 h-16 rounded-full bg-[#FAF7F0] border border-[#D4AF6A] flex items-center justify-center mx-auto mb-3 text-[#996E2E]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                </div>
                <h3 class="font-serif-royal text-lg font-bold text-[#4A2C1D]">No Matching Jewels Found</h3>
                <p class="text-xs text-stone-500 mt-1">Try searching for keywords like "Kundan", "Chain", "Kada", "Ring", or "Bangles".</p>
                <a href="{{ route('home') }}" class="inline-block mt-4 text-xs font-bold text-[#996E2E] underline uppercase">
                    Back to Home
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $products->links() }}
    </div>

</div>
@endsection

