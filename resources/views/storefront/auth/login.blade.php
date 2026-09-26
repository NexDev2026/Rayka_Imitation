@extends('layouts.storefront')

@section('title', 'Sign In — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-md mx-auto px-4 py-8 sm:py-12" x-data="{ showPassword: false }">

    <div class="bg-white rounded-2xl border-2 border-[#D4AF6A]/50 p-6 sm:p-8 shadow-xl text-center space-y-6">
        
        <!-- Royal Luxury Crest Icon -->
        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#FAF7F0] to-[#F3ECE1] border-2 border-[#D4AF6A] flex items-center justify-center mx-auto shadow-sm text-[#996E2E]">
            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-[#996E2E]" viewBox="0 0 24 24" fill="currentColor">
                <path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/>
            </svg>
        </div>

        <div>
            <span class="text-[10px] uppercase font-bold tracking-[0.25em] text-[#996E2E]">Patron Portal</span>
            <h1 class="font-serif-royal text-2xl font-bold text-[#4A2C1D] mt-0.5">
                Royal Patron Sign In
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                Access your orders, saved addresses, and royal wishlist.
            </p>
        </div>

        <!-- Form Status and Error Notifications (Single Container) -->
        @if(session('error'))
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium text-left flex items-start gap-2.5">
                <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium text-left space-y-1">
                @foreach($errors->all() as $err)
                    <div class="flex items-center gap-2">
                        <span class="text-rose-600 font-bold">•</span>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium text-left flex items-start gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Direct Email + Password Login Form -->
        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4 text-left text-xs">
            @csrf
            <input type="hidden" name="redirect" id="customer_redirect_input" value="{{ old('redirect', $redirect ?? request('redirect', '')) }}">

            <!-- Email Address Field -->
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Email Address *</label>
                <div class="relative">
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="off"
                           placeholder="example@raykajewellery.com" 
                           class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pl-9 focus:border-[#D4AF6A] focus:outline-hidden text-xs">
                    <svg class="w-4 h-4 text-stone-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                </div>
            </div>

            <!-- Password Field -->
            <div class="space-y-1">
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-semibold text-stone-700">Password *</label>
                    <a href="{{ route('password.forgot') }}" class="text-[11px] text-[#996E2E] hover:underline font-medium">Forgot Password?</a>
                </div>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="••••••••" 
                           class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pr-9 focus:border-[#D4AF6A] focus:outline-hidden">
                    <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-2.5 text-stone-400 hover:text-stone-700 cursor-pointer">
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" value="1" class="rounded border-[#D4AF6A] text-[#4A2C1D] focus:ring-[#D4AF6A]">
                    <span class="text-[11px] text-stone-600 font-medium">Keep me signed in</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-[#E7C77B] rounded-full font-bold text-xs uppercase tracking-wider hover:shadow-md transition border border-[#D4AF6A] mt-2 flex items-center justify-center gap-2 cursor-pointer">
                <span>Sign In to Royal Account</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>

        <div class="pt-2 text-xs text-stone-500 border-t border-[#D4AF6A]/20 flex items-center justify-center gap-1">
            <span>New to Rayka Jewellery?</span>
            <a href="{{ route('register', array_filter(['redirect' => old('redirect', $redirect ?? request('redirect', ''))])) }}" class="font-bold text-[#996E2E] hover:underline">Create an Account</a>
        </div>

    </div>

</div>

@push('scripts')
<script>
    (function() {
        try {
            const redirectInput = document.getElementById('customer_redirect_input');
            if (redirectInput && (!redirectInput.value || redirectInput.value.trim() === '')) {
                const savedUrl = sessionStorage.getItem('rayka_customer_last_url');
                if (savedUrl && !savedUrl.includes('/login') && !savedUrl.includes('/register') && !savedUrl.includes('/logout')) {
                    const parsed = new URL(savedUrl, window.location.origin);
                    if (parsed.origin === window.location.origin) {
                        redirectInput.value = savedUrl;
                    }
                }
            }
        } catch(e) {}

        // BFCache Buster: When clicking browser back button after logging in, force page reload so server auth check redirects back to account/intended page
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && (window.performance.navigation && window.performance.navigation.type === 2) || (window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0] && window.performance.getEntriesByType('navigation')[0].type === 'back_forward'))) {
                window.location.reload();
            }
        });
    })();
</script>
@endpush
@endsection


