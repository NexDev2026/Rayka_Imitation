@extends('layouts.storefront')

@section('title', 'My Royal Wishlist — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="text-center max-w-xl mx-auto mb-10">
        <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Saved Treasures</span>
        <h1 class="font-serif-royal text-3xl font-bold text-[#4A2C1D] mt-1">
            My Royal Wishlist
        </h1>
        <div class="rangoli-divider">
            <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
        </div>
        <p class="text-xs text-stone-600">
            Items saved here remain preserved across your visits. Log in to access your wishlist from any device.
        </p>
    </div>

    @if($wishlists->isEmpty())
        <div class="max-w-md mx-auto text-center py-16 bg-white rounded-2xl border border-[#D4AF6A]/30 p-8 shadow-xs">
            <div class="w-16 h-16 rounded-full bg-[#FAF7F0] border border-[#D4AF6A] flex items-center justify-center mx-auto mb-4 text-2xl text-[#D4AF6A]">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </div>
            <h3 class="font-serif-royal text-lg font-bold text-[#4A2C1D]">Your Wishlist is Empty</h3>
            <p class="text-xs text-stone-500 mt-1 mb-6">
                Explore our handcrafted Indian jewellery collections and save your favorite heirloom pieces.
            </p>
            <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 bg-[#4A2C1D] text-[#E7C77B] px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#2E180E] transition">
                <span>Discover Creations</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($wishlists as $item)
                @if($item->product)
                    <div class="royal-card rounded-xl overflow-hidden flex flex-col h-full bg-white relative" x-data>
                        <!-- Remove button -->
                        <form action="{{ route('wishlist.remove', $item->id) }}" method="POST" class="absolute top-2.5 right-2.5 z-20">
                            @csrf
                            <button type="submit" class="w-8 h-8 rounded-full bg-white/90 border border-stone-200 text-stone-400 hover:text-rose-600 hover:border-rose-300 flex items-center justify-center transition shadow-2xs" title="Remove">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </form>

                        <!-- Product Image -->
                        <a href="{{ route('product.show', $item->product->slug) }}" class="block aspect-square bg-[#FAF7F0] border-b border-[#D4AF6A]/20 overflow-hidden">
                            <img src="{{ $item->product->effective_primary_image }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover hover:scale-105 transition duration-300">
                        </a>

                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <p class="text-[10px] uppercase font-bold tracking-wider text-[#996E2E] mb-1">
                                    {{ $item->product->category?->name }}
                                </p>
                                <a href="{{ route('product.show', $item->product->slug) }}" class="font-serif-royal text-sm font-semibold text-[#4A2C1D] hover:text-[#996E2E] line-clamp-2">
                                    {{ $item->product->name }}
                                </a>
                            </div>

                            <div class="pt-3 border-t border-[#D4AF6A]/20 mt-3 space-y-3">
                                <div class="flex items-baseline space-x-2">
                                    <span class="text-base font-bold text-[#4A2C1D]">₹{{ number_format($item->product->price) }}</span>
                                    @if($item->product->mrp > $item->product->price)
                                        <span class="text-xs text-stone-400 line-through">₹{{ number_format($item->product->mrp) }}</span>
                                    @endif
                                </div>

                                <!-- Interactive Move to Bag / - [qty] + Controller -->
                                <div x-show="$store.rayka.getCartQty({{ $item->product->id }}) <= 0">
                                    <button type="button" 
                                            @click.prevent.stop="$store.rayka.addToCart({{ $item->product->id }}, 1)" 
                                            class="w-full mt-2 py-2 rounded-lg bg-[#FAF7F0] hover:bg-[#4A2C1D] text-[#4A2C1D] hover:text-[#E7C77B] border border-[#D4AF6A] text-xs font-bold transition flex items-center justify-center space-x-1 uppercase tracking-wider shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        <span>Add To Bag</span>
                                    </button>
                                </div>

                                <div x-show="$store.rayka.getCartQty({{ $item->product->id }}) > 0"
                                     class="w-full mt-2 flex items-center justify-between rounded-lg bg-[#4A2C1D] border border-[#D4AF6A] text-[#E7C77B] overflow-hidden shadow-xs text-sm font-bold"
                                     @click.prevent.stop>
                                    <button type="button" 
                                            @click.prevent.stop="$store.rayka.changeQty({{ $item->product->id }}, -1)"
                                            class="px-3 py-1.5 hover:bg-[#2E180E] text-[#E7C77B] transition font-bold active:scale-90"
                                            title="Decrease quantity">
                                        −
                                    </button>
                                    <span class="px-2 py-1 text-sm font-bold text-white min-w-[2rem] text-center"
                                          x-text="$store.rayka.getCartQty({{ $item->product->id }})"></span>
                                    <button type="button" 
                                            @click.prevent.stop="$store.rayka.changeQty({{ $item->product->id }}, 1)"
                                            class="px-3 py-1.5 hover:bg-[#2E180E] text-[#E7C77B] transition font-bold active:scale-90"
                                            title="Increase quantity">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

</div>
@endsection

