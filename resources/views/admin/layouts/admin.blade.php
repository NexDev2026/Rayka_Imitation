<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Administration') — Rayka Royal Portal</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('images/favicon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        * { -webkit-tap-highlight-color: transparent; }
        button, [type='button'], [type='reset'], [type='submit'], [role='button'], select, summary, a {
            cursor: pointer;
        }
        button:disabled, [type='button']:disabled, [type='submit']:disabled {
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-stone-100 text-stone-800 font-sans min-h-screen flex overflow-x-hidden" x-data="{ sidebarOpen: false }">

    <!-- 1. SIDEBAR -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 h-[100dvh] lg:h-screen bg-[#261007] text-[#FAF7F0] border-r border-[#D4AF6A]/30 flex flex-col transition-transform duration-300 lg:sticky lg:top-0 lg:translate-x-0"
           :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">
        
        <!-- Admin Brand Crest -->
        <div class="p-5 border-b border-[#D4AF6A]/20 flex items-center justify-between shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                <img src="{{ asset('images/rayka-logo.png') }}" alt="Rayka" class="h-10 w-10 object-contain">
                <div>
                    <span class="font-serif-royal font-bold text-lg text-[#E7C77B] tracking-wider block">RAYKA ADMIN</span>
                    <span class="text-[9px] uppercase tracking-widest text-stone-400">Royal Control Center</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-stone-400 hover:text-white p-1">✕</button>
        </div>

        <!-- Sidebar Navigation Links (Fully Scrollable) -->
        <nav class="flex-1 overflow-y-auto overscroll-contain p-3 space-y-1 text-xs">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg></span>
                    <span>Dashboard & Analytics</span>
                </a>

                <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.orders*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <div class="flex items-center space-x-3">
                        <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg></span>
                        <span>Orders & QR Proofs</span>
                    </div>
                    @php $pendingOrders = \App\Models\Order::where('status', 'Pending Verification')->count(); @endphp
                    @if($pendingOrders > 0)
                        <span class="bg-amber-500 text-black font-bold text-[10px] px-2 py-0.5 rounded-full">{{ $pendingOrders }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.products.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.products*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" /></svg></span>
                    <span>Product Catalog</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.categories*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 00-1.883 2.542l.857 6a2.25 2.25 0 002.227 1.932H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-1.883-2.542m-16.5 0V6A2.25 2.25 0 016 3.75h3.879a1.5 1.5 0 011.06.44l2.122 2.12a1.5 1.5 0 001.06.44H18A2.25 2.25 0 0120.25 9v.776" /></svg></span>
                    <span>Category Manager</span>
                </a>

                <a href="{{ route('admin.nav_menu.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.nav_menu*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" /></svg></span>
                    <span>Mega-Menu Nav Manager</span>
                </a>

                <a href="{{ route('admin.attributes.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.attributes*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg></span>
                    <span>Dynamic Filter Builder</span>
                </a>

                <a href="{{ route('admin.banners.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.banners*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg></span>
                    <span>Hero Banners</span>
                </a>

                <a href="{{ route('admin.reviews.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.reviews*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <div class="flex items-center space-x-3">
                        <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" /></svg></span>
                        <span>Reviews Moderation</span>
                    </div>
                    @php $pendingRev = \App\Models\Review::where('status', 'pending')->count(); @endphp
                    @if($pendingRev > 0)
                        <span class="bg-amber-400 text-black font-bold text-[10px] px-2 py-0.5 rounded-full">{{ $pendingRev }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.coupons.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.coupons*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg></span>
                    <span>Coupons & Offers</span>
                </a>

                <a href="{{ route('admin.offers.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.offers*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.75a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.913-.143z" clip-rule="evenodd" /></svg></span>
                    <span>Category Flash Sales</span>
                </a>

                <a href="{{ route('admin.inquiries.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.inquiries*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <div class="flex items-center space-x-3">
                        <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg></span>
                        <span>Inquiries</span>
                    </div>
                    @php $newInq = \App\Models\Inquiry::where('status', 'new')->count(); @endphp
                    @if($newInq > 0)
                        <span class="bg-rose-500 text-white font-bold text-[10px] px-2 py-0.5 rounded-full">{{ $newInq }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.reports.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.reports*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a1.195 1.195 0 011.58 0L21.75 8.25M21.75 8.25v4.5m0-4.5h-4.5" /></svg></span>
                    <span>Reports & Traffic</span>
                </a>

                <a href="{{ route('admin.activity_logs.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.activity_logs*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></span>
                    <span>Activity & Audit Logs</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <div class="flex items-center space-x-3">
                        <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg></span>
                        <span>Customer Directory</span>
                    </div>
                    @php $totalUsers = \App\Models\User::where('role', 'customer')->count(); @endphp
                    @if($totalUsers > 0)
                        <span class="bg-sky-600 text-white font-bold text-[10px] px-2 py-0.5 rounded-full">{{ $totalUsers }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.settings.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('admin.settings*') ? 'bg-[#4A2C1D] text-[#E7C77B] font-semibold border-l-4 border-[#D4AF6A]' : 'text-stone-300 hover:bg-white/5 hover:text-white' }} transition">
                    <span class="text-base"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg></span>
                    <span>Store QR & Settings</span>
                </a>
            </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-[#D4AF6A]/20 bg-[#1A0B05] shrink-0">
            <div class="flex items-center gap-3">
                {{-- User Avatar --}}
                <div class="w-8 h-8 rounded-full bg-[#4A2C1D] border border-[#D4AF6A]/40 flex items-center justify-center shrink-0">
                    <span class="text-[#E7C77B] font-bold text-xs">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                {{-- User Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-[#E7C77B] text-xs truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-stone-400 capitalize">{{ Auth::user()->role }}</p>
                </div>
                {{-- Logout Button --}}
                <form action="{{ route('admin.logout') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit"
                        title="Sign out"
                        class="w-8 h-8 rounded-lg bg-[#4A2C1D]/60 hover:bg-rose-700 border border-[#D4AF6A]/20 hover:border-rose-600 text-stone-400 hover:text-white flex items-center justify-center transition-all duration-200 group">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Mobile sidebar backdrop -->
    <div class="fixed inset-0 z-40 bg-black/50 backdrop-blur-xs lg:hidden"
         x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         x-cloak></div>

    <!-- 2. MAIN ADMIN CONTENT CONTAINER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
        
        <!-- Admin Top Navigation Bar -->
        <header class="bg-white border-b border-stone-200 h-16 flex items-center justify-between px-4 sm:px-6 shrink-0 gap-2">
            <div class="flex items-center gap-3 min-w-0">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-stone-600 hover:text-stone-900 shrink-0 -ml-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="font-serif-royal font-bold text-base sm:text-lg text-[#4A2C1D] truncate">
                    @yield('page_title', 'Admin Console')
                </h2>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-[#D4AF6A] bg-[#FAF7F0] text-[#4A2C1D] text-xs font-semibold hover:bg-[#D4AF6A] hover:text-[#2E180E] transition" title="View Live Storefront">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
                    <span class="hidden sm:inline">View Live Storefront</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-3 sm:px-6 lg:px-8 pt-3 sm:pt-4">
            @if($errors->any())
                <div class="bg-rose-50 border-2 border-rose-300 text-rose-900 px-4 py-3 rounded-xl text-xs font-medium mb-4 shadow-xs">
                    <div class="flex items-center gap-2 font-bold text-rose-800 mb-1.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span>Please fix the following validation errors:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded-xl text-xs font-medium flex items-center justify-between mb-4">
                    <span><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg> {{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 font-bold">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-300 text-rose-900 px-4 py-3 rounded-xl text-xs font-medium flex items-center justify-between mb-4">
                    <span><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg> {{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 font-bold">✕</button>
                </div>
            @endif
        </div>

        <!-- Page Body Content -->
        <main class="flex-1 p-3 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
