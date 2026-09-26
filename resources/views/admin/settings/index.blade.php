@php /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Category> $allCategories */ @endphp
@extends('admin.layouts.admin')

@section('title', 'Store Settings & Payment QR')
@section('page_title', 'Store Settings & Static QR Code Configuration')

@section('content')
<div class="max-w-4xl bg-white rounded-2xl border border-stone-200 p-4 sm:p-8 shadow-xs">

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8 text-xs">
        @csrf

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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
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
                             x-data="{ isDropping: false, fileSelected: false, fileName: '' }"
                             @dragover.prevent="isDropping = true" 
                             @dragleave.prevent="isDropping = false" 
                             @drop.prevent="isDropping = false; $refs.qrInput.files = $event.dataTransfer.files; if($refs.qrInput.files.length){ fileSelected = true; fileName = $refs.qrInput.files[0].name; }"
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
                                       @change="if($el.files.length){ fileSelected = true; fileName = $el.files[0].name; } else { fileSelected = false; fileName = ''; }">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-center p-4 bg-white rounded-xl border border-[#D4AF6A]/50">
                    <span class="text-[10px] uppercase font-bold text-[#996E2E] block mb-2">Current Active QR Code:</span>
                    <img src="{{ asset($settings['qr_code_image']) }}" alt="Active QR" class="w-40 h-40 object-contain mx-auto border rounded p-1">
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
                    <input type="text" name="trust_badge_3" value="{{ old('trust_badge_3', $settings['trust_badge_3']) }}" class="w-full border rounded-lg p-2 mb-1" placeholder="e.g. Easy Replacement Guarantee">
                    <input type="text" name="trust_badge_4" value="{{ old('trust_badge_4', $settings['trust_badge_4']) }}" class="w-full border rounded-lg p-2" placeholder="e.g. Heritage Quality Craftsmanship">
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
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5">
                        @foreach($allCategories ?? [] as $cat)
                            <label class="flex items-center space-x-2.5 p-2.5 rounded-lg border bg-white cursor-pointer transition hover:border-[#D4AF6A] {{ in_array((string)$cat->id, $settings['showcase_categories'] ?? []) ? 'border-[#D4AF6A] bg-[#FFFDF7]' : 'border-stone-200' }}">
                                <input type="checkbox" name="showcase_categories[]" value="{{ $cat->id }}" 
                                       {{ in_array((string)$cat->id, $settings['showcase_categories'] ?? []) ? 'checked' : '' }} 
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
                        Creates an immediate compressed snapshot (<code class="font-mono text-[10px]">.zip</code>), saves to <code class="font-mono text-[10px]">/backups</code> outside <code class="font-mono text-[10px]">public_html</code>, and emails it with attachment to <strong class="text-stone-700">{{ $settings['admin_email'] ?: 'Admin Email' }}</strong>.
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
