@extends('admin.layouts.admin')

@section('title', 'Dynamic Filter Builder')
@section('page_title', 'Dynamic Filter & Attribute Group Builder')

@section('content')
<div class="space-y-6 sm:space-y-8" x-data="{
    searchCatCreate: '',
    categoryFilterGroup: {}
}">

    <!-- 1. Header Banner & Quick SaaS KPI Metrics -->
    <div class="bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-white rounded-2xl sm:rounded-3xl p-5 sm:p-7 border border-[#D4AF6A]/40 shadow-sm relative overflow-hidden">
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none">
            <svg class="w-64 h-64 text-[#E7C77B]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        </div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#E7C77B]/15 border border-[#E7C77B]/30 text-[#E7C77B] text-xs font-semibold uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    E-Commerce Filter Engine
                </div>
                <h2 class="font-serif-royal text-xl sm:text-2xl lg:text-3xl font-bold text-[#FAF7F0] tracking-wide">
                    Dynamic Category Filter Builder
                </h2>
                <p class="text-xs sm:text-sm text-stone-200/90 leading-relaxed">
                    Create reusable jewellery attribute groups (e.g. <em>Metal Tone</em>, <em>Occasion</em>, <em>Chain Type</em>, <em>Purity</em>), define filter values, and link them to categories. The storefront category pages instantly display interactive sidebar filters for shoppers!
                </p>
            </div>

            <!-- KPI Pill Counters -->
            @php
                $totalOptionValues = 0;
                foreach($groups as $g) {
                    $totalOptionValues += count($g->values);
                }
            @endphp
            <div class="grid grid-cols-3 gap-2.5 sm:gap-3 shrink-0">
                <div class="bg-black/25 backdrop-blur-xs border border-white/10 rounded-xl sm:rounded-2xl p-3 sm:p-4 text-center">
                    <span class="block text-xl sm:text-2xl font-bold text-[#E7C77B] font-mono">{{ count($groups) }}</span>
                    <span class="text-[10px] sm:text-xs text-stone-300 uppercase tracking-wider font-medium">Filter Groups</span>
                </div>
                <div class="bg-black/25 backdrop-blur-xs border border-white/10 rounded-xl sm:rounded-2xl p-3 sm:p-4 text-center">
                    <span class="block text-xl sm:text-2xl font-bold text-[#FAF7F0] font-mono">{{ $totalOptionValues }}</span>
                    <span class="text-[10px] sm:text-xs text-stone-300 uppercase tracking-wider font-medium">Filter Values</span>
                </div>
                <div class="bg-black/25 backdrop-blur-xs border border-white/10 rounded-xl sm:rounded-2xl p-3 sm:p-4 text-center">
                    <span class="block text-xl sm:text-2xl font-bold text-[#E7C77B] font-mono">{{ count($categories) }}</span>
                    <span class="text-[10px] sm:text-xs text-stone-300 uppercase tracking-wider font-medium">Categories</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Create New Filter Group Card -->
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-stone-200 p-5 sm:p-7 shadow-xs" 
         x-data="{ 
            selectedCats: [], 
            catSearch: '',
            toggleCat(id) {
                if (this.selectedCats.includes(id)) {
                    this.selectedCats = this.selectedCats.filter(c => c !== id);
                } else {
                    this.selectedCats.push(id);
                }
            },
            selectAll() {
                this.selectedCats = [{{ $categories->pluck('id')->join(',') }}];
            },
            clearAll() {
                this.selectedCats = [];
            }
         }">
        
        <div class="flex items-center justify-between pb-4 border-b border-stone-100 flex-wrap gap-2">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center text-[#4A2C1D] font-bold text-base shadow-2xs">
                    +
                </span>
                <div>
                    <h3 class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                        Create New Filter Group
                    </h3>
                    <p class="text-xs text-stone-500">Define a filter facet and choose which storefront categories will display it.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.attributes.groups.store') }}" method="POST" class="mt-5 space-y-5">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                <!-- Group Name Input -->
                <div class="lg:col-span-4 space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                        Filter Group Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               name="name" 
                               required 
                               placeholder="e.g. Metal Tone, Occasion, Stone Type" 
                               class="w-full text-xs sm:text-sm bg-stone-50/50 border border-stone-300 rounded-xl px-3.5 py-2.5 sm:py-3 focus:bg-white focus:border-[#996E2E] focus:ring-2 focus:ring-[#D4AF6A]/20 transition outline-none text-[#4A2C1D] font-medium placeholder:text-stone-400">
                    </div>
                    <p class="text-[11px] text-stone-400">Unique identifier will automatically generate as slug (e.g. <code>metal-tone</code>).</p>
                </div>

                <!-- Interactive Category Selector (Replaces cramped select box) -->
                <div class="lg:col-span-8 space-y-2">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                            Assign to Categories (Optional)
                            <span class="text-stone-400 font-normal ml-1" x-text="'(' + selectedCats.length + ' selected)'"></span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="selectAll()" class="text-[11px] font-semibold text-[#996E2E] hover:underline cursor-pointer">
                                Select All
                            </button>
                            <span class="text-stone-300">•</span>
                            <button type="button" @click="clearAll()" class="text-[11px] font-semibold text-stone-400 hover:text-stone-600 hover:underline cursor-pointer">
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Search filter for categories if list is long -->
                    <div class="relative mb-2">
                        <input type="text" 
                               x-model="catSearch" 
                               placeholder="Search categories..." 
                               class="w-full text-[11px] bg-stone-50 border border-stone-200 rounded-lg px-3 py-1.5 focus:bg-white focus:border-[#996E2E] outline-none text-stone-700 placeholder:text-stone-400">
                    </div>

                    <!-- Categories Chips Container -->
                    <div class="bg-stone-50/80 border border-stone-200/80 rounded-xl p-3 max-h-36 overflow-y-auto">
                        <div class="flex flex-wrap gap-1.5 sm:gap-2">
                            @foreach($categories as $cat)
                                <div x-show="!catSearch || '{{ strtolower(addslashes($cat->name)) }}'.includes(catSearch.toLowerCase())">
                                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer transition select-none"
                                           :class="selectedCats.includes({{ $cat->id }}) 
                                                    ? 'bg-[#FAF7F0] border-2 border-[#D4AF6A] text-[#4A2C1D] shadow-2xs font-semibold' 
                                                    : 'bg-white border border-stone-200 text-stone-600 hover:border-stone-300 hover:bg-stone-50'">
                                        <input type="checkbox" 
                                               name="categories[]" 
                                               value="{{ $cat->id }}" 
                                               :checked="selectedCats.includes({{ $cat->id }})"
                                               @change="toggleCat({{ $cat->id }})" 
                                               class="hidden">
                                        
                                        <!-- Checkmark Icon -->
                                        <svg x-show="selectedCats.includes({{ $cat->id }})" class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="w-3.5 h-3.5 rounded-full border border-stone-300 flex items-center justify-center shrink-0" x-show="!selectedCats.includes({{ $cat->id }})"></span>
                                        <span>{{ $cat->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Row -->
            <div class="pt-4 border-t border-stone-100 flex justify-end">
                <button type="submit" 
                        class="w-full sm:w-auto px-7 py-3 bg-[#4A2C1D] hover:bg-[#2E180E] text-[#E7C77B] rounded-xl font-bold uppercase tracking-wider text-xs transition duration-200 shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-[#E7C77B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Filter Group
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Existing Filter Groups Grid (Responsive: 1 col on mobile, 2 col on desktop) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-serif-royal text-lg font-bold text-[#4A2C1D] flex items-center gap-2">
                <span>Configured Filter Groups</span>
                <span class="text-xs font-mono font-normal text-stone-500">({{ count($groups) }})</span>
            </h3>
        </div>

        @if(count($groups) === 0)
            <div class="bg-white rounded-3xl border border-stone-200 p-12 text-center shadow-xs">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-[#FAF7F0] border border-[#D4AF6A]/50 flex items-center justify-center text-[#996E2E]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
                <h4 class="font-serif-royal text-base font-bold text-[#4A2C1D]">No Filter Groups Configured</h4>
                <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">Use the form above to create your first attribute group such as Metal Tone, Occasion, or Stone Type.</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
                @foreach($groups as $group)
                    <div class="bg-white rounded-2xl sm:rounded-3xl border border-stone-200/90 shadow-xs hover:shadow-md transition-shadow duration-300 flex flex-col justify-between overflow-hidden"
                         x-data="{
                            catOpen: false,
                            groupCatSearch: '',
                            linkedCats: [{{ $group->categories->pluck('id')->join(',') }}],
                            toggleGroupCat(id) {
                                if (this.linkedCats.includes(id)) {
                                    this.linkedCats = this.linkedCats.filter(c => c !== id);
                                } else {
                                    this.linkedCats.push(id);
                                }
                            },
                            selectAll() {
                                this.linkedCats = [{{ $categories->pluck('id')->join(',') }}];
                            },
                            clearAll() {
                                this.linkedCats = [];
                            }
                         }">
                        
                        <!-- Top Card Header -->
                        <div class="p-5 sm:p-6 pb-4 border-b border-stone-100 bg-[#FAF7F0]/40">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-serif-royal text-base sm:text-lg font-bold text-[#4A2C1D]">
                                            {{ $group->name }}
                                        </h4>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-stone-100 text-stone-500 border border-stone-200">
                                            slug: {{ $group->slug }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 mt-1.5 text-xs text-stone-500">
                                        <span class="flex items-center gap-1 font-medium">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            {{ $group->values->count() }} Options
                                        </span>
                                        <span>•</span>
                                        <span class="font-medium text-[#996E2E]">
                                            {{ $group->categories->count() }} Categories Connected
                                        </span>
                                    </div>
                                </div>

                                <!-- Delete Group Form -->
                                <form action="{{ route('admin.attributes.groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete \'{{ addslashes($group->name) }}\' and all its options?');" class="shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            title="Delete Filter Group"
                                            class="p-2 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition duration-150 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 sm:p-6 space-y-5 flex-1">

                            <!-- Category Linking Panel with Toggle -->
                            <div class="bg-stone-50/70 border border-stone-200/80 rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-stone-700 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        Linked Categories
                                        <span class="text-stone-400 font-normal" x-text="'(' + linkedCats.length + ')'"></span>
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="selectAll()" class="text-[10px] font-semibold text-[#996E2E] hover:underline cursor-pointer">
                                            Select All
                                        </button>
                                        <span class="text-stone-300">•</span>
                                        <button type="button" @click="clearAll()" class="text-[10px] font-semibold text-stone-400 hover:text-stone-600 hover:underline cursor-pointer">
                                            Clear
                                        </button>
                                    </div>
                                </div>

                                <form action="{{ route('admin.attributes.groups.categories', $group->id) }}" method="POST">
                                    @csrf

                                    <!-- Category Chips for this Group -->
                                    <div class="flex flex-wrap gap-1.5 max-h-32 overflow-y-auto pr-1 py-1">
                                        @foreach($categories as $cat)
                                            <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] cursor-pointer transition select-none"
                                                   :class="linkedCats.includes({{ $cat->id }}) 
                                                            ? 'bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] font-bold shadow-2xs' 
                                                            : 'bg-white border border-stone-200 text-stone-500 hover:border-stone-300 hover:bg-stone-50'">
                                                <input type="checkbox" 
                                                       name="categories[]" 
                                                       value="{{ $cat->id }}" 
                                                       :checked="linkedCats.includes({{ $cat->id }})"
                                                       @change="toggleGroupCat({{ $cat->id }})" 
                                                       class="hidden">
                                                
                                                <svg x-show="linkedCats.includes({{ $cat->id }})" class="w-3 h-3 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span class="w-3 h-3 rounded-full border border-stone-300 flex items-center justify-center shrink-0" x-show="!linkedCats.includes({{ $cat->id }})"></span>
                                                <span>{{ $cat->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="mt-3 pt-2.5 border-t border-stone-200/60 flex items-center justify-between">
                                        <span class="text-[10px] text-stone-400">Remember to save after toggling categories.</span>
                                        <button type="submit" 
                                                class="px-3.5 py-1.5 bg-[#FAF7F0] hover:bg-[#D4AF6A] text-[#4A2C1D] hover:text-[#2E180E] border border-[#D4AF6A] rounded-lg font-bold text-[11px] transition shadow-2xs flex items-center gap-1 cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Save Categories
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Filter Values List -->
                            <div class="space-y-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 flex items-center justify-between">
                                    <span>Available Filter Options</span>
                                    <span class="text-[10px] font-mono text-stone-400">{{ $group->values->count() }} Active</span>
                                </label>

                                <div class="flex flex-wrap gap-2 min-h-10 items-center">
                                    @forelse($group->values as $val)
                                        <div class="inline-flex items-center gap-1.5 bg-[#FAF7F0] border border-[#D4AF6A]/60 text-[#4A2C1D] text-xs font-medium pl-3 pr-1.5 py-1 rounded-full shadow-2xs group hover:border-[#996E2E] transition">
                                            <span>{{ $val->value }}</span>
                                            <form action="{{ route('admin.attributes.values.destroy', $val->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove \'{{ addslashes($val->value) }}\'?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        title="Delete option"
                                                        class="w-5 h-5 rounded-full flex items-center justify-center text-stone-400 hover:text-white hover:bg-rose-500 font-bold text-xs transition cursor-pointer">
                                                    ×
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <div class="w-full py-3 px-4 rounded-xl bg-stone-50 border border-dashed border-stone-200 text-center">
                                            <p class="text-xs text-stone-400">No filter values created yet. Add one below.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer: Add New Value Form -->
                        <div class="p-4 sm:p-5 bg-stone-50/90 border-t border-stone-100">
                            <form action="{{ route('admin.attributes.values.store') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="hidden" name="attribute_group_id" value="{{ $group->id }}">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           name="value" 
                                           required 
                                           placeholder="Add new option (e.g. 18K Gold, Rose Gold, Navratri)..." 
                                           class="w-full text-xs bg-white border border-stone-300 rounded-xl px-3.5 py-2.5 focus:border-[#996E2E] focus:ring-2 focus:ring-[#D4AF6A]/20 transition outline-none text-[#4A2C1D] placeholder:text-stone-400">
                                </div>
                                <button type="submit" 
                                        class="px-4 py-2.5 bg-[#4A2C1D] hover:bg-[#2E180E] text-[#E7C77B] font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-1 shrink-0 cursor-pointer">
                                    <span class="text-base leading-none">+</span>
                                    <span>Add</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
