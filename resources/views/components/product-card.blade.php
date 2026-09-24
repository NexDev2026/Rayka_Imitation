@props(['product'])

<div class="royal-card rounded-xl overflow-hidden group flex flex-col h-full relative bg-white border border-[#D4AF6A]/30 hover:border-[#D4AF6A] transition-all duration-300 shadow-xs hover:shadow-lg" x-data>
    
    <!-- Discount & Offer Badge -->
    @if($product->has_active_offer)
        <div class="absolute top-2 left-2 z-20 max-w-[80%] bg-gradient-to-r from-rose-600 to-amber-600 text-white font-bold text-[8.5px] sm:text-[10px] tracking-tight sm:tracking-wider uppercase px-1.5 sm:px-2 py-0.5 rounded-sm shadow-md flex items-center space-x-0.5 sm:space-x-1 animate-pulse">
            <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-amber-200 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
            <span class="sm:hidden whitespace-nowrap">{{ (int) $product->active_category_offer->discount_percentage }}% OFF</span>
            <span class="hidden sm:inline truncate">{{ (int) $product->active_category_offer->discount_percentage }}% FLASH SALE</span>
        </div>
    @elseif($product->discount_percent > 0)
        <div class="absolute top-2 left-2 z-20 max-w-[80%] bg-[#F0B429] text-[#2E180E] font-bold text-[8.5px] sm:text-[10px] tracking-tight sm:tracking-wider uppercase px-1.5 sm:px-2 py-0.5 rounded-sm shadow-xs truncate">
            {{ $product->discount_percent }}% OFF
        </div>
    @endif

    <!-- Wishlist Heart Button -->
    <button type="button" 
            @click.prevent.stop="$store.rayka.toggleWishlist({{ $product->id }}, $el)" 
            class="absolute top-2 right-2 z-20 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/95 backdrop-blur-xs border border-[#D4AF6A]/40 flex items-center justify-center text-[#4A2C1D] hover:text-rose-600 hover:scale-110 transition shadow-xs cursor-pointer"
            title="Add to Wishlist">
        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition" 
             :class="{ 'fill-rose-600 text-rose-600': $store.rayka.isInWishlist({{ $product->id }}), 'fill-none text-[#4A2C1D]': !$store.rayka.isInWishlist({{ $product->id }}) }" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
        </svg>
    </button>

    <!-- Product Image with Dual-Image Swap on Hover -->
    <a href="{{ route('product.show', $product->slug) }}" class="block relative aspect-square overflow-hidden bg-[#FAF7F0] border-b border-[#D4AF6A]/20">
        <!-- Primary Image -->
        <img src="{{ $product->effective_primary_image }}" 
             alt="{{ $product->name }}" 
             loading="lazy"
             decoding="async"
             class="w-full h-full object-cover group-hover:scale-105 group-hover:opacity-0 transition-all duration-300">

        <!-- Secondary Image (Hover Swap) -->
        <img src="{{ $product->effective_secondary_image }}" 
             alt="{{ $product->name }} Alternate" 
             loading="lazy"
             decoding="async"
             class="w-full h-full object-cover absolute inset-0 opacity-0 group-hover:opacity-100 group-hover:scale-105 transition-all duration-300">
        
        <!-- YouTube Video Indicator Pill -->
        @if($product->youtube_id)
            <div class="absolute bottom-2 right-2 z-10 bg-black/75 backdrop-blur-xs text-white text-[9px] sm:text-[10px] px-1.5 sm:px-2 py-0.5 rounded-full flex items-center space-x-1 shadow-sm border border-white/20">
                <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-rose-500 fill-rose-500" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                <span class="font-medium tracking-wide">Video</span>
            </div>
        @endif

        <!-- Stock Indicator Overlay if Low -->
        @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
            <div class="absolute bottom-2 left-2 z-10 bg-amber-900/85 text-amber-200 text-[9px] sm:text-[10px] px-1.5 sm:px-2 py-0.5 rounded-sm font-medium">
                Only {{ $product->stock_quantity }} Left
            </div>
        @elseif($product->stock_quantity <= 0)
            <div class="absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center z-10">
                <span class="bg-rose-900 text-white font-bold text-[10px] sm:text-xs uppercase px-2.5 py-1 rounded-sm tracking-wider">Out of Stock</span>
            </div>
        @endif
    </a>

    <!-- Card Content Block -->
    <div class="p-2.5 sm:p-4 flex flex-col flex-1 justify-between bg-white">
        <div>
            @if($product->category)
                <p class="text-[9.5px] sm:text-[10px] uppercase font-bold tracking-widest text-[#996E2E] mb-0.5 line-clamp-1">
                    {{ $product->category->name }}
                </p>
            @endif

            <a href="{{ route('product.show', $product->slug) }}" class="block font-serif-royal text-xs sm:text-sm font-semibold text-[#4A2C1D] hover:text-[#996E2E] transition line-clamp-2 leading-snug mb-1">
                {{ $product->name }}
            </a>

            <!-- Customer Reviews Summary (Truncated properly on mobile) -->
            <div class="flex items-center space-x-1 text-[9.5px] sm:text-[11px] mb-1">
                <div class="flex text-[#F0B429]">
                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                </div>
                <span class="font-bold text-stone-700">5.0</span>
                <span class="text-stone-400 shrink-0">•</span>
                <span class="text-stone-500 truncate min-w-0">Verified</span>
            </div>
        </div>

        <div class="pt-2 border-t border-[#D4AF6A]/20 mt-1.5 sm:mt-2.5 space-y-1.5 sm:space-y-2">
            <!-- Price Block: Effective Sale Price + MRP/Regular Price + Savings -->
            @php
                $currentPrice = $product->effective_price;
                $referencePrice = $product->has_active_offer ? $product->price : $product->mrp;
            @endphp
            <div class="flex items-center justify-between flex-wrap gap-0.5 sm:gap-1">
                <div class="flex items-baseline space-x-1 sm:space-x-1.5 flex-wrap">
                    <span class="text-xs sm:text-base font-bold {{ $product->has_active_offer ? 'text-rose-700' : 'text-[#4A2C1D]' }}">
                        ₹{{ number_format($currentPrice) }}
                    </span>
                    @if($referencePrice > $currentPrice)
                        <span class="text-[10px] sm:text-[11px] text-stone-400 line-through">
                            ₹{{ number_format($referencePrice) }}
                        </span>
                    @endif
                </div>
                @if($referencePrice > $currentPrice)
                    <span class="text-[8.5px] sm:text-[10px] {{ $product->has_active_offer ? 'text-rose-600 font-bold bg-rose-50' : 'text-emerald-700 font-semibold bg-emerald-50' }} px-1 sm:px-1.5 py-0.5 rounded-sm border border-current/20">
                        Save ₹{{ number_format($referencePrice - $currentPrice) }}
                    </span>
                @endif
            </div>

            <!-- Add to Bag / Interactive - [qty] + Controller -->
            <div>
                @if($product->stock_quantity <= 0)
                    <span class="w-full h-8 sm:h-9 rounded-lg bg-stone-100 text-stone-400 border border-stone-200 text-[10px] sm:text-xs font-bold flex items-center justify-center uppercase tracking-wider">
                        Sold Out
                    </span>
                @else
                    <!-- Initial "+ Add" Button -->
                    <div x-show="$store.rayka.getCartQty({{ $product->id }}) <= 0">
                        <button type="button" 
                                @click.prevent.stop="$store.rayka.addToCart({{ $product->id }}, 1)" 
                                class="w-full h-8 sm:h-9 rounded-lg bg-[#FAF7F0] hover:bg-[#4A2C1D] text-[#4A2C1D] hover:text-[#E7C77B] border border-[#D4AF6A] text-[10px] sm:text-xs font-bold transition-all duration-200 flex items-center justify-center gap-1 sm:gap-1.5 uppercase tracking-normal sm:tracking-wider shadow-2xs hover:shadow-md active:scale-98 cursor-pointer px-1 sm:px-2">
                            <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-[#D4AF6A] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                            <span class="whitespace-nowrap">Add To Bag</span>
                        </button>
                    </div>

                    <!-- Interactive - [qty] + Controller -->
                    <div x-show="$store.rayka.getCartQty({{ $product->id }}) > 0" 
                         class="w-full h-8 sm:h-9 flex items-center justify-between rounded-lg bg-[#4A2C1D] border border-[#D4AF6A] text-[#E7C77B] overflow-hidden shadow-xs text-xs font-bold"
                         @click.prevent.stop>
                        <button type="button" 
                                @click.prevent.stop="$store.rayka.changeQty({{ $product->id }}, -1)"
                                :disabled="$store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}]"
                                class="h-full px-2.5 sm:px-4 hover:bg-[#2E180E] text-[#E7C77B] transition text-sm font-bold active:scale-90 flex items-center justify-center cursor-pointer"
                                title="Decrease quantity">
                            −
                        </button>
                        <span class="h-full px-1 py-1 text-xs font-bold text-white min-w-[1.5rem] flex items-center justify-center"
                              x-text="$store.rayka.getCartQty({{ $product->id }})"></span>
                        <button type="button" 
                                @click.prevent.stop="$store.rayka.changeQty({{ $product->id }}, 1)"
                                :disabled="($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}]) || $store.rayka.getCartQty({{ $product->id }}) >= {{ (int) $product->stock_quantity }}"
                                class="h-full px-2.5 sm:px-4 hover:bg-[#2E180E] text-[#E7C77B] transition text-sm font-bold active:scale-90 flex items-center justify-center cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                                title="Increase quantity">
                            +
                        </button>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
