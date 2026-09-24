@extends('admin.layouts.admin')

@section('title', 'Add New Category')
@section('page_title', 'Create Jewellery Category')

@section('content')
<div class="max-w-3xl bg-white rounded-2xl border border-stone-200 p-8 shadow-xs">

    @if($errors->any())
        <div class="mb-6 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 text-xs text-rose-700 space-y-1">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    @endif

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" id="cat-form" class="space-y-6 text-xs">
        @csrf

        {{-- Section 1: Basic Info --}}
        <div>
            <h3 class="font-serif-royal text-sm font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4">1. Category Details</h3>
            <div class="space-y-4">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Kundan Chokers"
                        class="w-full border border-stone-300 rounded-xl p-2.5 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Category Page Hero Title</label>
                        <input type="text" name="hero_title" value="{{ old('hero_title') }}" placeholder="e.g. Imperial Royal Kundan"
                            class="w-full border border-stone-300 rounded-xl p-2.5 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition">
                    </div>
                    <div>
                        <label class="block font-semibold text-stone-700 mb-1 flex items-center gap-1.5">
                            Sort Order
                            <span class="text-[10px] font-normal text-stone-400 bg-stone-100 px-1.5 py-0.5 rounded">Auto-filled</span>
                        </label>
                        <input type="number" name="sort_order" value="{{ $nextSortOrder }}"
                            class="w-full border border-stone-300 rounded-xl p-2.5 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition bg-amber-50/60">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Category Page Hero Subtitle</label>
                    <textarea name="hero_subtitle" rows="2" placeholder="e.g. Heirloom craftsmanship set with uncut polki glass stones..."
                        class="w-full border border-stone-300 rounded-xl p-2.5 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] transition resize-none">{{ old('hero_subtitle') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 2: Images — Standard HTML file inputs (most reliable) --}}
        <div>
            <h3 class="font-serif-royal text-sm font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4">2. Category Images</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- Tile Image --}}
                <div>
                    <label class="block font-semibold text-stone-700 mb-2">
                        Homepage Circular Tile Image <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative group" id="tile-dropzone-wrap">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-[#D4AF6A] to-[#996E2E] rounded-xl blur-sm opacity-20 group-hover:opacity-50 transition duration-300"></div>
                        <label for="image_file" class="relative flex flex-col items-center justify-center bg-[#FDFBF6] border-2 border-dashed border-[#D4AF6A]/60 hover:border-[#996E2E] hover:bg-[#FAF7F0] rounded-xl p-6 text-center cursor-pointer transition-all duration-300 min-h-[140px]" id="tile-dropzone">
                            <div id="tile-placeholder">
                                <div class="w-10 h-10 mx-auto bg-white rounded-full flex items-center justify-center border border-[#D4AF6A]/30 shadow-sm mb-2">
                                    <svg class="w-5 h-5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v8m-4-4h8"/></svg>
                                </div>
                                <p class="font-semibold text-[#4A2C1D] text-xs">Click or drag tile image</p>
                                <p class="text-[10px] text-stone-400 mt-0.5">PNG, JPG, WebP, SVG (max 10MB)</p>
                            </div>
                            <div id="tile-preview" class="hidden w-full">
                                <img id="tile-preview-img" src="" alt="Tile preview" class="w-20 h-20 rounded-full object-cover mx-auto border-2 border-[#D4AF6A] shadow-sm mb-2">
                                <p id="tile-preview-name" class="text-[10px] text-stone-500 truncate text-center px-2"></p>
                                <p class="text-[9px] text-emerald-600 font-semibold mt-0.5">✓ Image selected — click to change</p>
                            </div>
                        </label>
                    </div>
                    {{-- Direct named file input inside the form --}}
                    <input type="file" name="image_file" id="image_file" accept="image/*" class="hidden"
                           onchange="handleCatFilePreview(this, 'tile-preview', 'tile-preview-img', 'tile-preview-name', 'tile-placeholder')">
                </div>

                {{-- Hero Banner --}}
                <div>
                    <label class="block font-semibold text-stone-700 mb-2">Category Page Hero Banner</label>
                    <div class="relative group" id="hero-dropzone-wrap">
                        <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-400/40 to-[#D4AF6A] rounded-xl blur-sm opacity-20 group-hover:opacity-50 transition duration-300"></div>
                        <label for="hero_file" class="relative flex flex-col items-center justify-center bg-[#FDFBF6] border-2 border-dashed border-indigo-300/60 hover:border-indigo-500 hover:bg-indigo-50/30 rounded-xl p-6 text-center cursor-pointer transition-all duration-300 min-h-[140px]" id="hero-dropzone">
                            <div id="hero-placeholder">
                                <div class="w-10 h-10 mx-auto bg-white rounded-full flex items-center justify-center border border-indigo-200/60 shadow-sm mb-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="font-semibold text-[#4A2C1D] text-xs">Click or drag hero banner</p>
                                <p class="text-[10px] text-stone-400 mt-0.5">Recommended: 1600×400px</p>
                            </div>
                            <div id="hero-preview" class="hidden w-full">
                                <img id="hero-preview-img" src="" alt="Hero preview" class="w-full h-16 object-cover rounded-lg border border-indigo-200 shadow-sm mb-2">
                                <p id="hero-preview-name" class="text-[10px] text-stone-500 truncate text-center px-2"></p>
                                <p class="text-[9px] text-emerald-600 font-semibold mt-0.5">✓ Image selected — click to change</p>
                            </div>
                        </label>
                    </div>
                    {{-- Direct named file input inside the form --}}
                    <input type="file" name="hero_file" id="hero_file" accept="image/*" class="hidden"
                           onchange="handleCatFilePreview(this, 'hero-preview', 'hero-preview-img', 'hero-preview-name', 'hero-placeholder')">
                </div>
            </div>
        </div>

        {{-- Section 3: Mega-Menu Navigation Placement --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-2.5 border-b border-stone-200 mb-3 gap-1 sm:gap-4">
                <h3 class="font-serif-royal text-sm sm:text-base font-bold text-[#4A2C1D] flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-[#FAF7F0] border border-[#D4AF6A] flex items-center justify-center text-[#996E2E] shrink-0 font-mono text-xs font-bold">3</span>
                    <span>Mega-Menu Navigation Placement</span>
                </h3>
                <span class="text-[10px] sm:text-xs text-stone-400">Controls top header navbar dropdowns</span>
            </div>
            <p class="text-[11px] text-stone-500 mb-3 leading-relaxed">
                Select which top-level menu dropdowns (<strong>Men</strong>, <strong>Women</strong>, <strong>1 Gram Jewellery</strong>) will display this category tile for shoppers. You can select multiple placements.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" x-data="{
                selectedNavs: {{ json_encode(array_map('intval', old('nav_groups', []))) }},
                toggleNav(id) {
                    if (this.selectedNavs.includes(id)) {
                        this.selectedNavs = this.selectedNavs.filter(n => n !== id);
                    } else {
                        this.selectedNavs.push(id);
                    }
                }
            }">
                @foreach($navGroups as $nav)
                    <label class="flex items-center gap-3 p-3 sm:p-3.5 rounded-xl border-2 cursor-pointer transition select-none min-h-[56px]"
                           :class="selectedNavs.includes({{ $nav->id }}) 
                                ? 'bg-[#FAF7F0] border-[#D4AF6A] shadow-xs' 
                                : 'bg-stone-50/60 border-stone-200 hover:border-stone-300 hover:bg-stone-50'">
                        <input type="checkbox" 
                               name="nav_groups[]" 
                               value="{{ $nav->id }}" 
                               :checked="selectedNavs.includes({{ $nav->id }})"
                               @change="toggleNav({{ $nav->id }})"
                               class="hidden">
                        
                        <div class="w-5 h-5 rounded-md flex items-center justify-center border shrink-0 transition"
                             :class="selectedNavs.includes({{ $nav->id }}) 
                                ? 'bg-[#4A2C1D] border-[#4A2C1D] text-[#E7C77B]' 
                                : 'border-stone-300 bg-white'">
                            <svg x-show="selectedNavs.includes({{ $nav->id }})" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        {{-- Premium Luxury SVG Category Group Icon --}}
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition"
                             :class="selectedNavs.includes({{ $nav->id }}) 
                                ? 'bg-[#4A2C1D] text-[#E7C77B]' 
                                : 'bg-[#FAF7F0] text-[#996E2E] border border-[#D4AF6A]/30'">
                            @if(stripos($nav->name, 'men') !== false && stripos($nav->name, 'women') === false)
                                {{-- Men Icon: Royal Sovereign Signet --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            @elseif(stripos($nav->name, 'women') !== false)
                                {{-- Women Icon: Royal Tiara Crown --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2 19h20M5 19V9l4.5 4L12 6l2.5 7L19 9v10" />
                                </svg>
                            @else
                                {{-- 1 Gram Jewellery: Faceted Diamond Gem --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3h12l4 6-10 12L2 9l4-6z M2 9h20 M12 21L8 9 M12 21l4-12 M6 3l2 6 M18 3l-2 6" />
                                </svg>
                            @endif
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <span class="block font-bold text-xs sm:text-sm text-[#4A2C1D]">
                                {{ $nav->name }} Menu
                            </span>
                            <span class="text-[10px] text-stone-500 block mt-0.5 leading-snug truncate">
                                Show in {{ $nav->name }} mega dropdown
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Active --}}
        <div class="flex items-center space-x-2 pt-1">
            <input type="checkbox" name="is_active" value="1" checked id="catActive" class="rounded text-[#996E2E] focus:ring-[#996E2E]">
            <label for="catActive" class="font-semibold text-stone-700 select-none cursor-pointer">Active in catalogue</label>
        </div>

        {{-- Submit --}}
        <div class="pt-4 border-t border-stone-200 flex items-center space-x-3">
            <button type="submit" class="px-8 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-[#2E180E] transition shadow-xs cursor-pointer">
                Create Category
            </button>
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-stone-500 hover:text-stone-800 transition">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
/**
 * Show preview for category image inputs.
 * Called directly from the input's onchange attribute — no DataTransfer needed.
 */
function handleCatFilePreview(input, previewId, previewImgId, previewNameId, placeholderId) {
    const file = input.files && input.files[0];
    if (!file) return;

    const preview  = document.getElementById(previewId);
    const img      = document.getElementById(previewImgId);
    const nameTxt  = document.getElementById(previewNameId);
    const ph       = document.getElementById(placeholderId);

    const reader = new FileReader();
    reader.onload = e => {
        img.src = e.target.result;
        nameTxt.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
        if (ph) ph.classList.add('hidden');
        preview.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

// Drag-and-drop wiring (drops the file onto the hidden input via DataTransfer)
document.addEventListener('DOMContentLoaded', () => {
    function wireDrop(dropzoneId, inputId) {
        const dz  = document.getElementById(dropzoneId);
        const inp = document.getElementById(inputId);
        if (!dz || !inp) return;

        ['dragenter', 'dragover'].forEach(ev => dz.addEventListener(ev, e => {
            e.preventDefault(); e.stopPropagation();
            dz.classList.add('border-[#996E2E]', 'bg-[#FAF7F0]');
        }));
        ['dragleave', 'drop'].forEach(ev => dz.addEventListener(ev, e => {
            e.preventDefault(); e.stopPropagation();
            dz.classList.remove('border-[#996E2E]', 'bg-[#FAF7F0]');
        }));
        dz.addEventListener('drop', e => {
            const files = e.dataTransfer.files;
            if (files && files[0] && files[0].type.startsWith('image/')) {
                try {
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    inp.files = dt.files;
                } catch (_) { return; }
                inp.dispatchEvent(new Event('change'));
            }
        });
    }

    wireDrop('tile-dropzone', 'image_file');
    wireDrop('hero-dropzone', 'hero_file');
});
</script>
@endpush
