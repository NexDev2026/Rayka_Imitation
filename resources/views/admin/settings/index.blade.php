@php /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $allCategories */ @endphp
@extends('admin.layouts.admin')

@section('title', 'Store Settings & Payment QR')
@section('page_title', 'Store Settings & QR')

@section('content')
<div class="max-w-4xl bg-white rounded-2xl border border-stone-200 p-4 sm:p-8 shadow-xs">

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8 text-xs">
        @csrf

        <!-- 0. STOREFRONT ORDERING & BUY BUTTONS VISIBILITY (1-CLICK CATALOG MODE) -->
        <div class="p-4 sm:p-6 rounded-2xl {{ $settings['hide_order_buttons'] == '1' ? 'bg-amber-50/90 border-2 border-amber-400' : 'bg-emerald-50/80 border-2 border-emerald-400' }} shadow-xs transition-colors space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-3 border-b {{ $settings['hide_order_buttons'] == '1' ? 'border-amber-300' : 'border-emerald-300' }}">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl shrink-0 shadow-xs"
                         style="background-color: {{ $settings['hide_order_buttons'] == '1' ? '#D97706' : '#059669' }} !important; color: #FFFFFF !important;">
                        @if($settings['hide_order_buttons'] == '1')
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                                Storefront "Add to Bag" & "Buy Now" Buttons Control
                            </h3>
                            @if($settings['hide_order_buttons'] == '1')
                                <span class="text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider"
                                      style="background-color: #D97706 !important; color: #FFFFFF !important;">
                                    Currently: Buttons are Hidden (Catalog Mode)
                                </span>
                            @else
                                <span class="text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider"
                                      style="background-color: #059669 !important; color: #FFFFFF !important;">
                                    Currently: Buttons are Live (Ordering ON)
                                </span>
                            @endif
                        </div>
                        <p class="text-stone-600 text-[11px] mt-0.5 leading-relaxed">
                            One-click control to hide all purchase and add-to-bag buttons across product cards, product details, wishlist, and mobile sticky bars while updating prices & product details.
                        </p>
                    </div>
                </div>

                <!-- 1-Click Fast Toggle Button -->
                <div class="shrink-0">
                    <button type="submit" 
                            formaction="{{ route('admin.settings.toggle_order_buttons') }}"
                            formnovalidate
                            style="background-color: {{ $settings['hide_order_buttons'] == '1' ? '#047857' : '#D97706' }} !important; color: #FFFFFF !important;"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-200 shadow-md hover:brightness-110 flex items-center justify-center gap-2 cursor-pointer active:scale-95 text-white">
                        @if($settings['hide_order_buttons'] == '1')
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>1-Click Show Buttons (Make Live)</span>
                        @else
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            <span>1-Click Hide Buttons (Catalog Mode)</span>
                        @endif
                    </button>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-stone-700 mb-1">Customer Notification Message (shown on product page & bag while buttons are hidden):</label>
                <input type="text" name="order_buttons_notice" value="{{ old('order_buttons_notice', $settings['order_buttons_notice']) }}" class="w-full border rounded-lg p-2.5 text-xs bg-white text-stone-800" placeholder="Online ordering is temporarily paused while we update our product catalog and pricing. You can still explore all specifications and photos.">
                <p class="text-[10px] text-stone-500 mt-1">When hidden, customers can browse all products, view full gallery photos, specs, and send direct WhatsApp inquiries without placing incomplete/wrong-price orders.</p>
            </div>
        </div>

        <!-- 0.5. CASH ON DELIVERY (COD) & DYNAMIC ADVANCE PAYMENT ENGINE -->
        <div class="p-4 sm:p-6 rounded-2xl {{ $settings['cod_enabled'] == '1' ? 'bg-[#FAF7F0] border-2 border-[#D4AF6A]' : 'bg-stone-100/90 border-2 border-stone-300' }} shadow-xs transition-colors space-y-5"
             x-data="{
                codEnabled: {{ $settings['cod_enabled'] == '1' ? 'true' : 'false' }},
                codAdvance: {{ (int) ($settings['cod_advance_percentage'] ?? 20) }},
                codExtra: {{ (float) ($settings['cod_extra_charge'] ?? 0) }},
                codNotice: '{{ addslashes($settings['cod_notice'] ?? 'Pay advance online via UPI to confirm booking. Remaining balance can be paid on delivery via Cash or QR.') }}',
                sampleOrderTotal: 1000,
                calcAdvance() {
                    let total = this.sampleOrderTotal + (parseFloat(this.codExtra) || 0);
                    return Math.round(total * (this.codAdvance / 100));
                },
                calcBalance() {
                    let total = this.sampleOrderTotal + (parseFloat(this.codExtra) || 0);
                    return Math.max(0, total - this.calcAdvance());
                }
             }">

            <!-- Hidden input preserves current cod_enabled status on general form save -->
            <input type="hidden" name="cod_enabled" value="{{ $settings['cod_enabled'] == '1' ? '1' : '0' }}">

            <!-- Header with High-Contrast Turn On / Turn Off Action -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-[#D4AF6A]/30">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-xs"
                         style="background-color: {{ $settings['cod_enabled'] == '1' ? '#4A2C1D' : '#4B5563' }} !important; color: {{ $settings['cod_enabled'] == '1' ? '#E7C77B' : '#FFFFFF' }} !important;">
                        <svg class="w-6 h-6 shrink-0" style="color: {{ $settings['cod_enabled'] == '1' ? '#E7C77B' : '#FFFFFF' }} !important;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                                Cash on Delivery (COD) & Dynamic Advance Payment Engine
                            </h3>
                            @if($settings['cod_enabled'] == '1')
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-xs"
                                      style="background-color: #059669 !important; color: #FFFFFF !important;">
                                    <span class="w-2 h-2 rounded-full" style="background-color: #FFFFFF !important;"></span>
                                    <span style="color: #FFFFFF !important;">CURRENTLY: COD ACTIVE ON SITE</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-xs"
                                      style="background-color: #4B5563 !important; color: #FFFFFF !important;">
                                    <span class="w-2 h-2 rounded-full" style="background-color: #D1D5DB !important;"></span>
                                    <span style="color: #FFFFFF !important;">CURRENTLY: COD TURNED OFF (HIDDEN)</span>
                                </span>
                            @endif
                        </div>
                        <p class="text-stone-600 text-[11px] mt-0.5 leading-relaxed">
                            Configure customer Cash on Delivery availability, mandatory token advance percentage (10% to 100%), extra delivery handling fee, customer notification, and after-delivery final payment QR.
                        </p>
                    </div>
                </div>

                <!-- High-Contrast Turn On / Turn Off Button -->
                <div class="shrink-0">
                    @if($settings['cod_enabled'] == '1')
                        <button type="submit" 
                                formaction="{{ route('admin.settings.toggle_cod') }}"
                                formnovalidate
                                style="background-color: #B91C1C !important; color: #FFFFFF !important;"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg hover:brightness-110 flex items-center justify-center gap-2 cursor-pointer active:scale-95 text-white transition-all">
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            <span class="text-white font-bold">1-Click Turn Off COD</span>
                        </button>
                    @else
                        <button type="submit" 
                                formaction="{{ route('admin.settings.toggle_cod') }}"
                                formnovalidate
                                style="background-color: #047857 !important; color: #FFFFFF !important;"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg hover:brightness-110 flex items-center justify-center gap-2 cursor-pointer active:scale-95 text-white transition-all">
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-white font-bold">1-Click Turn On COD</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Informational Status Banner (Clear explanation without redundant checkbox) -->
            @if($settings['cod_enabled'] == '1')
                <div class="p-3.5 rounded-xl border flex items-center justify-between gap-3 text-xs shadow-xs"
                     style="background-color: #ECFDF5 !important; border-color: #A7F3D0 !important; color: #065F46 !important;">
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0"
                              style="background-color: #059669 !important; color: #FFFFFF !important;">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <p class="leading-relaxed font-medium" style="color: #064E3B !important;">
                            <strong style="color: #065F46 !important;">Cash on Delivery is currently ENABLED.</strong> Customers can choose COD at checkout and pay the required advance percentage online.
                        </p>
                    </div>
                    <span class="hidden sm:inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0 border"
                          style="background-color: #D1FAE5 !important; border-color: #A7F3D0 !important; color: #065F46 !important;">
                        Live on Checkout
                    </span>
                </div>
            @else
                <div class="p-3.5 rounded-xl border flex items-center justify-between gap-3 text-xs shadow-xs"
                     style="background-color: #F3F4F6 !important; border-color: #D1D5DB !important; color: #374151 !important;">
                    <div class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0"
                              style="background-color: #4B5563 !important; color: #FFFFFF !important;">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                        <p class="leading-relaxed font-medium" style="color: #1F2937 !important;">
                            <strong style="color: #111827 !important;">Cash on Delivery is currently TURNED OFF.</strong> The COD payment option is completely hidden from customers at checkout (Full Online UPI only).
                        </p>
                    </div>
                    <span class="hidden sm:inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0 border"
                          style="background-color: #E5E7EB !important; border-color: #D1D5DB !important; color: #374151 !important;">
                        Hidden from Site
                    </span>
                </div>
            @endif

            <!-- 10% to 100% Dynamic Advance Selection System -->
            <div class="p-4 bg-white rounded-xl border border-[#D4AF6A]/40 shadow-2xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-stone-100">
                    <div>
                        <label class="block font-bold text-xs text-[#4A2C1D]">
                            COD Advance Payment Percentage (10% to 100%) *
                        </label>
                        <p class="text-[11px] text-stone-500">
                            Customer must pay this percentage online via UPI before order is dispatched. The remainder is collected on delivery.
                        </p>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <span class="text-xs font-semibold text-stone-500">Advance:</span>
                        <div class="flex items-center">
                            <input type="number" 
                                   name="cod_advance_percentage" 
                                   min="10" 
                                   max="100" 
                                   x-model.number="codAdvance" 
                                   value="{{ old('cod_advance_percentage', $settings['cod_advance_percentage'] ?? 20) }}"
                                   class="w-20 border-2 border-[#D4AF6A] rounded-lg p-1.5 font-mono text-center font-bold text-sm text-[#4A2C1D] bg-[#FAF7F0] focus:outline-hidden">
                            <span class="ml-1 text-sm font-bold text-[#4A2C1D]">%</span>
                        </div>
                    </div>
                </div>

                <!-- Interactive Preset Quick-Selection Chips (Instantly rendered in HTML, crystal-clear buttons) -->
                <div>
                    <label class="block text-[11px] font-semibold text-stone-600 mb-2">Preset Quick-Select System (Click to Set):</label>
                    <div class="grid grid-cols-4 sm:grid-cols-8 gap-1.5 sm:gap-2">
                        @foreach([10, 15, 20, 25, 30, 40, 50, 100] as $preset)
                            <button type="button" 
                                    @click="codAdvance = {{ $preset }}"
                                    :class="codAdvance == {{ $preset }} 
                                        ? 'border-[#4A2C1D] shadow-sm font-bold ring-2 ring-[#D4AF6A]' 
                                        : 'border-[#D4AF6A]/40 hover:bg-[#D4AF6A]/20 font-medium'"
                                    :style="codAdvance == {{ $preset }}
                                        ? 'background-color: #4A2C1D !important; color: #E7C77B !important;'
                                        : 'background-color: #FAF7F0 !important; color: #4A2C1D !important;'"
                                    style="background-color: {{ (int)($settings['cod_advance_percentage'] ?? 20) == $preset ? '#4A2C1D' : '#FAF7F0' }}; color: {{ (int)($settings['cod_advance_percentage'] ?? 20) == $preset ? '#E7C77B' : '#4A2C1D' }};"
                                    class="py-2.5 px-1 text-center rounded-xl border text-xs transition cursor-pointer flex flex-col items-center justify-center min-w-0">
                                <span class="text-xs font-bold">{{ $preset }}%</span>
                                <span class="text-[9px] opacity-80 truncate">{{ $preset === 100 ? 'Full' : ($preset === 50 ? 'Half' : ($preset === 10 ? 'Min Token' : 'Token')) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Smooth Percentage Range Slider -->
                <div class="space-y-1 pt-1">
                    <div class="flex justify-between text-[11px] text-stone-500 font-medium">
                        <span>10% (Minimum Token)</span>
                        <span class="font-bold text-[#996E2E]" x-text="codAdvance + '% Online Advance'"></span>
                        <span>100% (Full Advance)</span>
                    </div>
                    <input type="range" 
                           min="10" 
                           max="100" 
                           step="5" 
                           x-model.number="codAdvance" 
                           class="w-full h-2.5 bg-stone-200 rounded-lg appearance-none cursor-pointer accent-[#996E2E]">
                </div>

                <!-- Real-Time Calculation Explainer Card -->
                <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/80 text-xs text-amber-900 space-y-1.5">
                    <div class="flex items-center gap-1.5 font-bold text-[11px] uppercase tracking-wider text-amber-950">
                        <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Real-Time Math Demonstration (For ₹1,000 Order):</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 font-medium">
                        <div class="bg-white p-2 rounded-lg border border-amber-200/60">
                            <span class="text-[10px] text-stone-500 block">Advance Payable Now:</span>
                            <span class="text-xs font-bold text-emerald-800" x-text="'₹' + calcAdvance() + ' (' + codAdvance + '%)'"></span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-amber-200/60">
                            <span class="text-[10px] text-stone-500 block">Balance on Delivery:</span>
                            <span class="text-xs font-bold text-[#4A2C1D]" x-text="'₹' + calcBalance() + ' (' + (100 - codAdvance) + '%)'"></span>
                        </div>
                        <div class="bg-white p-2 rounded-lg border border-amber-200/60">
                            <span class="text-[10px] text-stone-500 block">Extra COD Charge:</span>
                            <span class="text-xs font-bold text-stone-700" x-text="(parseFloat(codExtra) > 0 ? '+ ₹' + codExtra : '₹0 (Free)')"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Extra Fee & Notice Configuration -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">
                        Extra COD Handling Charge (₹)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-stone-500 font-bold">₹</span>
                        <input type="number" 
                               name="cod_extra_charge" 
                               min="0" 
                               step="1" 
                               x-model.number="codExtra" 
                               value="{{ old('cod_extra_charge', $settings['cod_extra_charge'] ?? '0') }}" 
                               class="w-full border rounded-lg pl-8 p-2.5 font-mono text-xs bg-white text-stone-800" 
                               placeholder="e.g. 50 (or 0 for no extra fee)">
                    </div>
                    <p class="text-[10px] text-stone-500 mt-1">
                        Optional extra rupees added to the order total when customer chooses Cash on Delivery to offset courier COD processing costs.
                    </p>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">
                        Customer Notification & Description Banner (Shown on COD selection):
                    </label>
                    <textarea name="cod_notice" 
                              rows="2" 
                              x-model="codNotice" 
                              class="w-full border rounded-lg p-2.5 text-xs bg-white text-stone-800" 
                              placeholder="e.g. Pay advance online via UPI to confirm booking. Remaining balance can be paid on delivery via Cash or QR.">{{ old('cod_notice', $settings['cod_notice'] ?? 'Pay advance online via UPI to confirm booking. Remaining balance can be paid on delivery via Cash or QR.') }}</textarea>
                    <p class="text-[10px] text-stone-500 mt-1">
                        Explains the advance booking requirement and final cash/QR payment terms directly to the customer at checkout.
                    </p>
                </div>
            </div>

            <!-- After-Delivery Final Payment QR Code Settings -->
            <div class="p-4 bg-white rounded-xl border border-[#D4AF6A]/40 shadow-2xs space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-stone-100">
                    <div>
                        <h4 class="font-serif-royal text-sm font-bold text-[#4A2C1D] flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>After-Delivery Final Balance Payment QR & Payee Details</span>
                        </h4>
                        <p class="text-[11px] text-stone-500">
                            Option to set a dedicated UPI QR code and VPA for customers paying the remaining balance after delivery or to courier. If left empty, the store's primary UPI QR code is automatically used.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center" x-data="{ isDropping: false, fileSelected: false, fileName: '', filePreview: null }">
                    <div class="space-y-3">
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Delivery UPI ID (VPA) (Optional)</label>
                            <input type="text" name="cod_delivery_upi_id" value="{{ old('cod_delivery_upi_id', $settings['cod_delivery_upi_id'] ?? '') }}" class="w-full border rounded-lg p-2.5 font-mono text-xs" placeholder="Leave empty to use store primary UPI ID ({{ $settings['upi_id'] ?: 'Not set' }})">
                        </div>

                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Delivery Payee Display Name (Optional)</label>
                            <input type="text" name="cod_delivery_payee_name" value="{{ old('cod_delivery_payee_name', $settings['cod_delivery_payee_name'] ?? '') }}" class="w-full border rounded-lg p-2.5 text-xs" placeholder="Leave empty to use store payee name ({{ $settings['upi_payee_name'] ?: 'Not set' }})">
                        </div>

                        <div>
                            <label class="block font-semibold text-stone-700 mb-1.5">Upload Dedicated Delivery QR Code Image</label>
                            <div class="relative" 
                                 @dragover.prevent="isDropping = true" 
                                 @dragleave.prevent="isDropping = false" 
                                 @drop.prevent="isDropping = false; if($event.dataTransfer.files.length){ $refs.deliveryQrInput.files = $event.dataTransfer.files; fileSelected = true; fileName = $refs.deliveryQrInput.files[0].name; let r = new FileReader(); r.onload = (e) => { filePreview = e.target.result; }; r.readAsDataURL($refs.deliveryQrInput.files[0]); }">
                                <label :class="isDropping ? 'border-[#4A2C1D] bg-[#FAF7F0]' : 'border-[#D4AF6A] bg-stone-50/50 hover:bg-[#FAF7F0]'" 
                                       class="flex flex-col items-center justify-center w-full min-h-[130px] border-2 border-dashed rounded-xl cursor-pointer transition-colors group p-3.5">
                                    
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <!-- Empty State -->
                                        <div x-show="!fileSelected" class="space-y-1">
                                            <svg class="w-7 h-7 mx-auto text-[#996E2E] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                            <p class="text-xs text-stone-700 font-semibold"><span class="font-bold text-[#4A2C1D] underline">Click to upload</span> or drag and drop</p>
                                            <p class="text-[10px] text-stone-400">PNG, JPG, WEBP up to 10MB (Square QR recommended)</p>
                                        </div>
                                        
                                        <!-- Selected State -->
                                        <div x-show="fileSelected" x-cloak class="space-y-1">
                                            <svg class="w-6 h-6 mx-auto text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p class="text-xs font-bold text-emerald-800 truncate max-w-[220px]" x-text="fileName"></p>
                                            <p class="text-[10px] text-stone-400">Click or drag to replace selected QR</p>
                                        </div>
                                    </div>
                                    
                                    <input x-ref="deliveryQrInput" type="file" name="cod_delivery_qr_file" accept="image/*" class="hidden" 
                                           @change="if($el.files.length){ fileSelected = true; fileName = $el.files[0].name; let r = new FileReader(); r.onload = (e) => { filePreview = e.target.result; }; r.readAsDataURL($el.files[0]); } else { fileSelected = false; fileName = ''; filePreview = null; }">
                                </label>
                            </div>
                            <p class="text-[10px] text-stone-500 mt-1">If left empty, the store's primary UPI QR code will be used automatically.</p>
                        </div>
                    </div>

                    <div class="text-center p-3.5 bg-[#FAF7F0] rounded-xl border border-[#D4AF6A]/40 min-h-[170px] flex flex-col items-center justify-center">
                        <span class="text-[10px] uppercase font-bold text-[#996E2E] block mb-1.5">Active Delivery QR Preview:</span>
                        
                        <!-- When new file selected in input -->
                        <div x-show="filePreview" x-cloak class="space-y-1">
                            <img :src="filePreview" alt="New Delivery QR Preview" class="w-32 h-32 object-contain mx-auto border rounded-lg p-1 bg-white shadow-2xs">
                            <span class="text-[10px] text-emerald-700 font-bold block">New QR Selected (Save Settings to Apply)</span>
                        </div>

                        <!-- Current saved state -->
                        <div x-show="!filePreview" class="space-y-1">
                            @if(!empty($settings['cod_delivery_qr_image']))
                                <img src="{{ asset($settings['cod_delivery_qr_image']) }}" alt="Dedicated Delivery QR" class="w-32 h-32 object-contain mx-auto border rounded-lg p-1 bg-white shadow-2xs">
                                <span class="text-[10px] text-emerald-700 font-semibold block">Dedicated Delivery QR Configured</span>
                                <label class="inline-flex items-center gap-1.5 mt-1.5 text-[11px] text-red-600 hover:text-red-700 cursor-pointer">
                                    <input type="checkbox" name="remove_cod_delivery_qr" value="1" class="rounded border-stone-300 text-red-600 focus:ring-red-500">
                                    <span>Reset to Primary Store QR</span>
                                </label>
                            @else
                                <img src="{{ asset($settings['qr_code_image']) }}" alt="Primary Store QR" class="w-32 h-32 object-contain mx-auto border rounded-lg p-1 bg-white shadow-2xs">
                                <span class="text-[10px] text-stone-500 font-medium block">Using Primary Store UPI QR (Default)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Frontend Customer Preview Card (Mobile Responsive) -->
            <div class="p-3.5 bg-gradient-to-r from-[#FAF7F0] to-white rounded-xl border border-[#D4AF6A]/60 shadow-2xs space-y-2">
                <span class="text-[10px] uppercase font-bold tracking-wider text-[#996E2E] block">Live Storefront Checkout Preview (How Customer Sees COD):</span>
                <div class="p-3.5 rounded-xl bg-white border border-[#D4AF6A] shadow-xs space-y-2.5 max-w-xl">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-[#4A2C1D]">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            Cash on Delivery (COD) Selected
                        </span>
                        <span class="bg-[#4A2C1D] text-[#E7C77B] text-[10px] font-bold px-2 py-0.5 rounded-full" x-text="codAdvance + '% Advance Required'"></span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-[#FAF7F0] border border-[#D4AF6A]/40 text-[11px] text-stone-700 leading-relaxed" x-text="codNotice || 'Pay advance online via UPI to confirm booking. Remaining balance can be paid on delivery via Cash or QR.'"></div>
                    <div class="flex items-center justify-between text-xs pt-1 border-t border-stone-100">
                        <span class="text-stone-600">Advance to Pay Now via QR:</span>
                        <span class="font-bold text-emerald-800" x-text="'₹' + calcAdvance()"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-stone-600">Balance to Pay on Delivery:</span>
                        <span class="font-bold text-[#4A2C1D]" x-text="'₹' + calcBalance()"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1. PAYMENT QR CODE & UPI SETTINGS (Crucial for Phase 1 Checkout) -->
        <div class="p-4 sm:p-6 rounded-2xl bg-[#FAF7F0] border-2 border-[#D4AF6A] space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#D4AF6A]/40">
                <div>
                    <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                        Static UPI QR Code & Payee Information
                    </h3>
                    <p class="text-stone-600 text-[11px]">This QR code is displayed to customers at checkout for payment proof uploads.</p>
                </div>
                <span class="bg-[#4A2C1D] text-[#E7C77B] text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">
                    Phase 1 Gate
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center" x-data="{ isDropping: false, fileSelected: false, fileName: '', filePreview: null }">
                <div class="space-y-3">
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Official UPI ID (VPA) *</label>
                        <input type="text" name="upi_id" value="{{ old('upi_id', $settings['upi_id']) }}" required class="w-full border rounded-lg p-2.5 font-mono" placeholder="e.g. merchant@icici or 9876543210@upi">
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">UPI Payee Display Name</label>
                        <input type="text" name="upi_payee_name" value="{{ old('upi_payee_name', $settings['upi_payee_name']) }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. Rayka Imitation Jewellery Pvt Ltd">
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-2">Upload New UPI QR Code Image</label>
                        <div class="relative" 
                             @dragover.prevent="isDropping = true" 
                             @dragleave.prevent="isDropping = false" 
                             @drop.prevent="isDropping = false; if($event.dataTransfer.files.length){ $refs.qrInput.files = $event.dataTransfer.files; fileSelected = true; fileName = $refs.qrInput.files[0].name; let r = new FileReader(); r.onload = (e) => { filePreview = e.target.result; }; r.readAsDataURL($refs.qrInput.files[0]); }"
                        >
                            <label :class="isDropping ? 'border-[#4A2C1D] bg-[#FAF7F0]' : 'border-stone-300 bg-white hover:bg-stone-50'" 
                                   class="flex flex-col items-center justify-center w-full min-h-[140px] border-2 border-dashed rounded-xl cursor-pointer transition-colors group">
                                
                                <div class="flex flex-col items-center justify-center py-4 text-center px-4">
                                    <!-- Empty State -->
                                    <svg x-show="!fileSelected" class="w-8 h-8 mb-2 text-stone-400 group-hover:text-[#996E2E] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p x-show="!fileSelected" class="mb-1 text-[11px] text-stone-500 font-semibold"><span class="font-bold text-[#4A2C1D]">Click to upload</span> or drag and drop</p>
                                    <p x-show="!fileSelected" class="text-[10px] text-stone-400">PNG, JPG, WEBP (Square recommended)</p>
                                    
                                    <!-- Selected State -->
                                    <svg x-show="fileSelected" x-cloak class="w-7 h-7 mb-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p x-show="fileSelected" x-cloak class="text-[11px] font-bold text-emerald-600 truncate max-w-[200px]" x-text="fileName"></p>
                                    <p x-show="fileSelected" x-cloak class="text-[10px] text-stone-500 mt-1">Click or drag to replace</p>
                                </div>
                                
                                <input x-ref="qrInput" type="file" name="qr_code_file" accept="image/*" class="hidden" 
                                       @change="if($el.files.length){ fileSelected = true; fileName = $el.files[0].name; let r = new FileReader(); r.onload = (e) => { filePreview = e.target.result; }; r.readAsDataURL($el.files[0]); } else { fileSelected = false; fileName = ''; filePreview = null; }">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-center p-4 bg-white rounded-xl border border-[#D4AF6A]/50 min-h-[170px] flex flex-col items-center justify-center">
                    <span class="text-[10px] uppercase font-bold text-[#996E2E] block mb-2">Current Active QR Code:</span>
                    <div x-show="filePreview" x-cloak class="space-y-1">
                        <img :src="filePreview" alt="New Active QR Preview" class="w-40 h-40 object-contain mx-auto border rounded p-1 bg-white shadow-2xs">
                        <span class="text-[10px] text-emerald-700 font-bold block">New QR Selected (Save Settings to Apply)</span>
                    </div>
                    <div x-show="!filePreview">
                        <img src="{{ asset($settings['qr_code_image']) }}" alt="Active QR" class="w-40 h-40 object-contain mx-auto border rounded p-1">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. BOUTIQUE & CONTACT SETTINGS -->
        <div>
            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4">
                Boutique Profile & Contact
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Store Name</label>
                    <input type="text" name="store_name" value="{{ old('store_name', $settings['store_name']) }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. Rayka Imitation Jewellery">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Store Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $settings['tagline']) }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. Royal Heritage & 1 Gram Micro Gold Jewellery">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Helpline Phone</label>
                    <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone']) }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. +91 98765 43210">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">WhatsApp Number</label>
                    <input type="text" name="store_whatsapp" value="{{ old('store_whatsapp', $settings['store_whatsapp']) }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. +91 98765 43210">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Alternate Phone (Optional)</label>
                    <input type="text" name="store_alt_phone" value="{{ old('store_alt_phone', $settings['store_alt_phone'] ?? '') }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. +91 98765 43211">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Customer Support / Care Email</label>
                    <input type="email" name="store_email" value="{{ old('store_email', $settings['store_email']) }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. care@raykajewellery.com">
                    <p class="text-[10px] text-stone-400 mt-1">Displayed to customers in order confirmation emails, invoices, and help sections.</p>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Admin Alert / Notification Email</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email', $settings['admin_email'] ?? '') }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. admin@raykajewellery.com">
                    <p class="text-[10px] text-stone-400 mt-1">Receives instant notifications for new orders, user registrations, cancellations, and backups.</p>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Instagram Profile URL</label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. https://www.instagram.com/your_boutique">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Instagram Display Handle</label>
                    <input type="text" name="instagram_handle" value="{{ old('instagram_handle', $settings['instagram_handle'] ?? '') }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. @your_boutique">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-stone-700 mb-1">Google Maps Direction Link</label>
                    <input type="url" name="google_map_url" value="{{ old('google_map_url', $settings['google_map_url'] ?? '') }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. https://maps.app.goo.gl/your-location or Google Maps Link">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-stone-700 mb-1">Boutique Physical Address</label>
                    <textarea name="store_address" rows="2" class="w-full border rounded-lg p-2.5" placeholder="e.g. Shop No. 12, Complex Name, Near Landmark, Area, City - 380001, State">{{ old('store_address', $settings['store_address']) }}</textarea>
                </div>
            </div>
        </div>

        <!-- 3. SHIPPING & TRUST BADGE SETTINGS -->
        <div>
            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4">
                Shipping Thresholds & Trust Badges
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Free Shipping Threshold (₹)</label>
                    <input type="number" name="free_shipping_min" value="{{ old('free_shipping_min', $settings['free_shipping_min']) }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. 999">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Standard Flat Courier Fee (₹)</label>
                    <input type="number" name="shipping_flat_fee" value="{{ old('shipping_flat_fee', $settings['shipping_flat_fee']) }}" class="w-full border rounded-lg p-2.5" placeholder="e.g. 99">
                </div>

                <div class="sm:col-span-2 space-y-2">
                    <label class="block font-semibold text-stone-700">Homepage Trust Badges Text:</label>
                    <input type="text" name="trust_badge_1" value="{{ old('trust_badge_1', $settings['trust_badge_1']) }}" class="w-full border rounded-lg p-2 mb-1" placeholder="e.g. Free Express Shipping">
                    <input type="text" name="trust_badge_2" value="{{ old('trust_badge_2', $settings['trust_badge_2']) }}" class="w-full border rounded-lg p-2 mb-1" placeholder="e.g. 100% Secure Payment">
                    <input type="text" name="trust_badge_3" value="{{ old('trust_badge_3', $settings['trust_badge_3']) }}" class="w-full border rounded-lg p-2 mb-1" placeholder="e.g. Fast 7 Days Insured Delivery">
                    <input type="text" name="trust_badge_4" value="{{ old('trust_badge_4', $settings['trust_badge_4']) }}" class="w-full border rounded-lg p-2" placeholder="e.g. Heritage Quality Craftsmanship">
                </div>
            </div>
        </div>

        <!-- 3.5. PRODUCT PAGE TRUST BADGES & DELIVERY HIGHLIGHTS -->
        <div class="p-6 rounded-2xl bg-[#FAF7F0] border border-[#D4AF6A]/60 space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-[#D4AF6A]/30">
                <div>
                    <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] flex items-center space-x-2">
                        <svg class="w-5 h-5 text-[#996E2E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Product Page Delivery Badges & Guarantees</span>
                    </h3>
                    <p class="text-stone-600 text-xs mt-0.5">
                        Customize the 3 trust & delivery guarantee cards displayed directly below the Add to Bag & Buy Now buttons on every product page.
                    </p>
                </div>
                <span class="text-[10px] font-semibold text-[#996E2E] bg-white px-2.5 py-1 rounded-full border border-[#D4AF6A]/40 shadow-2xs">
                    Live On Product Pages
                </span>
            </div>

            <!-- 3 Badges Configuration Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" x-data="{
                b1_title: '{{ addslashes(old('product_badge_1_title', $settings['product_badge_1_title'] ?? 'Premium Quality')) }}',
                b1_sub: '{{ addslashes(old('product_badge_1_subtitle', $settings['product_badge_1_subtitle'] ?? '1 Gram Micro Plated')) }}',
                b2_title: '{{ addslashes(old('product_badge_2_title', $settings['product_badge_2_title'] ?? 'Authentic Warranty')) }}',
                b2_sub: '{{ addslashes(old('product_badge_2_subtitle', $settings['product_badge_2_subtitle'] ?? '7 Days Delivery')) }}',
                b3_title: '{{ addslashes(old('product_badge_3_title', $settings['product_badge_3_title'] ?? 'Fast Delivery')) }}',
                b3_sub: '{{ addslashes(old('product_badge_3_subtitle', $settings['product_badge_3_subtitle'] ?? 'Express Courier')) }}'
            }">
                <!-- Badge 1: Quality -->
                <div class="p-4 bg-white rounded-xl border border-stone-200 shadow-2xs space-y-3">
                    <div class="flex items-center space-x-2 text-[#996E2E] font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <span>Card 1 (Quality Guarantee)</span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-stone-600 mb-1">Header Title</label>
                        <input type="text" name="product_badge_1_title" x-model="b1_title" class="w-full border rounded-lg p-2 text-xs font-bold text-[#4A2C1D]" placeholder="Premium Quality">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-stone-600 mb-1">Subtitle / Note</label>
                        <input type="text" name="product_badge_1_subtitle" x-model="b1_sub" class="w-full border rounded-lg p-2 text-xs text-stone-700" placeholder="1 Gram Micro Plated">
                    </div>
                </div>

                <!-- Badge 2: Delivery & Warranty (Replaces 7-day replacement with 7-day delivery) -->
                <div class="p-4 bg-white rounded-xl border-2 border-[#D4AF6A] shadow-2xs space-y-3">
                    <div class="flex items-center space-x-2 text-[#996E2E] font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Card 2 (Authentic & 7-Day Delivery)</span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-stone-600 mb-1">Header Title</label>
                        <input type="text" name="product_badge_2_title" x-model="b2_title" class="w-full border rounded-lg p-2 text-xs font-bold text-[#4A2C1D]" placeholder="Authentic Warranty">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-stone-600 mb-1">Subtitle / Note</label>
                        <input type="text" name="product_badge_2_subtitle" x-model="b2_sub" class="w-full border rounded-lg p-2 text-xs text-stone-700" placeholder="7 Days Delivery">
                    </div>
                </div>

                <!-- Badge 3: Fast Courier -->
                <div class="p-4 bg-white rounded-xl border border-stone-200 shadow-2xs space-y-3">
                    <div class="flex items-center space-x-2 text-[#996E2E] font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Card 3 (Courier & Shipping)</span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-stone-600 mb-1">Header Title</label>
                        <input type="text" name="product_badge_3_title" x-model="b3_title" class="w-full border rounded-lg p-2 text-xs font-bold text-[#4A2C1D]" placeholder="Fast Delivery">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-stone-600 mb-1">Subtitle / Note</label>
                        <input type="text" name="product_badge_3_subtitle" x-model="b3_sub" class="w-full border rounded-lg p-2 text-xs text-stone-700" placeholder="Express Courier">
                    </div>
                </div>

                <!-- Live Frontend Preview Box (Fully Responsive for 320px Mobile to Large Desktops) -->
                <div class="md:col-span-3 pt-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 block mb-2">Live Customer Preview on Product Page:</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-3 sm:p-4 bg-white rounded-xl border border-[#D4AF6A]/40 text-center max-w-xl mx-auto shadow-2xs">
                        <div class="p-3 sm:p-2.5 rounded-lg bg-[#FAF7F0] border border-[#D4AF6A]/30 flex flex-row sm:flex-col items-center justify-center gap-2.5 sm:gap-1">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 text-[#996E2E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <div class="text-left sm:text-center min-w-0">
                                <p class="text-xs sm:text-[11px] font-bold text-[#4A2C1D] leading-tight break-words" x-text="b1_title || 'Premium Quality'"></p>
                                <p class="text-[10px] sm:text-[9px] text-stone-500 leading-tight mt-0.5 break-words" x-text="b1_sub || '1 Gram Micro Plated'"></p>
                            </div>
                        </div>
                        <div class="p-3 sm:p-2.5 rounded-lg bg-[#FAF7F0] border border-[#D4AF6A]/30 flex flex-row sm:flex-col items-center justify-center gap-2.5 sm:gap-1">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 text-[#996E2E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <div class="text-left sm:text-center min-w-0">
                                <p class="text-xs sm:text-[11px] font-bold text-[#4A2C1D] leading-tight break-words" x-text="b2_title || 'Authentic Warranty'"></p>
                                <p class="text-[10px] sm:text-[9px] text-stone-500 leading-tight mt-0.5 break-words" x-text="b2_sub || '7 Days Delivery'"></p>
                            </div>
                        </div>
                        <div class="p-3 sm:p-2.5 rounded-lg bg-[#FAF7F0] border border-[#D4AF6A]/30 flex flex-row sm:flex-col items-center justify-center gap-2.5 sm:gap-1">
                            <svg class="w-5 h-5 sm:w-4 sm:h-4 text-[#996E2E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <div class="text-left sm:text-center min-w-0">
                                <p class="text-xs sm:text-[11px] font-bold text-[#4A2C1D] leading-tight break-words" x-text="b3_title || 'Fast Delivery'"></p>
                                <p class="text-[10px] sm:text-[9px] text-stone-500 leading-tight mt-0.5 break-words" x-text="b3_sub || 'Express Courier'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. HOMEPAGE CATEGORY TABS SHOWCASE CONFIGURATION -->
        <div class="p-6 rounded-2xl bg-[#FAF7F0] border border-[#D4AF6A]/60 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-[#D4AF6A]/30">
                <div>
                    <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] flex items-center space-x-2">
                        <span><svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 002.5 9.77a.75.75 0 01-.233-1.335A60.61 60.61 0 0111.7 2.805z" /><path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.251 4.284a.75.75 0 01-.46.711 47.87 47.87 0 00-8.105 4.342.75.75 0 01-.824 0 47.87 47.87 0 00-8.105-4.342.75.75 0 01-.46-.711c.03-1.441.117-2.87.251-4.284a48.5 48.5 0 017.666 3.282c.31.144.664.144.972 0z" /></svg></span>
                        <span>Homepage Category Tabs Showcase</span>
                    </h3>
                    <p class="text-stone-600 text-[11px] mt-0.5">
                        Configure the interactive category pills (Chains, Rings, Kadas, etc.) showcase on the homepage.
                    </p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="showcase_enabled" value="1" {{ ($settings['showcase_enabled'] ?? '1') == '1' ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-stone-300 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#996E2E]"></div>
                    <span class="ml-2 text-xs font-semibold text-[#4A2C1D]">Active</span>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Section Heading Title</label>
                    <input type="text" name="showcase_title" value="{{ old('showcase_title', $settings['showcase_title']) }}" class="w-full border rounded-lg p-2.5 bg-white" placeholder="e.g. Curated Royal Collections">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Products Count Per Tab</label>
                    <input type="number" name="showcase_limit" min="4" max="25" value="{{ old('showcase_limit', $settings['showcase_limit']) }}" class="w-full border rounded-lg p-2.5 bg-white" placeholder="e.g. 10">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-stone-700 mb-1">Section Subtitle</label>
                    <input type="text" name="showcase_subtitle" value="{{ old('showcase_subtitle', $settings['showcase_subtitle']) }}" class="w-full border rounded-lg p-2.5 bg-white" placeholder="e.g. Select a collection below to discover hand-finished 1 gram micro gold masterpieces.">
                </div>

                <!-- Category Checkboxes -->
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-stone-700 mb-2">Select Categories to Show in Showcase Tabs:</label>
                    @php
                        $showcaseCats = is_array($settings['showcase_categories'] ?? []) 
                            ? ($settings['showcase_categories'] ?? []) 
                            : (json_decode((string)($settings['showcase_categories'] ?? '[]'), true) ?: []);
                    @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5">
                        @foreach($allCategories ?? [] as $cat)
                            <label class="flex items-center space-x-2.5 p-2.5 rounded-lg border bg-white cursor-pointer transition hover:border-[#D4AF6A] {{ in_array((string)$cat->id, $showcaseCats) ? 'border-[#D4AF6A] bg-[#FFFDF7]' : 'border-stone-200' }}">
                                <input type="checkbox" name="showcase_categories[]" value="{{ $cat->id }}" 
                                       {{ in_array((string)$cat->id, $showcaseCats) ? 'checked' : '' }} 
                                       class="rounded text-[#996E2E] focus:ring-[#D4AF6A]">
                                <span class="text-xs font-semibold text-[#4A2C1D] flex-1 truncate">{{ $cat->name }}</span>
                                <span class="text-[10px] text-stone-500 bg-stone-100 px-1.5 py-0.5 rounded-full font-mono">{{ $cat->products_count ?? $cat->products()->count() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-stone-200">
            <button type="submit" class="px-8 py-3 bg-[#4A2C1D] text-[#E7C77B] rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-[#2E180E] transition shadow-xs cursor-pointer">
                Save Settings & Update Showcase
            </button>
        </div>
    </form>
</div>

<!-- 5. SYSTEM BACKUPS & EXTERNAL STORAGE SYNC (SERVERBYT / HOSTINGER) -->
<div id="backup-sync" class="max-w-4xl bg-white rounded-2xl border border-stone-200 p-4 sm:p-8 shadow-xs mt-8">
    <div class="p-4 sm:p-6 rounded-2xl bg-[#FAF7F0] border-2 border-[#D4AF6A] space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-[#D4AF6A]/30 gap-3">
            <div>
                <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    Automated Database Backups & External Storage Sync
                </h3>
                <p class="text-stone-600 text-[11px] mt-0.5">
                    Snapshots database to <code class="bg-[#4A2C1D]/10 text-[#4A2C1D] px-1 py-0.5 rounded font-mono font-bold">/backups</code> and mirrors all uploads to <code class="bg-[#4A2C1D]/10 text-[#4A2C1D] px-1 py-0.5 rounded font-mono font-bold">/rayka_uploads</code> outside <code class="text-stone-600 font-mono">public_html</code>.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Twice Daily (14:00 & 02:00 IST)
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Run Instant Backup -->
            <div class="p-4 bg-white rounded-xl border border-[#D4AF6A]/40 shadow-xs flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-xs text-[#4A2C1D] flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Instant Database Backup
                    </h4>
                    <p class="text-[11px] text-stone-500 mb-4 leading-relaxed">
                        Creates an immediate compressed snapshot (<code class="font-mono text-[10px]">.zip</code>), saves to <code class="font-mono text-[10px]">/backups</code> outside <code class="font-mono text-[10px]">public_html</code>, and emails it with attachment to <strong class="text-stone-700">{{ $settings['admin_email'] ?? 'Admin Email' }}</strong>.
                    </p>
                </div>
                <form action="{{ route('admin.settings.backup') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-[#4A2C1D] to-[#2E180E] text-[#E7C77B] rounded-lg font-bold text-[11px] uppercase tracking-wider hover:opacity-95 transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-[#E7C77B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Run Instant Backup Now
                    </button>
                </form>
            </div>

            <!-- Sync Storage Mirroring -->
            <div class="p-4 bg-white rounded-xl border border-[#D4AF6A]/40 shadow-xs flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-xs text-[#4A2C1D] flex items-center gap-1.5 mb-1">
                        <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Sync All Uploads to rayka_uploads
                    </h4>
                    <p class="text-[11px] text-stone-500 mb-4 leading-relaxed">
                        Scans all product photos, category hero banners, circular tiles, QR codes, and documents in <code class="font-mono text-[10px]">public/uploads</code> and copies them directly into <code class="font-mono text-[10px]">/rayka_uploads</code> outside <code class="font-mono text-[10px]">public_html</code>.
                    </p>
                </div>
                <form action="{{ route('admin.settings.sync_storage') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-4 bg-white border border-[#D4AF6A] text-[#4A2C1D] hover:bg-[#FAF7F0] rounded-lg font-bold text-[11px] uppercase tracking-wider transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        Sync All Files to rayka_uploads
                    </button>
                </form>
            </div>
        </div>

        <!-- Serverbyt Scheduled Tasks (Cron Jobs) Guide -->
        <div class="p-4 bg-white rounded-xl border border-stone-200 space-y-2">
            <h4 class="font-bold text-xs text-[#4A2C1D] flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Serverbyt Scheduled Tasks / Cron Job Setup (Run Automatically at 2:00 PM)
            </h4>
            <p class="text-[11px] text-stone-600 leading-relaxed">
                In Serverbyt Control Panel (<a href="https://cp.serverbyt.in" target="_blank" class="text-[#996E2E] underline font-semibold">cp.serverbyt.in</a>), go to <strong>Scheduled Tasks (Cron Jobs)</strong> and add the following command to ensure the backup runs automatically:
            </p>
            <div class="bg-stone-900 text-amber-200 p-2.5 rounded-lg font-mono text-[11px] select-all overflow-x-auto">
                cd /home/raykaimitation.com/public_html && php artisan schedule:run >> /dev/null 2>&1
            </div>
            <p class="text-[10px] text-stone-500">
                Or to trigger the database backup directly once every day at 2:00 PM (14:00):
            </p>
            <div class="bg-stone-900 text-stone-300 p-2.5 rounded-lg font-mono text-[11px] select-all overflow-x-auto">
                cd /home/raykaimitation.com/public_html && php artisan db:backup >> /dev/null 2>&1
            </div>
        </div>
    </div>
</div>

<!-- Account Security Section -->
<div id="account-security" class="max-w-4xl bg-white rounded-2xl border border-stone-200 p-4 sm:p-8 shadow-xs mt-8">
    <div class="p-4 sm:p-6 rounded-2xl bg-stone-50 border border-stone-200 space-y-6" x-data="{ otpSent: {{ session('email_change_otp_sent') ? 'true' : 'false' }} }">
        <div class="pb-3 border-b border-stone-200">
            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                Account Security (Change Email)
            </h3>
            <p class="text-stone-600 text-[11px]">Update your primary administrator email address securely.</p>
        </div>

        <!-- Initial Form (Send OTP) -->
        <form action="{{ route('admin.settings.update_email') }}" method="POST" x-show="!otpSent" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-stone-700 mb-1">Current Password *</label>
                <input type="password" name="current_password" required autocomplete="new-password" placeholder="••••••••" class="w-full max-w-sm border border-stone-300 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-none">
            </div>
            <div>
                <label class="block font-semibold text-stone-700 mb-1">New Email Address *</label>
                <input type="email" name="new_email" required autocomplete="off" placeholder="e.g. newadmin@raykajewellery.com" class="w-full max-w-sm border border-stone-300 rounded-lg p-2.5 focus:border-[#D4AF6A] focus:outline-none">
            </div>
            <div class="pt-2">
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-lg font-bold text-[11px] uppercase tracking-wider hover:bg-[#2E180E] transition shadow-xs cursor-pointer">
                    Send Verification OTP
                </button>
            </div>
        </form>

        <!-- OTP Verification Form -->
        <form action="{{ route('admin.settings.verify_email') }}" method="POST" x-show="otpSent" x-cloak class="space-y-4 text-xs">
            @csrf
            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800">
                <p class="font-semibold flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> OTP Sent Successfully</p>
                <p class="mt-1 text-[11px]">Please check your new email address for the 6-digit verification code. It will expire in 15 minutes.</p>
            </div>
            
            <div>
                <label class="block font-semibold text-stone-700 mb-1">6-Digit Verification Code *</label>
                <input type="text" name="otp" required maxlength="6" pattern="[0-9]{6}" placeholder="123456" class="w-full max-w-xs border border-[#D4AF6A]/50 rounded-lg p-2.5 tracking-[0.5em] font-mono text-center focus:border-[#D4AF6A] focus:outline-none bg-[#FAF7F0]">
            </div>
            <div class="pt-2 flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4">
                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-[#F0B429] to-[#D4AF6A] text-[#240F06] border border-[#E7C77B] rounded-lg font-bold text-[11px] uppercase tracking-wider hover:opacity-90 transition shadow-xs cursor-pointer">
                    Verify & Update Email
                </button>
                <a href="{{ route('admin.settings.index') }}" class="w-full sm:w-auto text-center text-[11px] text-stone-500 hover:text-stone-700 underline font-medium">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
