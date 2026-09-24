@extends('layouts.storefront')

@section('title', 'Shopping Bag — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10" x-data="cartPage()" x-init="init()">

    <div class="text-center max-w-xl mx-auto mb-8 sm:mb-10">
        <span class="text-[11px] sm:text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Selected Creations</span>
        <h1 class="font-serif-royal text-2xl sm:text-3xl font-bold text-[#4A2C1D] mt-1">
            Your Shopping Bag
        </h1>
        <div class="rangoli-divider">
            <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
        </div>
    </div>

    <!-- Empty Bag State (Reactive) -->
    <div x-show="$store.rayka.cartCount === 0" 
         x-cloak
         class="max-w-md mx-auto text-center py-16 bg-white rounded-2xl border border-[#D4AF6A]/30 p-8 shadow-xs">
        <div class="w-16 h-16 rounded-full bg-[#FAF7F0] border border-[#D4AF6A] flex items-center justify-center mx-auto mb-4 text-[#D4AF6A]">
            <svg class="w-8 h-8 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
        </div>
        <h3 class="font-serif-royal text-lg font-bold text-[#4A2C1D]">Your Bag is Currently Empty</h3>
        <p class="text-xs text-stone-500 mt-1 mb-6">
            You have not selected any jewels yet. Explore our royal collections to begin your order.
        </p>
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 bg-[#4A2C1D] text-[#E7C77B] px-7 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider hover:bg-[#2E180E] transition shadow-md">
            <span>Explore Collections</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
    </div>

    <!-- Active Cart Items and Summary -->
    <div x-show="$store.rayka.cartCount > 0" class="space-y-8">
        <!-- Free Shipping Progress Bar -->
        <div class="max-w-4xl mx-auto bg-white p-4 rounded-xl border border-[#D4AF6A]/40 shadow-2xs" x-ref="progressBar">
            <div x-show="$store.rayka.cartSubtotal < {{ (float) $freeShippingMin }}">
                <p class="text-xs text-stone-700 mb-2 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <span>Add <strong x-text="'₹' + Math.max(0, Math.round({{ (float) $freeShippingMin }} - $store.rayka.cartSubtotal)).toLocaleString('en-IN')"></strong> more to unlock <strong>FREE Express Shipping</strong>!</span>
                    <span class="text-[#996E2E] font-bold" x-text="'₹' + ($store.rayka.cartSubtotal || 0).toLocaleString('en-IN') + ' / ₹{{ number_format($freeShippingMin) }}'"></span>
                </p>
                <div class="w-full bg-stone-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-[#D4AF6A] to-[#996E2E] h-2 rounded-full transition-all duration-500"
                         :style="'width:' + Math.min(100, (($store.rayka.cartSubtotal || 0) / {{ (float) $freeShippingMin }}) * 100) + '%'"></div>
                </div>
            </div>
            <div x-show="$store.rayka.cartSubtotal >= {{ (float) $freeShippingMin }}" class="flex items-center space-x-2 text-xs text-emerald-800 font-semibold">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Congratulations! You have unlocked <strong>FREE Express Shipping</strong> across India!</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT: CART ITEMS LIST (8 cols) -->
            <div class="lg:col-span-8 space-y-4">
                @if(!empty($hasOutOfStock))
                    <div class="p-4 rounded-2xl bg-rose-50 border-2 border-rose-300 text-rose-900 flex items-start gap-3 text-xs shadow-xs">
                        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="space-y-1">
                            <p class="font-bold text-rose-900 text-sm">High Concurrency Stock Notice</p>
                            <p class="text-rose-800 leading-relaxed">
                                One or more jewellery pieces in your bag were just purchased by other customers or have limited quantity. Please remove or adjust sold-out items to proceed with checkout.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-4 sm:p-6 shadow-xs space-y-1" id="cartItemsList">
                @foreach($cart->items as $item)
                    @php
                        $availableStock = $item->product ? (int) $item->product->stock_quantity : 0;
                        if ($item->variant) {
                            $availableStock = min($availableStock, (int) $item->variant->stock_quantity);
                        }
                    @endphp

                    <div class="cart-row py-5 first:pt-0 last:pb-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-[#D4AF6A]/20 last:border-b-0 transition-all duration-300 {{ $availableStock <= 0 ? 'bg-rose-50/50 p-3 rounded-xl border border-rose-200' : '' }}"
                         data-item-id="{{ $item->id }}"
                         data-product-id="{{ $item->product->id }}"
                         data-stock="{{ $availableStock }}"
                         data-price="{{ $item->price }}"
                         data-initial-qty="{{ $item->quantity }}"
                         x-data="{ 
                             removing: false, 
                             get qty() { 
                                 const sVal = $store.rayka.cartItems[{{ $item->product->id }}]; 
                                 return sVal !== undefined ? sVal : parseInt($el.dataset.initialQty || 0); 
                             },
                             async decrease() {
                                 if (this.qty <= 1) {
                                     this.removing = true;
                                     await $store.rayka.changeQty({{ $item->product->id }}, -1);
                                 } else {
                                     await $store.rayka.changeQty({{ $item->product->id }}, -1);
                                 }
                             },
                             async removeRow() {
                                 this.removing = true;
                                 await $store.rayka.changeQty({{ $item->product->id }}, -999);
                             }
                         }"
                         x-show="!removing && qty > 0"
                         x-transition:leave="transition ease-in duration-300 transform"
                         x-transition:leave-start="opacity-100 scale-100 max-h-48"
                         x-transition:leave-end="opacity-0 scale-95 max-h-0 py-0 overflow-hidden">

                        <div class="flex items-center space-x-4">
                            <!-- Product Image -->
                            <a href="{{ route('product.show', $item->product->slug) }}" class="w-20 h-20 rounded-xl overflow-hidden bg-[#FAF7F0] border border-[#D4AF6A]/30 shrink-0 relative">
                                <img src="{{ $item->product->effective_primary_image }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @if($availableStock <= 0)
                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                        <span class="text-[9px] font-bold text-white uppercase tracking-wider bg-rose-700 px-1 py-0.5 rounded">Sold Out</span>
                                    </div>
                                @endif
                            </a>

                            <!-- Name & Variant -->
                            <div class="space-y-1">
                                <a href="{{ route('product.show', $item->product->slug) }}" class="font-serif-royal text-sm sm:text-base font-bold text-[#4A2C1D] hover:text-[#996E2E] transition line-clamp-2">
                                    {{ $item->product->name }}
                                </a>
                                @if($item->variant)
                                    <p class="text-xs text-stone-500">Option: <span class="font-medium text-[#4A2C1D]">{{ $item->variant->value }}</span></p>
                                @endif
                                <p class="text-xs font-bold text-[#996E2E]">₹{{ number_format($item->price) }} each</p>
                                @if($availableStock <= 0)
                                    <span class="inline-flex items-center gap-1 bg-rose-100 border border-rose-300 text-rose-800 text-[10px] font-bold px-2 py-0.5 rounded">
                                        ✕ Sold Out (Purchased by another customer)
                                    </span>
                                @elseif($availableStock <= 5)
                                    <p class="text-[10px] text-amber-700 font-medium">Only {{ $availableStock }} left in stock</p>
                                @endif
                            </div>
                        </div>

                        <!-- AJAX Quantity Selector + Subtotal + Remove -->
                        <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto space-x-6">
                            @if($availableStock <= 0)
                                <div class="flex items-center space-x-3">
                                    <span class="text-xs text-rose-700 font-semibold italic">Item unavailable</span>
                                    <button type="button"
                                            @click="await removeRow()"
                                            class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-800 text-xs font-bold rounded-lg transition cursor-pointer">
                                        Remove
                                    </button>
                                </div>
                            @else
                                <!-- AJAX Quantity Controls -->
                                <div class="flex items-center border border-[#D4AF6A] rounded-lg overflow-hidden bg-[#FAF7F0]" :class="{'opacity-60 pointer-events-none': $store.rayka.loadingItems[{{ $item->product->id }}]}">
                                    <button type="button"
                                            @click="await decrease()"
                                            class="px-2.5 py-1.5 text-xs text-[#4A2C1D] hover:bg-white font-bold transition active:scale-90 flex items-center justify-center cursor-pointer"
                                            title="Decrease quantity">
                                        <span x-show="qty > 1">−</span>
                                        <svg x-show="qty <= 1" class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                    <span class="px-3 py-1 text-xs font-bold text-[#4A2C1D] min-w-[2rem] text-center" x-text="qty"></span>
                                    <button type="button"
                                            @click="await $store.rayka.changeQty({{ $item->product->id }}, 1);"
                                            :disabled="qty >= {{ $availableStock }}"
                                            class="px-2.5 py-1.5 text-xs text-[#4A2C1D] hover:bg-white disabled:opacity-30 font-bold transition active:scale-90 cursor-pointer"
                                            title="Increase quantity">+</button>
                                </div>

                                <!-- Subtotal -->
                                <div class="text-right min-w-[5rem]">
                                    <span class="font-sans font-bold text-base text-[#4A2C1D]" x-text="'₹' + ((qty || 0) * {{ (float) $item->price }}).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                                </div>

                                <!-- Remove item button (AJAX) -->
                                <button type="button"
                                        @click="await removeRow()"
                                        :class="{'animate-spin': removing}"
                                        class="text-stone-400 hover:text-rose-600 transition p-1 cursor-pointer" title="Remove Item">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
                </div>
            </div>


            <!-- RIGHT: SUMMARY & COUPON ENGINE (4 cols) -->
            <div class="lg:col-span-4 space-y-6" x-data="{ couponInput: '' }">
                
                <!-- Coupon Engine -->
                <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-4 sm:p-5 shadow-xs space-y-3">
                    <h4 class="font-serif-royal text-sm font-bold text-[#4A2C1D] flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        <span>Royal Offers & Promo Codes</span>
                    </h4>

                    @if(session('coupon_error'))
                        <p class="text-[11px] text-rose-700 bg-rose-50 p-2 rounded border border-rose-200">{{ session('coupon_error') }}</p>
                    @endif

                    @if(session('coupon_success'))
                        <p class="text-[11px] text-emerald-800 bg-emerald-50 p-2 rounded border border-emerald-200">{{ session('coupon_success') }}</p>
                    @endif

                    <!-- Dynamic Feedback Alerts -->
                    <p x-show="couponError" x-text="couponError" class="text-[11px] text-rose-700 bg-rose-50 p-2 rounded-lg border border-rose-200"></p>
                    <p x-show="couponSuccess" x-text="couponSuccess" class="text-[11px] text-emerald-800 bg-emerald-50 p-2 rounded-lg border border-emerald-200"></p>

                    @if($coupon)
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-amber-50/80 border border-[#D4AF6A] text-xs">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="font-bold text-[#4A2C1D] truncate">{{ $coupon->code }}</span>
                                <span class="text-emerald-700 ml-1 font-semibold shrink-0">(-₹{{ number_format($discount) }})</span>
                            </div>
                            <form action="{{ route('coupon.remove') }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" class="text-xs text-rose-700 font-bold hover:underline cursor-pointer">Remove</button>
                            </form>
                        </div>
                    @else
                        <form @submit.prevent="applyCouponCode(couponInput)" class="flex gap-2 min-w-0" id="couponForm">
                            <input type="text" x-model="couponInput" placeholder="e.g. ROYAL10" required class="flex-1 min-w-0 uppercase text-xs border border-[#D4AF6A]/50 rounded-lg px-3 py-2 focus:outline-hidden focus:border-[#D4AF6A]">
                            <button type="submit" :disabled="couponLoading" class="shrink-0 px-4 py-2 bg-[#4A2C1D] text-[#E7C77B] text-xs font-bold rounded-lg uppercase tracking-wider hover:bg-[#2E180E] transition disabled:opacity-50 cursor-pointer">
                                <span x-show="!couponLoading">Apply</span>
                                <span x-show="couponLoading">...</span>
                            </button>
                        </form>
                        <div class="space-y-1.5 pt-1">
                            <span class="text-[10px] text-stone-500 font-bold uppercase tracking-wider block">Available Offers:</span>
                            @if(isset($availableCoupons) && $availableCoupons->count() > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-[11px]">
                                    @foreach($availableCoupons as $c)
                                        @php
                                            $desc = $c->type === 'percentage' ? "{$c->value}% OFF" : "Flat ₹{$c->value} OFF";
                                            if ($c->min_order_value > 0) {
                                                $desc .= " (Min ₹" . number_format($c->min_order_value) . ")";
                                            }
                                        @endphp
                                        <button type="button" 
                                                @click="if (($store.rayka.cartSubtotal || 0) >= {{ (float) $c->min_order_value }}) { applyCouponCode('{{ $c->code }}'); }" 
                                                :disabled="couponLoading || (($store.rayka.cartSubtotal || 0) < {{ (float) $c->min_order_value }})"
                                                :class="(($store.rayka.cartSubtotal || 0) >= {{ (float) $c->min_order_value }}) ? 'bg-amber-50/80 border-[#D4AF6A] text-[#4A2C1D] hover:bg-amber-100/80 cursor-pointer shadow-2xs' : 'bg-stone-50 border-stone-200 text-stone-400 opacity-75 cursor-not-allowed'"
                                                class="flex items-center justify-between p-2 rounded-lg border text-left transition min-w-0">
                                            <div class="min-w-0 pr-2">
                                                <span class="font-mono font-bold text-[#996E2E]">{{ $c->code }}</span>
                                                <span class="block text-[10px] text-stone-500 truncate">{{ $desc }}</span>
                                            </div>
                                            <span class="text-[10px] font-bold shrink-0"
                                                  :class="(($store.rayka.cartSubtotal || 0) >= {{ (float) $c->min_order_value }}) ? 'text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full shadow-2xs' : 'text-stone-400 font-semibold'"
                                                  x-text="(($store.rayka.cartSubtotal || 0) >= {{ (float) $c->min_order_value }}) ? 'Apply' : 'Min ₹{{ number_format($c->min_order_value) }}'">
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-xs text-stone-500">No active offers at the moment.</div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Order Summary Breakdown -->
                <div class="bg-white rounded-2xl border border-[#D4AF6A]/40 p-6 shadow-xs space-y-4">
                    <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] pb-3 border-b border-[#D4AF6A]/30">
                        Order Summary
                    </h3>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between text-stone-600">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[#4A2C1D]" x-text="'₹' + ($store.rayka.cartSubtotal || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                        </div>

                        <div class="flex justify-between text-emerald-700 font-semibold" x-show="$store.rayka.cartDiscount > 0">
                            <span>Coupon Discount</span>
                            <span x-text="'- ₹' + ($store.rayka.cartDiscount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                        </div>

                        <div class="flex justify-between text-stone-600">
                            <span>Shipping Fee</span>
                            <span x-text="$store.rayka.cartSubtotal >= {{ (float) $freeShippingMin }} ? 'FREE' : '₹' + ($store.rayka.cartShipping || 0).toLocaleString('en-IN')"
                                  :class="$store.rayka.cartSubtotal >= {{ (float) $freeShippingMin }} ? 'text-emerald-700 font-bold uppercase text-[11px]' : 'font-semibold text-[#4A2C1D]'"></span>
                        </div>

                        <div class="pt-3 border-t border-[#D4AF6A]/30 flex justify-between items-baseline">
                            <span class="font-serif-royal text-base font-bold text-[#4A2C1D]">Total Amount</span>
                            <span class="font-sans font-bold text-2xl text-[#4A2C1D]"
                                  x-text="'₹' + ($store.rayka.cartTotal || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                        </div>
                        <p class="text-[10px] text-stone-400">Includes all applicable GST & royal craft packing</p>
                    </div>

                    <!-- Proceed to Checkout Button -->
                    @if(!empty($hasOutOfStock))
                        <div class="w-full py-3.5 px-6 rounded-full bg-stone-200 text-stone-500 font-bold text-xs uppercase tracking-widest text-center cursor-not-allowed border border-stone-300">
                            Adjust Bag to Checkout
                        </div>
                        <p class="text-[11px] text-rose-700 text-center font-semibold">
                            Please remove or adjust sold-out items to proceed.
                        </p>
                    @else
                        <a href="{{ route('checkout') }}" 
                           class="flex items-center justify-center gap-2.5 w-full py-4 px-6 rounded-full bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-[#E7C77B] font-bold text-xs uppercase tracking-widest shadow-lg hover:shadow-xl hover:scale-[1.01] active:scale-98 transition-all duration-200 border border-[#D4AF6A] group cursor-pointer">
                            <span>Proceed to Checkout</span>
                            <svg class="w-4 h-4 text-[#D4AF6A] group-hover:translate-x-1.5 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    @endif

                    <div class="text-center pt-1">
                        <a href="{{ route('home') }}" class="text-xs text-stone-500 hover:text-[#996E2E] transition">
                            ← Continue Browsing Collections
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function cartPage() {
    return {
        couponError: '',
        couponSuccess: '',
        couponLoading: false,
        init() {
            // Seed the store with server-rendered values so the UI is instantly correct
            // without waiting for the async fetchCounts() call to complete.
            this.$store.rayka.cartSubtotal = {{ (float) $subtotal }};
            this.$store.rayka.cartDiscount = {{ (float) $discount }};
            this.$store.rayka.cartTotal = {{ (float) $total }};
            this.$store.rayka.cartShipping = {{ (float) $shippingFee }};

            // Pre-populate cartItems from server-rendered quantities so cart rows
            // are never hidden during the async fetchCounts() round-trip.
            const serverItems = {!! json_encode(
                $cart->items->mapWithKeys(fn ($ci) => [(int) $ci->product_id => (int) $ci->quantity])->toArray()
            ) !!};
            // Only seed if the store hasn't already received a more recent value from fetchCounts.
            if (!this.$store.rayka.cartCount) {
                this.$store.rayka.cartItems = { ...serverItems, ...this.$store.rayka.cartItems };
                this.$store.rayka.cartCount = Object.values(serverItems).reduce((a, b) => a + b, 0);
            }
        },
        async applyCouponCode(code) {
            code = (code || '').trim().toUpperCase();
            if (!code) {
                this.couponError = 'Please enter a coupon code';
                return;
            }
            this.couponError = '';
            this.couponSuccess = '';
            this.couponLoading = true;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch('{{ route("coupon.apply") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ code: code })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.couponSuccess = data.message;
                    if (window.Alpine && Alpine.store('rayka')) {
                        Alpine.store('rayka').showToast(data.message, 'success');
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 400);
                } else {
                    this.couponError = data.message || 'Invalid coupon code.';
                    if (window.Alpine && Alpine.store('rayka')) {
                        Alpine.store('rayka').showToast(this.couponError, 'error');
                    }
                }
            } catch (e) {
                this.couponError = 'Network error applying coupon.';
            } finally {
                this.couponLoading = false;
            }
        }
    };
}
</script>
@endpush


