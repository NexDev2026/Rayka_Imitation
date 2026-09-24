@php /** @var \Illuminate\Pagination\LengthAwarePaginator<\App\Models\Product> $products */ @endphp
@extends('admin.layouts.admin')

@section('title', 'Product Catalog')
@section('page_title', 'Jewellery Catalog & Inventory')

@section('content')
<div class="space-y-6">

    <!-- Filters & Actions Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-xs flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or SKU..." class="border border-stone-300 rounded-lg px-3 py-2 bg-white w-full">
            
            <select name="category" class="border border-stone-300 rounded-lg px-3 py-2 bg-white w-full">
                <option value="">All Categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>

            <select name="type" class="border border-stone-300 rounded-lg px-3 py-2 bg-white w-full">
                <option value="">All Types</option>
                @foreach($types as $t)
                    <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ ucwords(str_replace('-', ' ', $t)) }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-stone-800 text-white rounded-lg font-semibold hover:bg-stone-900 transition">Filter</button>
                @if(request()->hasAny(['search', 'category', 'type']))
                    <a href="{{ route('admin.products.index') }}" class="px-2 py-2 text-rose-600 font-semibold underline text-[11px]">Clear</a>
                @endif
            </div>
        </form>

        <a href="{{ route('admin.products.create') }}" class="px-4 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-lg text-xs font-semibold hover:bg-[#2E180E] transition shrink-0 whitespace-nowrap text-center shadow-xs">
            + Add New Jewellery Piece
        </a>
    </div>

    <!-- Products Container -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        
        <!-- 1. MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($products as $p)
                <div class="p-4 space-y-3 hover:bg-stone-50/60 transition text-xs">
                    <div class="flex items-start gap-3">
                        <img src="{{ asset($p->effective_primary_image) }}" alt="{{ $p->name }}" class="w-16 h-16 rounded-xl object-cover border border-stone-200 bg-[#FAF7F0] shrink-0">
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] text-stone-400 font-medium block uppercase tracking-wider">{{ $p->category?->name }}</span>
                            <h4 class="font-serif-royal text-sm font-bold text-[#4A2C1D] truncate">{{ $p->name }}</h4>
                            <span class="text-stone-500 font-mono text-[11px] block mt-0.5">SKU: {{ $p->sku }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-stone-100">
                        <div>
                            <span class="text-stone-400 text-[10px]">Price:</span>
                            <div class="flex items-baseline gap-1.5">
                                <strong class="text-stone-900 text-sm font-bold">₹{{ number_format($p->price) }}</strong>
                                <span class="text-stone-400 line-through text-[11px]">₹{{ number_format($p->mrp) }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-stone-400 text-[10px] block">Inventory:</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold inline-block {{ $p->stock_quantity <= 10 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $p->stock_quantity }} in stock
                            </span>
                        </div>
                    </div>

                    @if($p->is_featured || $p->is_trending)
                        <div class="flex items-center gap-1.5 pt-1">
                            @if($p->is_featured)
                                <span class="bg-amber-100 text-amber-900 text-[10px] font-bold px-2 py-0.5 rounded-md">★ Featured</span>
                            @endif
                            @if($p->is_trending)
                                <span class="bg-rose-100 text-rose-900 text-[10px] font-bold px-2 py-0.5 rounded-md">🔥 Trending</span>
                            @endif
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-2 border-t border-stone-100">
                        <a href="{{ route('product.show', $p->slug) }}" target="_blank" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-semibold text-xs transition text-center shrink-0">
                            Live View
                        </a>
                        <a href="{{ route('admin.products.edit', $p->id) }}" class="flex-1 py-2 bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] text-center rounded-xl font-bold text-xs transition">
                            Edit Piece
                        </a>
                        <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" class="inline shrink-0" onsubmit="return confirm('Delete this product permanently?');">
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
                    No products found matching query.
                </div>
            @endforelse
        </div>

        <!-- 2. DESKTOP TABULAR VIEW (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 uppercase border-b border-stone-200 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4">Thumb</th>
                        <th class="p-4">Name & Category</th>
                        <th class="p-4">SKU</th>
                        <th class="p-4">Price / MRP</th>
                        <th class="p-4">Live Stock</th>
                        <th class="p-4">Flags</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($products as $p)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 w-16">
                                <img src="{{ asset($p->effective_primary_image) }}" alt="Thumb" class="w-12 h-12 rounded-lg object-cover border bg-[#FAF7F0]">
                            </td>
                            <td class="p-4 max-w-xs">
                                <strong class="font-serif-royal text-sm text-[#4A2C1D] block line-clamp-1">{{ $p->name }}</strong>
                                <span class="text-stone-400 text-[11px]">{{ $p->category?->name }}</span>
                            </td>
                            <td class="p-4 font-mono font-bold text-stone-700 whitespace-nowrap">
                                {{ $p->sku }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <strong class="text-stone-900">₹{{ number_format($p->price) }}</strong>
                                <span class="text-stone-400 line-through block text-[10px]">₹{{ number_format($p->mrp) }}</span>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $p->stock_quantity <= 10 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $p->stock_quantity }} units
                                </span>
                            </td>
                            <td class="p-4 space-x-1 whitespace-nowrap">
                                @if($p->is_featured)
                                    <span class="bg-amber-100 text-amber-900 text-[9px] font-bold px-1.5 py-0.5 rounded">Featured</span>
                                @endif
                                @if($p->is_trending)
                                    <span class="bg-rose-100 text-rose-900 text-[9px] font-bold px-1.5 py-0.5 rounded">Trending</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('product.show', $p->slug) }}" target="_blank" class="text-stone-400 hover:text-stone-700 font-medium">View</a>
                                <a href="{{ route('admin.products.edit', $p->id) }}" class="text-[#996E2E] font-semibold hover:underline">Edit</a>
                                <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 font-semibold hover:underline cursor-pointer">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-stone-400">No products found matching query.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    @if($products instanceof \Illuminate\Contracts\Pagination\Paginator)
        <div>
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection
