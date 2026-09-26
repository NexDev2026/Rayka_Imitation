@extends('layouts.storefront')

@section('title', 'Rayka Imitation Jewellery — Royal Indian Heritage & 1 Gram Micro Gold')

@section('content')
<div class="space-y-16 sm:space-y-24">

    <!-- 1. HERO BANNER SLIDER (Fast & Smooth Luxury SaaS) -->
    <section class="relative overflow-hidden bg-[#160601] select-none" 
             x-data="{ 
                activeSlide: 0, 
                totalSlides: {{ count($banners) }},
                progress: 0,
                duration: 4800,
                intervalStep: 40,
                timer: null,
                isPaused: false,
                touchStartX: 0,
                touchEndX: 0,
                init() {
                    this.startTimer();
                },
                startTimer() {
                    this.clearTimer();
                    this.progress = 0;
                    if (this.totalSlides <= 1) return;
                    const stepPercent = (this.intervalStep / this.duration) * 100;
                    this.timer = setInterval(() => {
                        if (!this.isPaused) {
                            this.progress += stepPercent;
                            if (this.progress >= 100) {
                                this.nextSlide();
                            }
                        }
                    }, this.intervalStep);
                },
                clearTimer() {
                    if (this.timer) clearInterval(this.timer);
                },
                goToSlide(index) {
                    if (this.activeSlide === index) return;
                    this.activeSlide = index;
                    this.startTimer();
                },
                nextSlide() {
                    this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                    this.startTimer();
                },
                prevSlide() {
                    this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
                    this.startTimer();
                },
                pause() {
                    this.isPaused = true;
                },
                resume() {
                    this.isPaused = false;
                },
                handleTouchStart(e) {
                    this.touchStartX = e.touches[0].clientX;
                },
                handleTouchEnd(e) {
                    this.touchEndX = e.changedTouches[0].clientX;
                    const diff = this.touchStartX - this.touchEndX;
                    if (diff > 40) {
                        this.nextSlide();
                    } else if (diff < -40) {
                        this.prevSlide();
                    }
                }
             }" 
             @mouseenter="pause()" 
             @mouseleave="resume()"
             @touchstart.passive="handleTouchStart($event)"
             @touchend.passive="handleTouchEnd($event)">

        <div class="relative min-h-[420px] sm:min-h-[480px] md:min-h-[540px] lg:min-h-[580px] flex items-center">
            @foreach($banners as $index => $banner)
                <div class="absolute inset-0 transition-opacity duration-500 ease-out transform-gpu will-change-[opacity]"
                     :class="{
                        'opacity-100 z-10 pointer-events-auto': activeSlide === {{ $index }},
                        'opacity-0 z-0 pointer-events-none': activeSlide !== {{ $index }}
                     }">
                    
                    <!-- Background Banner Image with Responsive Mobile WebP Framing -->
                    <picture class="absolute inset-0 w-full h-full">
                        @php
                            $mobileBannerPath = 'storage/banners/banner_' . ($index + 1) . '_mobile.webp';
                        @endphp
                        @if(file_exists(public_path($mobileBannerPath)))
                            <source media="(max-width: 639px)" srcset="{{ asset($mobileBannerPath) }}" type="image/webp">
                        @endif
                        <source media="(min-width: 640px)" srcset="{{ $banner->image_url ? asset($banner->image_url) : '' }}" type="image/webp">
                        <img src="{{ $banner->image_url ? asset($banner->image_url) : '' }}" 
                             alt="{{ $banner->title }}" 
                             loading="eager"
                             decoding="async"
                             class="w-full h-full object-cover object-center">
                    </picture>
                    
                    <!-- Multi-Layer Luxury Gradient Overlays: Deep Readability for Text, Bright Glowing Jewellery on Right -->
                    <div class="absolute inset-0 bg-gradient-to-t sm:bg-gradient-to-r from-[#160601]/95 via-[#160601]/60 via-55% sm:via-40% to-transparent pointer-events-none"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#160601]/80 via-[#160601]/30 via-55% to-transparent sm:hidden pointer-events-none"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#D4AF6A]/20 via-[#D4AF6A]/5 via-40% to-transparent pointer-events-none"></div>
                    <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/50 to-transparent pointer-events-none"></div>

                    <!-- Banner Content Aligned to Left Edge with Luxury SaaS Spacing -->
                    <div class="relative w-full max-w-[1700px] mx-auto px-4 sm:px-10 md:px-14 lg:px-16 xl:px-20 py-8 sm:py-16 lg:py-20 flex items-center">
                        <div class="max-w-xl lg:max-w-2xl text-left space-y-3 sm:space-y-5">
                            @if($banner->badge_text)
                                <div class="inline-flex items-center space-x-1.5 sm:space-x-2 bg-[#4A2C1D]/85 border border-[#D4AF6A]/70 px-3 py-1 sm:px-4 sm:py-1.5 rounded-full text-[10px] sm:text-xs font-semibold text-[#E7C77B] tracking-wider sm:tracking-widest uppercase backdrop-blur-md shadow-lg transition-all duration-500"
                                     :class="{ 'opacity-100 translate-y-0': activeSlide === {{ $index }}, 'opacity-0 translate-y-2': activeSlide !== {{ $index }} }">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#D4AF6A] animate-pulse"></span>
                                    <span class="inline-flex items-center">{!! $banner->badge_text !!}</span>
                                </div>
                            @endif

                            @if($index === 0)
                                <h1 class="font-serif-royal text-xl sm:text-3xl md:text-5xl lg:text-6xl font-bold text-[#FFF8EB] leading-[1.2] sm:leading-[1.18] drop-shadow-lg tracking-tight transition-all duration-500 delay-75"
                                    :class="{ 'opacity-100 translate-y-0': activeSlide === {{ $index }}, 'opacity-0 translate-y-3': activeSlide !== {{ $index }} }">
                                    {{ $banner->title }}
                                </h1>
                            @else
                                <h2 class="font-serif-royal text-xl sm:text-3xl md:text-5xl lg:text-6xl font-bold text-[#FFF8EB] leading-[1.2] sm:leading-[1.18] drop-shadow-lg tracking-tight transition-all duration-500 delay-75"
                                    :class="{ 'opacity-100 translate-y-0': activeSlide === {{ $index }}, 'opacity-0 translate-y-3': activeSlide !== {{ $index }} }">
                                    {{ $banner->title }}
                                </h2>
                            @endif

                            <p class="text-[11px] sm:text-sm md:text-base text-stone-200 font-normal leading-relaxed max-w-lg drop-shadow-sm transition-all duration-500 delay-150"
                                :class="{ 'opacity-100 translate-y-0': activeSlide === {{ $index }}, 'opacity-0 translate-y-3': activeSlide !== {{ $index }} }">
                                {{ $banner->subtitle }}
                            </p>

                            <div class="pt-1.5 sm:pt-4 flex flex-wrap items-center gap-2.5 sm:gap-4 transition-all duration-500 delay-200"
                                 :class="{ 'opacity-100 translate-y-0': activeSlide === {{ $index }}, 'opacity-0 translate-y-3': activeSlide !== {{ $index }} }">
                                <a href="{{ $banner->button_link }}" 
                                   class="group inline-flex items-center space-x-2 bg-gradient-to-r from-[#F0B429] via-[#E7C77B] to-[#D4AF6A] text-[#2E180E] font-bold text-[11px] sm:text-sm px-5 sm:px-8 py-2.5 sm:py-3.5 rounded-full shadow-lg shadow-amber-900/30 hover:shadow-xl hover:shadow-amber-600/40 hover:scale-105 active:scale-98 transition-all duration-300 tracking-wider uppercase border border-[#FFF6E3]/60 cursor-pointer">
                                    <span>{{ $banner->button_text }}</span>
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                                <a href="{{ route('trending') }}" 
                                   class="inline-flex items-center space-x-1.5 bg-white/10 hover:bg-white/20 text-[#FFF8EB] font-semibold text-[11px] sm:text-sm px-4 sm:px-7 py-2.5 sm:py-3.5 rounded-full border border-white/25 hover:border-white/50 backdrop-blur-md transition-all duration-300 hover:scale-105 active:scale-98 tracking-wider uppercase cursor-pointer">
                                    <span>View Trending</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- SaaS Minimalist Floating Progress Indicators (Compact Luxury Pill) -->
        @if(count($banners) > 1)
            <div class="absolute bottom-3 sm:bottom-6 md:bottom-8 right-3 sm:right-8 md:right-12 z-20 flex items-center space-x-1.5 sm:space-x-2 bg-black/45 hover:bg-black/65 backdrop-blur-md px-2.5 py-1.5 sm:px-3.5 sm:py-2 rounded-full border border-white/15 shadow-xl transition-colors duration-300">
                @foreach($banners as $index => $banner)
                    <button type="button" 
                            @click="goToSlide({{ $index }})" 
                            class="relative h-1 sm:h-1.5 rounded-full overflow-hidden transition-all duration-300 ease-out cursor-pointer"
                            :class="{ 'w-5 sm:w-8 md:w-10 bg-white/25': activeSlide === {{ $index }}, 'w-1.5 sm:w-2 bg-white/40 hover:bg-white/70': activeSlide !== {{ $index }} }"
                            aria-label="Go to slide {{ $index + 1 }}">
                        <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-[#D4AF6A] to-[#F5D77F] rounded-full transition-none"
                             :style="activeSlide === {{ $index }} ? 'width: ' + progress + '%' : 'width: 0%'"></div>
                    </button>
                @endforeach
            </div>
        @endif
    </section>

    <!-- 1.5 DOWNSIDE HERO LUXURY HERITAGE SHOWCASE (Curated Men & Women Royalty) -->
    @php
        $menThumb = \App\Models\StoreSetting::get('hero_men_thumbnail', '/storage/thumbnails/men_thumbnail.webp');
        $womenThumb = \App\Models\StoreSetting::get('hero_women_thumbnail', '/storage/thumbnails/women_thumbnail.webp');
        $menTitle = \App\Models\StoreSetting::get('hero_men_title', "The Royal Men's Realm");
        $womenTitle = \App\Models\StoreSetting::get('hero_women_title', "The Queen's Heritage Realm");
        $menSubtitle = \App\Models\StoreSetting::get('hero_men_subtitle', "Chains • Rajwadi Kadas • 2-Kaddi Luckies • Signet Rings");
        $womenSubtitle = \App\Models\StoreSetting::get('hero_women_subtitle', "Sacred Mangalsutras • Temple Pendants • Bridal Malas • Bangles");
        $menLink = \App\Models\StoreSetting::get('hero_men_link', route('nav.group', 'men'));
        $womenLink = \App\Models\StoreSetting::get('hero_women_link', route('nav.group', 'women'));
    @endphp
    <section class="max-w-[1680px] mx-auto px-4 sm:px-6 lg:px-8 mt-4 sm:-mt-8 md:-mt-12 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">

            <!-- MEN'S ROYAL SHOWCASE CARD -->
            <div class="group relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl sm:shadow-2xl shadow-stone-900/30 border border-[#D4AF6A]/40 bg-[#160601] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-amber-950/40">
                <div class="relative min-h-[440px] sm:min-h-0 sm:aspect-[21/11] md:aspect-[16/11] lg:aspect-[16/10] w-full overflow-hidden flex flex-col justify-end">
                    <!-- Image with Smooth Zoom -->
                    <img src="{{ asset(ltrim($menThumb, '/')) }}" 
                         alt="{{ $menTitle }}" 
                         loading="eager"
                         decoding="async"
                         class="absolute inset-0 w-full h-full object-cover object-top sm:object-center transform transition-transform duration-700 ease-out group-hover:scale-106">

                    <!-- Gradient Overlays for Supreme Readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#160601] via-[#160601]/80 via-55% to-transparent pointer-events-none"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#160601]/80 via-[#160601]/30 via-50% to-transparent pointer-events-none"></div>

                    <!-- Ambient Gold Shimmer Border on Hover -->
                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-[#E7C77B]/60 rounded-2xl sm:rounded-3xl transition-colors duration-500 pointer-events-none"></div>

                    <!-- Content Layout -->
                    <div class="relative z-10 p-4 sm:p-6 md:p-8 lg:p-9 flex flex-col justify-end">
                        <div class="space-y-2.5 sm:space-y-3.5 max-w-lg">
                            <!-- Badge -->
                            <div class="inline-flex items-center space-x-1.5 bg-[#3A1E11]/90 border border-[#D4AF6A]/60 px-2.5 py-0.5 sm:px-3.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold text-[#E7C77B] tracking-wider uppercase backdrop-blur-md shadow-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#E7C77B] animate-pulse"></span>
                                <span>Men's Royal Domain</span>
                            </div>

                            <!-- Title -->
                            <h3 class="font-serif-royal text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-[#FFF8EB] leading-tight drop-shadow-md">
                                {{ $menTitle }}
                            </h3>

                            <!-- Subtitle -->
                            <p class="text-[11px] sm:text-xs md:text-sm text-stone-200 line-clamp-2 leading-relaxed font-light">
                                {{ $menSubtitle }}
                            </p>

                            <!-- Category Quick Badges -->
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 pt-0.5 sm:pt-1">
                                <a href="{{ route('category.show', 'chains') }}" class="text-[10px] sm:text-[11px] px-2.5 py-0.5 sm:py-1 rounded-full bg-white/10 hover:bg-white/25 text-[#FAF7F0] border border-white/25 backdrop-blur-md transition-all hover:scale-105" style="color: #FAF7F0 !important;">Chains</a>
                                <a href="{{ route('category.show', 'kadas') }}" class="text-[10px] sm:text-[11px] px-2.5 py-0.5 sm:py-1 rounded-full bg-white/10 hover:bg-white/25 text-[#FAF7F0] border border-white/25 backdrop-blur-md transition-all hover:scale-105" style="color: #FAF7F0 !important;">Rajwadi Kadas</a>
                                <a href="{{ route('category.show', 'bracelets') }}" class="text-[10px] sm:text-[11px] px-2.5 py-0.5 sm:py-1 rounded-full bg-white/10 hover:bg-white/25 text-[#FAF7F0] border border-white/25 backdrop-blur-md transition-all hover:scale-105" style="color: #FAF7F0 !important;">Bracelets</a>
                                <a href="{{ route('category.show', '2-kaddi') }}" class="text-[10px] sm:text-[11px] px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full font-semibold transition-all backdrop-blur-md hover:scale-105 inline-flex items-center gap-1 shadow-sm" style="background: rgba(212, 175, 106, 0.28); border: 1.5px solid #E7C77B; color: #FFF8EB !important; text-shadow: 0 1px 3px rgba(0,0,0,0.8); box-shadow: 0 0 10px rgba(212, 175, 106, 0.2);">
                                    <span class="text-[#F5C542] text-[9px]">✦</span>
                                    <span style="color: #FFF8EB !important;">2-Kaddi</span>
                                </a>
                                <a href="{{ route('category.show', 'rings') }}" class="text-[10px] sm:text-[11px] px-2.5 py-0.5 sm:py-1 rounded-full bg-white/10 hover:bg-white/25 text-[#FAF7F0] border border-white/25 backdrop-blur-md transition-all hover:scale-105" style="color: #FAF7F0 !important;">Rings</a>
                            </div>

                            <!-- CTA Button -->
                            <div class="pt-1 sm:pt-2.5">
                                <a href="{{ $menLink }}" 
                                   class="inline-flex items-center space-x-2 bg-gradient-to-r from-[#F0B429] via-[#E7C77B] to-[#D4AF6A] text-[#2E180E] font-bold text-xs sm:text-sm px-5 sm:px-7 py-2.5 sm:py-3 rounded-full shadow-lg shadow-amber-950/40 hover:shadow-xl hover:scale-103 active:scale-98 transition-all duration-300 tracking-wider uppercase border border-[#FFF6E3]/60 cursor-pointer">
                                    <span>Explore Men's Realm</span>
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WOMEN'S HERITAGE SHOWCASE CARD -->
            <div class="group relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl sm:shadow-2xl shadow-stone-900/30 border border-[#D4AF6A]/40 bg-[#160601] transition-all duration-500 hover:-translate-y-1.5 hover:shadow-amber-950/40">
                <div class="relative min-h-[440px] sm:min-h-0 sm:aspect-[21/11] md:aspect-[16/11] lg:aspect-[16/10] w-full overflow-hidden flex flex-col justify-end">
                    <!-- Image with Smooth Zoom -->
                    <img src="{{ asset(ltrim($womenThumb, '/')) }}" 
                         alt="{{ $womenTitle }}" 
                         loading="eager"
                         decoding="async"
                         class="absolute inset-0 w-full h-full object-cover object-top sm:object-center transform transition-transform duration-700 ease-out group-hover:scale-106">

                    <!-- Gradient Overlays for Supreme Readability -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#160601] via-[#160601]/80 via-55% to-transparent pointer-events-none"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-[#160601]/80 via-[#160601]/30 via-50% to-transparent pointer-events-none"></div>

                    <!-- Ambient Gold Shimmer Border on Hover -->
                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-[#E7C77B]/60 rounded-2xl sm:rounded-3xl transition-colors duration-500 pointer-events-none"></div>

                    <!-- Content Layout -->
                    <div class="relative z-10 p-4 sm:p-6 md:p-8 lg:p-9 flex flex-col justify-end">
                        <div class="space-y-2.5 sm:space-y-3.5 max-w-lg">
                            <!-- Badge -->
                            <div class="inline-flex items-center space-x-1.5 bg-[#3A1E11]/90 border border-[#D4AF6A]/60 px-2.5 py-0.5 sm:px-3.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold text-[#E7C77B] tracking-wider uppercase backdrop-blur-md shadow-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#E7C77B] animate-pulse"></span>
                                <span>Queen's Heritage Domain</span>
                            </div>

                            <!-- Title -->
                            <h3 class="font-serif-royal text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-[#FFF8EB] leading-tight drop-shadow-md">
                                {{ $womenTitle }}
                            </h3>

                            <!-- Subtitle -->
                            <p class="text-[11px] sm:text-xs md:text-sm text-stone-200 line-clamp-2 leading-relaxed font-light">
                                {{ $womenSubtitle }}
                            </p>

                            <!-- Category Quick Badges -->
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 pt-0.5 sm:pt-1">
                                <a href="{{ route('category.show', 'mangalsutras') }}" class="text-[10px] sm:text-[11px] px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full font-semibold transition-all backdrop-blur-md hover:scale-105 inline-flex items-center gap-1 shadow-sm" style="background: rgba(212, 175, 106, 0.28); border: 1.5px solid #E7C77B; color: #FFF8EB !important; text-shadow: 0 1px 3px rgba(0,0,0,0.8); box-shadow: 0 0 10px rgba(212, 175, 106, 0.2);">
                                    <span class="text-[#F5C542] text-[9px]">✦</span>
                                    <span style="color: #FFF8EB !important;">Mangalsutras</span>
                                </a>
                                <a href="{{ route('category.show', 'pendants') }}" class="text-[10px] sm:text-[11px] px-2.5 py-0.5 sm:py-1 rounded-full bg-white/10 hover:bg-white/25 text-[#FAF7F0] border border-white/25 backdrop-blur-md transition-all hover:scale-105" style="color: #FAF7F0 !important;">Pendants</a>
                                <a href="{{ route('category.show', 'merrige-navrati-special') }}" class="text-[10px] sm:text-[11px] px-2.5 py-0.5 sm:py-1 rounded-full bg-white/10 hover:bg-white/25 text-[#FAF7F0] border border-white/25 backdrop-blur-md transition-all hover:scale-105" style="color: #FAF7F0 !important;">Mataji Haar</a>
                                <a href="{{ route('category.show', 'bangles') }}" class="text-[10px] sm:text-[11px] px-2.5 py-0.5 sm:py-1 rounded-full bg-white/10 hover:bg-white/25 text-[#FAF7F0] border border-white/25 backdrop-blur-md transition-all hover:scale-105" style="color: #FAF7F0 !important;">Bangles</a>
                                <a href="{{ route('category.show', 'necklaces-sets') }}" class="text-[10px] sm:text-[11px] px-2.5 py-0.5 sm:py-1 rounded-full bg-white/10 hover:bg-white/25 text-[#FAF7F0] border border-white/25 backdrop-blur-md transition-all hover:scale-105" style="color: #FAF7F0 !important;">Necklaces</a>
                            </div>

                            <!-- CTA Button -->
                            <div class="pt-1 sm:pt-2.5">
                                <a href="{{ $womenLink }}" 
                                   class="inline-flex items-center space-x-2 bg-gradient-to-r from-[#F0B429] via-[#E7C77B] to-[#D4AF6A] text-[#2E180E] font-bold text-xs sm:text-sm px-5 sm:px-7 py-2.5 sm:py-3 rounded-full shadow-lg shadow-amber-950/40 hover:shadow-xl hover:scale-103 active:scale-98 transition-all duration-300 tracking-wider uppercase border border-[#FFF6E3]/60 cursor-pointer">
                                    <span>Explore Women's Realm</span>
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. SHOP BY CATEGORY TILES (Larger Circular Images & Left-Right Slider) -->
    <section class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
            <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Royal Collections</span>
            <h2 class="font-serif-royal text-2xl sm:text-4xl font-bold text-[#4A2C1D] mt-1">
                Shop By Royal Category
            </h2>
            <div class="rangoli-divider">
                <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
            </div>
            <p class="text-xs sm:text-sm text-stone-600">
                Explore magnificent Indian jewellery curated for every ceremony and everyday majesty.
            </p>
        </div>

        <!-- Slider Container with Left & Right Arrows -->
        <div class="relative px-2 sm:px-6" 
             x-data="{ 
                canScrollLeft: false, 
                canScrollRight: true,
                scroll(dir) {
                    const el = this.$refs.catSlider;
                    const amount = el.clientWidth * 0.7;
                    el.scrollBy({ left: dir * amount, behavior: 'smooth' });
                },
                checkScroll() {
                    const el = this.$refs.catSlider;
                    this.canScrollLeft = el.scrollLeft > 15;
                    this.canScrollRight = el.scrollLeft < (el.scrollWidth - el.clientWidth - 15);
                }
             }" 
             x-init="setTimeout(() => checkScroll(), 350)">
            
            <!-- Left Slider Button (Liquid Glass) -->
            <button type="button" 
                    @click="scroll(-1)" 
                    :class="{ 'opacity-0 pointer-events-none': !canScrollLeft, 'opacity-100': canScrollLeft }"
                    class="absolute left-0 sm:-left-2 top-1/2 -translate-y-1/2 z-30 w-8 h-8 sm:w-9 sm:h-9 rounded-full text-[#4A2C1D] hover:text-[#2E180E] hover:scale-110 active:scale-95 flex items-center justify-center transition-all duration-300 cursor-pointer"
                    style="background: rgba(255, 255, 255, 0.45); backdrop-filter: blur(10px) saturate(180%); -webkit-backdrop-filter: blur(10px) saturate(180%); border: 1px solid rgba(255, 255, 255, 0.85); box-shadow: 0 4px 16px rgba(74, 44, 29, 0.15), inset 0 1px 1px rgba(255, 255, 255, 0.9);"
                    aria-label="Scroll left">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Horizontal Scroll Track with Larger Circular Category Cards -->
            <div x-ref="catSlider" 
                 @scroll.debounce.40ms="checkScroll()"
                 class="flex items-center gap-6 sm:gap-8 lg:gap-10 overflow-x-auto scroll-smooth py-4 px-3 no-scrollbar">
                @php
                    /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $categories */
                    $categories = $categories ?? collect();
                @endphp
                @if(count($categories) > 0)
                    @foreach($categories as $cat)
                        <a href="{{ route('category.show', $cat->slug) }}" class="group flex flex-col items-center text-center shrink-0 w-32 sm:w-38 lg:w-44">
                            <!-- Much Bigger Circular Image with Royal Dual Bezel -->
                            <div class="w-28 h-28 sm:w-34 sm:h-34 lg:w-40 lg:h-40 rounded-full p-1 sm:p-1.5 bg-gradient-to-b from-[#FFE7A8] via-[#D4AF6A] to-[#996E2E] shadow-md group-hover:shadow-2xl group-hover:scale-108 transition-all duration-300">
                                <div class="w-full h-full rounded-full overflow-hidden bg-[#FAF7F0] p-1.5 flex items-center justify-center border border-white/60">
                                    <img src="{{ !empty($cat->image) ? asset(ltrim($cat->image, '/')) : asset('images/categories/chains.svg') }}" 
                                         alt="{{ $cat->name }}" 
                                         loading="lazy"
                                         decoding="async"
                                         class="w-full h-full object-cover rounded-full group-hover:scale-110 transition-transform duration-500">
                                </div>
                            </div>
                            <span class="font-serif-royal text-xs sm:text-sm lg:text-[15px] font-bold text-[#4A2C1D] group-hover:text-[#996E2E] mt-3 sm:mt-4 tracking-wide transition line-clamp-1">
                                {{ $cat->name }}
                            </span>
                            <span class="text-[10px] sm:text-[11px] text-stone-500 uppercase tracking-widest mt-0.5 opacity-80 group-hover:opacity-100 font-medium">
                                Explore
                            </span>
                        </a>
                    @endforeach
                @endif
            </div>

            <!-- Right Slider Button (Liquid Glass) -->
            <button type="button" 
                    @click="scroll(1)" 
                    :class="{ 'opacity-0 pointer-events-none': !canScrollRight, 'opacity-100': canScrollRight }"
                    class="absolute right-0 sm:-right-2 top-1/2 -translate-y-1/2 z-30 w-8 h-8 sm:w-9 sm:h-9 rounded-full text-[#4A2C1D] hover:text-[#2E180E] hover:scale-110 active:scale-95 flex items-center justify-center transition-all duration-300 cursor-pointer"
                    style="background: rgba(255, 255, 255, 0.45); backdrop-filter: blur(10px) saturate(180%); -webkit-backdrop-filter: blur(10px) saturate(180%); border: 1px solid rgba(255, 255, 255, 0.85); box-shadow: 0 4px 16px rgba(74, 44, 29, 0.15), inset 0 1px 1px rgba(255, 255, 255, 0.9);"
                    aria-label="Scroll right">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </section>

    <!-- 2.5. CATEGORY TABS SHOWCASE (Interactive Pills Showcase - Exact Reference Design - Customizable from Admin) -->
    @if(($showcaseEnabled ?? true) && !empty($showcaseCategories) && count($showcaseCategories) > 0)
        @php
            $defaultTabId = (string) $showcaseCategories->first()->id;
        @endphp
        <section class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-4" 
                 x-data="{ activeTab: '{{ $defaultTabId }}' }">
            
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-10">
                <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Signature Showcase</span>
                <h2 class="font-serif-royal text-2xl sm:text-4xl font-bold text-[#4A2C1D] mt-1">
                    {{ $showcaseTitle ?? 'Curated Royal Collections' }}
                </h2>
                <div class="rangoli-divider">
                    <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
                </div>
                @if(!empty($showcaseSubtitle))
                    <p class="text-xs sm:text-sm text-stone-600">
                        {{ $showcaseSubtitle }}
                    </p>
                @endif
            </div>

            <!-- Category Filter Pills Row (Exact layout from reference) -->
            <div class="flex items-center justify-center gap-2.5 sm:gap-3.5 flex-wrap mb-8 sm:mb-10">
                @foreach($showcaseCategories as $cat)
                    <button type="button" 
                            @click="activeTab = '{{ $cat->id }}'" 
                            :class="{ 
                                'bg-[#F0B429] text-[#2E180E] border-[#F0B429] shadow-sm font-bold ring-2 ring-[#F0B429]/40': activeTab === '{{ $cat->id }}', 
                                'bg-white text-stone-700 border-stone-300 hover:border-[#D4AF6A] hover:text-[#4A2C1D] font-bold': activeTab !== '{{ $cat->id }}' 
                            }" 
                            class="px-5 sm:px-6 py-2 rounded-full border text-xs sm:text-sm uppercase tracking-wider transition-all duration-200 cursor-pointer">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            <!-- Tab Panels for Each Category with Smooth Zero-Flicker Switch -->
            <div class="relative min-h-[380px] sm:min-h-[440px]">
                @foreach($showcaseCategories as $cat)
                    <div x-show="activeTab === '{{ $cat->id }}'" 
                         x-cloak
                         style="{{ (string)$cat->id !== $defaultTabId ? 'display: none;' : '' }}">
                        
                        @if($cat->products->isEmpty())
                            <div class="text-center py-12 text-stone-500 text-sm bg-white rounded-2xl border border-[#D4AF6A]/30">
                                No products available in this collection yet.
                            </div>
                        @else
                            <!-- 5-column grid on desktop (responsive, matching reference image) -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4 lg:gap-5">
                                @foreach($cat->products as $prod)
                                    <x-product-card :product="$prod" />
                                @endforeach
                            </div>

                            <!-- View All Category Products Footer Button -->
                            <div class="text-center mt-8">
                                <a href="{{ route('category.show', $cat->slug) }}" class="inline-flex items-center space-x-2 px-6 py-2.5 rounded-full bg-white border-2 border-[#4A2C1D] text-[#4A2C1D] hover:bg-[#4A2C1D] hover:text-[#E7C77B] font-bold text-xs uppercase tracking-wider transition shadow-2xs hover:scale-105">
                                    <span>Explore All {{ $cat->name }} ({{ $cat->products_count ?? $cat->products()->count() }})</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

        </section>
    @endif

    <!-- CATEGORY FLASH SALE & TIMED OFFER SECTION (Automated from Admin) -->
    @if(isset($activeOffer) && $activeOffer && $activeOffer->is_live)
        <section class="max-w-[1600px] mx-auto px-2.5 sm:px-6 lg:px-8 py-4">
            <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border-2 border-[#D4AF6A] bg-gradient-to-br from-[#2E180E] via-[#4A2C1D] to-[#1A0B05] text-white p-3.5 sm:p-10">
                
                <!-- Background Ornate Glow Overlay -->
                <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-[#F0B429]/15 blur-3xl pointer-events-none"></div>
                <div class="absolute -left-16 -bottom-16 w-80 h-80 rounded-full bg-rose-600/15 blur-3xl pointer-events-none"></div>

                <!-- Offer Top Header Banner -->
                <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6 pb-8 border-b border-[#D4AF6A]/30">
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center space-x-2 bg-gradient-to-r from-rose-600 to-amber-600 text-white font-bold text-xs uppercase tracking-widest px-3.5 py-1 rounded-full shadow-md mb-3">
                            <span class="animate-ping w-2 h-2 rounded-full bg-white"></span>
                            <span class="inline-flex items-center">{!! $activeOffer->badge_text ?: ('FLASH SALE ' . (int) $activeOffer->discount_percentage . '% OFF') !!}</span>
                        </div>
                        <h2 class="font-serif-royal text-2xl sm:text-4xl font-bold text-[#F5EFE0] leading-tight">
                            {{ $activeOffer->title }}
                        </h2>
                        <p class="text-xs sm:text-sm text-[#E7C77B] mt-1 max-w-xl">
                            {{ $activeOffer->subtitle ?: ('Exclusive timed discount on all handcrafted ' . $activeOffer->category_names . '!') }}
                        </p>
                    </div>

                    <!-- Live Real-Time Countdown Timer Block -->
                    <div class="bg-black/40 backdrop-blur-md rounded-2xl border border-[#D4AF6A]/50 p-4 sm:p-5 text-center shadow-lg shrink-0 w-full sm:w-auto"
                         x-data="{
                            target: new Date('{{ $activeOffer->ends_at->toIso8601String() }}').getTime(),
                            days: 0,
                            hours: 0,
                            minutes: 0,
                            seconds: 0,
                            init() {
                                this.update();
                                setInterval(() => this.update(), 1000);
                            },
                            update() {
                                const now = new Date().getTime();
                                const diff = Math.max(0, this.target - now);
                                this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                            }
                         }">
                        <div class="flex items-center justify-center space-x-1.5 text-xs text-[#E7C77B] uppercase tracking-widest font-semibold mb-2">
                            <svg class="w-3.5 h-3.5 text-[#E7C77B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Offer Ends In</span>
                        </div>
                        <div class="flex items-center justify-center space-x-2 sm:space-x-3 text-center">
                            <div class="bg-[#2E180E] border border-[#D4AF6A]/40 rounded-xl px-2.5 sm:px-3.5 py-2 min-w-[52px]">
                                <span class="font-mono text-xl sm:text-2xl font-bold text-white block" x-text="String(days).padStart(2, '0')">00</span>
                                <span class="text-[9px] uppercase tracking-wider text-stone-400">Days</span>
                            </div>
                            <span class="text-[#D4AF6A] font-bold text-lg">:</span>
                            <div class="bg-[#2E180E] border border-[#D4AF6A]/40 rounded-xl px-2.5 sm:px-3.5 py-2 min-w-[52px]">
                                <span class="font-mono text-xl sm:text-2xl font-bold text-white block" x-text="String(hours).padStart(2, '0')">00</span>
                                <span class="text-[9px] uppercase tracking-wider text-stone-400">Hours</span>
                            </div>
                            <span class="text-[#D4AF6A] font-bold text-lg">:</span>
                            <div class="bg-[#2E180E] border border-[#D4AF6A]/40 rounded-xl px-2.5 sm:px-3.5 py-2 min-w-[52px]">
                                <span class="font-mono text-xl sm:text-2xl font-bold text-white block" x-text="String(minutes).padStart(2, '0')">00</span>
                                <span class="text-[9px] uppercase tracking-wider text-stone-400">Mins</span>
                            </div>
                            <span class="text-[#D4AF6A] font-bold text-lg">:</span>
                            <div class="bg-[#2E180E] border border-[#D4AF6A]/40 rounded-xl px-2.5 sm:px-3.5 py-2 min-w-[52px]">
                                <span class="font-mono text-xl sm:text-2xl font-bold text-[#F0B429] block" x-text="String(seconds).padStart(2, '0')">00</span>
                                <span class="text-[9px] uppercase tracking-wider text-[#F0B429]">Secs</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Discounted Products Showcase Grid -->
                @if(isset($offerProducts) && $offerProducts->isNotEmpty())
                    <div class="relative z-10 pt-8">
                        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                            <span class="text-xs sm:text-sm uppercase font-bold tracking-widest text-[#E7C77B] flex items-center space-x-2">
                                <svg class="w-3.5 h-3.5 text-[#F0B429]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                                <span>Special Discounted {{ $activeOffer->category_names }}</span>
                                <span class="bg-rose-600 text-white text-[10px] px-2 py-0.5 rounded-full font-sans">Flat {{ (int)$activeOffer->discount_percentage }}% Off</span>
                            </span>
                            @if($activeOffer->categories->isNotEmpty())
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @foreach($activeOffer->categories as $c)
                                        <a href="{{ route('category.show', $c->slug) }}" class="text-[11px] font-semibold text-[#F0B429] bg-white/10 hover:bg-white/20 border border-[#D4AF6A]/40 px-2.5 py-1 rounded-full transition flex items-center gap-1">
                                            {{ $c->name }} <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    @endforeach
                                </div>
                            @elseif($activeOffer->category)
                                <a href="{{ route('category.show', $activeOffer->category->slug) }}" class="text-xs font-semibold text-[#F0B429] hover:underline flex items-center space-x-1">
                                    <span>View All {{ $activeOffer->category->name }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-6">
                            @foreach($offerProducts as $prod)
                                <x-product-card :product="$prod" />
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </section>
    @endif

    <!-- 3. NEW ARRIVALS CAROUSEL/GRID -->
    <section class="max-w-7xl mx-auto px-2.5 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-end justify-between mb-8 pb-4 border-b border-[#D4AF6A]/30">
            <div>
                <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Fresh From The Atelier</span>
                <h2 class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#4A2C1D] mt-1">
                    New Arrivals
                </h2>
            </div>
            <a href="{{ route('trending') }}" class="text-xs font-semibold text-[#996E2E] hover:text-[#4A2C1D] flex items-center space-x-1 mt-2 sm:mt-0 transition">
                <span>View Full Showcase</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-6">
            @foreach($newArrivals as $prod)
                <x-product-card :product="$prod" />
            @endforeach
        </div>
    </section>

    <!-- 4. HERITAGE SPOTLIGHT BANNER -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="royal-card rounded-2xl p-6 sm:p-12 bg-gradient-to-r from-[#FAF7F0] via-[#F5EFEB] to-[#FAF7F0] border-2 border-[#D4AF6A]/50 relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="space-y-4 max-w-xl z-10">
                <span class="bg-[#4A2C1D] text-[#E7C77B] text-[10px] uppercase font-bold tracking-widest px-3 py-1 rounded-full inline-flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-[#E7C77B]" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                    <span>Sovereign 1 Gram Technology</span>
                </span>
                <h3 class="font-serif-royal text-2xl sm:text-4xl font-bold text-[#4A2C1D] leading-tight">
                    The Radiance of Real Gold, The Peace of Imitation
                </h3>
                <p class="text-xs sm:text-sm text-stone-700 leading-relaxed">
                    Every piece is crafted in pure non-allergenic copper alloy, coated in genuine 1 gram micron 24k gold bath and fortified with an anti-tarnish protective lacquer.
                </p>
                <div class="pt-2 flex flex-wrap gap-3 text-xs font-medium text-[#4A2C1D]">
                    <span class="flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Zero Skin Irritation</span>
                    </span>
                    <span class="flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Heirloom Weight & Finish</span>
                    </span>
                    <span class="flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Direct UPI QR Verification</span>
                    </span>
                </div>
            </div>

            <div class="relative z-10">
                <a href="{{ route('nav.group', '1-gram-jewellery') }}" class="inline-flex items-center space-x-2 bg-[#4A2C1D] text-[#E7C77B] hover:bg-[#2E180E] px-8 py-4 rounded-full font-bold text-xs uppercase tracking-wider border border-[#D4AF6A] shadow-md transition hover:scale-105">
                    <span>EXPLORE 1 GRAM COLLECTION</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <!-- Mandala Background Accent -->
            <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="absolute -right-16 -bottom-16 w-80 h-80 opacity-10 pointer-events-none" alt="Mandala">
        </div>
    </section>

    <!-- 4.5. CRAFTSMANSHIP VIDEO SHOWCASE -->
    @if(isset($videoProducts) && $videoProducts->isNotEmpty())
        @php
            $previewVideos = $videoProducts->take(8);
            $totalVideoCount = $videoProducts->count();
            // Build a JSON-safe array of ALL videos for the "View All" modal
            $allVideosJson = $videoProducts->map(fn($p) => [
                'name'      => $p->name,
                'embed_url' => $p->youtube_embed_url,
                'price'     => '₹' . number_format($p->price),
                'url'       => route('product.show', $p->slug),
                'thumb'     => $p->youtube_thumbnail_url ?: $p->effective_primary_image,
                'category'  => $p->category?->name ?? 'Royal Jewellery',
            ])->values()->toJson();
        @endphp
        <section class="max-w-[1600px] mx-auto px-0 sm:px-6 lg:px-8 py-2 select-none"
                 x-data="{
                     activeVideo: null,
                     showGrid: false,
                     canScrollLeft: false,
                     canScrollRight: true,
                     allVideos: {{ Js::from($videoProducts->map(fn($p) => [
                         'name'      => $p->name,
                         'embed_url' => $p->youtube_embed_url,
                         'price'     => '₹' . number_format($p->price),
                         'url'       => route('product.show', $p->slug),
                         'thumb'     => $p->youtube_thumbnail_url ?: $p->effective_primary_image,
                         'category'  => $p->category?->name ?? 'Royal Jewellery',
                     ])->values()) }},
                     scroll(dir) {
                         const el = this.$refs.videoSlider;
                         if (!el) return;
                         el.scrollBy({ left: dir * Math.floor(el.clientWidth * 0.8), behavior: 'smooth' });
                         setTimeout(() => this.checkScroll(), 350);
                     },
                     checkScroll() {
                         const el = this.$refs.videoSlider;
                         if (!el) return;
                         this.canScrollLeft  = el.scrollLeft > 8;
                         this.canScrollRight = el.scrollLeft < (el.scrollWidth - el.clientWidth - 8);
                     },
                     openVideo(prod) {
                         this.activeVideo = prod;
                         document.body.style.overflow = 'hidden';
                     },
                     closeVideo() {
                         this.activeVideo = null;
                         if (!this.showGrid) document.body.style.overflow = '';
                     },
                     openGrid() {
                         this.showGrid = true;
                         document.body.style.overflow = 'hidden';
                     },
                     closeGrid() {
                         this.showGrid = false;
                         this.activeVideo = null;
                         document.body.style.overflow = '';
                     }
                 }"
                 x-init="setTimeout(() => checkScroll(), 400)"
                 @touchend.passive="setTimeout(() => checkScroll(), 200)">

            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-6 sm:mb-10 px-4">
                <div class="inline-flex items-center space-x-2 bg-[#FAF7F0] border border-[#D4AF6A]/60 px-4 py-1.5 rounded-full text-[11px] sm:text-xs font-bold uppercase tracking-widest text-[#996E2E] shadow-2xs mb-2">
                    <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                    <span>Royal Atelier Previews</span>
                </div>
                <h2 class="font-serif-royal text-2xl sm:text-4xl font-bold text-[#4A2C1D] mt-1">
                    Craftsmanship In Motion
                </h2>
                <div class="rangoli-divider">
                    <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
                </div>
                <p class="text-xs sm:text-sm text-stone-600 max-w-xl mx-auto">
                    Watch our creations in natural light — see the 1 gram micro gold lustre & royal drape before you choose.
                </p>
            </div>

            <!-- Slider Preview (first 8 cards) -->
            <div class="relative">
                <!-- Left Arrow -->
                <button type="button" @click="scroll(-1)"
                        :class="canScrollLeft ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
                        class="hidden sm:flex absolute left-2 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full items-center justify-center transition-all duration-300 cursor-pointer shadow-lg"
                        style="background:rgba(255,255,255,.75);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(212,175,106,.5);" aria-label="Scroll left">
                    <svg class="w-4 h-4 text-[#4A2C1D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <!-- Scrollable Track -->
                <div x-ref="videoSlider" @scroll.passive="checkScroll()"
                     class="flex items-stretch gap-3 sm:gap-5 overflow-x-auto scroll-smooth pb-4 pt-2 px-4 sm:px-12 no-scrollbar"
                     style="-webkit-overflow-scrolling:touch;touch-action:pan-x;">

                    @foreach($previewVideos as $vProd)
                        @php
                            $thumb   = $vProd->youtube_thumbnail_url ?: $vProd->effective_primary_image;
                            $catName = $vProd->category?->name ?? 'Royal Jewellery';
                        @endphp
                        <div class="group relative shrink-0 rounded-xl sm:rounded-2xl overflow-hidden border border-[#D4AF6A]/40 bg-[#160601] shadow-md hover:shadow-2xl transition-all duration-300 flex flex-col"
                             style="width:clamp(148px,44vw,300px);">

                            <!-- 16:9 Thumbnail -->
                            <div class="relative overflow-hidden cursor-pointer bg-stone-900 shrink-0"
                                 style="aspect-ratio:16/9;"
                                 @click="openVideo({name:'{{ addslashes($vProd->name) }}',embed_url:'{{ $vProd->youtube_embed_url }}',price:'{{ '₹'.number_format($vProd->price) }}',url:'{{ route('product.show',$vProd->slug) }}',category:'{{ addslashes($catName) }}'})">

                                <img src="{{ $thumb }}" alt="{{ $vProd->name }}" loading="lazy" decoding="async"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/15 to-transparent pointer-events-none"></div>

                                <!-- HD badge -->
                                <div class="absolute top-1.5 right-1.5 z-10 pointer-events-none">
                                    <span class="bg-black/75 backdrop-blur-sm text-white text-[9px] sm:text-[10px] font-bold px-1.5 py-0.5 rounded-full flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>HD
                                    </span>
                                </div>

                                <!-- Play button -->
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                                    <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-[#F5D77F] via-[#D4AF6A] to-[#996E2E] flex items-center justify-center shadow-xl border-2 border-white/70 group-hover:scale-110 transition-all duration-300">
                                        <span class="absolute inset-0 rounded-full bg-[#D4AF6A]/30 animate-ping opacity-70"></span>
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 fill-[#2E180E] ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Info bar -->
                            <div class="p-2 sm:p-2.5 bg-[#1A0805] flex items-center justify-between gap-1.5 flex-1 min-w-0">
                                <div class="min-w-0 flex-1 overflow-hidden">
                                    <p class="font-serif-royal font-bold text-[#F5D77F] text-[10px] sm:text-xs line-clamp-1 leading-tight">{{ $vProd->name }}</p>
                                    <p class="font-bold text-white text-[11px] sm:text-sm mt-0.5 leading-tight">₹{{ number_format($vProd->price) }}</p>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button type="button"
                                            @click="openVideo({name:'{{ addslashes($vProd->name) }}',embed_url:'{{ $vProd->youtube_embed_url }}',price:'{{ '₹'.number_format($vProd->price) }}',url:'{{ route('product.show',$vProd->slug) }}',category:'{{ addslashes($catName) }}'})"
                                            class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-[#4A2C1D] hover:bg-[#6B3F2A] text-rose-400 flex items-center justify-center transition cursor-pointer border border-[#D4AF6A]/30 shrink-0" title="Play Video">
                                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </button>
                                    <a href="{{ route('product.show', $vProd->slug) }}"
                                       class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-white/10 hover:bg-white/20 text-[#FFF8EB] flex items-center justify-center transition cursor-pointer border border-white/15 shrink-0" title="View Product">
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($totalVideoCount > 8)
                        <!-- "See All" peek card at end of slider -->
                        <div @click="openGrid()"
                             class="shrink-0 rounded-xl sm:rounded-2xl overflow-hidden border-2 border-dashed border-[#D4AF6A]/60 bg-[#1A0805] hover:bg-[#261008] flex flex-col items-center justify-center gap-2 p-4 cursor-pointer transition-all duration-300 group"
                             style="width:clamp(130px,38vw,200px);aspect-ratio:16/9;">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#D4AF6A]/20 to-[#996E2E]/20 border border-[#D4AF6A]/50 flex items-center justify-center group-hover:scale-110 group-hover:bg-[#D4AF6A]/30 transition-all duration-300">
                                <svg class="w-5 h-5 text-[#F5D77F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            </div>
                            <div class="text-center">
                                <p class="font-bold text-[#F5D77F] text-[11px] sm:text-xs leading-tight">+{{ $totalVideoCount - 8 }} More</p>
                                <p class="text-[10px] sm:text-[11px] text-[#D4AF6A]/80 font-semibold">See All Videos</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Arrow -->
                <button type="button" @click="scroll(1)"
                        :class="canScrollRight ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
                        class="hidden sm:flex absolute right-2 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full items-center justify-center transition-all duration-300 cursor-pointer shadow-lg"
                        style="background:rgba(255,255,255,.75);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(212,175,106,.5);" aria-label="Scroll right">
                    <svg class="w-4 h-4 text-[#4A2C1D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            @if($totalVideoCount > 8)
                <!-- "View All Videos" CTA below slider -->
                <div class="text-center mt-5 sm:mt-6 px-4">
                    <button type="button" @click="openGrid()"
                            class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full border-2 border-[#D4AF6A] bg-[#FAF7F0] hover:bg-[#4A2C1D] hover:border-[#4A2C1D] text-[#4A2C1D] hover:text-[#F5D77F] font-bold text-xs sm:text-sm uppercase tracking-wider transition-all duration-300 shadow-md hover:shadow-lg cursor-pointer group">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        View All {{ $totalVideoCount }} Videos
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            @endif

            <!-- ═══════════════════════════════════════════════════════════
                 ALL VIDEOS GRID MODAL (full-screen, scrollable, all products)
                 ═══════════════════════════════════════════════════════════ -->
            <div x-show="showGrid" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="closeGrid()"
                 class="fixed inset-0 z-[90] flex flex-col overflow-hidden"
                 style="background: rgba(14, 4, 2, 0.94); backdrop-filter: blur(20px) saturate(180%); -webkit-backdrop-filter: blur(20px) saturate(180%);">

                <!-- Grid Modal Header (pinned) -->
                <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-[#D4AF6A]/25 shrink-0 bg-[#160804]/90 backdrop-blur-md">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#F5D77F] to-[#D4AF6A] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 fill-[#2E180E]" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-serif-royal font-bold text-white text-sm sm:text-lg leading-tight">All Craftsmanship Videos</h3>
                            <p class="text-[10px] sm:text-xs text-[#D4AF6A]/80">{{ $totalVideoCount }} videos · Tap any to watch</p>
                        </div>
                    </div>
                    <button type="button" @click="closeGrid()"
                            class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 active:scale-95 text-white flex items-center justify-center transition shrink-0 cursor-pointer border border-white/15 ml-3"
                            aria-label="Close">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Scrollable Grid -->
                <div class="flex-1 overflow-y-auto overscroll-contain p-3 sm:p-5 md:p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2.5 sm:gap-4 max-w-7xl mx-auto">
                        <template x-for="(vid, i) in allVideos" :key="i">
                            <div class="group relative rounded-xl overflow-hidden border border-[#D4AF6A]/30 bg-[#160601] shadow hover:shadow-xl transition-all duration-300 cursor-pointer flex flex-col hover:border-[#D4AF6A]/70 hover:-translate-y-0.5"
                                 @click="openVideo(vid)">

                                <!-- 16:9 thumb -->
                                <div class="relative overflow-hidden bg-stone-900 shrink-0" style="aspect-ratio:16/9;">
                                    <img :src="vid.thumb" :alt="vid.name" loading="lazy" decoding="async"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent pointer-events-none"></div>

                                    <!-- Play icon overlay -->
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-[#F5D77F] to-[#D4AF6A] flex items-center justify-center shadow-lg border-2 border-white/70 group-hover:scale-110 transition-all duration-300 opacity-90">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 fill-[#2E180E] ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>

                                    <!-- HD badge -->
                                    <span class="absolute top-1.5 right-1.5 bg-black/75 text-white text-[8px] font-bold px-1 py-0.5 rounded-full flex items-center gap-1 pointer-events-none">
                                        <span class="w-1 h-1 rounded-full bg-rose-500"></span>HD
                                    </span>
                                </div>

                                <!-- Info -->
                                <div class="p-2 bg-[#1A0805] flex items-center justify-between gap-1 min-w-0 flex-1">
                                    <div class="min-w-0 flex-1 overflow-hidden">
                                        <p class="font-serif-royal text-[#F5D77F] text-[10px] sm:text-xs font-bold line-clamp-1 leading-tight" x-text="vid.name"></p>
                                        <p class="text-white text-[10px] sm:text-[11px] font-bold leading-tight" x-text="vid.price"></p>
                                    </div>
                                    <a :href="vid.url" @click.stop
                                       class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer shrink-0 ml-1"
                                       title="View Product">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Grid Modal Footer (pinned) -->
                <div class="shrink-0 border-t border-[#D4AF6A]/20 bg-[#160804]/90 backdrop-blur-md px-4 py-2.5 flex items-center justify-between gap-3">
                    <p class="text-[11px] text-[#D4AF6A]/80">{{ $totalVideoCount }} craftsmanship videos</p>
                    <button type="button" @click="closeGrid()"
                            class="px-4 py-1.5 rounded-full bg-white/10 hover:bg-white/20 text-stone-300 text-[11px] font-semibold transition cursor-pointer border border-white/15">
                        Close
                    </button>
                </div>
            </div>

            <!-- VIDEO PLAY MODAL — z-[100] > bottom nav (z-40) -->
            <div x-show="activeVideo" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @keydown.escape.window="closeVideo()"
                 class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-6 overflow-y-auto"
                 style="background: radial-gradient(ellipse at center, rgba(30, 12, 6, 0.85) 0%, rgba(10, 4, 2, 0.94) 100%); backdrop-filter: blur(24px) saturate(180%); -webkit-backdrop-filter: blur(24px) saturate(180%);">

                <!-- Tap outside backdrop to close -->
                <div class="fixed inset-0 z-0" @click="closeVideo()" aria-label="Close modal backdrop"></div>

                <!-- Glassmorphism Card Container (All-in-one luxury framed player) -->
                <div class="relative z-10 w-full max-w-2xl my-auto rounded-2xl sm:rounded-3xl border border-[#D4AF6A]/40 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.9),0_0_40px_rgba(212,175,106,0.22)] flex flex-col overflow-hidden"
                     style="background: linear-gradient(160deg, rgba(32, 13, 7, 0.94) 0%, rgba(14, 5, 2, 0.98) 100%); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);"
                     @click.stop>

                    <!-- Header -->
                    <div class="flex items-center justify-between gap-3 px-3.5 py-2.5 sm:px-5 sm:py-3.5 border-b border-[#D4AF6A]/20 bg-white/[0.04]">
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            <span class="relative flex h-2.5 w-2.5 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-serif-royal font-bold text-white text-xs sm:text-sm tracking-wide truncate"
                                   x-text="activeVideo ? activeVideo.name : ''"></p>
                                <p class="text-[10px] text-[#D4AF6A] font-medium hidden sm:block">Rayka Royal Craftsmanship</p>
                            </div>
                        </div>
                        <button type="button" @click="closeVideo()"
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-white/10 hover:bg-white/20 active:scale-95 text-stone-200 hover:text-white flex items-center justify-center transition shrink-0 cursor-pointer border border-white/15"
                                aria-label="Close">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- 16:9 Video Player -->
                    <div class="relative w-full bg-black" style="aspect-ratio: 16/9;">
                        <template x-if="activeVideo">
                            <iframe :src="activeVideo.embed_url + '&autoplay=1&rel=0&modestbranding=1&playsinline=1'"
                                    class="absolute inset-0 w-full h-full"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
                                    allowfullscreen></iframe>
                        </template>
                    </div>

                    <!-- Footer Bar with High-Contrast Pricing & Actions -->
                    <div class="flex items-center justify-between gap-2 sm:gap-4 px-3.5 py-2.5 sm:px-5 sm:py-3.5 border-t border-[#D4AF6A]/20 bg-black/60">
                        <div class="flex items-center gap-2 min-w-0 flex-1">
                            <span class="font-serif-royal font-bold text-[#F5D77F] text-base sm:text-lg shrink-0 tracking-tight"
                                  x-text="activeVideo ? activeVideo.price : ''"></span>
                            <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#D4AF6A]/15 text-[#E6CA85] border border-[#D4AF6A]/30 shrink-0">
                                1 Gram Micro Gold
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                            <a :href="activeVideo ? activeVideo.url : '#'"
                               class="inline-flex items-center gap-1.5 px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-gradient-to-r from-[#F5D77F] via-[#D4AF6A] to-[#B88E3E] text-[#241107] font-bold text-[10px] sm:text-xs uppercase tracking-wider shadow-lg hover:shadow-[#D4AF6A]/30 hover:scale-[1.03] active:scale-95 transition cursor-pointer whitespace-nowrap">
                                <span>View Product</span>
                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            <button type="button" @click="closeVideo()"
                                    class="px-2.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-stone-200 text-[10px] sm:text-xs font-semibold transition cursor-pointer border border-white/15 whitespace-nowrap">
                                Close
                            </button>
                        </div>
                    </div>

                </div>
            </div>



        </section>
    @endif



    <!-- 5. BEST SELLERS / MOST LOVED -->
    <section class="max-w-7xl mx-auto px-2.5 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Heirloom Favorites</span>
            <h2 class="font-serif-royal text-2xl sm:text-4xl font-bold text-[#4A2C1D] mt-1">
                Best Sellers & Most Loved
            </h2>
            <div class="rangoli-divider">
                <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
            </div>
            <p class="text-xs sm:text-sm text-stone-600">
                Treasured by thousands of patrons across India for festive celebrations and bridal trousseaus.
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-6">
            @foreach($bestSellers as $prod)
                <x-product-card :product="$prod" />
            @endforeach
        </div>
    </section>

    <!-- 6. TRUST BADGES ROW -->
    <section class="bg-[#FAF7F0] border-y border-[#D4AF6A]/40 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                @foreach($trustBadges as $badge)
                    <div class="flex items-center space-x-4 p-4 rounded-xl bg-white border border-[#D4AF6A]/30 shadow-2xs">
                        <div class="w-12 h-12 rounded-full bg-[#FAF7F0] border border-[#D4AF6A] flex items-center justify-center shrink-0 text-[#996E2E]">
                            @if($badge['icon'] === 'truck')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                            @elseif($badge['icon'] === 'shield-check')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            @elseif($badge['icon'] === 'refresh')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-serif-royal text-sm font-bold text-[#4A2C1D]">{{ $badge['title'] }}</h4>
                            <p class="text-[11px] text-stone-500 leading-tight mt-0.5">{{ $badge['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 7. CUSTOMER REVIEWS STRIP (Approved Only) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Patron Testimonials</span>
            <h2 class="font-serif-royal text-2xl sm:text-4xl font-bold text-[#4A2C1D] mt-1">
                Words From Our Royal Patrons
            </h2>
            <div class="rangoli-divider">
                <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($customerReviews as $rev)
                <div class="royal-card p-6 rounded-2xl bg-white border border-[#D4AF6A]/30 flex flex-col justify-between">
                    <div>
                        <!-- 5 Stars -->
                        <div class="flex items-center space-x-1 text-[#F0B429] mb-3">
                            @for($i = 0; $i < $rev->rating; $i++)
                                <span><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg></span>
                            @endfor
                        </div>
                        <h4 class="font-serif-royal text-base font-bold text-[#4A2C1D] mb-2">
                            "{{ $rev->title ?: 'Magnificent Jewellery' }}"
                        </h4>
                        <p class="text-xs text-stone-600 leading-relaxed italic">
                            "{{ $rev->comment }}"
                        </p>
                    </div>

                    <div class="pt-4 border-t border-[#D4AF6A]/20 mt-4 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-xs text-[#4A2C1D]">{{ $rev->customer_name }}</p>
                            @if($rev->is_verified_purchase)
                                <span class="text-[10px] text-emerald-700 font-semibold flex items-center space-x-1">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Verified Buyer</span>
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] text-stone-400">{{ $rev->created_at->format('M Y') }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-6 text-stone-500 text-sm">
                    No approved reviews published yet.
                </div>
            @endforelse
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('reviews.all') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-[#996E2E] hover:text-[#4A2C1D] transition uppercase tracking-wider">
                <span>View All Verified Reviews</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </section>

</div>

@push('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "JewelryStore",
  "name": "Rayka Imitation Jewellery",
  "url": "{{ url()->current() }}",
  "logo": "{{ asset('images/rayka-logo.png') }}",
  "description": "Discover opulent imitation jewellery, Rajputana Kundan sets, temple jewellery, and 1 gram micro gold plated chains and bangles by Rayka.",
  "address": {
    "@@type": "PostalAddress",
    "addressLocality": "Ahmedabad",
    "addressRegion": "Gujarat",
    "addressCountry": "IN"
  },
  "contactPoint": {
    "@@type": "ContactPoint",
    "telephone": "{{ $storeSettings['store_phone'] ?? '+91-9638868024' }}",
    "contactType": "Customer Service"
  }
}
</script>
@endpush
@endsection
