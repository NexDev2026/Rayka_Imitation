@extends('admin.layouts.admin')

@section('title', 'Reviews Moderation')
@section('page_title', 'Customer Reviews Moderation Queue')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <p class="text-xs text-stone-500">
            Only approved customer reviews appear on the storefront. Review pending submissions below.
        </p>

        <div class="flex flex-wrap items-center gap-2 text-xs">
            <a href="{{ route('admin.reviews.index') }}" class="px-3 py-1.5 rounded-lg {{ $status === 'all' ? 'bg-[#4A2C1D] text-[#E7C77B] font-bold' : 'bg-white border' }}">
                All Reviews
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-lg {{ $status === 'pending' ? 'bg-amber-600 text-white font-bold' : 'bg-white text-amber-900 border border-amber-300' }}">
                Pending ({{ $pendingCount }})
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-lg {{ $status === 'approved' ? 'bg-emerald-700 text-white font-bold' : 'bg-white border' }}">
                Approved
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        
        <!-- 1. MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($reviews as $rev)
                <div class="p-4 space-y-3 hover:bg-stone-50/60 transition text-xs">
                    <!-- Customer & Status -->
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <strong class="text-stone-900 block font-semibold text-sm">{{ $rev->customer_name }}</strong>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-stone-400 text-[10px]">{{ $rev->created_at->format('d M, Y') }}</span>
                                @if($rev->is_verified_purchase)
                                    <span class="text-[9px] text-emerald-800 bg-emerald-100 px-1.5 py-0.2 rounded font-semibold">Verified</span>
                                @endif
                            </div>
                        </div>

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase shrink-0 {{ $rev->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($rev->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                            {{ $rev->status }}
                        </span>
                    </div>

                    <!-- Product & Rating -->
                    <div class="pt-1 border-t border-stone-100 flex items-center justify-between gap-2">
                        <a href="{{ route('product.show', $rev->product->slug) }}" target="_blank" class="font-serif-royal font-bold text-[#4A2C1D] hover:underline truncate flex-1">
                            {{ $rev->product?->name }}
                        </a>
                        <div class="text-[#F0B429] text-xs shrink-0 flex items-center">
                            @for($i = 0; $i < $rev->rating; $i++) ★ @endfor
                            <span class="text-stone-400 text-[10px] ml-1">({{ $rev->rating }}/5)</span>
                        </div>
                    </div>

                    <!-- Review Text -->
                    <div class="bg-stone-50 p-2.5 rounded-xl border border-stone-100 space-y-1">
                        @if($rev->title)
                            <strong class="text-stone-800 block text-xs">"{{ $rev->title }}"</strong>
                        @endif
                        <p class="text-stone-600 italic leading-relaxed text-[11px]">{{ $rev->comment }}</p>
                    </div>

                    <!-- Moderation Actions -->
                    <div class="flex items-center gap-2 pt-2 border-t border-stone-100">
                        @if($rev->status !== 'approved')
                            <form action="{{ route('admin.reviews.update_status', $rev->id) }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="w-full py-2 bg-emerald-700 text-white rounded-xl text-xs font-bold hover:bg-emerald-800 transition shadow-2xs">
                                    ✓ Approve
                                </button>
                            </form>
                        @endif

                        @if($rev->status !== 'rejected')
                            <form action="{{ route('admin.reviews.update_status', $rev->id) }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="w-full py-2 bg-stone-200 text-stone-700 rounded-xl text-xs font-bold hover:bg-stone-300 transition">
                                    Reject
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete review?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl font-bold text-xs transition">
                                ✕
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-stone-400 text-xs">
                    No reviews found in queue.
                </div>
            @endforelse
        </div>

        <!-- 2. DESKTOP TABULAR VIEW (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 uppercase border-b border-stone-200 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Jewellery Product</th>
                        <th class="p-4">Rating</th>
                        <th class="p-4">Title & Comments</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Moderation Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($reviews as $rev)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 whitespace-nowrap">
                                <strong class="text-stone-900 block">{{ $rev->customer_name }}</strong>
                                <span class="text-stone-400 text-[10px]">{{ $rev->created_at->format('d M, Y') }}</span>
                                @if($rev->is_verified_purchase)
                                    <span class="text-[9px] text-emerald-800 bg-emerald-100 px-1.5 py-0.2 rounded font-semibold block mt-0.5">Verified</span>
                                @endif
                            </td>
                            <td class="p-4 max-w-xs">
                                <a href="{{ route('product.show', $rev->product->slug) }}" target="_blank" class="font-serif-royal font-bold text-[#4A2C1D] hover:underline line-clamp-1">
                                    {{ $rev->product?->name }}
                                </a>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="text-[#F0B429] text-sm">
                                    @for($i = 0; $i < $rev->rating; $i++) ★ @endfor
                                </span>
                                <span class="text-stone-400 text-[10px] block">({{ $rev->rating }}/5)</span>
                            </td>
                            <td class="p-4 max-w-md">
                                @if($rev->title)
                                    <strong class="text-stone-800 block mb-0.5">"{{ $rev->title }}"</strong>
                                @endif
                                <p class="text-stone-600 italic leading-relaxed">{{ $rev->comment }}</p>
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $rev->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($rev->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $rev->status }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                                @if($rev->status !== 'approved')
                                    <form action="{{ route('admin.reviews.update_status', $rev->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-700 text-white rounded text-[11px] font-bold hover:bg-emerald-800">
                                            Approve
                                        </button>
                                    </form>
                                @endif

                                @if($rev->status !== 'rejected')
                                    <form action="{{ route('admin.reviews.update_status', $rev->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-2.5 py-1 bg-stone-200 text-stone-700 rounded text-[11px] font-bold hover:bg-stone-300">
                                            Reject
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete review?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 font-bold ml-1 hover:underline">✕</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-400">No reviews found in queue.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $reviews->links() }}
    </div>

</div>
@endsection

