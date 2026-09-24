@extends('layouts.storefront')

@section('title', 'Saved Delivery Addresses — Rayka Imitation Jewellery')

@section('content')
<div class="bg-stone-50 min-h-screen pt-8 pb-24 sm:py-16" x-data="addressManager()">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div>
                <div class="inline-flex items-center space-x-2 bg-amber-100/50 border border-amber-200 text-amber-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Delivery Locations</span>
                </div>
                <h1 class="font-serif-royal text-3xl sm:text-4xl font-bold text-[#4A2C1D]">
                    Saved Addresses
                </h1>
                <p class="text-sm text-stone-500 mt-2 max-w-xl leading-relaxed">
                    Manage your delivery locations for swift, 1-click checkout. Keep your addresses updated to receive parcels safely at your preferred doorstep.
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
            <a href="{{ route('account.addresses') }}" class="pb-3 border-b-2 border-[#996E2E] text-[#4A2C1D] font-bold text-sm tracking-wide whitespace-nowrap">
                Saved Addresses
            </a>
            <a href="{{ route('account.settings') }}" class="pb-3 border-b-2 border-transparent text-stone-500 hover:text-[#4A2C1D] font-semibold text-sm tracking-wide whitespace-nowrap transition">
                Account Security
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <p class="font-bold mb-1">Please correct the following:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Action Bar: Add New Address Button -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <p class="text-xs text-stone-500 font-medium">
                Showing <strong class="text-[#4A2C1D]">{{ $addresses->count() }}</strong> saved delivery {{ Str::plural('address', $addresses->count()) }}
            </p>
            <button type="button" @click="openAddModal = true" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-[#4A2C1D] to-[#2E180E] text-[#E7C77B] hover:shadow-lg hover:scale-105 font-bold text-xs uppercase tracking-wider transition shadow-sm cursor-pointer">
                <svg class="w-4 h-4 text-[#D4AF6A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Add New Address</span>
            </button>
        </div>

        @if($addresses->isEmpty())
            <div class="bg-white rounded-3xl border border-[#D4AF6A]/30 p-10 sm:p-14 text-center shadow-sm">
                <div class="w-20 h-20 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center mx-auto mb-5 text-[#996E2E]">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="font-serif-royal text-2xl font-bold text-[#4A2C1D]">No Saved Delivery Addresses</h3>
                <p class="text-sm text-stone-500 mt-2 mb-8 max-w-md mx-auto leading-relaxed">
                    You haven't saved any delivery address yet. Add your home or office address for a swift, seamless checkout experience.
                </p>
                <button type="button" @click="openAddModal = true" class="inline-flex items-center space-x-2 bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-[#E7C77B] px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider hover:shadow-lg hover:scale-105 transition-all duration-300 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Add Delivery Address</span>
                </button>
            </div>
        @else
            <!-- Address Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($addresses as $addr)
                    <div class="bg-white rounded-3xl border {{ $addr->is_default ? 'border-[#996E2E] ring-2 ring-[#D4AF6A]/30' : 'border-[#D4AF6A]/30' }} p-5 sm:p-6 shadow-xs hover:shadow-md transition duration-300 flex flex-col justify-between relative overflow-hidden">
                        
                        @if($addr->is_default)
                            <div class="absolute top-0 right-0 bg-gradient-to-l from-[#4A2C1D] to-[#996E2E] text-[#E7C77B] text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-wider shadow-xs flex items-center gap-1">
                                <svg class="w-3 h-3 text-[#E7C77B]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <span>Default Address</span>
                            </div>
                        @endif

                        <div>
                            <!-- Header Info -->
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-8 h-8 rounded-full bg-[#FAF7F0] border border-[#D4AF6A]/40 flex items-center justify-center text-[#996E2E] shrink-0 font-bold text-xs">
                                    {{ substr($addr->name, 0, 1) }}
                                </span>
                                <div>
                                    <h3 class="font-serif-royal font-bold text-base sm:text-lg text-[#4A2C1D] leading-tight">{{ $addr->name }}</h3>
                                    <p class="text-[11px] text-stone-400 font-medium">Added {{ $addr->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <!-- Full Address Body -->
                            <div class="space-y-1.5 text-xs text-stone-600 bg-[#FAF7F0]/40 p-4 rounded-2xl border border-[#D4AF6A]/20 mb-4 min-w-0">
                                <p class="leading-relaxed text-stone-700 font-medium break-words">{{ $addr->address_line }}</p>
                                @if($addr->street && $addr->street !== $addr->address_line)
                                    <p class="text-stone-600 break-words"><span class="text-stone-400">Area:</span> {{ $addr->street }}</p>
                                @endif
                                @if($addr->landmark)
                                    <p class="text-stone-600 break-words"><span class="text-stone-400">Landmark:</span> Near {{ $addr->landmark }}</p>
                                @endif
                                <p class="font-semibold text-[#4A2C1D] pt-1">
                                    {{ $addr->city }}, {{ $addr->state }} — <span class="font-mono text-sm tracking-wider font-bold text-[#996E2E]">{{ $addr->pincode }}</span>
                                </p>
                                <div class="pt-2 mt-2 border-t border-[#D4AF6A]/20 flex flex-wrap gap-x-4 gap-y-1 text-[11px]">
                                    <p class="flex items-center gap-1.5 text-stone-600">
                                        <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        <span class="font-medium font-mono text-stone-800">{{ $addr->mobile }}</span>
                                    </p>
                                    <p class="flex items-center gap-1.5 text-stone-600 min-w-0">
                                        <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        <span class="break-all min-w-0 text-stone-700">{{ $addr->email }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-3 border-t border-stone-100 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                @if(!$addr->is_default)
                                    <form action="{{ route('account.addresses.default', $addr->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-[#996E2E] hover:text-[#4A2C1D] hover:underline transition cursor-pointer">
                                            Make Default
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="editAddress({{ json_encode($addr) }})" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-stone-200 text-stone-700 hover:bg-stone-50 hover:border-[#D4AF6A] font-semibold text-xs transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    <span>Edit</span>
                                </button>

                                <form action="{{ route('account.addresses.destroy', $addr->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this delivery address?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 font-semibold text-xs transition cursor-pointer" title="Delete Address">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <!-- MODAL: ADD NEW ADDRESS -->
    <div x-show="openAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="min-h-screen px-4 text-center flex items-center justify-center p-0">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="openAddModal = false"></div>
            
            <div class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all my-8 max-w-lg w-full z-10 border border-[#D4AF6A]/40 p-6 sm:p-8 relative">
                
                <div class="flex items-center justify-between pb-4 border-b border-[#D4AF6A]/20 mb-6">
                    <div>
                        <h3 class="font-serif-royal text-xl font-bold text-[#4A2C1D]">Add New Delivery Address</h3>
                        <p class="text-xs text-stone-500 mt-0.5">Enter details for fast, reliable doorstep courier dispatches</p>
                    </div>
                    <button type="button" @click="openAddModal = false" class="w-8 h-8 rounded-full bg-stone-100 text-stone-500 hover:text-stone-800 flex items-center justify-center transition cursor-pointer">
                        ✕
                    </button>
                </div>

                <form action="{{ route('account.addresses.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Full Recipient Name *</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required placeholder="e.g. Maharani Devi" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Mobile Number (10 Digits) *</label>
                            <input type="tel" name="mobile" pattern="[0-9]{10}" maxlength="10" value="{{ old('mobile') }}" required placeholder="e.g. 9876543210" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Email Address (for Dispatch Updates) *</label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">PIN Code (6 Digits) *</label>
                            <div class="relative">
                                <input type="text" name="pincode" x-model="newPincode" @input="fetchNewPin" required pattern="[0-9]{6}" maxlength="6" inputmode="numeric" placeholder="e.g. 380001" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 pr-7 focus:border-[#D4AF6A] focus:outline-hidden">
                                <div x-show="pinLoading" class="absolute right-2 top-2.5">
                                    <svg class="w-4 h-4 animate-spin text-[#996E2E]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">City *</label>
                            <input type="text" name="city" x-model="newCity" required placeholder="e.g. Ahmedabad" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">State *</label>
                            <select name="state" x-model="newState" required class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden bg-white">
                                <option value="">Select State</option>
                                <template x-for="st in states" :key="st">
                                    <option :value="st" x-text="st"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Locality / Area / Street *</label>
                            <input type="text" name="street" x-model="newStreet" required placeholder="e.g. Nana Chiloda" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Flat / House No. / Building *</label>
                            <input type="text" name="address_line" required placeholder="e.g. B-111, Aatrey Ivaan 5" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Nearby Landmark (Optional)</label>
                            <input type="text" name="landmark" placeholder="e.g. Near SP Ring Road Circle" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Address Tag</label>
                            <select name="address_type" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden bg-white">
                                <option value="Home">Home (All-day delivery)</option>
                                <option value="Office">Office (10 AM - 6 PM)</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-stone-700">
                            <input type="checkbox" name="is_default" value="1" class="rounded text-[#996E2E] focus:ring-[#D4AF6A] border-stone-300">
                            <span>Make this my default delivery address</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-[#D4AF6A]/20 flex items-center justify-end gap-3">
                        <button type="button" @click="openAddModal = false" class="px-5 py-2.5 rounded-full border border-stone-300 text-stone-600 hover:bg-stone-50 font-bold text-xs uppercase tracking-wider transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-[#4A2C1D] to-[#2E180E] text-[#E7C77B] font-bold text-xs uppercase tracking-wider hover:shadow-lg hover:scale-105 transition duration-300 cursor-pointer">
                            Save Address
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- MODAL: EDIT ADDRESS -->
    <div x-show="openEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="min-h-screen px-4 text-center flex items-center justify-center p-0">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="openEditModal = false"></div>
            
            <div class="inline-block bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all my-8 max-w-lg w-full z-10 border border-[#D4AF6A]/40 p-6 sm:p-8 relative">
                
                <div class="flex items-center justify-between pb-4 border-b border-[#D4AF6A]/20 mb-6">
                    <div>
                        <h3 class="font-serif-royal text-xl font-bold text-[#4A2C1D]">Edit Delivery Address</h3>
                        <p class="text-xs text-stone-500 mt-0.5">Modify recipient or destination details</p>
                    </div>
                    <button type="button" @click="openEditModal = false" class="w-8 h-8 rounded-full bg-stone-100 text-stone-500 hover:text-stone-800 flex items-center justify-center transition cursor-pointer">
                        ✕
                    </button>
                </div>

                <form :action="editActionUrl" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Full Recipient Name *</label>
                            <input type="text" name="name" x-model="editData.name" required class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Mobile Number *</label>
                            <input type="tel" name="mobile" x-model="editData.mobile" pattern="[0-9]{10}" maxlength="10" required class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Email Address *</label>
                        <input type="email" name="email" x-model="editData.email" required class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">PIN Code *</label>
                            <input type="text" name="pincode" x-model="editData.pincode" required pattern="[0-9]{6}" maxlength="6" inputmode="numeric" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">City *</label>
                            <input type="text" name="city" x-model="editData.city" required class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">State *</label>
                            <select name="state" x-model="editData.state" required class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden bg-white">
                                <template x-for="st in states" :key="st">
                                    <option :value="st" :selected="editData.state === st" x-text="st"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Locality / Area *</label>
                            <input type="text" name="street" x-model="editData.street" required class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Flat / House No. / Building *</label>
                            <input type="text" name="address_line" x-model="editData.address_line" required class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Nearby Landmark (Optional)</label>
                        <input type="text" name="landmark" x-model="editData.landmark" class="w-full border border-[#D4AF6A]/50 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-hidden">
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-stone-700">
                            <input type="checkbox" name="is_default" value="1" :checked="editData.is_default" class="rounded text-[#996E2E] focus:ring-[#D4AF6A] border-stone-300">
                            <span>Set as default delivery address</span>
                        </label>
                    </div>

                    <div class="pt-4 border-t border-[#D4AF6A]/20 flex items-center justify-end gap-3">
                        <button type="button" @click="openEditModal = false" class="px-5 py-2.5 rounded-full border border-stone-300 text-stone-600 hover:bg-stone-50 font-bold text-xs uppercase tracking-wider transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-[#4A2C1D] to-[#2E180E] text-[#E7C77B] font-bold text-xs uppercase tracking-wider hover:shadow-lg hover:scale-105 transition duration-300 cursor-pointer">
                            Update Address
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

<script>
function addressManager() {
    return {
        openAddModal: false,
        openEditModal: false,
        pinLoading: false,
        newPincode: '',
        newCity: '',
        newState: '',
        newStreet: '',
        editData: {},
        editActionUrl: '',
        states: [
            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
            'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka',
            'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram',
            'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu',
            'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
            'Andaman & Nicobar', 'Chandigarh', 'Dadra & Nagar Haveli',
            'Delhi', 'Jammu & Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
        ],

        async fetchNewPin() {
            if (this.newPincode && this.newPincode.length === 6) {
                this.pinLoading = true;
                try {
                    const res = await fetch(`https://api.postalpincode.in/pincode/${this.newPincode}`);
                    const data = await res.json();
                    if (data && data[0] && data[0].Status === 'Success') {
                        const po = data[0].PostOffice[0];
                        if (po) {
                            this.newCity = po.District || po.Block;
                            this.newState = po.State;
                            if (!this.newStreet && po.Name) {
                                this.newStreet = po.Name;
                            }
                        }
                    }
                } catch(e) {}
                this.pinLoading = false;
            }
        },

        editAddress(addr) {
            this.editData = Object.assign({}, addr);
            this.editActionUrl = `/account/addresses/${addr.id}`;
            this.openEditModal = true;
        }
    };
}
</script>
@endsection
