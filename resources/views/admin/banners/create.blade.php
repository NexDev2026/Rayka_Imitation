@extends('admin.layouts.admin')

@section('title', 'Add New Hero Banner')
@section('page_title', 'Add New Hero Banner')

@section('content')
<div class="max-w-3xl bg-white rounded-2xl border border-stone-200 p-8 shadow-xs">

    @if($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 text-xs text-rose-700 space-y-1">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    @endif

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
        @csrf

        {{-- Section 1: Banner Content --}}
        <div>
            <h3 class="font-serif-royal text-sm font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4">1. Banner Content</h3>
            <div class="space-y-4">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Banner Headline <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. ROYAL HERITAGE KUNDAN LAUNCH"
                        class="w-full border border-stone-300 rounded-xl p-2.5 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition font-bold tracking-wide">
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Subtitle / Supporting Text</label>
                    <textarea name="subtitle" rows="2" placeholder="e.g. Handcrafted Rajputana Kundan & Polki sets with 1 Gram Micro Gold sheen..."
                        class="w-full border border-stone-300 rounded-xl p-2.5 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition resize-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Badge --}}
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">
                            Badge Label
                            <span class="text-[10px] font-normal text-stone-400 ml-1">(shown as badge chip on slider)</span>
                        </label>
                        <input type="text" name="badge_text" id="badge-text-input"
                            placeholder="e.g. NEW ARRIVAL"
                            class="w-full border border-stone-300 rounded-xl p-2.5 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition">
                        
                        {{-- Presets --}}
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            <button type="button" onclick="document.getElementById('badge-text-input').value='NEW ARRIVAL'" class="px-2 py-1 bg-stone-100 hover:bg-[#F5EFE0] hover:text-[#996E2E] rounded text-[10px] font-semibold text-stone-600 border border-stone-200 transition">NEW ARRIVAL</button>
                            <button type="button" onclick="document.getElementById('badge-text-input').value='FESTIVE EXCLUSIVE'" class="px-2 py-1 bg-stone-100 hover:bg-[#F5EFE0] hover:text-[#996E2E] rounded text-[10px] font-semibold text-stone-600 border border-stone-200 transition">FESTIVE EXCLUSIVE</button>
                            <button type="button" onclick="document.getElementById('badge-text-input').value='BESTSELLER'" class="px-2 py-1 bg-stone-100 hover:bg-[#F5EFE0] hover:text-[#996E2E] rounded text-[10px] font-semibold text-stone-600 border border-stone-200 transition">BESTSELLER</button>
                            <button type="button" onclick="document.getElementById('badge-text-input').value='LIMITED EDITION'" class="px-2 py-1 bg-stone-100 hover:bg-[#F5EFE0] hover:text-[#996E2E] rounded text-[10px] font-semibold text-stone-600 border border-stone-200 transition">LIMITED EDITION</button>
                            <button type="button" onclick="document.getElementById('badge-text-input').value='FLASH SALE'" class="px-2 py-1 bg-stone-100 hover:bg-[#F5EFE0] hover:text-[#996E2E] rounded text-[10px] font-semibold text-stone-600 border border-stone-200 transition">FLASH SALE</button>
                            <button type="button" onclick="document.getElementById('badge-text-input').value=''" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded text-[10px] font-semibold border border-rose-100 transition ml-auto">Clear</button>
                        </div>
                        <p class="text-[10px] text-stone-400 mt-1.5">Leave blank for no badge. Keep it short (2–3 words).</p>
                    </div>

                    {{-- Sort Order --}}
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1 flex items-center gap-1.5">
                            Sort Order
                            <span class="text-[10px] font-normal text-stone-400 bg-stone-100 px-1.5 py-0.5 rounded">Auto-filled</span>
                        </label>
                        <input type="number" name="sort_order" value="{{ $nextSortOrder }}"
                            class="w-full border border-stone-300 rounded-xl p-2.5 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition bg-amber-50/60">
                        <p class="text-[10px] text-stone-400 mt-1">Lower number = shown first on homepage.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Button Label <span class="text-rose-500">*</span></label>
                        <input type="text" name="button_text" value="SHOP THE COLLECTION" required
                            class="w-full border border-stone-300 rounded-xl p-2.5 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition"
                            placeholder="e.g. SHOP NOW">
                        <p class="text-[10px] text-stone-400 mt-1">Text shown on the CTA button.</p>
                    </div>
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Button URL <span class="text-rose-500">*</span></label>
                        <input type="text" name="button_link" value="/categories/necklaces-sets" required
                            class="w-full border border-stone-300 rounded-xl p-2.5 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition font-mono"
                            placeholder="/categories/your-category-slug">
                        <p class="text-[10px] text-stone-400 mt-1">Relative path or full URL.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Banner Image --}}
        <div>
            <h3 class="font-serif-royal text-sm font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4">2. Banner Image <span class="text-rose-500">*</span></h3>
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-[#D4AF6A] to-[#996E2E] rounded-xl blur-sm opacity-25 group-hover:opacity-60 transition duration-500"></div>
                <label for="banner-image-input" class="relative flex flex-col items-center justify-center bg-[#FDFBF6] border-2 border-dashed border-[#D4AF6A]/60 hover:border-[#996E2E] hover:bg-[#FAF7F0] rounded-xl p-10 text-center cursor-pointer transition-all duration-300 min-h-[180px]" id="banner-dropzone">
                    <div id="banner-placeholder">
                        <div class="w-14 h-14 mx-auto bg-white rounded-full flex items-center justify-center border border-[#D4AF6A]/30 shadow-sm mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="font-serif-royal text-base font-bold text-[#4A2C1D] mb-1">Click to Upload or Drag & Drop</h4>
                        <p class="text-xs text-stone-500 font-medium">PNG, JPG, WebP or SVG — Recommended: 1600×600px</p>
                    </div>
                    <div id="banner-preview" class="hidden w-full">
                        <img id="banner-preview-img" src="" alt="Banner preview" class="w-full h-36 object-cover rounded-xl border border-[#D4AF6A] shadow-sm mb-3">
                        <p id="banner-preview-name" class="text-xs text-stone-600 font-semibold"></p>
                        <p class="text-[10px] text-amber-600 font-semibold mt-1">Click or drag to replace</p>
                    </div>
                </label>
            </div>
            <input type="file" name="banner_image" id="banner-image-input" accept="image/*,.jpg,.jpeg,.png,.webp,.svg,.gif" required class="hidden">
        </div>

        {{-- Active --}}
        <div class="flex items-center space-x-2">
            <input type="checkbox" name="is_active" value="1" checked id="isActive" class="rounded text-[#996E2E] focus:ring-[#996E2E]">
            <label for="isActive" class="font-semibold text-stone-700 select-none cursor-pointer">Activate on homepage slider immediately</label>
        </div>

        {{-- Submit --}}
        <div class="pt-4 border-t border-stone-200 flex items-center space-x-3">
            <button type="submit" class="px-8 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-[#2E180E] transition shadow-xs cursor-pointer">
                Save Banner
            </button>
            <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 text-stone-500 hover:text-stone-800 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>


(function() {
    const dropzone = document.getElementById('banner-dropzone');
    const input    = document.getElementById('banner-image-input');
    const preview  = document.getElementById('banner-preview');
    const img      = document.getElementById('banner-preview-img');
    const name     = document.getElementById('banner-preview-name');
    const ph       = document.getElementById('banner-placeholder');

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            name.textContent = file.name + ' · ' + (file.size / 1024).toFixed(0) + ' KB';
            ph.classList.add('hidden');
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    input.addEventListener('change', () => { if (input.files[0]) showPreview(input.files[0]); });

    ['dragenter','dragover'].forEach(ev => dropzone.addEventListener(ev, e => {
        e.preventDefault(); e.stopPropagation();
        dropzone.style.borderColor = '#4A2C1D'; dropzone.style.background = '#F5EFE0';
    }));
    ['dragleave','drop'].forEach(ev => dropzone.addEventListener(ev, e => {
        e.preventDefault(); e.stopPropagation();
        dropzone.style.borderColor = ''; dropzone.style.background = '';
    }));
    dropzone.addEventListener('drop', e => {
        const f = e.dataTransfer.files[0];
        if (f && f.type.startsWith('image/')) {
            const dt = new DataTransfer(); dt.items.add(f);
            input.files = dt.files;
            showPreview(f);
        }
    });
})();
</script>
@endpush
