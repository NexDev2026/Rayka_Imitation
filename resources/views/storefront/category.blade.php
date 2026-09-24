@extends('layouts.storefront')

@section('title', $category->meta_title ?: "{$category->name} — Rayka Imitation Jewellery")
@section('meta_description', $category->meta_description ?: ($category->description ?: "Explore exquisite {$category->name} at Rayka Imitation Jewellery."))

@section('content')
@php
    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator $products
     * @var \App\Models\Category $category
     * @var \Illuminate\Support\Collection $availableTypes
     */
@endphp
<div>

    <!-- 1. CATEGORY HERO BANNER (DYNAMIC BY TYPE) -->
    <div class="relative min-h-[220px] sm:min-h-[300px] bg-[#2E180E] flex items-center justify-center overflow-hidden border-b-2 border-[#D4AF6A]">
        <img src="{{ $activeHero['hero_image'] }}" 
             alt="{{ $activeHero['title'] }}" 
             class="absolute inset-0 w-full h-full object-cover opacity-35">
        
        <div class="relative z-10 text-center px-4 max-w-3xl mx-auto py-10">
            <span class="text-xs uppercase font-bold tracking-[0.3em] text-[#E7C77B]">
                {{ $activeHero['tag'] ?? 'Heritage Collection' }}
            </span>
            <h1 class="font-serif-royal text-3xl sm:text-5xl font-bold text-[#FAF7F0] mt-2 tracking-wide drop-shadow-md">
                {{ $activeHero['title'] }}
            </h1>
            @if(!empty($activeHero['subtitle']))
                <p class="text-xs sm:text-sm text-stone-200 mt-2 font-light max-w-xl mx-auto leading-relaxed">
                    {{ $activeHero['subtitle'] }}
                </p>
            @endif
        </div>
    </div>

    <!-- 2. QUICK TYPE SELECTOR TABS (IF MULTIPLE TYPES EXIST) -->
    @if(isset($availableTypes) && $availableTypes->count() > 1)
        <div class="bg-white border-b border-[#D4AF6A]/30 py-3.5 shadow-2xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar scrollbar-hide pb-1">
                    <span class="text-xs uppercase font-bold text-[#996E2E] tracking-wider shrink-0 mr-2 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 text-[#D4AF6A]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        Type:
                    </span>
                    <a href="{{ route('category.show', $category->slug) }}" 
                       class="shrink-0 px-4 py-1.5 rounded-full text-xs font-semibold transition {{ empty($selectedType) ? 'bg-[#2E180E] text-[#E7C77B] shadow-sm' : 'bg-[#FAF7F0] text-[#4A2C1D] hover:bg-[#D4AF6A]/20 border border-[#D4AF6A]/40' }}">
                        All {{ $category->name }}
                    </a>
                    @foreach($availableTypes as $t)
                        @php
                            $tName = $typeMeta[$t->product_type]['name'] ?? ucwords(str_replace('-', ' ', $t->product_type));
                            $isActive = ($selectedType === $t->product_type);
                        @endphp
                        <a href="{{ route('category.show', [$category->slug, 'type' => $t->product_type]) }}" 
                           class="shrink-0 px-4 py-1.5 rounded-full text-xs font-semibold transition {{ $isActive ? 'bg-[#2E180E] text-[#E7C77B] shadow-sm' : 'bg-[#FAF7F0] text-[#4A2C1D] hover:bg-[#D4AF6A]/20 border border-[#D4AF6A]/40' }}">
                            {{ $tName }} <span class="text-[10px] opacity-75 ml-0.5">({{ $t->count }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- 3. BREADCRUMB & CONTROLS -->
    <div class="bg-[#FAF7F0] border-b border-[#D4AF6A]/30 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center space-x-2 text-stone-600">
                <a href="{{ route('home') }}" class="hover:text-[#996E2E]">Home</a>
                <span>/</span>
                <span>Categories</span>
                <span>/</span>
                <a href="{{ route('category.show', $category->slug) }}" class="hover:text-[#996E2E]">{{ $category->name }}</a>
                @if($selectedType)
                    <span>/</span>
                    <span class="font-semibold text-[#4A2C1D]">{{ $activeHero['name'] }}</span>
                @endif
            </nav>

            <!-- Product Count & Mobile Filter Toggle -->
            <div class="flex items-center space-x-4 w-full sm:w-auto justify-between sm:justify-end">
                <span class="text-stone-500 font-medium">
                    Showing <strong class="text-[#4A2C1D]">{{ $products->total() }}</strong> creations
                </span>

                <!-- Mobile Filter Trigger -->
                <button type="button" 
                        @click="$dispatch('toggle-filter-drawer')"
                        class="lg:hidden inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg border border-[#D4AF6A] bg-white text-[#4A2C1D] font-semibold text-xs">
                    <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span>Filters</span>
                </button>
            </div>

        </div>
    </div>

    <!-- 4. MAIN CONTENT: LOCKED/STICKY FILTER SIDEBAR + SCROLLABLE PRODUCT GRID -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ mobileFilterOpen: false }" @toggle-filter-drawer.window="mobileFilterOpen = !mobileFilterOpen">
        <div class="flex gap-8 items-start">

            <!-- DESKTOP SIDEBAR FILTER PANEL (LOCKED/STICKY ON SCROLL) -->
            <aside class="hidden lg:block w-72 shrink-0 bg-white rounded-2xl p-6 border border-[#D4AF6A]/40 shadow-xs space-y-6 sticky top-24 self-start max-h-[calc(100vh-7rem)] overflow-y-auto">
                
                <div class="flex items-center justify-between pb-3 border-b border-[#D4AF6A]/30">
                    <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] flex items-center space-x-2">
                        <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        <span>Filter Attributes</span>
                    </h3>
                    @if(request()->hasAny(['type', 'attrs', 'min_price', 'max_price', 'sort']))
                        <a href="{{ route('category.show', $category->slug) }}" class="text-[11px] text-rose-700 hover:underline font-semibold">
                            Reset All
                        </a>
                    @endif
                </div>

                <form action="{{ route('category.show', $category->slug) }}" method="GET" id="filterForm">
                    
                    <!-- Hidden preserve sort -->
                    <input type="hidden" name="sort" value="{{ $sort }}">

                    <!-- Type Filter in Sidebar (e.g. Biskit Chain, Big Size Chain) -->
                    @if(isset($availableTypes) && $availableTypes->count() > 1)
                        <div class="border-b border-[#D4AF6A]/20 pb-5 mb-5" x-data="{ open: true }">
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-left font-serif-royal text-sm font-semibold text-[#4A2C1D] mb-3">
                                <span>{{ $category->name }} Type</span>
                                <span x-text="open ? '−' : '+'" class="text-[#D4AF6A] font-bold text-sm"></span>
                            </button>
                            <div x-show="open" class="space-y-2">
                                <label class="flex items-center space-x-2 text-xs text-[#4A2C1D] cursor-pointer hover:text-[#996E2E]">
                                    <input type="radio" 
                                           name="type" 
                                           value="" 
                                           @change="document.getElementById('filterForm').submit()"
                                           {{ empty($selectedType) ? 'checked' : '' }}
                                           class="border-[#D4AF6A] text-[#996E2E] focus:ring-[#D4AF6A]">
                                    <span class="font-medium">All Types</span>
                                </label>
                                @foreach($availableTypes as $t)
                                    @php $tName = $typeMeta[$t->product_type]['name'] ?? ucwords(str_replace('-', ' ', $t->product_type)); @endphp
                                    <label class="flex items-center justify-between text-xs text-[#4A2C1D] cursor-pointer hover:text-[#996E2E]">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" 
                                                   name="type" 
                                                   value="{{ $t->product_type }}" 
                                                   @change="document.getElementById('filterForm').submit()"
                                                   {{ $selectedType === $t->product_type ? 'checked' : '' }}
                                                   class="border-[#D4AF6A] text-[#996E2E] focus:ring-[#D4AF6A]">
                                            <span>{{ $tName }}</span>
                                        </div>
                                        <span class="text-[10px] text-stone-400">({{ $t->count }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @else
                        @if($selectedType)
                            <input type="hidden" name="type" value="{{ $selectedType }}">
                        @endif
                    @endif

                    <!-- Dynamic Attribute Groups & Values -->
                    @foreach($attributeGroups as $group)
                        <div class="border-b border-[#D4AF6A]/20 pb-5 mb-5" x-data="{ open: true }">
                            <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-left font-serif-royal text-sm font-semibold text-[#4A2C1D] mb-3">
                                <span>{{ $group->name }}</span>
                                <span x-text="open ? '−' : '+'" class="text-[#D4AF6A] font-bold text-sm"></span>
                            </button>

                            <div x-show="open" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                @forelse($group->values as $val)
                                    <label class="flex items-center space-x-2 text-xs text-[#4A2C1D] cursor-pointer hover:text-[#996E2E]">
                                        <input type="checkbox" 
                                               name="attrs[]" 
                                               value="{{ $val->id }}"
                                               @change="document.getElementById('filterForm').submit()"
                                               {{ in_array($val->id, request('attrs', [])) ? 'checked' : '' }}
                                               class="rounded-sm border-[#D4AF6A] text-[#996E2E] focus:ring-[#D4AF6A]">
                                        <span class="select-none">{{ $val->value }}</span>
                                    </label>
                                @empty
                                    <p class="text-[11px] text-stone-400">No filter values defined.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach

                    <!-- Price Filter -->
                    <div class="border-b border-[#D4AF6A]/20 pb-5 mb-5">
                        <h4 class="font-serif-royal text-sm font-semibold text-[#4A2C1D] mb-3">
                            Price Range (₹)
                        </h4>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <label class="text-[10px] text-stone-500">Min Price</label>
                                <input type="number" name="min_price" value="{{ request('min_price', $minPrice) }}" min="0" step="50" class="w-full border border-[#D4AF6A]/50 rounded px-2 py-1 text-xs">
                            </div>
                            <div>
                                <label class="text-[10px] text-stone-500">Max Price</label>
                                <input type="number" name="max_price" value="{{ request('max_price', $maxPrice) }}" min="0" step="50" class="w-full border border-[#D4AF6A]/50 rounded px-2 py-1 text-xs">
                            </div>
                        </div>
                        <button type="submit" class="w-full mt-3 py-1.5 bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] text-xs font-semibold rounded-md transition">
                            Apply Price
                        </button>
                    </div>

                </form>

            </aside>

            <!-- MOBILE BOTTOM-SHEET FILTER DRAWER -->
            <div x-show="mobileFilterOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-full"
                 class="fixed inset-0 z-50 bg-black/60 lg:hidden flex flex-col justify-end">
                
                <div class="bg-white rounded-t-3xl p-6 max-h-[85vh] overflow-y-auto w-full shadow-2xl" @click.outside="mobileFilterOpen = false">
                    <div class="flex items-center justify-between pb-4 border-b border-[#D4AF6A]/30 mb-4">
                        <h3 class="font-serif-royal text-lg font-bold text-[#4A2C1D]">Filters</h3>
                        <button @click="mobileFilterOpen = false" class="text-stone-500 font-bold p-1">✕</button>
                    </div>

                    <form action="{{ route('category.show', $category->slug) }}" method="GET">
                        <input type="hidden" name="sort" value="{{ $sort }}">

                        @if(isset($availableTypes) && $availableTypes->count() > 1)
                            <div class="border-b border-[#D4AF6A]/20 pb-4 mb-4">
                                <h4 class="font-serif-royal text-sm font-semibold text-[#4A2C1D] mb-2">{{ $category->name }} Type</h4>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" name="type" value="" {{ empty($selectedType) ? 'checked' : '' }} class="text-[#996E2E]">
                                        <span>All Types</span>
                                    </label>
                                    @foreach($availableTypes as $t)
                                        @php $tName = $typeMeta[$t->product_type]['name'] ?? ucwords(str_replace('-', ' ', $t->product_type)); @endphp
                                        <label class="flex items-center space-x-2">
                                            <input type="radio" name="type" value="{{ $t->product_type }}" {{ $selectedType === $t->product_type ? 'checked' : '' }} class="text-[#996E2E]">
                                            <span>{{ $tName }} ({{ $t->count }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            @if($selectedType)
                                <input type="hidden" name="type" value="{{ $selectedType }}">
                            @endif
                        @endif

                        @foreach($attributeGroups as $group)
                            <div class="border-b border-[#D4AF6A]/20 pb-4 mb-4">
                                <h4 class="font-serif-royal text-sm font-semibold text-[#4A2C1D] mb-2">{{ $group->name }}</h4>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    @foreach($group->values as $val)
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="attrs[]" value="{{ $val->id }}" {{ in_array($val->id, request('attrs', [])) ? 'checked' : '' }} class="rounded text-[#996E2E]">
                                            <span>{{ $val->value }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="pb-4 mb-4">
                            <h4 class="font-serif-royal text-sm font-semibold text-[#4A2C1D] mb-2">Price Bounds</h4>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <input type="number" name="min_price" value="{{ request('min_price', $minPrice) }}" placeholder="Min" class="border rounded p-2">
                                <input type="number" name="max_price" value="{{ request('max_price', $maxPrice) }}" placeholder="Max" class="border rounded p-2">
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 pt-2">
                            <a href="{{ route('category.show', $category->slug) }}" class="w-1/2 py-2.5 text-center border border-stone-300 text-stone-700 rounded-lg text-xs font-semibold">Reset</a>
                            <button type="submit" class="w-1/2 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-lg text-xs font-bold uppercase">Apply</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIGHT: PRODUCT GRID SECTION -->
            <div class="flex-1">
                
                <!-- Sort Dropdown Row -->
                <div class="flex items-center justify-between pb-6 border-b border-[#D4AF6A]/20 mb-6">
                    <!-- Active Filters Tag Pills -->
                    <div class="flex flex-wrap items-center gap-2">
                        @if(request()->filled('attrs'))
                            <span class="text-xs text-stone-500 font-medium">Active Filters:</span>
                            @foreach(request('attrs', []) as $selectedAttrId)
                                @php
                                    $attrVal = \App\Models\AttributeValue::find($selectedAttrId);
                                @endphp
                                @if($attrVal)
                                    <span class="inline-flex items-center space-x-1 bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] text-[11px] px-2.5 py-0.5 rounded-full">
                                        <span>{{ $attrVal->value }}</span>
                                    </span>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <!-- Sort Dropdown Form -->
                    <div class="flex items-center space-x-2 text-xs shrink-0">
                        <label class="text-stone-500 font-medium hidden sm:inline">Sort By:</label>
                        <select onchange="location = this.value" class="bg-white border border-[#D4AF6A]/50 rounded-lg px-3 py-1.5 text-xs text-[#4A2C1D] focus:outline-hidden focus:border-[#D4AF6A] font-medium shadow-2xs">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ $sort === 'newest' ? 'selected' : '' }}>Newest Additions</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ $sort === 'popular' ? 'selected' : '' }}>Popularity</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ $sort === 'price_low' ? 'selected' : '' }}>Price: Low → High</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ $sort === 'price_high' ? 'selected' : '' }}>Price: High → Low</option>
                        </select>
                    </div>
                </div>

                <!-- Product Cards Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                    @forelse($products as $prod)
                        <x-product-card :product="$prod" />
                    @empty
                        <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-[#D4AF6A]/30 p-8">
                            <div class="w-16 h-16 rounded-full bg-[#FAF7F0] border border-[#D4AF6A] flex items-center justify-center mx-auto mb-3 text-[#996E2E]">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <h3 class="font-serif-royal text-lg font-bold text-[#4A2C1D]">No Jewellery Found</h3>
                            <p class="text-xs text-stone-500 mt-1 max-w-sm mx-auto">
                                No creations match the selected filter criteria. Try clearing some attributes or adjusting the price range.
                            </p>
                            <a href="{{ route('category.show', $category->slug) }}" class="inline-block mt-4 text-xs font-bold text-[#996E2E] underline uppercase tracking-wider">
                                Reset All Filters
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination Links -->
                <div class="mt-10">
                    {{ $products->links() }}
                </div>

            </div>

        </div>
    </div>

</div>
@push('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [{
    "@@type": "ListItem",
    "position": 1,
    "name": "Home",
    "item": "{{ route('home') }}"
  },{
    "@@type": "ListItem",
    "position": 2,
    "name": "{{ $category->name }}",
    "item": "{{ route('category.show', $category->slug) }}"
  }
  @if($selectedType)
  ,{
    "@@type": "ListItem",
    "position": 3,
    "name": "{{ $activeHero['name'] }}",
    "item": "{{ route('category.show', [$category->slug, 'type' => $selectedType]) }}"
  }
  @endif
  ]
}
</script>
@endpush
@endsection

