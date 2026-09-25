@extends('layouts.storefront')

@section('title', $product->meta_title ?: "{$product->name} — Rayka Imitation Jewellery")
@section('meta_description', $product->meta_description ?: ($product->short_description ?: "Buy {$product->name} online from Rayka Imitation Jewellery."))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-28 sm:pb-12" 
     x-data="{ 
        selectedImage: '{{ asset($product->effective_primary_image) }}',
        selectedVariantId: {{ $product->variants->first()?->id ?: 'null' }},
        offerDiscount: {{ $product->active_category_offer ? (float)$product->active_category_offer->discount_percentage : 0 }},
        calcPrice(base) {
            if (this.offerDiscount > 0) {
                return Math.round(base * (1 - this.offerDiscount / 100));
            }
            return base;
        },
        selectedVariantPrice: {{ $product->variants->first() ? ($product->active_category_offer ? round(($product->variants->first()->price_override ?: $product->price) * (1 - $product->active_category_offer->discount_percentage / 100)) : ($product->variants->first()->price_override ?: $product->price)) : (int)$product->effective_price }},
        buyingNow: false,
        galleryImages: {{ json_encode($product->images->count() > 0 ? $product->images->pluck('image_url')->map(fn($u) => asset($u))->values()->all() : [asset($product->effective_primary_image)]) }},
        currentImageIndex: 0,
        nextImage() {
            if (this.galleryImages.length <= 1) return;
            this.currentImageIndex = (this.currentImageIndex + 1) % this.galleryImages.length;
            this.selectedImage = this.galleryImages[this.currentImageIndex];
        },
        prevImage() {
            if (this.galleryImages.length <= 1) return;
            this.currentImageIndex = (this.currentImageIndex - 1 + this.galleryImages.length) % this.galleryImages.length;
            this.selectedImage = this.galleryImages[this.currentImageIndex];
        },
        selectImage(imgUrl, idx) {
            this.selectedImage = imgUrl;
            this.currentImageIndex = idx;
        },
        touchStartX: 0,
        touchStartY: 0,
        handleTouchStart(e) {
            if (!e.touches || e.touches.length === 0) return;
            this.touchStartX = e.touches[0].clientX;
            this.touchStartY = e.touches[0].clientY;
        },
        handleTouchEnd(e) {
            if (!e.changedTouches || e.changedTouches.length === 0) return;
            const diffX = e.changedTouches[0].clientX - this.touchStartX;
            const diffY = e.changedTouches[0].clientY - this.touchStartY;
            if (Math.abs(diffX) > 35 && Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX < 0) {
                    this.nextImage();
                } else {
                    this.prevImage();
                }
            }
        },
        maxStock: {{ $product->stock_quantity }},
        reviewModal: false,
        videoModal: false,
        zoomActive: false,
        zoomX: 0,
        zoomY: 0,
        lensLeft: 0,
        lensTop: 0,
        lensSize: 140,

        handleMouseMove(e) {
            const rect = e.currentTarget.getBoundingClientRect();
            const halfLens = this.lensSize / 2;
            const mouseX = e.clientX - rect.left;
            const mouseY = e.clientY - rect.top;
            
            const minX = 0;
            const maxX = Math.max(0, rect.width - this.lensSize);
            const minY = 0;
            const maxY = Math.max(0, rect.height - this.lensSize);
            
            const posX = Math.max(minX, Math.min(maxX, mouseX - halfLens));
            const posY = Math.max(minY, Math.min(maxY, mouseY - halfLens));
            
            this.lensLeft = posX;
            this.lensTop = posY;
            
            this.zoomX = maxX > 0 ? (posX / maxX) * 100 : 0;
            this.zoomY = maxY > 0 ? (posY / maxY) * 100 : 0;
        }
      }">

    <!-- 1. BREADCRUMBS -->
    <nav class="flex items-center space-x-2 text-xs text-stone-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-[#996E2E]">Home</a>
        <span>/</span>
        @if($product->category)
            <a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-[#996E2E]">{{ $product->category->name }}</a>
            <span>/</span>
        @endif
        <span class="font-medium text-[#4A2C1D] truncate max-w-xs sm:max-w-md">{{ $product->name }}</span>
    </nav>

    <!-- 2. PRODUCT MAIN STAGE (GALLERY + DETAILS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start relative">

        <!-- LEFT COLUMN: IMAGE GALLERY WITH THUMBNAIL STRIP & AMAZON-STYLE ZOOM (5 cols) -->
        <div class="lg:col-span-6 flex flex-col-reverse sm:flex-row gap-4 relative">
            
            <!-- Thumbnail strip -->
            <div class="flex sm:flex-col gap-2.5 overflow-x-auto sm:overflow-y-auto sm:max-h-[520px] shrink-0 no-scrollbar scrollbar-hide">
                @foreach($product->images as $img)
                    <button type="button" 
                            @click="selectImage('{{ asset($img->image_url) }}', {{ $loop->index }})" 
                            :class="{ 'border-[#D4AF6A] shadow-md ring-2 ring-[#D4AF6A]/40': selectedImage === '{{ asset($img->image_url) }}', 'border-stone-200': selectedImage !== '{{ asset($img->image_url) }}' }"
                            class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border-2 bg-[#FAF7F0] shrink-0 transition p-0.5 cursor-pointer">
                        <img src="{{ asset($img->image_url) }}" alt="Thumbnail" loading="lazy" decoding="async" class="w-full h-full object-contain rounded-lg">
                    </button>
                @endforeach
            </div>

            <!-- Main Stage Image Container with Touch Swipe & Zoom Trigger -->
            <div class="flex-1 flex flex-col gap-3">
                <div class="relative aspect-square bg-white rounded-2xl border border-[#D4AF6A]/40 overflow-hidden shadow-sm select-none"
                     @mouseenter="zoomActive = true"
                     @mouseleave="zoomActive = false"
                     @mousemove="handleMouseMove($event)"
                     @touchstart="handleTouchStart($event)"
                     @touchend="handleTouchEnd($event)">
                    
                    <img :src="selectedImage" 
                         alt="{{ $product->name }}" 
                         decoding="async"
                         class="w-full h-full object-contain cursor-crosshair">

                    <!-- Reticle indicator (Desktop only on hover - Hidden on mobile) -->
                    <div x-show="zoomActive" 
                         x-cloak
                         class="hidden lg:block absolute pointer-events-none border-2 border-[#D4AF6A] bg-[#D4AF6A]/20 shadow-md rounded-xl backdrop-contrast-125 transition-none"
                         :style="`width: ${lensSize}px; height: ${lensSize}px; left: ${lensLeft}px; top: ${lensTop}px;`">
                    </div>

                    <!-- Liquid Glass Navigation Arrows (Compact for Mobile) -->
                    <template x-if="galleryImages.length > 1">
                        <div>
                            <!-- Prev Arrow -->
                            <button type="button" 
                                    @click.prevent.stop="prevImage()"
                                    class="absolute left-2.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/70 hover:bg-white/95 backdrop-blur-md border border-[#D4AF6A]/50 flex items-center justify-center text-[#4A2C1D] shadow-md hover:scale-105 active:scale-90 transition-all cursor-pointer select-none"
                                    aria-label="Previous photo">
                                <svg class="w-4 h-4 -ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <!-- Next Arrow -->
                            <button type="button" 
                                    @click.prevent.stop="nextImage()"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/70 hover:bg-white/95 backdrop-blur-md border border-[#D4AF6A]/50 flex items-center justify-center text-[#4A2C1D] shadow-md hover:scale-105 active:scale-90 transition-all cursor-pointer select-none"
                                    aria-label="Next photo">
                                <svg class="w-4 h-4 -mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </template>

                    <!-- Mobile Image Counter Pill -->
                    <div x-show="galleryImages.length > 1" 
                         class="sm:hidden absolute bottom-2.5 left-1/2 -translate-x-1/2 z-10 px-2.5 py-0.5 rounded-full bg-[#1A1412]/60 backdrop-blur-md text-[#FAF7F0] text-[10px] font-semibold tracking-wider pointer-events-none"
                         x-text="(currentImageIndex + 1) + ' / ' + galleryImages.length">
                    </div>

                    <!-- Wishlist Toggle Icon (Top Right) -->
                    <button type="button" 
                            @click.prevent.stop="$store.rayka.toggleWishlist({{ $product->id }})" 
                            class="absolute top-3 right-3 z-20 w-10 h-10 rounded-full bg-white/90 backdrop-blur-xs border border-[#D4AF6A]/50 flex items-center justify-center text-[#4A2C1D] hover:text-rose-600 transition shadow-sm hover:scale-110">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                </div>

                @if($product->youtube_embed_url)
                    <button type="button" 
                            @click="videoModal = true"
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-[#FAF7F0] via-[#F5EFE0] to-[#FAF7F0] border-2 border-[#D4AF6A] text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] font-bold text-xs uppercase tracking-wider transition flex items-center justify-center space-x-2.5 shadow-sm group">
                        <div class="w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center shadow-xs group-hover:scale-110 transition">
                            <svg class="w-3 h-3 fill-current ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                        <span>Watch Craftsmanship Video (HD)</span>
                    </button>
                @endif
            </div>

            <!-- DEDICATED SIDE-PANEL MAGNIFY ZOOM (Amazon / Myntra Style) -->
            <div x-show="zoomActive" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="hidden lg:block absolute left-[102%] top-0 w-[540px] h-[520px] rounded-2xl bg-[#FAF7F0] border-2 border-[#D4AF6A] shadow-2xl z-50 pointer-events-none overflow-hidden"
                 :style="`background-image: url('${selectedImage}'); background-repeat: no-repeat; background-size: 250%; background-position: ${zoomX}% ${zoomY}%;`">
                
                <div class="absolute bottom-3 left-3 bg-[#4A2C1D]/80 text-[#E7C77B] text-[10px] px-3 py-1 rounded-full font-medium tracking-wider uppercase backdrop-blur-xs flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    Ultra HD Craftsmanship Inspection
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: PRODUCT INFO, PRICE, STOCK SELECTOR & ACTIONS (6 cols) -->
        <div class="lg:col-span-6 space-y-6">

            <div>
                @if($product->category)
                    <p class="text-xs uppercase font-bold tracking-[0.2em] text-[#996E2E] mb-1">
                        {{ $product->category->name }}
                    </p>
                @endif
                <h1 class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#4A2C1D] leading-snug">
                    {{ $product->name }}
                </h1>
                <p class="text-xs text-stone-500 mt-1 font-mono">
                    SKU: {{ $product->sku }}
                </p>
            </div>

            <!-- Ratings & Review count summary -->
            <div class="flex items-center space-x-3 text-xs">
                <div class="flex items-center space-x-1 text-[#F0B429]">
                    @for($i = 0; $i < 5; $i++)
                        <span><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg></span>
                    @endfor
                </div>
                <span class="font-bold text-[#4A2C1D]">{{ number_format($product->average_rating, 1) }}</span>
                <span class="text-stone-400">|</span>
                <a href="#reviews-section" class="text-[#996E2E] hover:underline">
                    {{ $product->review_count }} Verified Customer {{ Str::plural('Review', $product->review_count) }}
                </a>
            </div>

            <!-- Category Flash Sale Banner (if active) -->
            @if($product->has_active_offer)
                @php $offer = $product->active_category_offer; @endphp
                <div class="p-4 rounded-xl bg-gradient-to-r from-rose-950 via-[#4A2C1D] to-amber-950 border border-[#D4AF6A] text-white shadow-md flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center space-x-3">
                        <span class="p-2 bg-amber-500/20 text-[#E7C77B] rounded-full border border-amber-400/40">
                            <svg class="w-5 h-5 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                        </span>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-rose-600 text-white font-bold text-[10px] tracking-wider uppercase px-2 py-0.5 rounded-sm">
                                    {{ (int) $offer->discount_percentage }}% OFF
                                </span>
                                <h4 class="font-serif-royal font-bold text-sm sm:text-base text-[#F5EFE0]">
                                    {{ $offer->title }}
                                </h4>
                            </div>
                            <p class="text-xs text-amber-200/80 mt-0.5">
                                {{ $offer->subtitle ?: 'Special category discount applied automatically across all products!' }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] uppercase tracking-wider text-amber-300 block font-semibold">Offer Ends In:</span>
                        <div class="font-mono text-xs font-bold text-white bg-black/40 px-2.5 py-1 rounded-md border border-[#D4AF6A]/40"
                             x-data="{
                                target: new Date('{{ $offer->ends_at->toIso8601String() }}').getTime(),
                                timerText: '',
                                init() {
                                    this.tick();
                                    setInterval(() => this.tick(), 1000);
                                },
                                tick() {
                                    const now = new Date().getTime();
                                    const diff = Math.max(0, this.target - now);
                                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                    const s = Math.floor((diff % (1000 * 60)) / 1000);
                                    this.timerText = `${d}d ${h}h ${m}m ${s}s`;
                                }
                             }">
                            <span x-text="timerText">Calculating...</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Price Block: MRP Strikethrough + Sale Price + Discount % -->
            @php
                $offer = $product->active_category_offer;
                $effectivePrice = (float) $product->effective_price;
                $basePrice = (float) $product->price;
                $mrp = (float) ($product->mrp ?? 0);
                $refPrice = (float) ($product->has_active_offer ? $basePrice : $mrp);
            @endphp
            <div class="p-4 rounded-xl bg-[#FAF7F0] border border-[#D4AF6A]/40 flex items-baseline space-x-3 flex-wrap gap-y-2">
                <span class="font-serif-royal text-3xl sm:text-4xl font-bold {{ $product->has_active_offer ? 'text-rose-700' : 'text-[#4A2C1D]' }}">
                    ₹<span x-text="selectedVariantPrice.toLocaleString('en-IN')">{{ number_format($effectivePrice) }}</span>
                </span>

                @if($refPrice > $effectivePrice)
                    <span class="text-base text-stone-400 line-through">
                        ₹{{ number_format($refPrice) }}
                    </span>
                    @if($product->has_active_offer)
                        <span class="bg-rose-600 text-white font-bold text-xs uppercase px-2.5 py-0.5 rounded-sm">
                            {{ (int) $offer->discount_percentage }}% FLASH SALE
                        </span>
                    @else
                        <span class="bg-[#F0B429] text-[#2E180E] font-bold text-xs uppercase px-2.5 py-0.5 rounded-sm">
                            {{ $product->discount_percent }}% OFF
                        </span>
                    @endif
                @endif
            </div>

            <p class="text-xs sm:text-sm text-stone-600 leading-relaxed">
                {{ $product->short_description }}
            </p>

            <!-- Variant Selector (if product has variants) -->
            @if($product->variants->count() > 0)
                <div class="space-y-3 pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#4A2C1D]">
                        Select Variant / Option:
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($product->variants as $variant)
                            <button type="button"
                                    @click="selectedVariantId = {{ $variant->id }}; maxStock = {{ $variant->stock_quantity }};"
                                    :class="selectedVariantId === {{ $variant->id }} ? 'border-[#4A2C1D] bg-[#FAF7F0] ring-1 ring-[#4A2C1D] font-bold' : 'border-[#D4AF6A]/40 bg-white hover:border-[#D4AF6A]'"
                                    class="p-2.5 rounded-xl border text-left transition flex flex-col justify-between space-y-1">
                                <span class="text-xs text-[#4A2C1D]">{{ $variant->name }}</span>
                                <span class="text-[11px] font-mono text-[#996E2E]">₹{{ number_format((float) ($variant->price ?? $product->sale_price ?? $product->price), 0) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Dynamic Tagged Attributes Summary Pills -->
            @if($product->attributeValues->isNotEmpty())
                <div class="pt-1">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#996E2E] block mb-1.5">Artisan Specs:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($product->attributeValues as $val)
                            <span class="bg-white border border-[#D4AF6A]/40 text-[#4A2C1D] text-[11px] px-2.5 py-1 rounded-md">
                                <strong class="text-stone-500 font-normal">{{ $val->group?->name }}:</strong> {{ $val->value }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Live Stock-Capped Quantity Selector & Buy Buttons -->
            <div class="space-y-4 pt-2">
                <!-- Stock badge -->
                <div>
                    @if($product->stock_quantity <= 0)
                        <span class="inline-flex items-center space-x-1.5 bg-rose-100 border border-rose-300 text-rose-800 text-xs font-bold px-3 py-1 rounded-md">
                            <svg class="w-3.5 h-3.5 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Out of Stock</span>
                        </span>
                    @elseif($product->stock_quantity <= 10)
                        <span class="inline-flex items-center space-x-1.5 bg-amber-100 border border-amber-300 text-amber-900 text-xs font-semibold px-3 py-1 rounded-md animate-pulse">
                            <svg class="w-3.5 h-3.5 text-amber-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.817-.855 1.76-1.077 2.65-.246.993-.385 1.954-.45 2.74a4.992 4.992 0 00-1.745-.98c-.4-.146-.84.092-.93.518-.32 1.53-.13 3.19.68 4.542A6.002 6.002 0 0013 18a6.002 6.002 0 005.99-5.32 7.02 7.02 0 00-.77-3.082 8.01 8.01 0 00-2.32-2.905 10.96 10.96 0 00-2.82-1.892 1 1 0 00-.685-.248z" clip-rule="evenodd"/></svg>
                            <span>High Demand: Only <strong x-text="maxStock">{{ $product->stock_quantity }}</strong> pieces left in stock!</span>
                        </span>
                    @else
                        <span class="inline-flex items-center space-x-1.5 bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-medium px-3 py-1 rounded-md">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>In Stock & Ready for Express Dispatch</span>
                        </span>
                    @endif
                </div>

                <!-- Dynamic SaaS Action Buttons -->
                @if($product->stock_quantity > 0)
                    <div class="space-y-3 pt-2">
                        
                        <!-- State 1: NOT IN BAG -->
                        <div x-show="$store.rayka.getCartQty({{ $product->id }}) === 0"
                             x-cloak
                             class="flex flex-col sm:flex-row gap-3 items-stretch">
                            <!-- Add to Bag Button -->
                            <button type="button" 
                                    @click.prevent="$store.rayka.addToCart({{ $product->id }}, 1, selectedVariantId || null)"
                                    :class="{'opacity-60 pointer-events-none': buyingNow || ($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}])}"
                                    class="flex-1 min-h-[52px] py-3.5 px-4 rounded-xl bg-[#FAF7F0] hover:bg-[#4A2C1D] text-[#4A2C1D] hover:text-[#E7C77B] border-2 border-[#D4AF6A] font-bold text-sm uppercase tracking-wider transition-all duration-300 shadow-xs hover:shadow-md flex items-center justify-center gap-2 group cursor-pointer active:scale-98">
                                
                                <svg x-show="!($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}])" class="w-5 h-5 text-[#996E2E] group-hover:text-[#E7C77B] transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                <span x-show="!($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}])">
                                    Add to Bag
                                </span>
                                
                                <span x-show="$store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}]" class="flex items-center gap-2" x-cloak>
                                    <svg class="animate-spin h-5 w-5 text-[#D4AF6A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Adding...</span>
                                </span>
                            </button>

                            <!-- Buy Now Button -->
                            <button type="button" 
                                    @click.prevent="
                                        if (buyingNow) return;
                                        buyingNow = true;
                                        $store.rayka.addToCart({{ $product->id }}, 1, selectedVariantId || null)
                                            .then(success => {
                                                if (success) { window.location.href = '{{ route('checkout') }}'; }
                                                else { buyingNow = false; }
                                            }).catch(() => { buyingNow = false; });
                                    "
                                    :class="{'opacity-60 pointer-events-none': buyingNow || ($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}])}"
                                    class="flex-1 min-h-[52px] py-3.5 px-4 rounded-xl bg-[#4A2C1D] hover:bg-[#2E180E] text-[#E7C77B] border-2 border-[#4A2C1D] hover:border-[#2E180E] font-bold text-sm uppercase tracking-wider transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center gap-2 active:scale-98 cursor-pointer">
                                <svg x-show="!buyingNow" class="w-5 h-5 shrink-0 text-[#E7C77B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span x-show="!buyingNow">Buy Now</span>

                                <span x-show="buyingNow" class="flex items-center gap-2" x-cloak>
                                    <svg class="animate-spin h-5 w-5 text-[#E7C77B]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Processing...</span>
                                </span>
                            </button>
                        </div>

                        <!-- State 2: ALREADY IN BAG -->
                        <div x-show="$store.rayka.getCartQty({{ $product->id }}) > 0" x-cloak class="flex flex-col sm:flex-row gap-3 items-stretch">
                            <!-- Integrated Quantity Stepper -->
                            <div class="flex items-stretch justify-between rounded-xl bg-[#4A2C1D] border-2 border-[#D4AF6A] text-[#E7C77B] overflow-hidden shadow-xs shrink-0 sm:w-40 min-h-[52px]">
                                <button type="button" 
                                        @click.prevent="$store.rayka.changeQty({{ $product->id }}, -1)"
                                        :disabled="$store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}]"
                                        class="w-12 flex items-center justify-center hover:bg-[#2E180E] transition-colors text-xl font-bold active:scale-90 cursor-pointer disabled:opacity-50"
                                        title="Decrease quantity">
                                    -
                                </button>
                                <div class="flex-1 flex items-center justify-center font-bold text-[#E7C77B] text-base font-mono relative">
                                    <span x-show="!($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}])" x-text="$store.rayka.getCartQty({{ $product->id }})"></span>
                                    <span x-show="$store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}]" class="absolute">
                                        <svg class="w-5 h-5 animate-spin text-[#E7C77B]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </span>
                                </div>
                                <button type="button" 
                                        @click.prevent="$store.rayka.changeQty({{ $product->id }}, 1)"
                                        :disabled="($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}]) || $store.rayka.getCartQty({{ $product->id }}) >= maxStock"
                                        class="w-12 flex items-center justify-center hover:bg-[#2E180E] transition-colors text-xl font-bold active:scale-90 cursor-pointer disabled:opacity-50"
                                        title="Increase quantity">
                                    +
                                </button>
                            </div>
                            <!-- Proceed To Bag Button -->
                            <a href="{{ route('cart') }}" class="flex-1 min-h-[52px] rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white border-2 border-emerald-800 font-bold text-sm uppercase tracking-wider transition-all duration-300 shadow-md flex items-center justify-center gap-2 active:scale-98 cursor-pointer">
                                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <span>Proceed to Bag</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="pt-2 space-y-2">
                        <div class="w-full py-4 px-6 rounded-xl bg-stone-100 border-2 border-stone-300 text-stone-500 font-bold text-sm uppercase tracking-wider flex items-center justify-center gap-2 cursor-not-allowed select-none shadow-xs">
                            <svg class="w-5 h-5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <span>Currently Out of Stock / Sold Out</span>
                        </div>
                        <p class="text-xs text-stone-500 text-center">
                            This exclusive jewellery piece was recently ordered. Contact our royal concierge for restock inquiries.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Trust Icons Row -->
            <div class="grid grid-cols-3 gap-3 pt-4 border-t border-[#D4AF6A]/30 text-center">
                <div class="p-2.5 rounded-lg bg-[#FAF7F0] border border-[#D4AF6A]/30 flex flex-col items-center">
                    <svg class="w-5 h-5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <p class="text-[11px] font-bold text-[#4A2C1D] mt-1">Premium Quality</p>
                    <p class="text-[9px] text-stone-500">1 Gram Micro Plated</p>
                </div>
                <div class="p-2.5 rounded-lg bg-[#FAF7F0] border border-[#D4AF6A]/30 flex flex-col items-center">
                    <svg class="w-5 h-5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <p class="text-[11px] font-bold text-[#4A2C1D] mt-1">Authentic Warranty</p>
                    <p class="text-[9px] text-stone-500">7-Day Replacement</p>
                </div>
                <div class="p-2.5 rounded-lg bg-[#FAF7F0] border border-[#D4AF6A]/30 flex flex-col items-center">
                    <svg class="w-5 h-5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <p class="text-[11px] font-bold text-[#4A2C1D] mt-1">Fast Delivery</p>
                    <p class="text-[9px] text-stone-500">Express Courier</p>
                </div>
            </div>

            <!-- Royal Care & Longevity Quick Notice -->
            <div class="p-3 sm:p-3.5 rounded-xl bg-[#FAF7F0] border border-[#D4AF6A]/40 flex items-center justify-between text-xs gap-3">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <svg class="w-4 h-4 text-[#996E2E] shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    <p class="text-stone-700 truncate">
                        <span class="font-bold text-[#4A2C1D]">Jewellery Care:</span> Wear Last • Avoid Water & Perfume
                    </p>
                </div>
                <button type="button" 
                        @click="document.getElementById('product-details-tabs').scrollIntoView({ behavior: 'smooth' });" 
                        class="text-[#996E2E] font-bold hover:underline shrink-0 flex items-center space-x-1">
                    <span>Care Guide</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>

        </div>

    </div>

    <!-- 2.5 ATELIER CRAFTSMANSHIP & SPECIFICATIONS & DOCUMENTS -->
    @if($product->description || $product->specifications || ($product->documents && $product->documents->count() > 0))
        <div class="mt-16 bg-white rounded-2xl border border-[#D4AF6A]/40 p-6 sm:p-10 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#D4AF6A]/30">
                <div class="flex items-center space-x-2.5">
                    <div class="p-2 bg-[#FAF7F0] border border-[#D4AF6A] rounded-lg">
                        <svg class="w-5 h-5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-serif-royal text-xl sm:text-2xl font-bold text-[#4A2C1D]">
                            Atelier Story & Specifications
                        </h3>
                        <p class="text-xs text-stone-500 mt-0.5">Heritage craftsmanship details, metallurgy, and official documentation</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 {{ $product->specifications ? 'lg:grid-cols-2' : '' }} gap-8">
                @if($product->description)
                    <div class="space-y-2">
                        <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D] uppercase tracking-wider">
                            The Creation Story
                        </h4>
                        <div class="text-xs sm:text-sm text-stone-700 leading-relaxed whitespace-pre-line bg-[#FAF7F0]/60 p-5 rounded-xl border border-[#D4AF6A]/20">
                            {{ $product->description }}
                        </div>
                    </div>
                @endif

                @if($product->specifications && count($product->parsed_specifications) > 0)
                    <div class="space-y-3">
                        <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D] uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#996E2E]"></span>
                            <span>Technical Specifications</span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($product->parsed_specifications as $specKey => $specValue)
                                <div class="bg-[#FAF7F0] p-3.5 rounded-xl border border-[#D4AF6A]/30 flex flex-col justify-center shadow-2xs">
                                    <span class="text-[10px] uppercase tracking-wider text-stone-500 font-semibold">
                                        {{ $specKey }}
                                    </span>
                                    <span class="text-xs sm:text-[13px] font-bold text-[#4A2C1D] mt-0.5">
                                        {{ $specValue }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($product->specifications)
                    <div class="space-y-2">
                        <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D] uppercase tracking-wider">
                            Technical Specifications
                        </h4>
                        <div class="text-xs sm:text-sm text-stone-700 leading-relaxed whitespace-pre-line bg-[#FAF7F0]/60 p-5 rounded-xl border border-[#D4AF6A]/20">
                            {{ $product->specifications }}
                        </div>
                    </div>
                @endif
            </div>

            @if($product->documents && $product->documents->count() > 0)
                <div class="pt-4 border-t border-[#D4AF6A]/20 space-y-3">
                    <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D] uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Downloadable Authenticity Documents & Catalogs ({{ $product->documents->count() }})</span>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($product->documents as $doc)
                            <a href="{{ asset($doc->file_path) }}" target="_blank" download class="p-3 bg-[#FAF7F0] hover:bg-white border border-[#D4AF6A]/40 hover:border-[#996E2E] rounded-xl flex items-center justify-between transition shadow-2xs group">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs border border-rose-200 shrink-0 uppercase">
                                        {{ $doc->type ?: 'PDF' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-[#4A2C1D] truncate group-hover:text-[#996E2E]">{{ $doc->title }}</p>
                                        <p class="text-[10px] text-stone-500 uppercase">{{ $doc->type }} document</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-[#996E2E] group-hover:translate-y-0.5 transition shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- 3. JEWELLERY CARE & MAINTENANCE GUIDE -->
    <div id="product-details-tabs" class="mt-16 bg-white rounded-2xl border border-[#D4AF6A]/40 p-6 sm:p-10 shadow-xs">
        <div class="flex items-center justify-between pb-4 border-b border-[#D4AF6A]/30 mb-6">
            <div class="flex items-center space-x-2.5">
                <div class="p-2 bg-[#FAF7F0] border border-[#D4AF6A] rounded-lg">
                    <svg class="w-5 h-5 text-[#996E2E]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <div>
                    <h3 class="font-serif-royal text-xl sm:text-2xl font-bold text-[#4A2C1D]">
                        Jewellery Care & Safety Guide
                    </h3>
                    <p class="text-xs text-stone-500 mt-0.5">Essential habits to keep your 1 gram micro gold jewellery shining for years</p>
                </div>
            </div>
            <span class="bg-[#F0B429] text-[#2E180E] text-[10px] font-bold px-2.5 py-1 rounded-sm uppercase tracking-wider hidden sm:inline-block">
                ESSENTIAL CARE
            </span>
        </div>

        <div>
            <x-jewellery-care-guide />
        </div>
    </div>

    <!-- 4. REVIEWS & RATINGS SECTION -->
    <div id="reviews-section" class="mt-16 bg-white rounded-2xl border border-[#D4AF6A]/40 p-6 sm:p-10 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-[#D4AF6A]/30 gap-4">
            <div>
                <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D]">
                    Customer Reviews & Ratings
                </h3>
                <p class="text-xs text-stone-500 mt-0.5">
                    Real impressions from verified patrons of this creation.
                </p>
            </div>
            
            <button type="button" 
                    @click="reviewModal = true"
                    class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-full bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] hover:bg-[#4A2C1D] hover:text-[#E7C77B] text-xs font-semibold transition">
                <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Write a Review</span>
            </button>
        </div>

        <!-- Reviews List -->
        <div class="divide-y divide-[#D4AF6A]/20 mt-6 space-y-6">
            @forelse($product->approvedReviews as $rev)
                <div class="pt-6 first:pt-0 space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-xs text-[#4A2C1D]">{{ $rev->customer_name }}</span>
                            @if($rev->is_verified_purchase)
                                <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-semibold flex items-center gap-1">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Verified Purchase
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] text-stone-400">{{ $rev->created_at->format('d M, Y') }}</span>
                    </div>

                    <div class="flex items-center text-[#F0B429] text-xs">
                        @for($i = 0; $i < $rev->rating; $i++)
                            <span><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg></span>
                        @endfor
                    </div>

                    @if($rev->title)
                        <h5 class="font-serif-royal text-sm font-bold text-[#4A2C1D]">{{ $rev->title }}</h5>
                    @endif

                    <p class="text-xs text-stone-600 leading-relaxed italic">
                        "{{ $rev->comment }}"
                    </p>
                </div>
            @empty
                <div class="py-8 text-center text-stone-500 text-xs">
                    Be the first royal patron to review this jewellery piece!
                </div>
            @endforelse
        </div>
    </div>

    <!-- 5. "WRITE A REVIEW" MODAL -->
    <div x-show="reviewModal" 
         x-cloak
         x-transition 
         class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
        
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 border border-[#D4AF6A] shadow-2xl space-y-4" @click.outside="reviewModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-[#D4AF6A]/30">
                <h4 class="font-serif-royal text-lg font-bold text-[#4A2C1D]">Share Your Experience</h4>
                <button type="button" @click="reviewModal = false" class="text-stone-400 hover:text-stone-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('review.submit') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div>
                    <label class="block font-semibold mb-1">Your Full Name *</label>
                    <input type="text" name="customer_name" value="{{ Auth::user()?->name }}" required class="w-full border rounded-lg p-2.5">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Rating *</label>
                    <select name="rating" required class="w-full border rounded-lg p-2.5">
                        <option value="5">(5/5) — Extraordinary Royal Quality</option>
                        <option value="4">(4/5) — Very Satisfied</option>
                        <option value="3">(3/5) — Average Experience</option>
                        <option value="2">(2/5) — Below Expectations</option>
                        <option value="1">(1/5) — Unsatisfied</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Review Title</label>
                    <input type="text" name="title" placeholder="e.g. Stunning Kundan finish!" class="w-full border rounded-lg p-2.5">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Your Review / Comments *</label>
                    <textarea name="comment" rows="4" required placeholder="Describe the polish, craftsmanship, and how it looked..." class="w-full border rounded-lg p-2.5"></textarea>
                </div>

                <p class="text-[11px] text-stone-500">
                    Note: To maintain authenticity, all patron reviews undergo brief moderation before going live.
                </p>

                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="reviewModal = false" class="px-4 py-2 border rounded-lg text-stone-600">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-[#4A2C1D] text-[#E7C77B] rounded-lg font-semibold uppercase tracking-wider">Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 6. "MORE FROM [CATEGORY]" RECOMMENDATION CAROUSEL (Section 10 Requirement) -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-20">
            <div class="flex items-center justify-between pb-4 border-b border-[#D4AF6A]/30 mb-8">
                <div>
                    <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Complementary Curations</span>
                    <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D] mt-1">
                        More from {{ $product->category->name }}
                    </h3>
                </div>
                <a href="{{ route('category.show', $product->category->slug) }}" class="text-xs font-semibold text-[#996E2E] hover:underline">
                    <span class="flex items-center gap-1.5">Explore All {{ $product->category->name }} <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></span>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($relatedProducts as $rel)
                    <x-product-card :product="$rel" />
                @endforeach
            </div>
        </div>
    @endif

    <!-- 7. YouTube Video Modal (HD Craftsmanship Player - Ultra Responsive for Mobile & Desktop) -->
    @if($product->youtube_embed_url)
        <div x-show="videoModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="videoModal = false"
             class="fixed inset-0 z-50 overflow-y-auto bg-black/85 backdrop-blur-sm flex items-center justify-center p-2.5 sm:p-4 md:p-6"
             style="padding-bottom: env(safe-area-inset-bottom, 16px);">
            
            <div @click.away="videoModal = false" 
                 class="relative bg-[#201009] border border-[#D4AF6A]/60 sm:border-2 sm:border-[#D4AF6A] rounded-xl sm:rounded-2xl max-w-2xl sm:max-w-3xl w-full overflow-hidden shadow-2xl my-auto">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-3.5 py-2.5 sm:px-6 sm:py-3.5 border-b border-[#D4AF6A]/30 bg-gradient-to-r from-[#4A2C1D] to-[#2E180E] gap-2">
                    <div class="flex items-center space-x-2 min-w-0 flex-1">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping shrink-0"></span>
                        <h3 class="font-serif-royal font-bold text-white text-xs sm:text-base md:text-lg truncate">
                            {{ $product->name }} - Craftsmanship Video
                        </h3>
                    </div>
                    <button type="button" 
                            @click="videoModal = false" 
                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/10 hover:bg-white/20 active:scale-95 text-stone-300 hover:text-white flex items-center justify-center transition shrink-0 cursor-pointer"
                            aria-label="Close Video">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Video Aspect Ratio Frame -->
                <div class="relative aspect-video w-full bg-black">
                    <template x-if="videoModal">
                        <iframe src="{{ $product->youtube_embed_url }}&autoplay=1" 
                                class="absolute inset-0 w-full h-full" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                        </iframe>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="px-3.5 py-2 sm:px-5 sm:py-2.5 bg-[#160804] flex items-center justify-between gap-2 text-xs text-stone-400">
                    <span class="flex items-center space-x-1.5 min-w-0 truncate">
                        <span class="text-rose-500 shrink-0">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                        </span>
                        <span class="truncate text-[11px] sm:text-xs text-stone-300">Authentic 1 Gram Gold Shine Preview</span>
                    </span>
                    <button type="button" @click="videoModal = false" class="px-3 py-1 rounded-full bg-[#D4AF6A]/20 hover:bg-[#D4AF6A]/30 text-[#F5D77F] text-[11px] sm:text-xs font-bold transition shrink-0 cursor-pointer">
                        Close Video
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MOBILE STICKY BOTTOM BAR -->
    @if($product->stock_quantity > 0)
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-[#D4AF6A]/30 p-3 shadow-[0_-4px_10px_-1px_rgba(0,0,0,0.1)] sm:hidden flex flex-col gap-2 pb-safe" style="padding-bottom: env(safe-area-inset-bottom, 12px);">
            <!-- State 1: NOT IN BAG -->
            <div x-show="$store.rayka.getCartQty({{ $product->id }}) === 0" x-cloak class="flex items-center gap-2">
                <!-- Add to Bag Button -->
                <button type="button" 
                        @click.prevent="$store.rayka.addToCart({{ $product->id }}, 1, selectedVariantId || null)"
                        :class="{'opacity-60 pointer-events-none': buyingNow || ($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}])}"
                        class="flex-1 min-h-[48px] rounded-xl bg-[#FAF7F0] text-[#4A2C1D] border border-[#D4AF6A] font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-1 shadow-xs">
                    <span x-show="!($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}])">Add to Bag</span>
                    <span x-show="$store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}]" class="flex items-center gap-1">
                        <svg class="animate-spin h-3.5 w-3.5 text-[#D4AF6A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Wait..
                    </span>
                </button>
                <!-- Buy Now Button -->
                <button type="button" 
                        @click.prevent="if(buyingNow) return; buyingNow=true; $store.rayka.addToCart({{ $product->id }}, 1, selectedVariantId || null).then(s=>{if(s)window.location.href='{{ route('checkout') }}';else buyingNow=false}).catch(()=>buyingNow=false);"
                        :class="{'opacity-60 pointer-events-none': buyingNow || ($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}])}"
                        class="flex-1 min-h-[48px] rounded-xl bg-[#4A2C1D] text-[#E7C77B] border border-[#4A2C1D] font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-1 shadow-md">
                    <span x-show="!buyingNow">Buy Now</span>
                    <span x-show="buyingNow" class="flex items-center gap-1">
                        <svg class="animate-spin h-3.5 w-3.5 text-[#E7C77B]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Wait..
                    </span>
                </button>
            </div>
            
            <!-- State 2: ALREADY IN BAG -->
            <div x-show="$store.rayka.getCartQty({{ $product->id }}) > 0" x-cloak class="flex items-center gap-2">
                <div class="flex items-stretch justify-between rounded-xl bg-[#4A2C1D] border border-[#D4AF6A] text-[#E7C77B] overflow-hidden w-[110px] min-h-[48px] shrink-0 shadow-xs">
                    <button type="button" @click.prevent="$store.rayka.changeQty({{ $product->id }}, -1)" :disabled="$store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}]" class="w-10 flex items-center justify-center font-bold text-lg active:bg-[#2E180E] disabled:opacity-50">-</button>
                    <div class="flex-1 flex items-center justify-center font-bold text-sm relative">
                        <span x-show="!($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}])" x-text="$store.rayka.getCartQty({{ $product->id }})"></span>
                        <span x-show="$store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}]" class="absolute">
                            <svg class="w-4 h-4 animate-spin text-[#E7C77B]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </span>
                    </div>
                    <button type="button" @click.prevent="$store.rayka.changeQty({{ $product->id }}, 1)" :disabled="($store.rayka.loadingItems && $store.rayka.loadingItems[{{ $product->id }}]) || $store.rayka.getCartQty({{ $product->id }}) >= maxStock" class="w-10 flex items-center justify-center font-bold text-lg active:bg-[#2E180E] disabled:opacity-50">+</button>
                </div>
                <a href="{{ route('cart') }}" class="flex-1 min-h-[48px] rounded-xl bg-emerald-700 text-white font-bold text-xs uppercase flex items-center justify-center gap-1 shadow-md">
                    Proceed to Bag
                </a>
            </div>
        </div>
    @endif

</div>
@push('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org/",
  "@@type": "Product",
  "name": "{{ $product->name }}",
  "image": [
    @foreach($product->images as $img)
    "{{ asset($img->image_url) }}"{{ !$loop->last ? ',' : '' }}
    @endforeach
  ],
  "description": "{{ $product->meta_description ?: $product->short_description }}",
  "sku": "{{ $product->sku }}",
  "offers": {
    "@@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "INR",
    "price": "{{ $product->active_category_offer ? round(($product->price) * (1 - $product->active_category_offer->discount_percentage / 100)) : $product->price }}",
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "{{ $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
  }
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [{
    "@@type": "ListItem",
    "position": 1,
    "name": "Home",
    "item": "{{ route('home') }}"
  }
  @if($product->category)
  ,{
    "@@type": "ListItem",
    "position": 2,
    "name": "{{ $product->category->name }}",
    "item": "{{ route('category.show', $product->category->slug) }}"
  },{
    "@@type": "ListItem",
    "position": 3,
    "name": "{{ $product->name }}",
    "item": "{{ url()->current() }}"
  }
  @else
  ,{
    "@@type": "ListItem",
    "position": 2,
    "name": "{{ $product->name }}",
    "item": "{{ url()->current() }}"
  }
  @endif
  ]
}
</script>
@endpush
@endsection

