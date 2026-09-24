@extends('admin.layouts.admin')

@section('title', 'Coupons & Offers')
@section('page_title', 'Coupons & Royal Discount Codes')

@section('content')
<div class="space-y-8">

    <!-- Create Coupon Form -->
    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs">
        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] mb-4">
            + Create New Promo Code
        </h3>

        <form action="{{ route('admin.coupons.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            @csrf

            <div>
                <label class="block font-semibold text-stone-700 mb-1">Coupon Code *</label>
                <input type="text" name="code" required placeholder="e.g. FESTIVE25" class="w-full border rounded-lg p-2.5 uppercase font-mono font-bold">
            </div>

            <div>
                <label class="block font-semibold text-stone-700 mb-1">Discount Type *</label>
                <select name="type" class="w-full border rounded-lg p-2.5">
                    <option value="percentage">Percentage (%)</option>
                    <option value="flat">Flat Amount (₹)</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-stone-700 mb-1">Discount Value *</label>
                <input type="number" step="0.01" name="value" required placeholder="e.g. 15 for 15% or 200 for ₹200" class="w-full border rounded-lg p-2.5">
            </div>

            <div>
                <label class="block font-semibold text-stone-700 mb-1">Min Order Value (₹)</label>
                <input type="number" step="0.01" name="min_order_value" placeholder="e.g. 999" class="w-full border rounded-lg p-2.5">
            </div>

            <div>
                <label class="block font-semibold text-stone-700 mb-1">Max Cap Discount (₹)</label>
                <input type="number" step="0.01" name="max_discount" placeholder="e.g. 500 (for % type)" class="w-full border rounded-lg p-2.5">
            </div>

            <div>
                <label class="block font-semibold text-stone-700 mb-1">Usage Limit Count</label>
                <input type="number" name="usage_limit" placeholder="e.g. 500 orders" class="w-full border rounded-lg p-2.5">
            </div>

            <div>
                <label class="block font-semibold text-stone-700 mb-1">Expiry Date</label>
                <input type="date" name="expires_at" class="w-full border rounded-lg p-2.5">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-lg font-bold uppercase tracking-wider hover:bg-[#2E180E] transition cursor-pointer">
                    Create Coupon
                </button>
            </div>
        </form>
    </div>

    <!-- Coupons Container -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        
        <!-- 1. MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($coupons as $c)
                <div class="p-4 space-y-3 hover:bg-stone-50/60 transition text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono font-bold text-base text-[#4A2C1D] bg-[#FAF7F0] border border-[#D4AF6A]/50 px-3 py-1 rounded-lg">
                            {{ $c->code }}
                        </span>
                        <form action="{{ route('admin.coupons.toggle', $c->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded-full text-[10px] font-bold uppercase transition {{ $c->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-500' }}">
                                {{ $c->is_active ? 'Active' : 'Disabled' }}
                            </button>
                        </form>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1 border-t border-stone-100">
                        <div>
                            <span class="text-stone-400 text-[10px] block">Discount:</span>
                            <strong class="text-stone-900 text-sm">
                                {{ $c->type === 'percentage' ? $c->value . '%' : '₹' . number_format($c->value) }}
                            </strong>
                            @if($c->max_discount)
                                <span class="text-stone-400 block text-[10px]">(Max: ₹{{ number_format($c->max_discount) }})</span>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="text-stone-400 text-[10px] block">Min Order:</span>
                            <span class="font-semibold text-stone-800">₹{{ number_format($c->min_order_value) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 border-t border-stone-100 text-[11px]">
                        <span class="text-stone-500">Usage: <strong>{{ $c->used_count }}</strong>{{ $c->usage_limit ? ' / ' . $c->usage_limit : '' }}</span>
                        <span class="text-stone-400">{{ $c->expires_at ? 'Expires ' . $c->expires_at->format('d M, Y') : 'Lifetime Validity' }}</span>
                    </div>

                    <div class="pt-2 border-t border-stone-100 text-right">
                        <form action="{{ route('admin.coupons.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this coupon?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl font-semibold text-xs transition">
                                Delete Coupon
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-stone-400 text-xs">
                    No promo coupons created yet.
                </div>
            @endforelse
        </div>

        <!-- 2. DESKTOP TABULAR VIEW (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 uppercase border-b border-stone-200 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4">Code</th>
                        <th class="p-4">Discount</th>
                        <th class="p-4">Min Order</th>
                        <th class="p-4">Usage Count</th>
                        <th class="p-4">Expiry</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($coupons as $c)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 font-mono font-bold text-[#4A2C1D] whitespace-nowrap">
                                {{ $c->code }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                {{ $c->type === 'percentage' ? $c->value . '%' : '₹' . number_format($c->value) }}
                                @if($c->max_discount)
                                    <span class="text-stone-400 block text-[10px]">(Max: ₹{{ number_format($c->max_discount) }})</span>
                                @endif
                            </td>
                            <td class="p-4 whitespace-nowrap">₹{{ number_format($c->min_order_value) }}</td>
                            <td class="p-4 whitespace-nowrap">
                                <strong>{{ $c->used_count }}</strong>
                                @if($c->usage_limit) / {{ $c->usage_limit }} @endif
                            </td>
                            <td class="p-4 text-stone-500 whitespace-nowrap">
                                {{ $c->expires_at ? $c->expires_at->format('d M, Y') : 'Lifetime' }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <form action="{{ route('admin.coupons.toggle', $c->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $c->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-500' }}">
                                        {{ $c->is_active ? 'Active' : 'Disabled' }}
                                    </button>
                                </form>
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <form action="{{ route('admin.coupons.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete coupon?');">
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

