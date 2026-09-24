@extends('admin.layouts.admin')

@section('title', 'Categories')
@section('page_title', 'Category Manager')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <p class="text-xs text-stone-500">Manage jewellery categories, homepage circular tiles, and landing hero banners.</p>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-lg text-xs font-semibold hover:bg-[#2E180E] transition self-start sm:self-auto shrink-0 shadow-xs">
            + Add New Category
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        
        <!-- 1. MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($categories as $cat)
                <div class="p-4 space-y-3 hover:bg-stone-50/60 transition text-xs">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset($cat->image ?: 'images/categories/chains.svg') }}" alt="{{ $cat->name }}" class="w-12 h-12 rounded-full object-cover border border-stone-200 p-0.5 bg-[#FAF7F0] shrink-0">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-serif-royal text-sm font-bold text-[#4A2C1D] truncate">{{ $cat->name }}</h4>
                            <span class="text-stone-400 font-mono text-[10px] block truncate">/categories/{{ $cat->slug }}</span>
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                @forelse($cat->navGroups as $ng)
                                    <span class="inline-flex items-center gap-1 bg-[#FAF7F0] border border-[#D4AF6A]/70 text-[#4A2C1D] text-[9.5px] font-bold px-2 py-0.5 rounded-md shadow-2xs">
                                        @if(stripos($ng->name, 'men') !== false && stripos($ng->name, 'women') === false)
                                            <svg class="w-2.5 h-2.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        @elseif(stripos($ng->name, 'women') !== false)
                                            <svg class="w-2.5 h-2.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 19h20M5 19V9l4.5 4L12 6l2.5 7L19 9v10" /></svg>
                                        @else
                                            <svg class="w-2.5 h-2.5 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 3h12l4 6-10 12L2 9l4-6z" /></svg>
                                        @endif
                                        <span>{{ $ng->name }}</span>
                                    </span>
                                @empty
                                    <span class="text-stone-400 italic text-[10px]">No Nav Menu</span>
                                @endforelse
                            </div>
                        </div>
                        <span class="bg-stone-100 text-stone-700 px-2.5 py-1 rounded-full font-bold text-[10px] shrink-0">
                            {{ $cat->products_count }} items
                        </span>
                    </div>

                    @if($cat->hero_title || $cat->hero_subtitle)
                        <div class="bg-stone-50 p-2.5 rounded-xl border border-stone-100 space-y-0.5">
                            <span class="text-[10px] font-bold text-stone-600 block">{{ $cat->hero_title ?: $cat->name }}</span>
                            @if($cat->hero_subtitle)
                                <p class="text-stone-500 text-[11px] line-clamp-1">{{ $cat->hero_subtitle }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-xs pt-1">
                        <span class="text-stone-400 text-[11px]">Display Order: <strong class="text-stone-800 font-mono">{{ $cat->sort_order }}</strong></span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 pt-2 border-t border-stone-100">
                        <a href="{{ route('category.show', $cat->slug) }}" target="_blank" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-semibold text-xs transition text-center shrink-0">
                            View
                        </a>
                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="flex-1 py-2 bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] text-center rounded-xl font-bold text-xs transition">
                            Edit Category
                        </a>
                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline shrink-0" onsubmit="return confirm('Delete category? All associated products will be deleted.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl font-semibold text-xs transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-stone-400 text-xs">
                    No categories found.
                </div>
            @endforelse
        </div>

        <!-- 2. DESKTOP TABULAR VIEW (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 uppercase border-b border-stone-200 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4">Icon / Tile</th>
                        <th class="p-4">Name & Slug</th>
                        <th class="p-4">Mega-Menu Placement</th>
                        <th class="p-4">Hero Title & Subtitle</th>
                        <th class="p-4">Products</th>
                        <th class="p-4">Order</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($categories as $cat)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 w-20">
                                <img src="{{ asset($cat->image ?: 'images/categories/chains.svg') }}" alt="Icon" class="w-12 h-12 rounded-full object-cover border p-0.5 bg-[#FAF7F0]">
                            </td>
                            <td class="p-4">
                                <strong class="font-serif-royal text-sm text-[#4A2C1D] block">{{ $cat->name }}</strong>
                                <span class="text-stone-400 font-mono text-[11px]">/categories/{{ $cat->slug }}</span>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @forelse($cat->navGroups as $ng)
                                        <span class="inline-flex items-center gap-1.5 bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] text-[10px] font-bold px-2.5 py-1 rounded-full shadow-2xs whitespace-nowrap">
                                            @if(stripos($ng->name, 'men') !== false && stripos($ng->name, 'women') === false)
                                                <svg class="w-3 h-3 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            @elseif(stripos($ng->name, 'women') !== false)
                                                <svg class="w-3 h-3 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 19h20M5 19V9l4.5 4L12 6l2.5 7L19 9v10" /></svg>
                                            @else
                                                <svg class="w-3 h-3 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 3h12l4 6-10 12L2 9l4-6z" /></svg>
                                            @endif
                                            <span>{{ $ng->name }}</span>
                                        </span>
                                    @empty
                                        <span class="text-stone-400 italic text-[11px]">None</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="p-4 max-w-xs">
                                <p class="font-semibold text-stone-800">{{ $cat->hero_title ?: $cat->name }}</p>
                                <p class="text-stone-500 text-[11px] line-clamp-1">{{ $cat->hero_subtitle }}</p>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="bg-stone-100 text-stone-700 px-2.5 py-1 rounded-full font-bold text-[11px]">
                                    {{ $cat->products_count }} items
                                </span>
                            </td>
                            <td class="p-4 font-mono font-bold whitespace-nowrap">{{ $cat->sort_order }}</td>
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('category.show', $cat->slug) }}" target="_blank" class="text-stone-500 hover:text-stone-800 font-medium">View</a>
                                <a href="{{ route('admin.categories.edit', $cat->id) }}" class="text-[#996E2E] font-semibold hover:underline">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete category? All associated products will be deleted.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 font-semibold hover:underline cursor-pointer">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
