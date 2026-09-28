@extends('admin.layouts.admin')

@section('title', 'Edit Product')
@section('page_title', 'Edit Jewellery: ' . $product->name)

@section('content')
<div class="w-full max-w-5xl mx-auto bg-white rounded-xl sm:rounded-2xl border border-stone-200 p-3 sm:p-8 shadow-xs">

    <form id="product-form" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
        @csrf
        @method('PUT')

        <!-- General Info -->
        <div>
            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4">
                1. General Information
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-stone-700 mb-1">Product Title / Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full border rounded-lg p-2.5">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Category *</label>
                    <select name="category_id" required class="w-full border rounded-lg p-2.5">
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $product->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">SKU *</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full border rounded-lg p-2.5 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Selling Price (&#8377;) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="w-full border rounded-lg p-2.5 font-bold">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">MRP Price (&#8377;) *</label>
                    <input type="number" step="0.01" name="mrp" value="{{ old('mrp', $product->mrp) }}" required class="w-full border rounded-lg p-2.5">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Live Stock Quantity *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required class="w-full border rounded-lg p-2.5">
                    <p class="text-[11px] text-stone-500 mt-1">
                        Base catalogue stock. If variants (sizes/lengths) are enabled in Section 3.5, each option maintains its own dedicated live inventory.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-6 pt-6">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-semibold text-stone-700">Active / Published</span>
                    </label>

                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded text-[#996E2E]">
                        <span class="font-semibold text-stone-700">Best Seller / Featured</span>
                    </label>

                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_trending" value="1" {{ old('is_trending', $product->is_trending) ? 'checked' : '' }} class="rounded text-[#996E2E]">
                        <span class="font-semibold text-stone-700">Trending Badge</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Images Gallery Management -->
        <div>
            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4 flex items-center justify-between">
                <span>2. Product Gallery Images</span>
                <span class="text-xs text-stone-500 font-normal">({{ $product->images->count() }} uploaded)</span>
            </h3>

            <!-- Existing Gallery Images Grid -->
            @if($product->images->count() > 0)
                <div class="mb-6">
                    <h4 class="font-semibold text-stone-700 mb-3 flex items-center gap-2">
                        <span>Current Gallery Images</span>
                        <span class="text-[11px] text-stone-400 font-normal">(Click buttons below each image to change roles or delete)</span>
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($product->images as $img)
                            <div class="relative bg-white border {{ $img->is_primary ? 'border-amber-500 ring-2 ring-amber-400/40' : ($img->is_secondary ? 'border-indigo-400 ring-2 ring-indigo-300/30' : 'border-stone-200') }} rounded-xl p-2.5 shadow-xs flex flex-col justify-between group transition">
                                
                                <!-- Role Badge -->
                                <div class="mb-2 flex items-center justify-between">
                                    @if($img->is_primary)
                                        <span class="bg-amber-500 text-stone-900 text-[9.5px] font-extrabold px-2 py-0.5 rounded shadow-xs uppercase tracking-wider flex items-center gap-0.5">
                                            <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Primary
                                        </span>
                                    @elseif($img->is_secondary)
                                        <span class="bg-indigo-600 text-white text-[9.5px] font-bold px-2 py-0.5 rounded shadow-xs uppercase tracking-wider flex items-center gap-0.5">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Hover
                                        </span>
                                    @else
                                        <span class="bg-stone-100 text-stone-600 text-[9.5px] font-semibold px-1.5 py-0.5 rounded">
                                            Gallery #{{ $loop->iteration }}
                                        </span>
                                    @endif

                                    <button type="submit" 
                                            formaction="{{ route('admin.products.images.delete', $img->id) }}" 
                                            formmethod="POST" 
                                            onclick="return confirm('Remove this image from product?');" 
                                            class="text-rose-500 hover:text-white hover:bg-rose-600 p-1 rounded-md transition text-xs leading-none"
                                            title="Delete image">
                                        &times;
                                    </button>
                                </div>

                                <!-- Image Preview -->
                                <div class="w-full aspect-square bg-[#FAF7F0] rounded-lg overflow-hidden flex items-center justify-center p-1 border border-stone-100">
                                    <img src="{{ $img->url }}" alt="Preview" class="w-full h-full object-contain">
                                </div>

                                <!-- Quick Action Buttons -->
                                <div class="mt-2.5 flex flex-col gap-1">
                                    @if(!$img->is_primary)
                                        <button type="submit" 
                                                formaction="{{ route('admin.products.images.primary', $img->id) }}" 
                                                formmethod="POST" 
                                                class="w-full py-1 px-1.5 bg-[#FAF7F0] hover:bg-[#D4AF6A] text-[#4A2C1D] hover:text-[#2E180E] border border-[#D4AF6A] rounded text-[10px] font-bold transition text-center shadow-2xs flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Set as Primary
                                        </button>
                                    @endif

                                    @if(!$img->is_secondary)
                                        <button type="submit" 
                                                formaction="{{ route('admin.products.images.secondary', $img->id) }}" 
                                                formmethod="POST" 
                                                class="w-full py-1 px-1.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border border-indigo-200 rounded text-[10px] font-semibold transition text-center shadow-2xs flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Set as Hover Swap
                                        </button>
                                    @endif
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Upload New Images Dropzone -->
            <div class="relative group mb-3">
                <div class="absolute -inset-1 bg-gradient-to-r from-[#D4AF6A] to-[#996E2E] rounded-xl blur-sm opacity-25 group-hover:opacity-60 transition duration-500"></div>
                <div class="relative bg-[#FDFBF6] border-2 border-dashed border-[#D4AF6A]/60 hover:border-[#996E2E] hover:bg-[#FAF7F0] rounded-xl p-8 text-center cursor-pointer transition-all duration-300" id="images-dropzone" onclick="triggerImagesInput()">
                    <div class="w-14 h-14 mx-auto bg-white rounded-full flex items-center justify-center border border-[#D4AF6A]/30 shadow-sm mb-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-7 h-7 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="font-serif-royal text-base font-bold text-[#4A2C1D] mb-1">Click to Upload or Drag & Drop New Images</h4>
                    <p class="text-xs text-stone-500 font-medium tracking-wide">SVG, PNG, JPG, JPEG or WEBP (max. 10MB per image)</p>
                </div>
            </div>
            <input type="file" id="images-input" name="images[]" multiple accept="image/*,.jpg,.jpeg,.png,.webp,.svg,.gif" class="hidden">

            <label class="flex items-center space-x-2 bg-[#FAF7F0] p-2.5 rounded-xl border border-[#D4AF6A]/40 cursor-pointer">
                <input type="checkbox" name="set_new_primary" value="1" checked class="rounded text-[#996E2E] focus:ring-[#996E2E]">
                <svg class="w-4 h-4 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span class="font-bold text-stone-800 text-xs">Set first newly uploaded image as Primary (Main Cover Image)</span>
            </label>
            
            <div id="new-images-section" class="mt-4 hidden">
                <h4 class="font-semibold text-stone-700 mb-2">New Images to Upload (Pending Save)</h4>
                <div id="images-preview-container" class="flex flex-wrap gap-4"></div>
            </div>
        </div>

        <!-- Downloadable Documents -->
        <div>
            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4">
                2.5 Downloadable Product Documents
            </h3>
            
            @if($product->documents->count() > 0)
                <div class="flex flex-col gap-2 mb-4">
                    @foreach($product->documents as $doc)
                        <div class="flex items-center justify-between p-3 bg-white border border-stone-200 rounded-lg shadow-xs">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <div>
                                    <div class="font-bold text-stone-800 text-xs">{{ $doc->title }}</div>
                                    <div class="text-[10px] text-stone-500 uppercase tracking-wide">{{ $doc->type }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ asset($doc->file_path) }}" target="_blank" class="text-[#996E2E] hover:text-[#4A2C1D] p-1.5 rounded bg-[#FAF7F0]" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <button type="submit" 
                                        formaction="{{ route('admin.products.documents.delete', $doc->id) }}" 
                                        formmethod="POST" 
                                        onclick="return confirm('Remove this document?');" 
                                        class="text-rose-500 hover:text-white hover:bg-rose-500 p-1.5 rounded border border-rose-200 transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="relative group mt-2">
                <div class="absolute -inset-1 bg-gradient-to-r from-rose-600/30 to-amber-600/30 rounded-xl blur-sm opacity-25 group-hover:opacity-60 transition duration-500"></div>
                <label for="documents-input" class="relative flex flex-col items-center justify-center bg-white border-2 border-dashed border-rose-300 hover:border-rose-500 hover:bg-rose-50/50 rounded-xl p-8 text-center cursor-pointer transition-all duration-300" id="documents-dropzone">
                    <div class="w-12 h-12 mx-auto bg-rose-50 rounded-full flex items-center justify-center border border-rose-200 shadow-sm mb-3 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <h4 class="font-serif-royal text-base font-bold text-stone-800 mb-1">Upload Documents</h4>
                    <p class="text-[11px] text-stone-500 font-medium tracking-wide">PDF, DOC, DOCX (max. 15MB per file, multiple allowed)</p>
                </label>
            </div>
            {{-- Direct multi-file input named doc_files[] — submits natively --}}
            <input type="file" id="documents-input" name="doc_files[]" multiple class="hidden"
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.txt"
                   onchange="handleDocumentsChange(this)">

            <div id="documents-upload-container" class="flex flex-col gap-3 mt-3"></div>
        </div>

        <!-- YouTube Video Link -->
        <div class="p-5 bg-gradient-to-r from-[#FAF7F0] to-[#F5EFE0] rounded-2xl border border-[#D4AF6A]/40 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                        <svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd" /></svg>
                    </div>
                    <div>
                        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                            Product Video (YouTube Link)
                        </h3>
                        <p class="text-xs text-stone-500">Add or edit YouTube video URL (standard, Shorts, or youtu.be)</p>
                    </div>
                </div>
                @if($product->youtube_id)
                    <a href="https://www.youtube.com/watch?v={{ $product->youtube_id }}" target="_blank" class="inline-flex items-center space-x-1.5 px-3 py-1 bg-white border border-[#D4AF6A]/50 rounded-full text-xs text-[#996E2E] font-medium hover:bg-[#FAF7F0]">
                        <span>Test Link &nearr;</span>
                    </a>
                @endif
            </div>
            <div class="mt-3">
                <input type="url" name="youtube_url" value="{{ old('youtube_url', $product->youtube_url) }}" 
                       placeholder="https://www.youtube.com/watch?v=..." 
                       class="w-full border border-stone-300 rounded-xl p-3 text-sm focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] bg-white">
                <span class="text-[11px] text-stone-500 mt-1 block">Customers can click "Watch Craftsmanship Video" on the storefront to view product shine and finishing.</span>
            </div>
        </div>

        @php
            $initialSpecs = [];
            $rawSpecs = old('specifications', $product->specifications ?? '');
            if (!empty($rawSpecs)) {
                $decoded = json_decode($rawSpecs, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $k => $v) {
                        if (in_array(strtolower($k), ['views', 'type'])) continue;
                        if (is_scalar($v) && trim((string)$v) !== '') {
                            $initialSpecs[] = ['key' => (string)$k, 'value' => (string)$v];
                        }
                    }
                } else {
                    $lines = preg_split('/[\r\n]+/', (string)$rawSpecs);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if ($line === '') continue;
                        if (str_contains($line, ':')) {
                            [$k, $v] = explode(':', $line, 2);
                            $initialSpecs[] = ['key' => trim($k), 'value' => trim($v)];
                        } else {
                            $initialSpecs[] = ['key' => 'Specification', 'value' => $line];
                        }
                    }
                }
            }
            if (empty($initialSpecs)) {
                $initialSpecs = [
                    ['key' => 'Material', 'value' => 'Pure Brass Core'],
                    ['key' => 'Plating', 'value' => '1 Gram Micro Gold Plating'],
                    ['key' => 'Finish', 'value' => 'High Polish Mirror Finish'],
                    ['key' => 'Occasion', 'value' => 'Festive, Wedding, Daily Wear'],
                    ['key' => 'Country', 'value' => 'India'],
                ];
            }
            $cleanCareInstructions = strip_tags(old('care_instructions', $product->care_instructions ?? \App\Models\Product::DEFAULT_CARE_INSTRUCTIONS));
            $cleanCareInstructions = preg_replace('/<\/?svg[^>]*>.*?<\/svg>/is', '', $cleanCareInstructions);
            $cleanCareInstructions = trim($cleanCareInstructions);
        @endphp

        <!-- Product Summary, Detailed Story, Dynamic Specifications & Care -->
        <div>
            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4">
                3. Product Descriptions, Specifications & Care Guide
            </h3>

            <div class="space-y-5">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Short Description (Summary displayed above Buy button)</label>
                    <textarea name="short_description" rows="2" class="w-full border rounded-lg p-2.5 text-stone-800">{{ old('short_description', $product->short_description) }}</textarea>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Detailed Description (Craftsmanship & Story)</label>
                    <textarea name="description" rows="3" placeholder="Detailed story of the handcrafted piece..." class="w-full border rounded-lg p-2.5 text-stone-700">{{ old('description', $product->description) }}</textarea>
                </div>

                <!-- Dynamic Technical Specifications Builder (SaaS Grade) -->
                <div x-data="{
                    rows: JSON.parse(atob('{{ base64_encode(json_encode(array_map(function($spec) { $spec['id'] = uniqid(); return $spec; }, $initialSpecs))) }}')),
                    quickChips: [
                        { name: 'Base Metal', defaultVal: 'Pure Brass Core' },
                        { name: 'Micro Plating', defaultVal: '1 Gram Micro Gold Plating' },
                        { name: 'Necklace Length', defaultVal: '24 Inches Adjustable' },
                        { name: 'Earring Type', defaultVal: 'Push Back / Screw Back' },
                        { name: 'Weight', defaultVal: 'Approx. 25-30 Grams' },
                        { name: 'Length / Size', defaultVal: '24 Inches' },
                        { name: 'Lock / Clasp', defaultVal: 'S-Hook Safety Lock' },
                        { name: 'Occasion', defaultVal: 'Festive, Wedding, Daily Wear' },
                        { name: 'Country of Origin', defaultVal: 'India' }
                    ],
                    addQuick(chip) {
                        if (!this.rows.find(r => r.key === chip.name)) {
                            this.rows.push({ id: Date.now() + Math.random(), key: chip.name, value: chip.defaultVal });
                        }
                    },
                    serialized() {
                        const obj = {};
                        this.rows.forEach(r => {
                            const k = (r.key || '').trim();
                            const v = (r.value || '').trim();
                            if (k && v) obj[k] = v;
                        });
                        return Object.keys(obj).length ? JSON.stringify(obj) : '';
                    }
                }" class="space-y-3 bg-[#FAF7F0]/70 p-4 sm:p-5 rounded-2xl border border-[#D4AF6A]/40 shadow-2xs">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-[#D4AF6A]/30 gap-2">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-lg bg-white border border-[#D4AF6A] flex items-center justify-center text-[#996E2E] shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D]">Technical Specifications (Dimensions, Metal, Weight)</h4>
                                <p class="text-[11px] text-stone-500">Dynamic Key-Value Builder. Clean structured data without code or curly brackets.</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold text-[#996E2E] bg-white px-2.5 py-1 rounded-full border border-[#D4AF6A]/40 self-start sm:self-auto shadow-2xs">
                            SaaS Dynamic Builder
                        </span>
                    </div>

                    <!-- Quick Add Common Attribute Chips (No emojis, premium icons) -->
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 block mb-1.5">Quick-Add Common Attributes:</span>
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <template x-for="chip in quickChips" :key="chip.name">
                                <button type="button" 
                                        @click="
                                            let existing = rows.find(r => r.key.toLowerCase() === chip.name.toLowerCase());
                                            if (existing) {
                                                if (!existing.value.trim()) existing.value = chip.defaultVal;
                                            } else {
                                                rows.push({ key: chip.name, value: chip.defaultVal });
                                            }
                                        "
                                        class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A]/50 hover:border-[#996E2E] text-stone-700 hover:text-[#4A2C1D] text-xs font-medium transition shadow-2xs cursor-pointer active:scale-95">
                                    <svg class="w-3 h-3 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    <span x-text="chip.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Specifications Table / Rows Container -->
                    <div class="space-y-2 pt-1">
                        <template x-for="(row, index) in rows" :key="row.id">
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 p-2 sm:p-2.5 rounded-xl bg-white border border-stone-200 hover:border-[#D4AF6A]/70 transition shadow-2xs">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-stone-500 mb-0.5 sm:hidden">Attribute Name</label>
                                        <input type="text" 
                                               x-model="row.key" 
                                               placeholder="e.g. Base Metal, Weight"
                                               class="w-full border border-stone-300 rounded-lg px-3 py-2 text-xs font-semibold text-[#4A2C1D] focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] bg-stone-50/60">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-stone-500 mb-0.5 sm:hidden">Specification Value</label>
                                        <input type="text" 
                                               x-model="row.value" 
                                               placeholder="e.g. Pure Brass Core, 24 Inches, 24 Grams"
                                               class="w-full border border-stone-300 rounded-lg px-3 py-2 text-xs text-stone-800 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E]">
                                    </div>
                                </div>
                                <button type="button" 
                                        @click="rows.splice(index, 1)" 
                                        class="self-end sm:self-center p-2 rounded-lg text-stone-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer shrink-0"
                                        title="Remove attribute">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>

                        <div x-show="rows.length === 0" class="py-6 text-center text-stone-400 text-xs border-2 border-dashed border-stone-200 rounded-xl bg-white">
                            No specifications added yet. Click "+ Add Custom Attribute" or select a quick attribute above.
                        </div>
                    </div>

                    <div class="pt-1 flex items-center justify-between">
                        <button type="button" 
                                @click="rows.push({ id: Date.now() + Math.random(), key: '', value: '' })" 
                                class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-lg bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A] text-[#996E2E] hover:text-[#4A2C1D] text-xs font-semibold transition shadow-2xs active:scale-95 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Add Custom Attribute</span>
                        </button>
                        <span class="text-[11px] text-stone-500 font-medium" x-text="rows.length + ' attributes configured'"></span>
                    </div>

                    <!-- Hidden serialized JSON and array inputs for 100% backend compatibility -->
                    <input type="hidden" name="specifications" :value="serialized()">
                    <template x-for="(r, i) in rows" :key="'h-'+i">
                        <div>
                            <input type="hidden" name="spec_keys[]" :value="r.key">
                            <input type="hidden" name="spec_values[]" :value="r.value">
                        </div>
                    </template>
                </div>

                <!-- SaaS Care & Maintenance Instructions Component with 1-Click Luxury Presets -->
                <div x-data="{
                    careContent: JSON.parse(atob('{{ base64_encode(json_encode($cleanCareInstructions)) }}'))
                }" class="space-y-3 bg-[#FAF7F0]/70 p-4 sm:p-5 rounded-2xl border border-[#D4AF6A]/40 shadow-2xs">

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-[#D4AF6A]/30 gap-2">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-lg bg-white border border-[#D4AF6A] flex items-center justify-center text-[#996E2E] shadow-2xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D]">Care & Maintenance Instructions</h4>
                                <p class="text-[11px] text-stone-500">1-Click Luxury Presets. Clean human-readable guidelines without HTML or code.</p>
                            </div>
                        </div>

                        <span class="text-[10px] font-semibold text-[#996E2E] bg-white px-2.5 py-1 rounded-full border border-[#D4AF6A]/40 self-start sm:self-auto shadow-2xs">
                            1-Click Luxury Presets
                        </span>
                    </div>

                    <!-- Quick Preset Buttons (NO EMOJIS, PREMIUM SVG ICONS) -->
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 pt-1">
                        <button type="button" 
                                @click="careContent = '• Keep away from water: Remove before bath, swimming, rain, or washing hands.\n• Avoid chemicals: Perfume, deodorants, hairsprays, and lotions can dull the micro gold shine.\n• Wear last, remove first: Wear after makeup/perfume dries; remove before sleeping.\n• Wipe after use: Gently clean with a soft, dry cotton cloth after each wear.\n• Safe storage: Store in an airtight zip pouch or separate velvet box to prevent scratches.'" 
                                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A]/60 text-stone-700 hover:text-[#4A2C1D] text-xs font-medium transition shadow-2xs cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            <span>Standard 1 Gram Gold</span>
                        </button>

                        <button type="button" 
                                @click="careContent = '• Strictly avoid contact with water, moisture, and alcohol-based liquids.\n• Kundan stones and faux pearls should never be soaked, immersed, or washed.\n• Store flat in a padded jewellery box lined with soft cotton to prevent chipping.\n• Clean gently using a dry, lint-free micro-fiber cloth only.\n• Apply perfumes, oils, and body lotions completely before wearing.'" 
                                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A]/60 text-stone-700 hover:text-[#4A2C1D] text-xs font-medium transition shadow-2xs cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span>Kundan & Polki</span>
                        </button>

                        <button type="button" 
                                @click="careContent = '• Preserve the handcrafted antique matte finish by keeping away from soaps and detergents.\n• Do not use ultrasonic cleaners, toothpaste, brushes, or chemical dips.\n• Store separately in a moisture-free pouch to protect the authentic finish.\n• Gently wipe with a soft dry muslin cloth after every wear.\n• Keep in a cool, dry place away from direct sunlight and humid areas.'" 
                                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A]/60 text-stone-700 hover:text-[#4A2C1D] text-xs font-medium transition shadow-2xs cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>Antique Matte Finish</span>
                        </button>

                        <button type="button" 
                                @click="careContent = {{ json_encode(strip_tags(\App\Models\Product::DEFAULT_CARE_INSTRUCTIONS)) }}" 
                                class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-lg text-stone-500 hover:text-[#996E2E] hover:bg-white text-xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Reset Default</span>
                        </button>

                        <button type="button" 
                                @click="careContent = ''" 
                                class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-lg text-stone-400 hover:text-rose-600 hover:bg-rose-50 text-xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Clear</span>
                        </button>
                    </div>

                    <!-- Clean Textarea (no raw code or SVG) -->
                    <div>
                        <textarea name="care_instructions" 
                                  x-model="careContent" 
                                  rows="4" 
                                  placeholder="Enter care instructions or select a luxury preset above..." 
                                  class="w-full border border-stone-300 rounded-xl p-3 text-xs text-stone-700 font-sans focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E] leading-relaxed bg-white"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3.5. Product Variants & Sizing (SaaS Dynamic Options) -->
        @php
            $existingVariants = $product->variants->map(function($v) use ($product) {
                return [
                    'id' => $v->id,
                    'name' => ($v->name && $v->name !== 'Option / Size' && $v->name !== 'Size / Option') ? $v->name : 'Size',
                    'value' => $v->value,
                    'stock_quantity' => (int) $v->stock_quantity,
                    'price_override' => $v->price_override !== null ? (float) $v->price_override : '',
                ];
            })->values()->all();
        @endphp
        <div class="space-y-3 sm:space-y-4 bg-[#FAF7F0]/70 p-2.5 sm:p-6 rounded-xl sm:rounded-2xl border border-[#D4AF6A]/50 shadow-2xs"
             x-data="{
                hasVariants: {{ count($existingVariants) > 0 ? 'true' : 'false' }},
                variants: {{ json_encode($existingVariants) }},
                basePrice: '{{ (float) $product->price }}',
                baseStock: '{{ (int) $product->stock_quantity }}',
                totalVariantStock() {
                    return this.variants.reduce((sum, v) => sum + (parseInt(v.stock_quantity) || 0), 0);
                },
                syncBaseStockWithVariants() {
                    const total = this.totalVariantStock();
                    const baseInput = document.getElementById('stock_quantity') || document.querySelector('input[name=\'stock_quantity\']');
                    if (baseInput) {
                        baseInput.value = total;
                        this.baseStock = total;
                        baseInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                },
                addVariant(name = 'Size', value = '', stock = null, price = '') {
                    const baseStk = parseInt(this.baseStock) || 10;
                    if (this.variants.length === 1 && (!this.variants[0].value || this.variants[0].value.trim() === '')) {
                        this.variants[0] = {
                            id: null,
                            name: name,
                            value: value,
                            stock_quantity: stock !== null ? stock : baseStk,
                            price_override: price
                        };
                        return;
                    }
                    this.variants.push({
                        id: null,
                        name: name,
                        value: value,
                        stock_quantity: stock !== null ? stock : baseStk,
                        price_override: price
                    });
                },
                removeVariant(index) {
                    this.variants.splice(index, 1);
                    if (this.variants.length === 0) {
                        this.hasVariants = false;
                    }
                },
                applyPreset(type) {
                    this.hasVariants = true;
                    const baseStk = parseInt(this.baseStock) || 10;
                    let items = [];
                    if (type === 'bangles') {
                        items = [
                            { name: 'Size', value: '2.4 Size (2-4/16 inch)', price: '' },
                            { name: 'Size', value: '2.6 Size (2-6/16 inch)', price: '' },
                            { name: 'Size', value: '2.8 Size (2-8/16 inch)', price: '' }
                        ];
                    } else if (type === 'chains') {
                        items = [
                            { name: 'Length', value: '20 Inch Length', price: '' },
                            { name: 'Length', value: '22 Inch Length', price: '' },
                            { name: 'Length', value: '24 Inch Length', price: '' }
                        ];
                    } else if (type === 'tones') {
                        items = [
                            { name: 'Tone', value: 'Yellow Gold Tone', price: '' },
                            { name: 'Tone', value: 'Rose Gold Tone', price: '' },
                            { name: 'Tone', value: 'Antique Matte Gold', price: '' }
                        ];
                    } else if (type === 'sets') {
                        items = [
                            { name: 'Option', value: 'Standard Set', price: '' },
                            { name: 'Option', value: 'With Maang Tikka', price: '' }
                        ];
                    }

                    if (this.variants.length === 1 && (!this.variants[0].value || this.variants[0].value.trim() === '')) {
                        this.variants = [];
                    }

                    items.forEach(it => {
                        if (!this.variants.some(v => v.value.toLowerCase() === it.value.toLowerCase())) {
                            this.variants.push({
                                id: null,
                                name: it.name,
                                value: it.value,
                                stock_quantity: baseStk,
                                price_override: it.price
                            });
                        }
                    });
                }
             }">

            <input type="hidden" name="variants_present" value="1">

            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-[#D4AF6A]/30 gap-2">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white border border-[#D4AF6A] flex items-center justify-center text-[#996E2E] shadow-2xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <h4 class="font-serif-royal font-bold text-sm text-[#4A2C1D]">
                            3.5 Product Variants &amp; Sizing (SaaS Dynamic Options)
                        </h4>
                        <p class="text-[11px] text-stone-500">
                            Configure multiple sizes, lengths, or finish options. Customers can dynamically select their variant on the product page.
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <label class="inline-flex items-center space-x-2 cursor-pointer bg-white px-3 py-1.5 rounded-lg border border-[#D4AF6A]/60 shadow-2xs hover:border-[#D4AF6A] transition">
                        <input type="checkbox" x-model="hasVariants" @change="if(hasVariants && variants.length === 0) addVariant('Size', '2.4 Size (2-4/16 inch)', parseInt(baseStock) || 10, '');" class="rounded text-[#996E2E] focus:ring-[#996E2E]">
                        <span class="text-xs font-semibold text-[#4A2C1D]">Enable Variants</span>
                    </label>
                </div>
            </div>

            <!-- Presets & Variant Manager -->
            <div x-show="hasVariants" class="space-y-3 pt-1">
                <!-- 1-Click Common Jewellery Presets -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-stone-500 block mb-1.5">1-Click Fast Jewellery Presets:</span>
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <button type="button" @click="applyPreset('bangles')" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A]/60 text-stone-700 hover:text-[#4A2C1D] text-xs font-medium transition shadow-2xs cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Bangle Sizes (2.4, 2.6, 2.8)</span>
                        </button>
                        <button type="button" @click="applyPreset('chains')" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A]/60 text-stone-700 hover:text-[#4A2C1D] text-xs font-medium transition shadow-2xs cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Chain Lengths (20&quot;, 22&quot;, 24&quot;)</span>
                        </button>
                        <button type="button" @click="applyPreset('tones')" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A]/60 text-stone-700 hover:text-[#4A2C1D] text-xs font-medium transition shadow-2xs cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Plating Tones</span>
                        </button>
                        <button type="button" @click="applyPreset('sets')" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A]/60 text-stone-700 hover:text-[#4A2C1D] text-xs font-medium transition shadow-2xs cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Bridal Set Options</span>
                        </button>
                    </div>
                </div>

                <!-- Variants Table / Rows -->
                <div class="space-y-2 pt-2">
                    <!-- Desktop Table Column Header (Hidden on Mobile) -->
                    <div class="hidden sm:grid sm:grid-cols-12 sm:gap-2.5 sm:items-center px-3 py-2 text-[10.5px] font-bold text-stone-600 uppercase tracking-wider bg-white/95 border border-stone-200/90 rounded-xl shadow-2xs">
                        <div class="sm:col-span-2 flex items-center space-x-1">
                            <span>Option Type</span>
                        </div>
                        <div class="sm:col-span-5 flex items-center space-x-1">
                            <span>Variant Value / Sizing Label</span>
                            <span class="text-rose-500 font-bold">*</span>
                        </div>
                        <div class="sm:col-span-2 flex items-center space-x-1">
                            <span>Price Override</span>
                            <span class="text-stone-400 font-normal normal-case">(₹)</span>
                        </div>
                        <div class="sm:col-span-2 flex items-center space-x-1">
                            <span>Live Stock</span>
                            <span class="text-rose-500 font-bold">*</span>
                            <span class="text-stone-400 font-normal normal-case">(Units)</span>
                        </div>
                        <div class="sm:col-span-1 text-center">
                            <span>Del</span>
                        </div>
                    </div>

                    <template x-for="(v, index) in variants" :key="index">
                        <div class="p-3 sm:p-2.5 rounded-xl bg-white border border-stone-200 hover:border-[#D4AF6A] transition-all shadow-2xs space-y-2.5 sm:space-y-0 sm:grid sm:grid-cols-12 sm:gap-2.5 sm:items-center">
                            <input type="hidden" :name="'variants[' + index + '][id]'" :value="v.id">

                            <!-- Mobile Top Header Bar (Hidden on Desktop) -->
                            <div class="flex items-center justify-between gap-2 sm:hidden pb-2 border-b border-stone-100">
                                <div class="flex items-center gap-1.5 min-w-0 flex-1">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-[#FAF7F0] border border-[#D4AF6A] text-[#996E2E] font-bold text-[10px] shrink-0 font-mono" x-text="'#' + (index + 1)"></span>
                                    <span class="text-xs font-bold text-[#4A2C1D] whitespace-nowrap">Variant <span x-text="'#' + (index + 1)"></span></span>
                                    <span x-show="v.value" class="text-[11px] text-stone-500 font-medium truncate min-w-0" x-text="'(' + v.value + ')'"></span>
                                </div>
                                <button type="button" 
                                        @click="removeVariant(index)" 
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2 py-1 rounded-lg border border-rose-200/80 transition cursor-pointer shrink-0 active:scale-95"
                                        title="Delete Variant">
                                    <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span class="leading-none">Delete</span>
                                </button>
                            </div>

                            <!-- Col 1: Option Type (Desktop: 2 cols, Mobile: full) -->
                            <div class="sm:col-span-2">
                                <label class="block text-[10.5px] font-bold text-stone-600 mb-1 sm:hidden">Option Type</label>
                                <input type="text" 
                                       :name="'variants[' + index + '][name]'" 
                                       x-model="v.name" 
                                       placeholder="e.g. Size" 
                                       class="w-full border border-stone-300 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-[#4A2C1D] bg-stone-50/70 focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E]" required>
                            </div>

                            <!-- Col 2: Variant Value / Sizing Label (Desktop: 5 cols, Mobile: full) -->
                            <div class="sm:col-span-5">
                                <label class="block text-[10.5px] font-bold text-stone-600 mb-1 sm:hidden">Variant Name / Sizing Value *</label>
                                <input type="text" 
                                       :name="'variants[' + index + '][value]'" 
                                       x-model="v.value" 
                                       placeholder="e.g. 2.4 Size (2-4/16 inch) or 20 Inch" 
                                       class="w-full border border-stone-300 rounded-lg px-3 py-1.5 text-xs text-stone-800 font-medium focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E]" required>
                            </div>

                            <!-- Col 3: Price Override (Desktop: 2 cols, Mobile: full) -->
                            <div class="sm:col-span-2">
                                <label class="block text-[10.5px] font-bold text-stone-600 mb-1 sm:hidden">Price Override (Blank = Base ₹<span x-text="basePrice"></span>)</label>
                                <div class="relative">
                                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-stone-400 font-semibold text-xs pointer-events-none select-none">₹</span>
                                    <input type="number" 
                                           step="0.01" 
                                           :name="'variants[' + index + '][price_override]'" 
                                           x-model="v.price_override" 
                                           :placeholder="'Base (₹' + (basePrice || 0) + ')'" 
                                           class="w-full border border-stone-300 rounded-lg pl-6 pr-2.5 py-1.5 text-xs font-mono text-[#996E2E] font-bold focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E]">
                                </div>
                            </div>

                            <!-- Col 4: Live Stock (Desktop: 2 cols, Mobile: full) -->
                            <div class="sm:col-span-2">
                                <label class="block text-[10.5px] font-bold text-stone-600 mb-1 sm:hidden">Stock Quantity (Units) *</label>
                                <div class="relative">
                                    <input type="number" 
                                           min="0"
                                           step="1"
                                           :name="'variants[' + index + '][stock_quantity]'" 
                                           x-model="v.stock_quantity" 
                                           placeholder="10" 
                                           class="w-full border border-stone-300 rounded-lg pl-2.5 pr-9 py-1.5 text-xs font-mono text-stone-900 font-semibold focus:border-[#996E2E] focus:ring-1 focus:ring-[#996E2E]" required>
                                    <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-bold text-stone-400 uppercase tracking-wider pointer-events-none select-none">pcs</span>
                                </div>
                            </div>

                            <!-- Col 5: Desktop Delete Button (Desktop: 1 col, Mobile: hidden) -->
                            <div class="hidden sm:flex sm:col-span-1 items-center justify-center">
                                <button type="button" 
                                        @click="removeVariant(index)" 
                                        class="p-1.5 rounded-lg text-stone-400 hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer" 
                                        title="Delete Variant">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="variants.length === 0" class="py-6 text-center text-stone-400 text-xs border-2 border-dashed border-stone-200 rounded-xl bg-white">
                        No variants added yet. Click "+ Add Another Variant" or pick a preset above.
                    </div>
                </div>

                <!-- Live SaaS Variant Inventory Summary & Sync Bar -->
                <div x-show="variants.length > 0" class="p-3 bg-white rounded-xl border border-[#D4AF6A]/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs shadow-2xs">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-stone-100 text-stone-700 font-medium">
                            <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <span x-text="variants.length + ' Active Variants'"></span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>Total Variant Inventory: <strong class="font-mono text-emerald-900" x-text="totalVariantStock() + ' pcs'"></strong></span>
                        </span>
                        <span x-show="variants.some(v => parseInt(v.stock_quantity) <= 0)" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-medium border border-rose-200 text-[11px]" x-cloak>
                            <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Has Sold-Out Variant</span>
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="syncBaseStockWithVariants()" 
                                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-[#FAF7F0] hover:bg-[#FAF3E0] border border-[#D4AF6A] text-[#996E2E] hover:text-[#4A2C1D] text-xs font-semibold transition cursor-pointer active:scale-95 shadow-2xs"
                                title="Set base product stock to match total variant units">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>1-Click Sync Base Stock (<span x-text="totalVariantStock()"></span> units)</span>
                        </button>
                    </div>
                </div>

                <!-- Add Button & Count -->
                <div class="pt-2 flex items-center justify-between">
                    <button type="button" 
                            @click="addVariant('Size', '', parseInt(baseStock) || 10, '')" 
                            class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-lg bg-white hover:bg-[#FAF7F0] border border-[#D4AF6A] text-[#996E2E] hover:text-[#4A2C1D] text-xs font-semibold transition shadow-2xs active:scale-95 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Add Another Variant</span>
                    </button>
                    <span class="text-[11px] text-stone-500 font-medium" x-text="variants.length + ' variants active'"></span>
                </div>
            </div>

            <div x-show="!hasVariants" class="text-center py-4 bg-white/70 rounded-xl border border-dashed border-stone-300 text-stone-500 text-xs">
                <span>This product does not have variants. Selling price &amp; live stock from Section 1 apply.</span>
                <button type="button" @click="hasVariants = true; addVariant('Size', '2.4 Size (2-4/16 inch)', parseInt(baseStock) || 10, '');" class="text-[#996E2E] font-bold hover:underline ml-1">Enable Variants</button>
            </div>
        </div>

        <!-- Dynamic Attribute Values Tagging -->
        <div>
            <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] pb-2 border-b border-stone-200 mb-4">
                4. Tag Attributes
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @php $assignedAttrIds = $product->attributeValues->pluck('id')->toArray(); @endphp
                @foreach($attributeGroups as $grp)
                    <div class="p-3 bg-[#FAF7F0] rounded-xl border border-[#D4AF6A]/30">
                        <strong class="font-serif-royal text-[#4A2C1D] block mb-2">{{ $grp->name }}</strong>
                        <div class="space-y-1.5 max-h-36 overflow-y-auto">
                            @foreach($grp->values as $v)
                                <label class="flex items-center space-x-2 text-stone-700 cursor-pointer">
                                    <input type="checkbox" name="attributes[]" value="{{ $v->id }}" {{ in_array($v->id, $assignedAttrIds) ? 'checked' : '' }} class="rounded text-[#996E2E]">
                                    <span>{{ $v->value }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pt-6 border-t border-stone-200 flex items-center space-x-3">
            <button type="submit" class="px-8 py-3 bg-[#4A2C1D] text-[#E7C77B] rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-[#2E180E] transition shadow-xs cursor-pointer">
                Update Jewellery Piece
            </button>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-stone-500 hover:text-stone-800">Cancel</a>
        </div>
    </form>

</div>

@push('scripts')
<script>
// ----------------------------------------------------
// IMAGES - Drag & Drop Multi-Image Management
// Uses DataTransfer on the REAL images-input (browser accepts it since
// the input was involved in file selection dialog)
// ----------------------------------------------------
let selectedImageFiles = [];
let isSyncingInput = false;

function parseSize(sizeStr) {
    if (!sizeStr) return 0;
    let val = parseFloat(sizeStr);
    let last = sizeStr.toString().trim().toUpperCase().slice(-1);
    if (last === 'G') val *= 1024 * 1024 * 1024;
    if (last === 'M') val *= 1024 * 1024;
    if (last === 'K') val *= 1024;
    return val;
}

const serverMaxUploadBytes = parseSize('{{ ini_get("upload_max_filesize") }}') || (2 * 1024 * 1024);
const serverMaxPostBytes   = parseSize('{{ ini_get("post_max_size") }}') || (8 * 1024 * 1024);

function triggerImagesInput() {
    const input = document.getElementById('images-input');
    if (input) { input.value = ''; input.click(); }
}

function handleImageSelect(input) {
    if (isSyncingInput) return;
    if (!input || !input.files || !input.files.length) return;
    const appMaxBytes = 10 * 1024 * 1024;
    const maxSizeBytes = Math.min(appMaxBytes, serverMaxUploadBytes);
    Array.from(input.files).forEach(file => {
        if (file.size > maxSizeBytes) {
            if (serverMaxUploadBytes < appMaxBytes && file.size > serverMaxUploadBytes) {
                alert('Image "' + file.name + '" exceeds your server limit of {{ ini_get("upload_max_filesize") }}.\nIncrease upload_max_filesize in php.ini to allow up to 10MB.');
            } else {
                alert('Image "' + file.name + '" exceeds the 10MB size limit.');
            }
            return;
        }
        if (!selectedImageFiles.some(f => f.name === file.name && f.size === file.size)) {
            selectedImageFiles.push(file);
        }
    });
    syncInputAndRenderPreviews();
}

function removeSelectedImage(index) {
    selectedImageFiles.splice(index, 1);
    syncInputAndRenderPreviews();
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024, sizes = ['B', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function syncInputAndRenderPreviews() {
    const input = document.getElementById('images-input');
    const previews = document.getElementById('images-preview-container');
    const section = document.getElementById('new-images-section');
    if (input) {
        isSyncingInput = true;
        try {
            const dt = new DataTransfer();
            selectedImageFiles.forEach(f => dt.items.add(f));
            input.files = dt.files;
            if (selectedImageFiles.length > 0) {
                input.name = 'images[]';
            } else {
                input.removeAttribute('name');
            }
        } catch(err) { console.error('DataTransfer error:', err); }
        setTimeout(() => { isSyncingInput = false; }, 50);
    }
    if (section) {
        section.classList.toggle('hidden', selectedImageFiles.length === 0);
    }
    if (!previews) return;
    previews.innerHTML = '';
    selectedImageFiles.forEach((file, index) => {
        const card = document.createElement('div');
        card.style.cssText = 'position:relative;width:96px;height:96px;border-radius:12px;overflow:hidden;border:2px solid #D4AF6A;box-shadow:0 4px 12px rgba(0,0,0,.08);background:#fff;flex-shrink:0;transition:all .2s;';
        const img = document.createElement('img');
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
        img.title = file.name + ' (' + formatFileSize(file.size) + ')';
        const bar = document.createElement('div');
        bar.style.cssText = 'position:absolute;bottom:0;left:0;width:100%;background:rgba(74,44,29,.92);color:#E7C77B;font-size:9px;text-align:center;padding:2px 0;font-weight:700;text-transform:uppercase;pointer-events:none;letter-spacing:.5px;';
        bar.textContent = index === 0 ? 'NEW (COVER)' : 'NEW #' + (index + 1);
        const del = document.createElement('button');
        del.type = 'button';
        del.style.cssText = 'position:absolute;top:4px;right:4px;background:#ef4444;color:white;border:none;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:12px;font-weight:bold;z-index:10;box-shadow:0 2px 4px rgba(0,0,0,.2);';
        del.innerHTML = '&times;';
        del.onclick = function(e) { e.stopPropagation(); removeSelectedImage(index); };
        card.appendChild(img); card.appendChild(bar); card.appendChild(del);
        previews.appendChild(card);
        const reader = new FileReader();
        reader.onload = e => img.src = e.target.result;
        reader.readAsDataURL(file);
    });
}

// ----------------------------------------------------
// DOCUMENTS - Direct multi-file approach (edit view)
// Files are tracked in docMeta[] for title/type UI.
// doc_files[] input submits natively without DataTransfer.
// ----------------------------------------------------
let docMeta = [];

function handleDocumentsChange(input) {
    const container = document.getElementById('documents-upload-container');
    if (!container || !input.files || !input.files.length) return;
    docMeta = [];
    container.innerHTML = '';
    Array.from(input.files).forEach((file, idx) => {
        const ext = file.name.split('.').pop().toLowerCase();
        let defType = 'other';
        const nl = file.name.toLowerCase();
        if (nl.includes('coa')) defType = 'coa';
        else if (nl.includes('msds')) defType = 'msds';
        else if (nl.includes('tds')) defType = 'tds';
        else if (nl.includes('brochure') || nl.includes('catalog')) defType = 'brochure';
        const title = file.name.replace(/\.[^/.]+$/, '');
        docMeta.push({ index: idx, title, type: defType });
        const row = document.createElement('div');
        row.className = 'bg-white border border-stone-200 rounded-lg p-3 shadow-xs flex flex-wrap items-center gap-3';
        row.innerHTML = `
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-stone-800 truncate" title="${file.name}">${file.name}</p>
                    <p class="text-[10px] text-stone-400">${(file.size/1024).toFixed(0)} KB · ${ext.toUpperCase()}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                <select class="border rounded p-1.5 text-xs" data-idx="${idx}" onchange="docMeta[this.dataset.idx].type=this.value">
                    <option value="coa" ${defType==='coa'?'selected':''}>COA</option>
                    <option value="msds" ${defType==='msds'?'selected':''}>MSDS</option>
                    <option value="tds" ${defType==='tds'?'selected':''}>TDS</option>
                    <option value="brochure" ${defType==='brochure'?'selected':''}>Brochure</option>
                    <option value="other" ${defType==='other'?'selected':''}>Other</option>
                </select>
                <input type="text" value="${title}" placeholder="Document title" class="border rounded p-1.5 text-xs w-32 sm:w-40"
                       data-idx="${idx}" oninput="docMeta[this.dataset.idx].title=this.value">
                <span class="text-[10px] font-medium px-1.5 py-0.5 rounded ${file.size > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-600'}">
                    ${file.size > 0 ? '✓ Ready' : '⚠️ Empty'}
                </span>
            </div>
        `;
        container.appendChild(row);
    });
}

// ----------------------------------------------------
// DOM READY - Wire up dropzones and form submit
// ----------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    // - Image dropzone (existing DataTransfer for accumulated multi-select still works for this)
    const imgDropzone = document.getElementById('images-dropzone');
    const imgInput = document.getElementById('images-input');
    const form = document.getElementById('product-form') || document.querySelector('form');

    if (imgDropzone && imgInput) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => {
            imgDropzone.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }, false);
        });
        ['dragenter', 'dragover'].forEach(ev => imgDropzone.addEventListener(ev, () => {
            imgDropzone.style.borderColor = '#4A2C1D'; imgDropzone.style.background = '#F5EFE0';
        }));
        ['dragleave', 'drop'].forEach(ev => imgDropzone.addEventListener(ev, () => {
            imgDropzone.style.borderColor = '#D4AF6A'; imgDropzone.style.background = '#FAF7F0';
        }));
        imgDropzone.addEventListener('drop', e => {
            const files = e.dataTransfer.files;
            if (files && files.length) {
                Array.from(files).forEach(f => {
                    if (f.size > 10 * 1024 * 1024) { alert('File "' + f.name + '" exceeds 10MB.'); return; }
                    if (!selectedImageFiles.some(c => c.name === f.name && c.size === f.size)) selectedImageFiles.push(f);
                });
                syncInputAndRenderPreviews();
            }
        });
        imgInput.addEventListener('change', () => handleImageSelect(imgInput));
    }

    // - Document dropzone drag-and-drop
    const docDropzone = document.getElementById('documents-dropzone');
    const docInput = document.getElementById('documents-input');
    if (docDropzone && docInput) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => {
            docDropzone.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }, false);
        });
        ['dragenter', 'dragover'].forEach(ev => docDropzone.addEventListener(ev, () => {
            docDropzone.classList.add('border-rose-500', 'bg-rose-50/50');
        }));
        ['dragleave', 'drop'].forEach(ev => docDropzone.addEventListener(ev, () => {
            docDropzone.classList.remove('border-rose-500', 'bg-rose-50/50');
        }));
        docDropzone.addEventListener('drop', e => {
            const files = Array.from(e.dataTransfer.files);
            if (!files.length) return;
            try {
                const dt = new DataTransfer();
                Array.from(docInput.files || []).forEach(f => dt.items.add(f));
                files.forEach(f => dt.items.add(f));
                docInput.files = dt.files;
                docInput.dispatchEvent(new Event('change'));
            } catch (err) { console.warn('Drop error', err); }
        });
    }

    // - Form submit: sync images + inject doc metadata
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitter = e.submitter;
            if (submitter && submitter.hasAttribute('formaction')) {
                return; // Image delete / doc delete sub-form, skip
            }
            // Sync accumulated images (edit view uses selectedImageFiles array)
            if (imgInput) {
                if (selectedImageFiles.length > 0) {
                    try {
                        const dt = new DataTransfer();
                        selectedImageFiles.forEach(f => dt.items.add(f));
                        imgInput.files = dt.files;
                    } catch(err) {}
                    imgInput.name = 'images[]';
                } else {
                    imgInput.removeAttribute('name');
                    imgInput.value = '';
                }
            }
            // Inject doc metadata hidden inputs
            form.querySelectorAll('.injected-doc-meta').forEach(el => el.remove());
            docMeta.forEach(d => {
                const tIn = document.createElement('input');
                tIn.type = 'hidden'; tIn.name = 'doc_titles[]';
                tIn.value = d.title || ''; tIn.className = 'injected-doc-meta';
                const tyIn = document.createElement('input');
                tyIn.type = 'hidden'; tyIn.name = 'doc_types[]';
                tyIn.value = d.type || 'other'; tyIn.className = 'injected-doc-meta';
                form.appendChild(tIn);
                form.appendChild(tyIn);
            });
        });
    }
});
</script>
@endpush
@endsection
