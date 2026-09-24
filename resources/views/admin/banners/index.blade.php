@extends('admin.layouts.admin')

@section('title', 'Hero Banners')
@section('page_title', 'Hero Banner Slider Manager')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <p class="text-xs text-stone-500">Manage, reorder, and activate homepage royal sliders.</p>
        <a href="{{ route('admin.banners.create') }}" class="px-4 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-lg text-xs font-semibold hover:bg-[#2E180E] transition self-start sm:self-auto shrink-0 shadow-xs">
            + Add New Banner
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        
        <!-- 1. MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($banners as $b)
                <div class="p-4 space-y-3 hover:bg-stone-50/60 transition text-xs">
                    <!-- Image Preview with Badge Overlay -->
                    <div class="relative rounded-xl overflow-hidden border border-stone-200 aspect-[16/7] bg-[#240F06]">
                        <img src="{{ asset($b->image_url) }}" alt="{{ $b->title }}" class="w-full h-full object-cover">
                        <div class="absolute top-2 left-2 flex items-center gap-1.5">
                            @if($b->badge_text)
                                <span class="bg-[#261007]/90 text-[#E7C77B] border border-[#D4AF6A]/60 px-2 py-0.5 rounded text-[10px] font-bold">
                                    {{ $b->badge_text }}
                                </span>
                            @endif
                        </div>
                        <div class="absolute top-2 right-2">
                            <form action="{{ route('admin.banners.toggle', $b->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase transition {{ $b->is_active ? 'bg-emerald-600 text-white' : 'bg-stone-800 text-stone-300' }}">
                                    {{ $b->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Title & Subtitle -->
                    <div>
                        <h4 class="font-serif-royal text-sm font-bold text-[#4A2C1D]">{{ $b->title }}</h4>
                        @if($b->subtitle)
                            <span class="text-[11px] text-stone-500 block mt-0.5">{{ $b->subtitle }}</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-stone-500 pt-1 border-t border-stone-100">
                        <span>CTA: <strong class="text-stone-800">{{ $b->button_text }}</strong> ({{ $b->button_link }})</span>
                        <span class="font-mono">Sort: #{{ $b->sort_order }}</span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 pt-2 border-t border-stone-100">
                        <a href="{{ route('admin.banners.edit', $b->id) }}" class="flex-1 py-2 bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] text-center rounded-xl font-bold text-xs transition">
                            Edit Banner
                        </a>
                        <form action="{{ route('admin.banners.destroy', $b->id) }}" method="POST" class="inline shrink-0" onsubmit="return confirm('Delete this banner?');">
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
                    No hero banners created yet.
                </div>
            @endforelse
        </div>

        <!-- 2. DESKTOP TABULAR VIEW (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 uppercase border-b border-stone-200 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4">Preview</th>
                        <th class="p-4">Title & Subtitle</th>
                        <th class="p-4">Badge / CTA</th>
                        <th class="p-4">Order</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($banners as $b)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 w-36">
                                <img src="{{ asset($b->image_url) }}" alt="Banner" class="w-28 h-12 object-cover rounded-lg border">
                            </td>
                            <td class="p-4">
                                <strong class="font-serif-royal text-sm text-[#4A2C1D] block">{{ $b->title }}</strong>
                                <span class="text-stone-500 text-[11px] line-clamp-1">{{ $b->subtitle }}</span>
                            </td>
                            <td class="p-4">
                                <span class="bg-amber-50 text-amber-900 border border-amber-200 px-2 py-0.5 rounded text-[10px] font-semibold">{{ $b->badge_text }}</span>
                                <span class="block text-stone-400 text-[10px] mt-1">{{ $b->button_text }} ({{ $b->button_link }})</span>
                            </td>
                            <td class="p-4 font-mono font-bold whitespace-nowrap">{{ $b->sort_order }}</td>
                            <td class="p-4 whitespace-nowrap">
                                <form action="{{ route('admin.banners.toggle', $b->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $b->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-500' }}">
                                        {{ $b->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.banners.edit', $b->id) }}" class="text-[#996E2E] font-semibold hover:underline">Edit</a>
                                <form action="{{ route('admin.banners.destroy', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this banner?');">
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
