<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Admin Portal Login — Rayka Royal Administration</title>
    <link rel="icon" type="image/png" href="{{ asset('images/rayka-logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#150904] flex items-center justify-center min-h-screen p-4 antialiased text-stone-200">

    <div class="max-w-md w-full bg-[#240F06] rounded-3xl border border-[#D4AF6A]/50 p-6 sm:p-10 shadow-2xl space-y-6" x-data="{ showPass: false }">
        
        <!-- Brand Crest -->
        <div class="text-center space-y-3">
            <div class="w-20 h-20 rounded-full bg-[#FAF7F0] border-2 border-[#D4AF6A] p-2 flex items-center justify-center mx-auto shadow-lg shadow-black/40 ring-4 ring-[#D4AF6A]/20">
                <img src="{{ asset('images/rayka-logo.png') }}" alt="Rayka Crest" class="w-full h-full object-contain">
            </div>

            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-[#3D1A0A] border border-[#D4AF6A]/40 text-[10px] uppercase font-bold tracking-[0.25em] text-[#E7C77B]">
                    Restricted Area
                </span>
                <h1 class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#FAF7F0] mt-2">
                    Rayka Administration
                </h1>
                <p class="text-xs text-stone-400 mt-1">
                    Sign in with your verified administrator credentials
                </p>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-rose-950/80 border border-rose-500/60 text-rose-200 text-xs p-3.5 rounded-xl flex items-start gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <div class="flex-1 leading-relaxed">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-emerald-950/80 border border-emerald-500/60 text-emerald-200 text-xs p-3.5 rounded-xl flex items-start gap-2.5 shadow-sm">
                <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <div class="flex-1 leading-relaxed">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4 text-left text-xs" novalidate>
            @csrf
            <input type="hidden" name="redirect" id="admin_redirect_input" value="{{ old('redirect', $redirect ?? request('redirect', '')) }}">

            <!-- Email Address Field -->
            <div>
                <label for="admin_email" class="block text-stone-300 font-semibold mb-1.5 flex items-center justify-between">
                    <span>Admin Email Address</span>
                    <span class="text-[10px] text-stone-400 font-normal">Official Work Email</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" /></svg>
                    </div>
                    <input type="email" 
                           id="admin_email"
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="off"
                           autofocus
                           placeholder="example@raykajewellery.com" 
                           class="w-full bg-[#180B05] border {{ $errors->has('email') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-[#D4AF6A]/40' }} rounded-xl pl-9 pr-3.5 py-3 text-stone-100 placeholder-stone-500 focus:outline-hidden focus:border-[#E7C77B] focus:ring-2 focus:ring-[#D4AF6A]/30 transition">
                </div>
                @error('email')
                    <p class="text-rose-400 text-[11px] mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="admin_password" class="block text-stone-300 font-semibold flex items-center justify-between">
                        <span>Admin Password</span>
                    </label>
                    <a href="{{ route('admin.password.forgot') }}" class="text-[11px] text-[#D4AF6A] hover:text-[#E7C77B] transition font-medium">Forgot Password?</a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <input :type="showPass ? 'text' : 'password'" 
                           id="admin_password"
                           name="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="••••••••" 
                           class="w-full bg-[#180B05] border {{ $errors->has('password') ? 'border-rose-500 ring-1 ring-rose-500' : 'border-[#D4AF6A]/40' }} rounded-xl pl-9 pr-10 py-3 text-stone-100 placeholder-stone-500 focus:outline-hidden focus:border-[#E7C77B] focus:ring-2 focus:ring-[#D4AF6A]/30 transition font-mono">
                    <button type="button" 
                            @click="showPass = !showPass" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-stone-400 hover:text-[#E7C77B] transition cursor-pointer"
                            aria-label="Toggle password visibility">
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-rose-400 text-[11px] mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Remember Me & Security Policy Note -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-stone-300">
                    <input type="checkbox" name="remember" value="1" class="rounded border-[#D4AF6A]/50 bg-[#180B05] text-[#D4AF6A] focus:ring-[#D4AF6A]/40">
                    <span class="text-[11px]">Remember this device</span>
                </label>
                <span class="text-[10px] text-stone-500">256-Bit Encrypted</span>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-[#F0B429] via-[#E7C77B] to-[#D4AF6A] text-[#240F06] font-bold text-xs uppercase tracking-wider hover:opacity-95 hover:shadow-lg hover:shadow-amber-900/30 transition border border-[#E7C77B] mt-3 cursor-pointer">
                Enter Administration &rarr;
            </button>
        </form>

        <div class="pt-4 border-t border-[#D4AF6A]/20 text-center">
            <a href="{{ route('home') }}" class="text-[11px] text-stone-400 hover:text-[#E7C77B] transition flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                <span>Return to Rayka Jewellery Storefront</span>
            </a>
        </div>

    </div>

    <script>
        (function() {
            // Restore intended URL if user came after cache clear or session timeout
            try {
                const redirectInput = document.getElementById('admin_redirect_input');
                if (redirectInput && (!redirectInput.value || redirectInput.value.trim() === '')) {
                    const savedUrl = sessionStorage.getItem('rayka_admin_last_url') || localStorage.getItem('rayka_admin_last_url');
                    if (savedUrl && !savedUrl.includes('/admin/login') && !savedUrl.includes('/admin/logout')) {
                        const parsed = new URL(savedUrl, window.location.origin);
                        if (parsed.origin === window.location.origin && parsed.pathname.startsWith('/admin')) {
                            redirectInput.value = savedUrl;
                        }
                    }
                }
            } catch(e) {}

            // BFCache Buster: When clicking browser back button after logging in, force page reload so server auth check kicks in
            window.addEventListener('pageshow', function(event) {
                if (event.persisted || (window.performance && (window.performance.navigation && window.performance.navigation.type === 2) || (window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0] && window.performance.getEntriesByType('navigation')[0].type === 'back_forward'))) {
                    window.location.reload();
                }
            });
        })();
    </script>
</body>
</html>

