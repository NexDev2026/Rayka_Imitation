@extends('admin.layouts.admin')

@section('title', 'Mega-Menu Nav Manager')
@section('page_title', 'Mega-Menu Navigation Groupings')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <!-- 1. Header Banner & Quick Guidance -->
    <div class="bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-white rounded-2xl sm:rounded-3xl p-5 sm:p-7 border border-[#D4AF6A]/40 shadow-sm relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 relative z-10">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#E7C77B]/15 border border-[#E7C77B]/30 text-[#E7C77B] text-xs font-semibold uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    Storefront Mega-Menu Engine
                </div>
                <h2 class="font-serif-royal text-xl sm:text-2xl lg:text-3xl font-bold text-[#FAF7F0] tracking-wide">
                    Header Navigation Mega-Menu Manager
                </h2>
                <p class="text-xs sm:text-sm text-stone-200/90 leading-relaxed">
                    Assign which jewellery categories appear under each top-level navbar dropdown (<strong>Men</strong>, <strong>Women</strong>, and <strong>1 Gram Jewellery</strong>). Whenever shoppers hover over these menus on desktop or tap in the mobile menu, their linked categories appear instantly with circular image thumbnails!
                </p>
            </div>

            <!-- Quick Metrics -->
            <div class="grid grid-cols-3 gap-2.5 sm:gap-3 shrink-0">
                @foreach($navGroups as $nav)
                    <div class="bg-black/25 backdrop-blur-xs border border-white/10 rounded-xl sm:rounded-2xl p-3 sm:p-4 text-center">
                        <span class="block text-xl sm:text-2xl font-bold text-[#E7C77B] font-mono">{{ $nav->categories->count() }}</span>
                        <span class="text-[10px] sm:text-xs text-stone-300 uppercase tracking-wider font-medium truncate block">
                            {{ $nav->name }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 2. Nav Menu Cards Grid (3 Columns: Men, Women, 1 Gram Jewellery) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
        @foreach($navGroups as $nav)
            @php
                $assignedIds = $nav->categories->pluck('id')->toArray();
            @endphp
            <div class="bg-white rounded-2xl sm:rounded-3xl border border-stone-200/90 shadow-xs hover:shadow-md transition-shadow duration-300 flex flex-col justify-between overflow-hidden"
                 x-data="{
                    search: '',
                    selected: {{ json_encode($assignedIds) }},
                    toggle(id) {
                        if (this.selected.includes(id)) {
                            this.selected = this.selected.filter(x => x !== id);
                        } else {
                            this.selected.push(id);
                        }
                    },
                    selectAll() {
                        this.selected = [{{ $allCategories->pluck('id')->join(',') }}];
                    },
                    clearAll() {
                        this.selected = [];
                    }
                 }">
                
                <!-- Card Header -->
                <div class="p-5 sm:p-6 pb-4 border-b border-stone-100 bg-[#FAF7F0]/50">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <div class="w-8 h-8 rounded-lg bg-[#FAF7F0] border border-[#D4AF6A]/60 flex items-center justify-center text-[#996E2E] shrink-0 shadow-2xs">
                                    @if(stripos($nav->name, 'men') !== false && stripos($nav->name, 'women') === false)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    @elseif(stripos($nav->name, 'women') !== false)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2 19h20M5 19V9l4.5 4L12 6l2.5 7L19 9v10" /></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 3h12l4 6-10 12L2 9l4-6z M2 9h20 M12 21L8 9 M12 21l4-12 M6 3l2 6 M18 3l-2 6" /></svg>
                                    @endif
                                </div>
                                <h3 class="font-serif-royal text-lg sm:text-xl font-bold text-[#4A2C1D]">
                                    {{ $nav->name }} Menu
                                </h3>
                            </div>
                            <span class="text-[10px] text-stone-400 font-mono block mt-0.5">/collection/{{ $nav->slug }}</span>
                        </div>

                        <span class="text-xs bg-white border border-[#D4AF6A] text-[#996E2E] px-3 py-1 rounded-full font-bold shadow-2xs shrink-0"
                              x-text="selected.length + ' Categories'">
                            {{ count($assignedIds) }} Categories
                        </span>
                    </div>

                    <!-- Search Input & Quick Actions -->
                    <div class="mt-4 space-y-2">
                        <div class="relative">
                            <input type="text" 
                                   x-model="search" 
                                   placeholder="Search categories for {{ $nav->name }}..." 
                                   class="w-full text-xs bg-white border border-stone-200 rounded-xl pl-8 pr-3 py-2 outline-none focus:border-[#996E2E] text-stone-800 placeholder:text-stone-400 transition">
                            <svg class="w-3.5 h-3.5 text-stone-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <div class="flex items-center justify-between text-[11px] pt-1">
                            <span class="text-stone-400 text-[10px]">Select categories to include:</span>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="selectAll()" class="font-bold text-[#996E2E] hover:underline cursor-pointer">Select All</button>
                                <span class="text-stone-300">•</span>
                                <button type="button" @click="clearAll()" class="font-semibold text-stone-400 hover:text-stone-600 hover:underline cursor-pointer">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form & Category List -->
                <form action="{{ route('admin.nav_menu.update', $nav->id) }}" method="POST" class="flex-1 flex flex-col justify-between">
                    @csrf

                    <div class="p-4 sm:p-5 space-y-2 max-h-[420px] overflow-y-auto flex-1">
                        @foreach($allCategories as $category)
                            <div x-show="!search || '{{ strtolower(addslashes($category->name)) }}'.includes(search.toLowerCase())">
                                <label class="flex items-center gap-3 p-2.5 rounded-xl border-2 cursor-pointer transition select-none"
                                       :class="selected.includes({{ $category->id }}) 
                                            ? 'bg-[#FAF7F0] border-[#D4AF6A] shadow-2xs font-semibold' 
                                            : 'bg-white border-stone-200/80 hover:border-stone-300 text-stone-600 hover:bg-stone-50'">
                                    
                                    <input type="checkbox" 
                                           name="categories[]" 
                                           value="{{ $category->id }}"
                                           :checked="selected.includes({{ $category->id }})"
                                           @change="toggle({{ $category->id }})"
                                           class="hidden">

                                    <!-- Checkmark box -->
                                    <div class="w-5 h-5 rounded-md flex items-center justify-center border shrink-0 transition"
                                         :class="selected.includes({{ $category->id }}) 
                                            ? 'bg-[#4A2C1D] border-[#4A2C1D] text-[#E7C77B]' 
                                            : 'border-stone-300 bg-white'">
                                        <svg x-show="selected.includes({{ $category->id }})" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>

                                    <!-- Category thumbnail -->
                                    <img src="{{ asset($category->image ?: 'images/categories/chains.svg') }}" 
                                         alt="{{ $category->name }}" 
                                         class="w-8 h-8 rounded-full object-cover border border-stone-200 shrink-0 bg-stone-100">

                                    <div class="flex-1 min-w-0">
                                        <span class="block text-xs text-[#4A2C1D] truncate leading-tight">
                                            {{ $category->name }}
                                        </span>
                                        <span class="text-[10px] text-stone-400 font-normal">
                                            {{ $category->products_count ?? $category->products()->count() }} items
                                        </span>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <!-- Card Footer Submit -->
                    <div class="p-4 sm:p-5 border-t border-stone-100 bg-stone-50/70">
                        <button type="submit" 
                                class="w-full py-2.5 sm:py-3 bg-[#4A2C1D] hover:bg-[#2E180E] text-[#E7C77B] rounded-xl font-bold uppercase tracking-wider text-xs transition duration-200 shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-[#E7C77B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save {{ $nav->name }} Mega-Menu
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>

</div>
@endsection
