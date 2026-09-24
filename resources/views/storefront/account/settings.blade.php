@extends('layouts.storefront')

@section('title', 'Account Security - Rayka Imitation Jewellery')

@section('content')
<div class="bg-stone-50 min-h-screen pt-8 pb-24 sm:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <div class="inline-flex items-center space-x-2 bg-amber-100/50 border border-amber-200 text-amber-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Security & Preferences</span>
                </div>
                <h1 class="font-serif-royal text-3xl sm:text-4xl font-bold text-[#4A2C1D]">
                    Account Security
                </h1>
                <p class="text-sm text-stone-500 mt-2 max-w-xl leading-relaxed">
                    Update your primary email address and manage your account security settings.
                </p>
            </div>
            
            <div class="bg-white px-5 py-3 rounded-2xl border border-[#D4AF6A]/30 shadow-sm flex items-center space-x-4 shrink-0 max-w-full">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#4A2C1D] to-[#996E2E] flex items-center justify-center text-[#E7C77B] font-serif-royal font-bold text-lg shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-[#4A2C1D] truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[11px] text-stone-500 font-mono truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-6 sm:gap-8 border-b border-[#D4AF6A]/30 mb-8 overflow-x-auto no-scrollbar scrollbar-hide">
            <a href="{{ route('account.orders') }}" class="pb-3 border-b-2 border-transparent text-stone-500 hover:text-[#4A2C1D] font-semibold text-sm tracking-wide whitespace-nowrap transition">
                My Orders
            </a>
            <a href="{{ route('account.addresses') }}" class="pb-3 border-b-2 border-transparent text-stone-500 hover:text-[#4A2C1D] font-semibold text-sm tracking-wide whitespace-nowrap transition">
                Saved Addresses
            </a>
            <a href="{{ route('account.settings') }}" class="pb-3 border-b-2 border-[#996E2E] text-[#4A2C1D] font-bold text-sm tracking-wide whitespace-nowrap">
                Account Security
            </a>
        </div>

        <!-- Settings Form -->
        <div id="account-security" class="max-w-2xl bg-white rounded-3xl border border-[#D4AF6A]/30 p-6 sm:p-10 shadow-sm">
            <div x-data="{ otpSent: {{ session('email_change_otp_sent') ? 'true' : 'false' }} }" class="space-y-6">
                
                <div>
                    <h3 class="font-serif-royal text-xl font-bold text-[#4A2C1D]">Update Email Address</h3>
                    <p class="text-sm text-stone-500 mt-1">To ensure your security, you must enter your current password to update your registered email address.</p>
                </div>

                @if(session('error'))
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-start gap-2">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-start gap-2">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Initial Form (Send OTP) -->
                <form action="{{ route('account.settings.update_email') }}" method="POST" x-show="!otpSent" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block font-bold text-stone-700 text-sm mb-1.5">Current Password *</label>
                        <input type="password" name="current_password" required autocomplete="new-password" placeholder="••••••••" class="w-full border border-stone-300 rounded-xl p-3 focus:border-[#D4AF6A] focus:ring-2 focus:ring-[#D4AF6A]/20 focus:outline-none transition">
                        @error('current_password')
                            <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block font-bold text-stone-700 text-sm mb-1.5">New Email Address *</label>
                        <input type="email" name="new_email" required autocomplete="off" placeholder="example@raykajewellery.com" class="w-full border border-stone-300 rounded-xl p-3 focus:border-[#D4AF6A] focus:ring-2 focus:ring-[#D4AF6A]/20 focus:outline-none transition">
                        @error('new_email')
                            <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-[#E7C77B] rounded-xl font-bold text-[13px] uppercase tracking-wider hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer">
                            Request Email Update
                        </button>
                    </div>
                </form>

                <!-- OTP Verification Form -->
                <form action="{{ route('account.settings.verify_email') }}" method="POST" x-show="otpSent" x-cloak class="space-y-5">
                    @csrf
                    <div>
                        <label class="block font-bold text-stone-700 text-sm mb-1.5">6-Digit Verification Code *</label>
                        <input type="text" name="otp" required maxlength="6" pattern="[0-9]{6}" placeholder="123456" class="w-full sm:w-64 border border-[#D4AF6A] rounded-xl p-3 tracking-[0.5em] font-mono text-center focus:border-[#D4AF6A] focus:ring-2 focus:ring-[#D4AF6A]/20 focus:outline-none bg-[#FAF7F0] text-lg">
                    </div>
                    <div class="pt-2 flex flex-col sm:flex-row items-center gap-4">
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-[#F0B429] via-[#E7C77B] to-[#D4AF6A] text-[#240F06] border border-[#E7C77B] rounded-xl font-bold text-[13px] uppercase tracking-wider hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer">
                            Verify & Save
                        </button>
                        <a href="{{ route('account.settings') }}" class="text-[13px] text-stone-500 hover:text-stone-700 underline font-medium">Cancel Update</a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection



