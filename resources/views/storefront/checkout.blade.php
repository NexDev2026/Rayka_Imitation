@extends('layouts.storefront')

@section('title', 'Secure Checkout — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10" x-data="checkoutPage()">

    <div class="text-center max-w-xl mx-auto mb-8 sm:mb-10">
        <span class="text-[11px] sm:text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Secure Verification Checkout</span>
        <h1 class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#4A2C1D] mt-1">
            Complete Your Royal Order
        </h1>
        <div class="rangoli-divider">
            <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
        </div>
    </div>

    @if($errors->any())
        <div class="max-w-3xl mx-auto mb-6 p-4 sm:p-5 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-900 shadow-sm">
            <div class="flex items-center gap-2 font-bold text-sm mb-2 text-rose-800">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Please complete the required details before submitting:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs text-rose-700 pl-1 font-medium">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-xl mx-auto mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
            
            <!-- LEFT: 10-FIELD INDIAN ADDRESS & IDENTITY (7 cols) -->
            <div class="lg:col-span-7 space-y-6 sm:space-y-8">
                
                <!-- 1. Customer Identity -->
                <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[#D4AF6A]/30">
                        <h3 class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D] flex items-center space-x-2">
                            <span>1. Customer Identity</span>
                        </h3>
                        <span class="text-xs text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Express Checkout</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Full Name *</label>
                            <input type="text" name="name" x-model="recipientName" required class="w-full border @error('name') border-rose-500 bg-rose-50/30 @else border-[#D4AF6A]/50 @enderror rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                            @error('name')
                                <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Mobile Number (10 Digits) *</label>
                            <input type="tel" name="mobile" x-model="recipientMobile" pattern="[0-9]{10}" maxlength="10" title="Please enter exactly 10 digits" required placeholder="e.g. 9876543210" class="w-full border @error('mobile') border-rose-500 bg-rose-50/30 @else border-[#D4AF6A]/50 @enderror rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                            @error('mobile')
                                <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block font-semibold text-stone-700 mb-1">Email Address *</label>
                            <input type="email" name="email" x-model="recipientEmail" required placeholder="For order invoice and dispatch tracking updates" class="w-full border @error('email') border-rose-500 bg-rose-50/30 @else border-[#D4AF6A]/50 @enderror rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                            @error('email')
                                <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- 2. Shipping & Delivery Address (India) -->
                <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-5 sm:p-6 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-[#D4AF6A]/30 gap-2.5">
                        <h3 class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                            2. Shipping & Delivery Address (India)
                        </h3>
                        
                        @if($savedAddresses->isNotEmpty())
                            <!-- Professional Switcher between Saved and New Address -->
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        x-show="selectedAddressId !== 'new'" 
                                        @click="openNewAddressForm()" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border border-[#D4AF6A] bg-[#FAF7F0] text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] transition shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    <span>Add New Address</span>
                                </button>
                                <button type="button" 
                                        x-show="selectedAddressId === 'new'" 
                                        @click="selectedAddressId = '{{ $savedAddresses->firstWhere('is_default', true)?->id ?? $savedAddresses->first()?->id }}'" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border border-stone-200 bg-white text-stone-700 hover:border-[#D4AF6A] transition shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                    <span>Use Saved Address</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($savedAddresses->isNotEmpty())
                        <!-- SAVED ADDRESSES SELECTOR CARDS -->
                        <div x-show="selectedAddressId !== 'new'" class="space-y-3">
                            <div class="flex items-center justify-between">
                                <p class="text-xs text-stone-500 font-medium">
                                    Select your preferred delivery location:
                                </p>
                                <a href="{{ route('account.addresses') }}" class="text-[11px] font-bold text-[#996E2E] hover:text-[#4A2C1D] hover:underline flex items-center gap-1" target="_blank">
                                    <span>Manage Addresses</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                @foreach($savedAddresses as $addr)
                                    <label class="relative block rounded-2xl border-2 p-4 cursor-pointer transition duration-200 select-none overflow-hidden"
                                           :class="selectedAddressId == {{ $addr->id }} ? 'border-[#996E2E] bg-[#FAF7F0] shadow-sm ring-2 ring-[#D4AF6A]/30' : 'border-stone-200 bg-white hover:border-[#D4AF6A]/60'">
                                        <input type="radio" 
                                               name="selected_address_id" 
                                               value="{{ $addr->id }}" 
                                               x-model="selectedAddressId"
                                               :disabled="selectedAddressId === 'new'"
                                               @change="selectSavedAddress({{ json_encode($addr) }})"
                                               class="sr-only">

                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                                                      :class="selectedAddressId == {{ $addr->id }} ? 'bg-[#4A2C1D] text-[#E7C77B]' : 'bg-stone-100 text-stone-600'">
                                                    {{ substr($addr->name, 0, 1) }}
                                                </span>
                                                <span class="font-serif-royal font-bold text-sm text-[#4A2C1D] truncate">{{ $addr->name }}</span>
                                            </div>
                                            @if($addr->is_default)
                                                <span class="text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded-full shrink-0">
                                                    Default
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-xs text-stone-600 space-y-1 pl-9">
                                            <p class="font-medium text-stone-800 leading-snug break-words">{{ $addr->address_line }}</p>
                                            @if($addr->street && $addr->street !== $addr->address_line)
                                                <p class="text-stone-500 break-words"><span class="text-stone-400">Area:</span> {{ $addr->street }}</p>
                                            @endif
                                            @if($addr->landmark)
                                                <p class="text-stone-500 break-words"><span class="text-stone-400">Landmark:</span> Near {{ $addr->landmark }}</p>
                                            @endif
                                            <p class="font-semibold text-[#4A2C1D] pt-0.5">
                                                {{ $addr->city }}, {{ $addr->state }} — <span class="font-mono text-[#996E2E]">{{ $addr->pincode }}</span>
                                            </p>
                                            <p class="text-[11px] text-stone-500 font-mono pt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                <span>{{ $addr->mobile }}</span>
                                            </p>
                                        </div>

                                        <div class="mt-3 pt-2.5 border-t border-stone-200/60 flex items-center justify-between text-xs">
                                            <span class="text-[11px] font-bold flex items-center gap-1.5"
                                                  :class="selectedAddressId == {{ $addr->id }} ? 'text-[#996E2E]' : 'text-stone-400'">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span x-text="selectedAddressId == {{ $addr->id }} ? 'Deliver to this address' : 'Click to select'"></span>
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- NEW ADDRESS FORM (SHOWN IF GUEST, OR selectedAddressId === 'new') -->
                    <div x-show="selectedAddressId === 'new'" class="space-y-4 text-xs">
                        @if($savedAddresses->isNotEmpty())
                            <input type="hidden" name="selected_address_id" value="new" :disabled="selectedAddressId !== 'new'">
                        @endif

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-2">
                            <span class="text-xs font-bold text-[#4A2C1D]">
                                Enter New Indian Delivery Details:
                            </span>
                            
                            <!-- GPS Auto-Detect Button -->
                            <button type="button" 
                                    @click="detectCurrentLocation" 
                                    :disabled="detectingLocation || selectedAddressId !== 'new'"
                                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold border border-[#D4AF6A] bg-[#FAF7F0] text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] transition duration-200 shadow-2xs cursor-pointer shrink-0 disabled:opacity-60"
                                    title="Auto-detect address from your current GPS location">
                                <template x-if="detectingLocation">
                                    <svg class="w-3.5 h-3.5 animate-spin text-[#996E2E]" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </template>
                                <template x-if="!detectingLocation">
                                    <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </template>
                                <span x-text="detectingLocation ? 'Detecting GPS...' : 'Use Current Location'"></span>
                            </button>
                        </div>

                        <!-- City, State, Pin Code -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block font-semibold text-stone-700 mb-1">PIN Code (6 Digits) *</label>
                                <div class="relative">
                                    <input type="text" name="pincode" x-model="pincode" @input="checkPincode" :disabled="selectedAddressId !== 'new'" :required="selectedAddressId === 'new'" value="{{ old('pincode', $savedAddresses->isEmpty() ? ($savedAddress?->pincode ?? '') : '') }}" pattern="[0-9]{6}" maxlength="6" inputmode="numeric" placeholder="e.g. 302003" title="Please enter a valid 6-digit Indian PIN code" class="w-full border @error('pincode') border-rose-500 bg-rose-50/30 @else border-[#D4AF6A]/50 @enderror rounded-lg p-2.5 pr-8 focus:border-[#D4AF6A] focus:outline-hidden">
                                    <div x-show="loadingPin" class="absolute right-2 top-3">
                                        <svg class="w-4 h-4 animate-spin text-[#996E2E]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    </div>
                                </div>
                                @error('pincode')
                                    <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block font-semibold text-stone-700 mb-1">City *</label>
                                <input type="text" name="city" x-model="city" :disabled="selectedAddressId !== 'new'" :required="selectedAddressId === 'new'" value="{{ old('city', $savedAddresses->isEmpty() ? ($savedAddress?->city ?? '') : '') }}" placeholder="e.g. Jaipur" class="w-full border @error('city') border-rose-500 bg-rose-50/30 @else border-[#D4AF6A]/50 @enderror rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                                @error('city')
                                    <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block font-semibold text-stone-700 mb-1">State *</label>
                                <select name="state" :disabled="selectedAddressId !== 'new'" :required="selectedAddressId === 'new'" class="w-full border @error('state') border-rose-500 bg-rose-50/30 @else border-[#D4AF6A]/50 @enderror rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden bg-white">
                                    <option value="">Select State</option>
                                    @php
                                        $states = [
                                            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
                                            'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka',
                                            'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram',
                                            'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu',
                                            'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
                                            'Andaman & Nicobar', 'Chandigarh', 'Dadra & Nagar Haveli',
                                            'Delhi', 'Jammu & Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
                                        ];
                                    @endphp
                                    <template x-for="st in states" :key="st">
                                        <option :value="st" :selected="state === st" x-text="st"></option>
                                    </template>
                                </select>
                                @error('state')
                                    <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Locality & Street -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-stone-700 mb-1">Locality / Area *</label>
                                <template x-if="loadingPin">
                                    <div class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 bg-[#FAF7F0] text-stone-500 text-sm font-medium flex items-center gap-2">
                                        <svg class="w-4 h-4 animate-spin text-[#996E2E]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        Fetching locality...
                                    </div>
                                </template>
                                <template x-if="!loadingPin && postOffices.length > 0">
                                    <div class="relative">
                                        <select name="street" x-model="street" :disabled="selectedAddressId !== 'new'" :required="selectedAddressId === 'new'" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pr-8 focus:border-[#D4AF6A] focus:outline-hidden bg-white font-medium text-stone-700">
                                            <template x-for="po in postOffices" :key="po">
                                                <option :value="po" x-text="po"></option>
                                            </template>
                                        </select>
                                    </div>
                                </template>
                                <template x-if="!loadingPin && postOffices.length === 0">
                                    <input type="text" name="street" x-model="street" :disabled="selectedAddressId !== 'new'" :required="selectedAddressId === 'new'" value="{{ old('street', $savedAddresses->isEmpty() ? ($savedAddress?->street ?? '') : '') }}" placeholder="e.g. Navrangpura" class="w-full border @error('street') border-rose-500 bg-rose-50/30 @else border-[#D4AF6A]/50 @enderror rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                                </template>
                                @error('street')
                                    <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block font-semibold text-stone-700 mb-1">Flat / House No. / Building / Apartment *</label>
                                <input type="text" name="address_line" :disabled="selectedAddressId !== 'new'" :required="selectedAddressId === 'new'" value="{{ old('address_line', $savedAddresses->isEmpty() ? ($savedAddress?->address_line ?? '') : '') }}" placeholder="e.g. Flat 402, Royal Palms Residency" class="w-full border @error('address_line') border-rose-500 bg-rose-50/30 @else border-[#D4AF6A]/50 @enderror rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                                @error('address_line')
                                    <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Landmark & Address Type -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-stone-700 mb-1">Nearby Landmark (Optional)</label>
                                <input type="text" name="landmark" :disabled="selectedAddressId !== 'new'" value="{{ old('landmark', $savedAddresses->isEmpty() ? ($savedAddress?->landmark ?? '') : '') }}" placeholder="e.g. Near Metro Pillar 124" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                            </div>
                            <div>
                                <label class="block font-semibold text-stone-700 mb-1">Address Type</label>
                                <div class="flex items-center gap-3 pt-1">
                                    <label class="flex items-center gap-2 cursor-pointer p-2 border rounded-lg flex-1 text-center justify-center transition-colors" :class="addressType === 'Home' ? 'border-[#996E2E] bg-[#FAF7F0] font-bold text-[#4A2C1D]' : 'border-stone-200 text-stone-600'">
                                        <input type="radio" name="address_type" value="Home" x-model="addressType" :disabled="selectedAddressId !== 'new'" class="hidden">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                        Home
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer p-2 border rounded-lg flex-1 text-center justify-center transition-colors" :class="addressType === 'Office' ? 'border-[#996E2E] bg-[#FAF7F0] font-bold text-[#4A2C1D]' : 'border-stone-200 text-stone-600'">
                                        <input type="radio" name="address_type" value="Office" x-model="addressType" :disabled="selectedAddressId !== 'new'" class="hidden">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Office
                                    </label>
                                </div>
                            </div>
                        </div>

                        @auth
                            <!-- Professional Save Address Toggle -->
                            <div class="pt-3 border-t border-stone-100 flex items-center justify-between">
                                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none text-xs font-semibold text-[#4A2C1D]">
                                    <input type="checkbox" name="save_address" value="1" {{ old('save_address', '1') ? 'checked' : '' }} :disabled="selectedAddressId !== 'new'" class="w-4 h-4 rounded text-[#996E2E] focus:ring-[#D4AF6A] border-stone-300">
                                    <span>Save this address to my account for faster future checkouts</span>
                                </label>
                            </div>
                        @endauth
                    </div>

                    <!-- Special Order Notes -->
                    <div class="pt-2">
                        <label class="block text-xs font-semibold text-stone-700 mb-1.5 flex items-center justify-between">
                            <span>Special Order Notes (Optional)</span>
                            <span class="text-[10px] text-stone-400 font-normal">Packaging or delivery instructions</span>
                        </label>
                        <textarea name="notes" rows="2" placeholder="e.g. Please deliver in festive velvet pouch, call before arriving..." class="w-full border border-[#D4AF6A]/50 rounded-xl p-2.5 text-xs text-stone-700 placeholder-stone-400 focus:border-[#D4AF6A] focus:ring-1 focus:ring-[#D4AF6A]/30 focus:outline-hidden resize-none bg-stone-50/40">{{ old('notes') }}</textarea>
                    </div>
                </div>

            </div>

            <!-- RIGHT: ORDER REVIEW & STATIC QR PAYMENT PROOF UPLOAD (5 cols) -->
            <div class="lg:col-span-5 space-y-6 sm:space-y-8">

                <!-- 1. Order Review Summary -->
                <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-5 sm:p-6 shadow-xs space-y-4">
                    <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] pb-3 border-b border-[#D4AF6A]/30">
                        3. Order Review ({{ $cart->item_count }} Items)
                    </h3>

                    <div class="max-h-52 overflow-y-auto divide-y divide-stone-100 pr-1 space-y-3">
                        @foreach($cart->items as $item)
                            <div class="pt-3 first:pt-0 flex items-center justify-between text-xs">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $item->product->effective_primary_image }}" alt="Thumb" class="w-10 h-10 rounded-lg object-cover bg-[#FAF7F0] border border-[#D4AF6A]/30">
                                    <div>
                                        <p class="font-semibold text-[#4A2C1D] line-clamp-1 max-w-[170px] sm:max-w-[200px]">{{ $item->product->name }}</p>
                                        <p class="text-stone-400 text-[11px]">{{ $item->quantity }} × ₹{{ number_format($item->price) }}</p>
                                    </div>
                                </div>
                                <span class="font-bold text-[#4A2C1D]">₹{{ number_format($item->subtotal) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 border-t border-[#D4AF6A]/20 space-y-2 text-xs">
                        <div class="flex justify-between text-stone-600">
                            <span>Subtotal:</span>
                            <span>₹{{ number_format($subtotal) }}</span>
                        </div>
                        @if($discount > 0)
                            <div class="flex justify-between text-emerald-700 font-semibold">
                                <span>Coupon ({{ $coupon->code }}):</span>
                                <span>− ₹{{ number_format($discount) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-stone-600">
                            <span>Shipping:</span>
                            <span>{{ $shippingFee == 0 ? 'FREE' : '₹' . number_format($shippingFee) }}</span>
                        </div>
                        <div class="pt-2 border-t border-[#D4AF6A]/30 flex justify-between items-baseline">
                            <span class="font-serif-royal font-bold text-sm text-[#4A2C1D]">Payable Amount:</span>
                            <span class="font-serif-royal font-bold text-2xl text-[#996E2E]">₹{{ number_format($total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. STATIC QR CODE & PAYMENT PROOF UPLOAD -->
                <div class="bg-gradient-to-b from-[#FAF7F0] to-white rounded-2xl border-2 border-[#D4AF6A] p-5 sm:p-6 shadow-md space-y-5">
                    <div class="text-center space-y-1">
                        <span class="inline-flex items-center gap-1 bg-[#4A2C1D] text-[#E7C77B] text-[10px] uppercase font-bold tracking-widest px-3 py-1 rounded-full shadow-xs">
                            <svg class="w-3.5 h-3.5 text-[#E7C77B]" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                            <span>Step 4: Scan & Pay via UPI</span>
                        </span>
                        <h4 class="font-serif-royal text-lg font-bold text-[#4A2C1D] pt-2">
                            Pay ₹{{ number_format($total) }} to Rayka
                        </h4>
                        <p class="text-xs text-stone-500">
                            Scan with Google Pay, PhonePe, Paytm, or BHIM UPI
                        </p>
                    </div>

                    <!-- QR Code Display Box -->
                    <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl border border-[#D4AF6A]/50 shadow-inner">
                        <img src="{{ $qrCodeImage }}" alt="UPI QR Code" class="w-44 h-44 sm:w-48 sm:h-48 object-contain">
                        
                        <!-- UPI ID with 1-Click Copy -->
                        <div class="mt-3 flex items-center space-x-2 bg-[#FAF7F0] px-3 py-1.5 rounded-full border border-[#D4AF6A]/40 text-xs">
                            <span class="font-mono text-stone-700 select-all">{{ $upiId }}</span>
                            <button type="button" @click="copyUpi('{{ $upiId }}')" class="font-bold text-[#996E2E] hover:text-[#4A2C1D] transition flex items-center gap-1">
                                <span x-show="!copiedUpi" class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span>Copy</span>
                                </span>
                                <span x-show="copiedUpi" class="flex items-center gap-1 text-emerald-700">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Copied!</span>
                                </span>
                            </button>
                        </div>
                        <p class="text-[10px] text-stone-400 mt-1">Verified Payee: {{ $upiPayeeName }}</p>
                    </div>

                    <!-- Customer Payment Screenshot Upload -->
                    <div class="space-y-2 text-xs">
                        <label class="block font-bold text-[#4A2C1D] flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Upload Payment Screenshot (Proof) *</span>
                        </label>
                        <p class="text-[11px] text-stone-500 leading-snug">
                            After completing the UPI payment, please attach the confirmation screenshot so our admin can verify and dispatch your jewels.
                        </p>
                        
                        <div class="mt-2 flex flex-col items-center justify-center border-2 border-dashed @error('payment_screenshot') border-rose-500 bg-rose-50/20 @else border-[#D4AF6A] bg-white hover:bg-[#FAF7F0] @enderror rounded-xl p-4 transition cursor-pointer relative">
                            <input type="file" 
                                   name="payment_screenshot" 
                                   accept="image/*,.heic,.heif,.jfif" 
                                   :required="!screenshotPreview" 
                                   @change="previewFile($event)"
                                   class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">

                            <div x-show="!screenshotPreview" class="text-center space-y-1 pointer-events-none">
                                <svg class="w-8 h-8 text-[#996E2E] mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="font-semibold text-stone-700">Click or Drag Payment Screenshot here</p>
                                <p class="text-[10px] text-stone-400">JPEG, PNG, JPG, WebP, HEIC up to 10MB</p>
                            </div>

                            <div x-show="screenshotPreview" class="text-center space-y-2 pointer-events-none" x-cloak>
                                <img :src="screenshotPreview" alt="Proof Preview" class="max-h-40 rounded border shadow-sm mx-auto object-contain">
                                <p class="text-[11px] text-emerald-800 font-semibold flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Screenshot Selected (Click to change)</span>
                                </p>
                            </div>
                        </div>

                        <!-- 100% Fail-Safe Base64 Backup -->
                        <input type="hidden" name="payment_screenshot_base64" :value="screenshotPreview || ''">
                        @error('payment_screenshot')
                            <p class="text-rose-600 text-[11px] font-medium mt-1">{{ $message }}</p>
                        @enderror

                        <div class="pt-2">
                            <label class="block font-medium text-stone-700 mb-1">UPI Reference / UTR Number (Optional)</label>
                            <input type="text" name="transaction_reference" value="{{ old('transaction_reference') }}" placeholder="e.g. 428901839210 or UPI Transaction ID" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 font-mono text-xs focus:outline-hidden">
                        </div>
                    </div>

                    <!-- Complete Order CTA -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-4 text-center rounded-full bg-gradient-to-r from-[#F0B429] via-[#E7C77B] to-[#D4AF6A] text-[#2E180E] font-bold text-xs uppercase tracking-wider hover:shadow-xl hover:scale-[1.02] transition border border-[#E7C77B] flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 text-[#2E180E]" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5m14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
                            <span class="flex items-center gap-1.5">Submit Order for Verification <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></span>
                        </button>
                        <p class="text-[10px] text-center text-stone-500 mt-2">
                            Status will be set to <strong>"Pending Verification"</strong>. Upon admin confirmation, your order will be confirmed and the downloadable invoice will be unlocked.
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </form>

</div>

<script>
function checkoutPage() {
    return {
        screenshotPreview: null,
        copiedUpi: false,
        couponCodeInput: '',
        couponError: '',
        couponSuccess: '',
        couponLoading: false,
        selectedAddressId: '{{ old('selected_address_id', $savedAddresses->firstWhere('is_default', true)?->id ?? $savedAddresses->first()?->id ?? 'new') }}',
        recipientName: '{{ old('name', Auth::user()?->name ?: $savedAddress?->name) }}',
        recipientMobile: '{{ old('mobile', Auth::user()?->mobile ?: $savedAddress?->mobile) }}',
        recipientEmail: '{{ old('email', Auth::user()?->email ?: $savedAddress?->email) }}',
        pincode: '{{ old("pincode", $savedAddresses->isEmpty() ? ($savedAddress?->pincode ?? "") : "") }}',
        city: '{{ old("city", $savedAddresses->isEmpty() ? ($savedAddress?->city ?? "") : "") }}',
        state: '{{ old("state", $savedAddresses->isEmpty() ? ($savedAddress?->state ?? "") : "") }}',
        street: '{{ old("street", $savedAddresses->isEmpty() ? ($savedAddress?->street ?? "") : "") }}',
        addressType: '{{ old("address_type", "Home") }}',
        postOffices: [],
        loadingPin: false,
        detectingLocation: false,
        states: [
            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
            'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka',
            'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram',
            'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu',
            'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
            'Andaman & Nicobar', 'Chandigarh', 'Dadra & Nagar Haveli',
            'Delhi', 'Jammu & Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
        ],

        init: function() {
            this.restoreFormState();
            if (this.selectedAddressId === 'new' && this.pincode && this.pincode.length === 6) {
                this.checkPincode();
            }
        },

        openNewAddressForm: function() {
            this.selectedAddressId = 'new';
            this.pincode = '';
            this.city = '';
            this.state = '';
            this.street = '';
            this.postOffices = [];
            this.addressType = 'Home';
            var addrLine = document.querySelector('input[name="address_line"]');
            if (addrLine) addrLine.value = '';
            var landmark = document.querySelector('input[name="landmark"]');
            if (landmark) landmark.value = '';
        },

        selectSavedAddress: function(addr) {
            this.selectedAddressId = addr.id;
            if (addr.name) this.recipientName = addr.name;
            if (addr.mobile) this.recipientMobile = addr.mobile;
            if (addr.email) this.recipientEmail = addr.email;
        },

        detectCurrentLocation: function() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser. Please enter your PIN code manually.');
                return;
            }

            var self = this;
            self.detectingLocation = true;

            navigator.geolocation.getCurrentPosition(
                async function(position) {
                    var lat = position.coords.latitude;
                    var lon = position.coords.longitude;

                    var detectedPin = null;
                    var detectedCity = null;
                    var detectedState = null;
                    var detectedLocality = null;
                    var detectedRoad = null;

                    // 1. Primary: BigDataCloud Reverse Geocode (free client reverse geocoding API)
                    try {
                        var bdcRes = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=en`);
                        if (bdcRes.ok) {
                            var bdcData = await bdcRes.json();
                            if (bdcData) {
                                detectedPin = bdcData.postcode || null;
                                detectedCity = bdcData.city || bdcData.locality || null;
                                detectedState = bdcData.principalSubdivision || null;
                                detectedLocality = bdcData.locality || bdcData.localityInfo?.administrative?.[3]?.name || null;
                            }
                        }
                    } catch (e) {}

                    // 2. Fallback to OpenStreetMap Nominatim if PIN was not found
                    if (!detectedPin || detectedPin.length !== 6) {
                        try {
                            var osmRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}&accept-language=en`);
                            if (osmRes.ok) {
                                var osmData = await osmRes.json();
                                if (osmData && osmData.address) {
                                    var addr = osmData.address;
                                    detectedPin = addr.postcode ? addr.postcode.replace(/[^0-9]/g, '') : detectedPin;
                                    detectedCity = addr.city || addr.town || addr.municipality || addr.district || addr.county || detectedCity;
                                    detectedState = addr.state || detectedState;
                                    detectedLocality = addr.suburb || addr.neighbourhood || addr.residential || addr.village || detectedLocality;
                                    detectedRoad = addr.road || null;
                                }
                            }
                        } catch (e) {}
                    }

                    // Normalize PIN code
                    if (detectedPin) {
                        var cleanPin = detectedPin.replace(/[^0-9]/g, '');
                        if (cleanPin.length === 6) {
                            self.pincode = cleanPin;
                        }
                    }

                    if (detectedCity) {
                        self.city = detectedCity;
                    }

                    if (detectedState) {
                        // Match state with self.states list
                        var matchedState = self.states.find(function(s) {
                            return s.toLowerCase() === detectedState.toLowerCase() ||
                                   detectedState.toLowerCase().includes(s.toLowerCase()) ||
                                   s.toLowerCase().includes(detectedState.toLowerCase());
                        });
                        if (matchedState) {
                            self.state = matchedState;
                        } else {
                            self.state = detectedState;
                        }
                    }

                    if (detectedLocality || detectedRoad) {
                        self.street = detectedLocality || detectedRoad;
                    }

                    // Auto-fill address line / flat if empty
                    var addressLineInput = document.querySelector('input[name="address_line"]');
                    if (addressLineInput && !addressLineInput.value && (detectedRoad || detectedLocality)) {
                        addressLineInput.value = (detectedRoad ? detectedRoad + ', ' : '') + (detectedLocality || '');
                    }

                    // If PIN code was found, trigger checkPincode to synchronize delivery details
                    if (self.pincode && self.pincode.length === 6) {
                        await self.checkPincode();
                    }

                    self.detectingLocation = false;

                    if (window.Alpine && window.Alpine.store('rayka')) {
                        window.Alpine.store('rayka').showToast('Delivery location auto-detected successfully!', 'success');
                    }
                },
                function(error) {
                    self.detectingLocation = false;
                    var msg = 'Location access was not granted. Please enter your PIN code manually.';
                    if (error.code === error.POSITION_UNAVAILABLE) {
                        msg = 'Location information is currently unavailable. Please enter your PIN code manually.';
                    } else if (error.code === error.TIMEOUT) {
                        msg = 'Location request timed out. Please enter your PIN code manually.';
                    }
                    if (window.Alpine && window.Alpine.store('rayka')) {
                        window.Alpine.store('rayka').showToast(msg, 'error');
                    } else {
                        alert(msg);
                    }
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
            );
        },

        async checkPincode() {
            if (this.pincode.length === 6) {
                this.loadingPin = true;
                this.postOffices = [];
                try {
                    const res = await fetch(`https://api.postalpincode.in/pincode/${this.pincode}`);
                    const data = await res.json();
                    if (data && data[0] && data[0].Status === 'Success') {
                        const po = data[0].PostOffice[0];
                        if (po) {
                            this.city = po.District || po.Block;
                            this.state = po.State;
                            this.postOffices = data[0].PostOffice.map(p => p.Name);
                            // If current street is not in the list, default to first or keep empty
                            if (!this.postOffices.includes(this.street) && this.postOffices.length > 0) {
                                this.street = this.postOffices[0];
                            }
                            if (window.Alpine && window.Alpine.store('rayka')) {
                                window.Alpine.store('rayka').showToast('Delivery location details detected', 'success');
                            }
                        }
                    } else {
                        this.postOffices = [];
                    }
                } catch(e) {
                    this.postOffices = [];
                }
                this.loadingPin = false;
            }
        },

        previewFile: function(e) {
            var file = e.target.files[0];
            if (file) {
                var maxBytes = 10 * 1024 * 1024; // 10MB limit
                if (file.size > maxBytes) {
                    alert('Payment screenshot file size exceeds 10MB. Please select an image under 10MB.');
                    e.target.value = '';
                    this.screenshotPreview = null;
                    return;
                }
                var reader = new FileReader();
                var self = this;
                reader.onload = function(re) {
                    self.screenshotPreview = re.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        copyUpi: function(id) {
            var self = this;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(id);
            }
            this.copiedUpi = true;
            setTimeout(function() {
                self.copiedUpi = false;
            }, 2500);
        },


        saveFormState: function() {
            try {
                const form = document.querySelector('form[action*="checkout"]');
                if (form) {
                    const data = {};
                    new FormData(form).forEach((v, k) => {
                        if (k !== '_token' && k !== 'payment_screenshot') data[k] = v;
                    });
                    sessionStorage.setItem('rayka_checkout_draft', JSON.stringify(data));
                }
            } catch(e) {}
        },

        restoreFormState: function() {
            try {
                const saved = sessionStorage.getItem('rayka_checkout_draft');
                if (saved) {
                    const data = JSON.parse(saved);
                    const form = document.querySelector('form[action*="checkout"]');
                    if (form) {
                        Object.keys(data).forEach(k => {
                            const input = form.elements[k];
                            if (input && !input.value) {
                                input.value = data[k];
                            }
                        });
                    }
                    sessionStorage.removeItem('rayka_checkout_draft');
                }
            } catch(e) {}
        }
    };
}
</script>
@endsection

