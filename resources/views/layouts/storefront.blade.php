<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Rayka Imitation Jewellery — Royal Heritage & 1 Gram Gold Jewels')</title>
    <meta name="description" content="@yield('meta_description', 'Discover opulent imitation jewellery, Rajputana Kundan sets, temple jewellery, and 1 gram micro gold plated chains and bangles by Rayka.')">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Rayka Imitation Jewellery — Royal Heritage & 1 Gram Gold Jewels')">
    <meta property="og:description" content="@yield('meta_description', 'Discover opulent imitation jewellery, Rajputana Kundan sets, temple jewellery, and 1 gram micro gold plated chains and bangles by Rayka.')">
    <meta property="og:image" content="@yield('meta_image', asset('images/rayka-logo.png'))">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', 'Rayka Imitation Jewellery — Royal Heritage & 1 Gram Gold Jewels')">
    <meta name="twitter:description" content="@yield('meta_description', 'Discover opulent imitation jewellery, Rajputana Kundan sets, temple jewellery, and 1 gram micro gold plated chains and bangles by Rayka.')">
    <meta name="twitter:image" content="@yield('meta_image', asset('images/rayka-logo.png'))">

    <!-- Google Search & Browser Identity -->
    <meta name="application-name" content="Rayka Imitation">
    <meta name="apple-mobile-web-app-title" content="Rayka Imitation">
    <meta name="theme-color" content="#4A2C1D">

    <!-- Structured Data (Google Search Snippet Logo & Site Name) -->
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebSite',
                '@id' => url('/') . '/#website',
                'url' => url('/'),
                'name' => 'Rayka Imitation',
                'alternateName' => ['Rayka Imitation Jewellery', 'Rayka', 'Rayka Jewellery'],
                'publisher' => [
                    '@id' => url('/') . '/#organization',
                ],
            ],
            [
                '@type' => 'Organization',
                '@id' => url('/') . '/#organization',
                'name' => 'Rayka Imitation Jewellery',
                'url' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/rayka-logo.png'),
                    'width' => 250,
                    'height' => 250,
                ],
                'image' => asset('images/rayka-logo.png'),
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @stack('schema')

    <!-- Favicon & Touch Icons (Google Search Snippet Compliant: 48px+ multiples) -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/favicon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Professional SaaS UI: Disable default mobile tap highlights globally */
        * { -webkit-tap-highlight-color: transparent; }
    </style>

    <!-- Server Pre-Hydrated Store State (Instant 0ms Cart & Wishlist Sync) -->
    @php
        $navCart = \App\Services\GuestSessionService::getCart();
        $navCartCount = (int) $navCart->items->sum('quantity');
        $navCartItems = [];
        foreach($navCart->items as $ci) {
            $navCartItems[(int)$ci->product_id] = ($navCartItems[(int)$ci->product_id] ?? 0) + (int)$ci->quantity;
        }
        $navWishlist = [];
        if(Auth::check()) {
            $navWishlist = \App\Models\Wishlist::where('user_id', Auth::id())->pluck('product_id')->map(fn($id) => (int)$id)->toArray();
        } else {
            $navWishlist = \App\Models\Wishlist::where('guest_token', \App\Services\GuestSessionService::getGuestToken())->pluck('product_id')->map(fn($id) => (int)$id)->toArray();
        }
    @endphp
    <script>
        window.__INITIAL_RAYKA__ = {
            cartCount: {{ $navCartCount }},
            cartItems: {!! json_encode((object) $navCartItems) !!},
            wishlistCount: {{ count($navWishlist) }},
            wishlistItems: {!! json_encode($navWishlist) !!}
        };
    </script>

    <!-- Compiled CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[#FDFBF6] text-[#4A2C1D] min-h-screen flex flex-col font-sans selection:bg-[#D4AF6A]/30 selection:text-[#4A2C1D] overflow-x-hidden w-full" x-data="{ mobileMenuOpen: false, searchOpen: false }">

    <!-- Luxury Royal Jewellery Preloader -->
    <x-preloader />

    <!-- 2. Main Header -->
    <header class="bg-[#FAF7F0] border-b border-[#D4AF6A]/40 sticky top-0 z-40 shadow-xs w-full">
        <div class="max-w-[1600px] mx-auto px-2.5 sm:px-4 lg:px-6 2xl:px-10">
            <div class="flex items-center justify-between h-16 sm:h-20 gap-2 sm:gap-4 w-full">

                <!-- Left Section: Mobile Menu Toggle + Brand Logo -->
                <div class="flex items-center gap-1 sm:gap-3 shrink-0">
                    <!-- Mobile Hamburger (lg:hidden) -->
                    <button type="button" @click="mobileMenuOpen = true" 
                            class="lg:hidden p-1.5 sm:p-2 text-[#4A2C1D] hover:bg-[#F3ECE1] rounded-lg transition focus:outline-hidden shrink-0 cursor-pointer" 
                            aria-label="Open Mobile Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Brand Logo & Royal Title -->
                    <a href="{{ route('home') }}" class="flex items-center gap-1.5 sm:gap-3 group shrink-0">
                        <img src="{{ asset('images/rayka-logo.png') }}" alt="Rayka Imitation Jewellery" 
                             class="h-8 w-8 sm:h-11 sm:w-11 lg:h-12 lg:w-12 xl:h-13 xl:w-13 object-contain drop-shadow-sm group-hover:scale-105 transition-transform duration-300 shrink-0">
                        <div class="flex flex-col shrink-0">
                            <span class="font-serif-royal text-base sm:text-xl lg:text-2xl xl:text-3xl font-bold tracking-wider text-[#4A2C1D] uppercase leading-none">
                                RAYKA
                            </span>
                            <span class="text-[6.5px] sm:text-[8px] xl:text-[9.5px] uppercase tracking-[0.16em] sm:tracking-[0.25em] text-[#996E2E] mt-0.5 font-medium leading-tight whitespace-nowrap">
                                Imitation Jewellery
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Center Section: Desktop Navigation Bar -->
                <nav class="hidden lg:flex items-center justify-center flex-1 mx-2 xl:mx-4 2xl:mx-6 gap-2 xl:gap-3 2xl:gap-6 text-[11px] xl:text-xs 2xl:text-[13px] font-semibold tracking-normal xl:tracking-wider uppercase text-[#4A2C1D]">
                    <a href="{{ route('home') }}" class="shrink-0 text-[#4A2C1D] hover:text-[#996E2E] transition tracking-wider uppercase font-semibold py-1 border-b-2 {{ request()->routeIs('home') ? 'border-[#D4AF6A] text-[#996E2E]' : 'border-transparent hover:border-[#D4AF6A]' }} whitespace-nowrap">
                        Home
                    </a>

                    @foreach(($globalNavGroups ?? []) as $nav)
                        @php
                            if (!is_object($nav)) { continue; }
                            /** @var \App\Models\NavGroup $nav */
                        @endphp
                        <div class="relative group py-6 shrink-0" x-data="{ open: false }">
                            <a href="{{ route('nav.group', $nav->slug) }}" 
                               @mouseenter="open = true" 
                               @mouseleave="open = false" 
                               class="flex items-center gap-1 text-[#4A2C1D] hover:text-[#996E2E] transition tracking-wider uppercase font-semibold py-1 border-b-2 border-transparent hover:border-[#D4AF6A] whitespace-nowrap">
                                <span>{{ $nav->name }}</span>
                                <svg class="w-3 h-3 text-[#D4AF6A] group-hover:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </a>

                            <!-- Mega-Menu Dropdown Grid -->
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-2"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-2"
                                 @mouseenter="open = true" 
                                 @mouseleave="open = false" 
                                 class="absolute left-1/2 -translate-x-1/2 top-full w-[90vw] max-w-2xl xl:max-w-3xl bg-[#FAF7F0] border border-[#D4AF6A]/50 shadow-2xl rounded-b-xl p-5 sm:p-6 z-50">
                                
                                <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#D4AF6A]/30">
                                    <span class="font-serif-royal text-base font-semibold text-[#4A2C1D]">
                                        Explore {{ $nav->name }} Collection
                                    </span>
                                    <a href="{{ route('nav.group', $nav->slug) }}" class="text-xs text-[#996E2E] hover:underline flex items-center font-medium">
                                        <span class="flex items-center gap-1.5">View All Categories <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></span>
                                    </a>
                                </div>

                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 sm:gap-4">
                                    @forelse($nav->categories as $cat)
                                        @php
                                             if (!is_object($cat)) { continue; }
                                             /** @var \App\Models\Category $cat */
                                        @endphp
                                        <a href="{{ route('category.show', $cat->slug) }}" class="group/cat flex flex-col items-center text-center p-2 rounded-lg hover:bg-white/80 transition border border-transparent hover:border-[#D4AF6A]/40">
                                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full overflow-hidden border border-[#D4AF6A]/50 bg-white p-1 mb-2 group-hover/cat:scale-105 transition-transform duration-300 shadow-xs">
                                                <img src="{{ $cat->image ?: asset('images/categories/chains.svg') }}" alt="{{ $cat->name }}" class="w-full h-full object-cover rounded-full">
                                            </div>
                                            <span class="text-xs font-medium text-[#4A2C1D] group-hover/cat:text-[#996E2E] line-clamp-1">
                                                {{ $cat->name }}
                                            </span>
                                        </a>
                                    @empty
                                        <div class="col-span-4 text-center text-xs text-stone-500 py-4">
                                            Categories loading...
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Trending -->
                    <a href="{{ route('trending') }}" class="shrink-0 text-[#4A2C1D] hover:text-[#996E2E] transition tracking-wider uppercase font-semibold py-1 border-b-2 border-transparent hover:border-[#D4AF6A] flex items-center gap-1 whitespace-nowrap">
                        <span>Trending</span>
                        <span class="bg-[#F0B429] text-[#2E180E] text-[9px] xl:text-[10px] font-bold px-1.5 py-0.5 rounded-sm">HOT</span>
                    </a>

                    <!-- Contact (Available on wide desktops, footer & mobile menu) -->
                    <a href="{{ route('contact') }}" class="hidden 2xl:inline-block shrink-0 text-[#4A2C1D] hover:text-[#996E2E] transition tracking-wider uppercase font-semibold py-1 border-b-2 border-transparent hover:border-[#D4AF6A] whitespace-nowrap">
                        Contact
                    </a>

                    <!-- Reviews (Available on wide desktops, footer & mobile menu) -->
                    <a href="{{ route('reviews.all') }}" class="hidden 2xl:inline-block shrink-0 text-[#4A2C1D] hover:text-[#996E2E] transition tracking-wider uppercase font-semibold py-1 border-b-2 border-transparent hover:border-[#D4AF6A] whitespace-nowrap">
                        Reviews
                    </a>

                    <!-- Track Order -->
                    <a href="{{ route('order.track') }}" class="shrink-0 text-[#4A2C1D] hover:text-[#996E2E] transition tracking-wider uppercase font-semibold py-1 border-b-2 {{ request()->routeIs('order.track*') ? 'border-[#D4AF6A] text-[#996E2E]' : 'border-transparent hover:border-[#D4AF6A]' }} flex items-center gap-1 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span class="hidden 2xl:inline">Track Order</span>
                        <span class="2xl:hidden">Track</span>
                    </a>
                </nav>

                <!-- Right Section: Search & Utility Action Icons -->
                <div class="flex items-center gap-1.5 sm:gap-2.5 2xl:gap-3 shrink-0 z-10">
                    
                    <!-- Search Bar (Desktop 2xl+) -->
                    <div class="hidden 2xl:block relative w-48 shrink-0">
                        <form action="{{ route('search') }}" method="GET">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search jewellery..." 
                                   class="w-full bg-white/90 border border-[#D4AF6A]/50 rounded-full pl-8 pr-3 py-1.5 text-xs text-[#4A2C1D] placeholder-stone-400 focus:outline-hidden focus:border-[#D4AF6A] focus:bg-white shadow-2xs">
                            <button type="submit" class="absolute left-2.5 top-2 text-[#996E2E]" aria-label="Search">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Search Icon Button for Screens below 2xl (Mobile, Tablets, Laptops) -->
                    <button type="button" @click="searchOpen = !searchOpen" 
                            class="2xl:hidden p-1.5 sm:p-2 text-[#4A2C1D] hover:text-[#996E2E] hover:bg-[#F3ECE1] rounded-lg transition focus:outline-hidden shrink-0 cursor-pointer" 
                            aria-label="Toggle Search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>

                    <!-- Wishlist Icon with Live Count -->
                    <a href="{{ route('wishlist') }}" 
                       class="relative p-1.5 sm:p-2 text-[#4A2C1D] hover:text-[#996E2E] hover:bg-[#F3ECE1] rounded-lg transition shrink-0 cursor-pointer" 
                       title="My Wishlist">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <span x-text="$store.rayka.wishlistCount" x-show="$store.rayka.wishlistCount > 0" 
                              class="absolute top-0.5 right-0.5 bg-[#D4AF6A] text-[#2E180E] font-bold text-[9px] sm:text-[10px] w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full flex items-center justify-center shadow-xs">0</span>
                    </a>

                    <!-- Account Dropdown (Desktop & Tablets) -->
                    <div class="relative shrink-0 hidden sm:block" x-data="{ userMenu: false }">
                        <button type="button" @click="userMenu = !userMenu" @click.outside="userMenu = false" 
                                class="p-1.5 sm:p-2 text-[#4A2C1D] hover:text-[#996E2E] hover:bg-[#F3ECE1] rounded-lg transition flex items-center focus:outline-hidden cursor-pointer" 
                                title="Account">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </button>
                        <div x-show="userMenu" 
                             x-transition
                             class="absolute right-0 mt-2 w-52 bg-[#FAF7F0] border border-[#D4AF6A]/50 shadow-xl rounded-lg py-2 z-50 text-xs">
                            @auth
                                <div class="px-4 py-2 border-b border-[#D4AF6A]/30">
                                    <p class="font-bold text-[#4A2C1D] truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[11px] text-stone-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('account.orders') }}" class="flex items-center space-x-2 px-4 py-2.5 hover:bg-white text-[#4A2C1D] hover:text-[#996E2E] transition">
                                    <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <span>My Orders & Track Status</span>
                                </a>
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 px-4 py-2.5 bg-amber-50 font-semibold text-amber-900 hover:bg-amber-100 transition border-y border-amber-200">
                                        <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        <span>Admin Administration</span>
                                    </a>
                                @endif
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2.5 hover:bg-rose-50 text-rose-800 transition">
                                        Log Out
                                    </button>
                                </form>
                            @else
                                <div class="px-4 py-2 border-b border-[#D4AF6A]/30">
                                    <p class="font-semibold text-[#4A2C1D]">Royal Customer Account</p>
                                    <p class="text-[10px] text-stone-500">Access orders & track shipments</p>
                                </div>
                                <a href="{{ route('login') }}" class="block px-4 py-2.5 hover:bg-white text-[#4A2C1D] font-medium hover:text-[#996E2E] transition">
                                    Sign In to Account
                                </a>
                                <a href="{{ route('order.track') }}" class="block px-4 py-2.5 hover:bg-white text-stone-700 font-medium hover:text-[#996E2E] transition">
                                    Track Consignment
                                </a>
                                <a href="{{ route('register') }}" class="block px-4 py-2.5 hover:bg-white text-[#996E2E] font-medium transition">
                                    Create Account
                                </a>
                            @endauth
                        </div>
                    </div>

                    <!-- Cart Icon with Live Count (Always Visible on all devices) -->
                    <a href="{{ route('cart') }}" 
                       class="relative p-1.5 sm:p-2 text-[#4A2C1D] hover:text-[#996E2E] hover:bg-[#F3ECE1] rounded-lg transition shrink-0 flex items-center justify-center bg-[#F3ECE1]/60 border border-[#D4AF6A]/40 hover:border-[#D4AF6A] cursor-pointer" 
                       title="Royal Shopping Bag">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <span x-text="$store.rayka.cartCount" x-show="$store.rayka.cartCount > 0" 
                              class="absolute -top-1 -right-1 bg-[#4A2C1D] text-[#E7C77B] font-bold text-[9px] sm:text-[10px] w-4 h-4 sm:w-4.5 sm:h-4.5 rounded-full flex items-center justify-center shadow-xs border border-[#D4AF6A]">0</span>
                    </a>
                </div>

            </div>
        </div>

        <!-- Search Dropdown Input (Active on click for mobile, tablet, and laptops under 2xl) -->
        <div x-show="searchOpen" x-transition class="2xl:hidden border-t border-[#D4AF6A]/30 p-3 bg-white shadow-inner">
            <form action="{{ route('search') }}" method="GET" class="relative max-w-xl mx-auto">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search Royal jewellery collection..." 
                       class="w-full bg-[#FAF7F0] border border-[#D4AF6A]/50 rounded-full pl-10 pr-4 py-2 text-sm text-[#4A2C1D] focus:outline-hidden focus:border-[#D4AF6A]">
                <button type="submit" class="absolute left-3.5 top-2.5 text-[#996E2E]" aria-label="Submit Search">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </header>

    <!-- 3. Mobile Navigation Drawer -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-x-full"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 -translate-x-full"
         class="fixed inset-0 z-50 bg-black/60 lg:hidden flex">
        
        <div class="w-4/5 max-w-sm bg-[#FAF7F0] h-full shadow-2xl overflow-y-auto flex flex-col justify-between" @click.outside="mobileMenuOpen = false">
            <div>
                <div class="p-4 border-b border-[#D4AF6A]/30 flex items-center justify-between bg-white">
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('images/rayka-logo.png') }}" class="h-10 w-10 object-contain" alt="Logo">
                        <span class="font-serif-royal font-bold text-lg text-[#4A2C1D]">RAYKA JEWELLERY</span>
                    </div>
                    <button type="button" @click="mobileMenuOpen = false" class="p-1.5 text-stone-500 hover:text-stone-900">
                        ✕
                    </button>
                </div>

                <div class="p-4 space-y-3">
                    <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="flex items-center justify-between py-2 text-left font-serif-royal text-base font-semibold {{ request()->routeIs('home') ? 'text-[#996E2E]' : 'text-[#4A2C1D]' }} border-b border-[#D4AF6A]/30">
                        <span>Home</span>
                        <svg class="w-4 h-4 text-[#D4AF6A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>

                    <p class="text-[11px] font-bold uppercase tracking-wider text-[#996E2E] pt-1">Shop Collections</p>
                    @foreach(($globalNavGroups ?? []) as $nav)
                        @php
                            if (!is_object($nav)) { continue; }
                            /** @var \App\Models\NavGroup $nav */
                        @endphp
                        <div x-data="{ expanded: false }" class="border-b border-[#D4AF6A]/20 pb-2">
                            <button @click="expanded = !expanded" class="w-full flex items-center justify-between py-2 text-left font-serif-royal text-base font-semibold text-[#4A2C1D]">
                                <span>{{ $nav->name }}</span>
                                <span x-text="expanded ? '−' : '+'" class="text-lg text-[#D4AF6A]"></span>
                            </button>
                            <div x-show="expanded" class="pl-3 py-2 space-y-2 grid grid-cols-2 gap-2">
                                @foreach($nav->categories as $cat)
                                    @php
                                        if (!is_object($cat)) { continue; }
                                        /** @var \App\Models\Category $cat */
                                    @endphp
                                    <a href="{{ route('category.show', $cat->slug) }}" @click="mobileMenuOpen = false" class="text-xs text-[#4A2C1D] hover:text-[#996E2E] py-1 block">
                                        • {{ $cat->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="pt-2 space-y-2.5">
                        <a href="{{ route('trending') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-2 font-medium text-sm text-[#4A2C1D] hover:text-[#996E2E]">
                            <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.527.817-.855 1.76-1.077 2.65-.246.993-.385 1.954-.45 2.74a4.992 4.992 0 00-1.745-.98c-.4-.146-.84.092-.93.518-.32 1.53-.13 3.19.68 4.542A6.002 6.002 0 0013 18a6.002 6.002 0 005.99-5.32 7.02 7.02 0 00-.77-3.082 8.01 8.01 0 00-2.32-2.905 10.96 10.96 0 00-2.82-1.892 1 1 0 00-.685-.248z" clip-rule="evenodd"/></svg>
                            <span>Trending Products</span>
                        </a>
                        <a href="{{ route('reviews.all') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-2 font-medium text-sm text-[#4A2C1D] hover:text-[#996E2E]">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span>Customer Reviews</span>
                        </a>
                        <a href="{{ route('contact') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-2 font-medium text-sm text-[#4A2C1D] hover:text-[#996E2E]">
                            <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Contact Us</span>
                        </a>
                        <a href="{{ route('faq') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-2 font-medium text-sm text-[#4A2C1D] hover:text-[#996E2E]">
                            <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Frequently Asked Questions (FAQ)</span>
                        </a>
                        <a href="{{ route('order.track') }}" @click="mobileMenuOpen = false" class="flex items-center space-x-2 font-medium text-sm text-[#4A2C1D] hover:text-[#996E2E]">
                            <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Track Order Consignment</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-[#D4AF6A]/30 bg-[#F5EFEB] text-xs">
                @auth
                    <p class="font-medium text-[#4A2C1D]">Logged in as {{ Auth::user()->name }}</p>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="text-rose-700 font-semibold underline">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block w-full py-2 text-center bg-[#4A2C1D] text-[#E7C77B] rounded-sm font-semibold tracking-wider uppercase mb-2">Sign In</a>
                    <a href="{{ route('register') }}" class="block w-full py-2 text-center border border-[#D4AF6A] text-[#4A2C1D] rounded-sm font-semibold tracking-wider uppercase">Register</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- 4. Main Page Content -->
    <main class="flex-1 pb-16 lg:pb-0">
        <!-- Session Flash Alerts channeled through Global Toast (skipped on auth routes to avoid duplicate card errors) -->
        @if(session('success'))
            <div x-data x-init="$nextTick(() => { if (window.Alpine && Alpine.store('rayka')) { Alpine.store('rayka').showToast('{{ addslashes(session('success')) }}', 'success'); } })"></div>
        @endif
        @if(session('error') && !request()->routeIs('login*') && !request()->routeIs('register*') && !request()->routeIs('password.*'))
            <div x-data x-init="$nextTick(() => { if (window.Alpine && Alpine.store('rayka')) { Alpine.store('rayka').showToast('{{ addslashes(session('error')) }}', 'error'); } })"></div>
        @endif

        @yield('content')
    </main>

    <!-- 5. Heritage Boutique Footer -->
    <footer class="bg-[#1C0B05] text-[#FAF7F0] border-t-2 border-[#D4AF6A] pt-14 pb-20 lg:pb-10 mt-16 relative overflow-hidden shadow-2xl">
        <!-- Rangoli Mandala Watermark Background -->
        <div class="absolute -bottom-24 -right-24 opacity-[0.04] pointer-events-none">
            <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" alt="Watermark" class="w-96 h-96">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Top Heritage Accent Divider -->
            <div class="relative flex items-center justify-center mb-12">
                <div class="w-full border-t border-[#D4AF6A]/20"></div>
                <div class="absolute bg-[#1C0B05] px-4 flex items-center space-x-2 text-[#E7C77B]">
                    <span class="text-[10px] text-[#D4AF6A]">❖</span>
                    <span class="font-serif-royal text-[11px] tracking-[0.25em] uppercase text-[#D4AF6A] font-semibold">Heritage Craftsmanship & Royal Grandeur</span>
                    <span class="text-[10px] text-[#D4AF6A]">❖</span>
                </div>
            </div>

            <!-- Main Footer 12-Column Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-10 pb-12 border-b border-[#D4AF6A]/20">
                
                <!-- Column 1: Brand & Philosophy (Span 4) -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="flex items-center space-x-3.5">
                        <img src="{{ asset('images/rayka-logo.png') }}" alt="Rayka Crest" class="h-12 w-12 object-contain drop-shadow">
                        <div>
                            <span class="font-serif-royal text-2xl font-bold tracking-wider text-[#E7C77B] block leading-tight">RAYKA</span>
                            <p class="text-[10px] tracking-[0.25em] text-[#D4AF6A] uppercase font-medium">Imitation Jewellery</p>
                        </div>
                    </div>

                    <p class="text-xs text-stone-300 leading-relaxed">
                        Celebrating the opulent majesty of Indian regal craftsmanship. Specializing in 1/2 gram and 1 gram micro gold plated jewellery, Rajwadi chains, Kundan sets, and bridal masterworks delivered across India.
                    </p>

                    <div class="space-y-2.5 pt-1 text-xs">
                        <div class="flex items-start space-x-2.5 text-stone-300">
                            <svg class="w-4 h-4 text-[#D4AF6A] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="leading-relaxed">{{ $storeSettings['store_address'] ?? 'Shop No. 29, Shreeji Bapa Complex, Near Rita Nagar Bus Stand, Vastral Road, Amraiwadi, Ahmedabad - 380026, Gujarat' }}</span>
                        </div>

                        <div class="flex items-center flex-wrap gap-2 pt-1">
                            <a href="{{ $storeSettings['google_map_url'] ?? 'https://share.google/vaohJv28SH29hBV8j' }}" target="_blank" rel="noopener noreferrer" 
                               class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-[#2E180E] border border-[#D4AF6A]/40 text-[#E7C77B] hover:bg-[#3A1C0E] hover:border-[#E7C77B] transition text-xs group">
                                <svg class="w-3.5 h-3.5 text-[#D4AF6A] group-hover:scale-110 transition" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <span>Google Maps</span>
                            </a>
                            <a href="{{ $storeSettings['instagram_url'] ?? 'https://www.instagram.com/rayka_imitation_amdavad/?hl=en' }}" target="_blank" rel="noopener noreferrer" 
                               class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-[#2E180E] border border-[#D4AF6A]/40 text-pink-300 hover:text-pink-200 hover:bg-[#3A1C0E] hover:border-pink-400 transition text-xs group">
                                <svg class="w-3.5 h-3.5 fill-current text-pink-400 group-hover:scale-110 transition" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                <span>{{ $storeSettings['instagram_handle'] ?? '@rayka_imitation_amdavad' }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Quick Links (Span 2) -->
                <div class="lg:col-span-2 space-y-3.5 text-xs">
                    <h4 class="font-serif-royal text-sm font-semibold text-[#E7C77B] tracking-wider uppercase pb-1.5 border-b border-[#D4AF6A]/30 flex items-center justify-between">
                        <span>Shop Curations</span>
                    </h4>
                    <ul class="space-y-2.5 text-stone-300">
                        <li>
                            <a href="{{ route('nav.group', 'men') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Men's Chains & Kadas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('nav.group', 'women') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Women's Chokers & Sets</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('nav.group', '1-gram-jewellery') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>1 Gram Micro Gold</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('trending') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Trending Masterpieces</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reviews.all') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Verified Customer Reviews</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Customer Care & Policies (Span 3) -->
                <div class="lg:col-span-3 space-y-3.5 text-xs">
                    <h4 class="font-serif-royal text-sm font-semibold text-[#E7C77B] tracking-wider uppercase pb-1.5 border-b border-[#D4AF6A]/30 flex items-center justify-between">
                        <span>Heritage Concierge</span>
                    </h4>
                    <ul class="space-y-2.5 text-stone-300">
                        <li>
                            <a href="{{ route('order.track') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Track Consignment & Live Status</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('policy', 'shipping-policy') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Express Shipping Policy</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('policy', 'return-replacement-policy') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>7-Day Replacement Guarantee</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('policy', 'refund-policy') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Refund & Cancellation Policy</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('policy', 'privacy-policy') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Privacy & Security Policy</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('policy', 'terms') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Terms & Conditions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('faq') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Frequently Asked Questions (FAQ)</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}" class="hover:text-[#E7C77B] hover:translate-x-1 inline-flex items-center space-x-1.5 transition duration-200">
                                <span class="text-[#D4AF6A] text-[10px]">›</span>
                                <span>Contact Us & Showroom Visit</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: WhatsApp, Social & Payments (Span 3) -->
                <div class="lg:col-span-3 space-y-4 text-xs">
                    <h4 class="font-serif-royal text-sm font-semibold text-[#E7C77B] tracking-wider uppercase pb-1.5 border-b border-[#D4AF6A]/30 flex items-center justify-between">
                        <span>Stay Connected</span>
                    </h4>
                    <p class="text-stone-300 leading-relaxed">
                        WhatsApp orders, bespoke bridal sizing assistance, and daily jewellery previews.
                    </p>
                    
                    <!-- Direct Action Badges -->
                    @if(!empty($storeSettings['clean_whatsapp']) || !empty($storeSettings['instagram_url']))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-0.5">
                        @if(!empty($storeSettings['clean_whatsapp']))
                        <a href="{{ $storeSettings['whatsapp_url'] ?? ('https://wa.me/'.$storeSettings['clean_whatsapp']) }}" target="_blank" rel="noopener noreferrer" 
                           class="flex items-center justify-center space-x-1.5 bg-[#0F7A6A] hover:bg-[#0d6b5d] text-white px-3 py-2 rounded-lg font-medium text-xs transition border border-[#25D366]/40 shadow-xs group">
                            <svg class="w-4 h-4 fill-current group-hover:scale-110 transition" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            <span>WhatsApp</span>
                        </a>
                        @endif
                        @if(!empty($storeSettings['instagram_url']))
                        <a href="{{ $storeSettings['instagram_url'] }}" target="_blank" rel="noopener noreferrer" 
                           class="flex items-center justify-center space-x-1.5 bg-gradient-to-r from-[#833ab4] via-[#fd1d1d] to-[#fcb045] hover:opacity-95 text-white px-3 py-2 rounded-lg font-medium text-xs transition border border-pink-400/40 shadow-xs group">
                            <svg class="w-4 h-4 fill-current group-hover:scale-110 transition" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                            <span>Instagram</span>
                        </a>
                        @endif
                    </div>
                    @endif

                    <!-- Helpline Numbers -->
                    @if(!empty($storeSettings['store_phone']))
                    <div class="pt-1 text-xs">
                        <span class="text-stone-400 block text-[11px] mb-1">Direct Helplines:</span>
                        <div class="flex items-center flex-wrap gap-x-2.5 gap-y-1 text-[#E7C77B] font-medium">
                            <a href="tel:{{ $storeSettings['clean_phone'] ?? preg_replace('/[^0-9]/', '', $storeSettings['store_phone']) }}" class="hover:underline flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 text-[#E7C77B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span>{{ $storeSettings['store_phone'] }}</span>
                            </a>
                            @if(!empty($storeSettings['store_alt_phone']))
                                <span class="text-stone-500">•</span>
                                <a href="tel:{{ preg_replace('/[^0-9]/', '', $storeSettings['store_alt_phone']) }}" class="hover:underline flex items-center space-x-1">
                                    <span>{{ $storeSettings['store_alt_phone'] }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Payment Modes Accepted -->
                    <div class="pt-1.5">
                        <span class="text-stone-400 block text-[11px] mb-1.5">100% Verified Payment Modes:</span>
                        <div class="flex items-center flex-wrap gap-1.5 text-[10px] text-[#E7C77B]">
                            <span class="bg-[#2E180E] px-2 py-1 rounded border border-[#D4AF6A]/30">Direct UPI</span>
                            <span class="bg-[#2E180E] px-2 py-1 rounded border border-[#D4AF6A]/30">GPay</span>
                            <span class="bg-[#2E180E] px-2 py-1 rounded border border-[#D4AF6A]/30">PhonePe</span>
                            <span class="bg-[#2E180E] px-2 py-1 rounded border border-[#D4AF6A]/30">Paytm</span>
                            <span class="bg-[#2E180E] px-2 py-1 rounded border border-[#D4AF6A]/30">Static QR Code</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright & Designer Attribution Bar -->
            <div class="pt-5 border-t border-[#D4AF6A]/15 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-stone-400">
                <p class="text-center sm:text-left text-[11px] sm:text-xs">
                    © {{ date('Y') }} <span class="text-[#E7C77B] font-medium">{{ $storeSettings['store_name'] ?? 'Rayka Imitation Jewellery' }}</span>. All Rights Reserved. Crafted with Royal Pride in India.
                </p>
                
                <div class="flex items-center justify-center sm:justify-end flex-wrap gap-2 sm:gap-3 text-[11px] sm:text-xs">
                    <span class="whitespace-nowrap text-stone-400">Designed &amp; Crafted by</span>
                    <a href="https://nexdevstudio.in" target="_blank" rel="noopener noreferrer" 
                       class="inline-block px-3 py-1 rounded-full text-[#FAF7F0] hover:text-[#FFE7A8] font-medium text-xs tracking-wide transition-all duration-300 hover:scale-105 active:scale-95 cursor-pointer whitespace-nowrap"
                       style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(212, 175, 106, 0.45); box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25), inset 0 1px 1px rgba(255, 255, 255, 0.2);"
                       title="Visit NexDev Studio">
                        nexdevstudio.in
                    </a>
                    <span class="text-stone-600 hidden sm:inline">•</span>
                    <a href="{{ route('admin.login') }}" class="text-stone-400 hover:text-[#E7C77B] transition text-[11px] px-2 py-0.5 rounded hover:bg-white/5 whitespace-nowrap">
                        Admin Portal
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- 6. Mobile Sticky Bottom Bar (Home / Categories / Wishlist / Account) -->
    <div class="mobile-bottom-bar lg:hidden fixed bottom-0 left-0 right-0 bg-[#FAF7F0] border-t border-[#D4AF6A]/50 py-2 px-6 flex items-center justify-around z-40 shadow-2xl">
        <a href="{{ route('home') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('home') ? 'text-[#996E2E] font-bold' : 'text-[#4A2C1D]' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Home</span>
        </a>

        <button type="button" @click="mobileMenuOpen = true" class="flex flex-col items-center text-[10px] text-[#4A2C1D]">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
            <span>Categories</span>
        </button>

        <a href="{{ route('wishlist') }}" class="flex flex-col items-center text-[10px] relative {{ request()->routeIs('wishlist') ? 'text-[#996E2E] font-bold' : 'text-[#4A2C1D]' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <span>Wishlist</span>
            <span x-text="$store.rayka.wishlistCount" x-show="$store.rayka.wishlistCount > 0" class="absolute -top-1 right-2 bg-[#D4AF6A] text-[#2E180E] font-bold text-[9px] w-3.5 h-3.5 rounded-full flex items-center justify-center">0</span>
        </a>

        <a href="{{ route('cart') }}" class="flex flex-col items-center text-[10px] relative {{ request()->routeIs('cart') ? 'text-[#996E2E] font-bold' : 'text-[#4A2C1D]' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            <span>Bag</span>
            <span x-text="$store.rayka.cartCount" x-show="$store.rayka.cartCount > 0" class="absolute -top-1 right-1 bg-[#4A2C1D] text-[#E7C77B] font-bold text-[9px] w-3.5 h-3.5 rounded-full flex items-center justify-center">0</span>
        </a>

        <a href="{{ route('account.orders') }}" class="flex flex-col items-center text-[10px] {{ request()->routeIs('account*') ? 'text-[#996E2E] font-bold' : 'text-[#4A2C1D]' }}">
            <svg class="w-5 h-5 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span>Account</span>
        </a>
    </div>

    <!-- 7. Global Luxury Toast Notification Component (Professional SaaS Bottom Side Position) -->
    <div x-cloak 
         x-show="$store.rayka.toastVisible" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 sm:translate-y-4 scale-95"
         class="fixed bottom-20 sm:bottom-6 left-3 right-3 sm:left-auto sm:right-6 sm:inset-x-auto z-[9999] pointer-events-none flex justify-center sm:justify-end">
        <div :class="{
                'bg-[#2A170E]/95 border-[#D4AF6A] text-[#FAF7F0] shadow-[0_12px_35px_rgba(42,23,14,0.6)]': $store.rayka.toastType === 'success',
                'bg-rose-950/95 border-rose-500 text-white shadow-[0_12px_35px_rgba(136,19,55,0.5)]': $store.rayka.toastType === 'error',
                'bg-[#FAF7F0]/95 border-[#D4AF6A] text-[#4A2C1D] shadow-[0_12px_35px_rgba(74,44,29,0.3)]': $store.rayka.toastType === 'info'
             }" 
             class="pointer-events-auto backdrop-blur-md border-2 rounded-2xl p-3.5 sm:p-4 w-full sm:w-auto sm:min-w-[320px] sm:max-w-md flex items-center justify-between gap-3 transition-all duration-300">
            
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <!-- Icon badge -->
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 border"
                     :class="{
                         'bg-[#D4AF6A]/20 border-[#D4AF6A]/60 text-[#E7C77B]': $store.rayka.toastType === 'success',
                         'bg-rose-500/20 border-rose-400 text-rose-300': $store.rayka.toastType === 'error',
                         'bg-[#4A2C1D]/10 border-[#D4AF6A]/60 text-[#996E2E]': $store.rayka.toastType === 'info'
                     }">
                    <template x-if="$store.rayka.toastType === 'success'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </template>
                    <template x-if="$store.rayka.toastType === 'error'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </template>
                    <template x-if="$store.rayka.toastType === 'info'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </template>
                </div>

                <!-- Text -->
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-semibold leading-snug break-words" x-text="$store.rayka.toastMessage"></p>
                </div>
            </div>

            <!-- Quick Action (e.g. View Bag when adding to cart) -->
            <template x-if="$store.rayka.toastType === 'success' && $store.rayka.toastMessage.toLowerCase().includes('bag') && !window.location.pathname.includes('/cart') && !window.location.pathname.includes('/checkout')">
                <a href="{{ route('cart') }}" 
                   class="shrink-0 px-3 py-1 rounded-lg bg-[#E7C77B] text-[#2A170E] hover:bg-white text-[11px] font-bold uppercase tracking-wider transition shadow-xs active:scale-95">
                    View
                </a>
            </template>

            <!-- Close button -->
            <button type="button" 
                    @click="$store.rayka.toastVisible = false" 
                    class="p-1 rounded-lg text-stone-400 hover:text-white hover:bg-white/10 transition shrink-0 cursor-pointer"
                    title="Dismiss">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Instant SaaS 0ms Page & Tab Transitions (Instant Hover & Touch Prefetcher) -->
    <script>
        (function() {
            const prefetched = new Set();
            function prefetch(url) {
                if (!url || prefetched.has(url)) return;
                if (url.includes('/logout') || url.includes('/cart/') || url.includes('/checkout') || url.includes('#')) return;
                try {
                    const u = new URL(url, window.location.origin);
                    if (u.origin !== window.location.origin) return;
                    prefetched.add(url);
                    const link = document.createElement('link');
                    link.rel = 'prefetch';
                    link.href = url;
                    document.head.appendChild(link);
                } catch(e) {}
            }
            document.addEventListener('mouseover', function(e) {
                const a = e.target.closest('a');
                if (a && a.href) prefetch(a.href);
            }, { passive: true });
            document.addEventListener('touchstart', function(e) {
                const a = e.target.closest('a');
                if (a && a.href) prefetch(a.href);
            }, { passive: true });
        })();
    </script>

    <!-- Customer Intended URL & Section State Tracker -->
    <script>
        (function() {
            try {
                const path = window.location.pathname;
                if (!path.includes('/login') && !path.includes('/register') && !path.includes('/logout') && !path.includes('/password/')) {
                    if (path.startsWith('/account') || path.startsWith('/checkout') || path.startsWith('/cart') || path.startsWith('/track-order')) {
                        sessionStorage.setItem('rayka_customer_last_url', window.location.href);
                    }
                }
            } catch(e) {}
        })();
    </script>


    @stack('scripts')
</body>
</html>
