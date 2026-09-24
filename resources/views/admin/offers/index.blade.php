@extends('admin.layouts.admin')

@section('title', 'Category Flash Sales & Timed Offers')
@section('page_title', 'Category Flash Sales & Timed Offers')

@section('content')
<div class="space-y-8" x-data="{
    title: '{{ old('title') }}',
    subtitle: '{{ old('subtitle') }}',
    discount: '{{ old('discount_percentage') }}',
    startsAt: '{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}',
    endsAt: '{{ old('ends_at', now()->addDays(2)->format('Y-m-d\TH:i')) }}',
    badgeText: '{{ old('badge_text') }}',
    isActive: true,
    
    cloneOffer(t, sub, disc, s, e, badge) {
        this.title = t + ' (Clone)';
        this.subtitle = sub;
        this.discount = disc;
        
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        this.startsAt = now.toISOString().slice(0, 16);
        
        let end = new Date();
        end.setDate(end.getDate() + 2);
        end.setMinutes(end.getMinutes() - end.getTimezoneOffset());
        this.endsAt = end.toISOString().slice(0, 16);
        
        this.badgeText = badge;
        this.isActive = true;
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}">

    <!-- Header Description Banner -->
    <div class="p-5 bg-gradient-to-r from-[#4A2C1D] via-[#6B3F2A] to-[#2E180E] text-white rounded-2xl border border-[#D4AF6A]/50 shadow-sm flex items-center justify-between flex-wrap gap-4">
        <div>
            <div class="flex items-center space-x-2.5">
                <span class="text-2xl"><svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.75a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.913-.143z" clip-rule="evenodd" /></svg></span>
                <h2 class="font-serif-royal text-lg sm:text-xl font-bold text-[#E7C77B]">
                    Category Flash Sale & Timed Campaigns
                </h2>
            </div>
            <p class="text-xs text-stone-200 mt-1 max-w-2xl leading-relaxed">
                Schedule limited-time discount offers on any specific category. When active, all products in that category automatically receive the discounted price, dynamic badges, and a live countdown timer on the storefront!
            </p>
        </div>
    </div>

    <!-- Create Offer Form -->
    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs">
        <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D] mb-4 flex items-center space-x-2">
            <span>+</span>
            <span>Launch Timed Category Offer</span>
        </h3>

        <form action="{{ route('admin.offers.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Offer Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Royal Festive Multi-Category Sale" x-model="title" class="w-full border rounded-lg p-2.5">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Subtitle / Tagline</label>
                    <input type="text" name="subtitle" placeholder="e.g. Flat 20% Off!" x-model="subtitle" class="w-full border rounded-lg p-2.5">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Discount Percentage (%) *</label>
                    <div class="relative">
                        <input type="number" step="0.5" min="1" max="99" name="discount_percentage" required placeholder="e.g. 20" x-model="discount" class="w-full border rounded-lg p-2.5 pr-8 font-bold">
                        <span class="absolute right-3 top-2.5 text-stone-400 font-bold">%</span>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Start Date & Time *</label>
                    <input type="datetime-local" name="starts_at" required x-model="startsAt" class="w-full border rounded-lg p-2.5 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">End Date & Time *</label>
                    <input type="datetime-local" name="ends_at" required x-model="endsAt" class="w-full border rounded-lg p-2.5 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Badge Style (Optional)</label>
                    <select name="badge_text" x-model="badgeText" class="w-full border rounded-lg p-2.5">
                        <option value="">Default ([Discount]% FLASH SALE)</option>
                        <option value="FESTIVE SALE">FESTIVE SALE</option>
                        <option value="LIMITED TIME">LIMITED TIME</option>
                        <option value="MEGA DISCOUNT">MEGA DISCOUNT</option>
                        <option value="CLEARANCE">CLEARANCE</option>
                        <option value="WEDDING SPECIAL">WEDDING SPECIAL</option>
                    </select>
                </div>
            </div>

            <!-- Target Categories Multi-Select Box -->
            <div class="bg-[#FAF7F0] p-4 rounded-xl border border-[#D4AF6A]/40" 
                 x-data="{ 
                    selectAll: false,
                    toggleAll() {
                        const boxes = this.$el.querySelectorAll('.cat-checkbox');
                        boxes.forEach(b => b.checked = this.selectAll);
                    }
                 }">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-[#D4AF6A]/30 flex-wrap gap-2">
                    <div>
                        <label class="font-semibold text-stone-800 text-xs flex items-center space-x-1.5">
                            <span><svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 01.359.852L12.982 9.75h7.268a.75.75 0 01.548 1.262l-10.5 11.25a.75.75 0 01-1.272-.71l1.992-7.302H3.75a.75.75 0 01-.548-1.262l10.5-11.25a.75.75 0 01.913-.143z" clip-rule="evenodd" /></svg> Target Categories *</span>
                        </label>
                    </div>
                    <label class="flex items-center space-x-1.5 text-xs font-bold text-[#4A2C1D] cursor-pointer bg-white px-3 py-1 rounded-lg border border-[#D4AF6A]/40 hover:bg-stone-50 shadow-2xs">
                        <input type="checkbox" x-model="selectAll" @change="toggleAll()" class="rounded text-[#996E2E]">
                        <span>Select All</span>
                    </label>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 max-h-48 overflow-y-auto pr-1">
                    @foreach($categories as $cat)
                        <label class="flex items-center space-x-2 p-2 bg-white rounded-lg border border-stone-200 hover:border-[#D4AF6A] cursor-pointer text-xs transition">
                            <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" class="cat-checkbox rounded text-[#996E2E]">
                            <span class="font-medium text-stone-800 truncate">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-wrap sm:flex-nowrap items-center justify-between gap-4 pt-5 border-t border-stone-100">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" x-model="isActive" class="rounded text-[#996E2E]">
                    <span class="font-semibold text-stone-700">Enable Campaign Immediately</span>
                </label>

                <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-lg font-bold uppercase tracking-wider hover:bg-[#2E180E] transition shadow-xs cursor-pointer">
                    Launch Timed Offer
                </button>
            </div>
        </form>
    </div>

    <!-- Active & Scheduled Campaigns Table -->
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-stone-200 flex items-center justify-between bg-[#FAF7F0]">
            <div>
                <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                    All Category Flash Sales & Offers
                </h3>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-white rounded-full border border-[#D4AF6A]/40 text-[#4A2C1D]">
                Total: {{ $offers->total() }} Offers
            </span>
        </div>

        <!-- DESKTOP / TABLET TABULAR VIEW (md and up) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs min-w-[700px]">
                <thead class="bg-stone-50 text-stone-600 uppercase font-semibold tracking-wider border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 whitespace-nowrap">Status</th>
                        <th class="py-3 px-4 whitespace-nowrap min-w-[200px]">Offer Title</th>
                        <th class="py-3 px-4 whitespace-nowrap">Badge</th>
                        <th class="py-3 px-4 whitespace-nowrap">Discount</th>
                        <th class="py-3 px-4 whitespace-nowrap">Duration</th>
                        <th class="py-3 px-4 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($offers as $offer)
                        <tr class="hover:bg-stone-50/60 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if(! $offer->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-stone-100 text-stone-600">Disabled</span>
                                @elseif($offer->is_live)
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span><span>LIVE NOW</span>
                                    </span>
                                @elseif($offer->is_upcoming)
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span><span>Upcoming</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-600">Expired</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <strong class="font-serif-royal text-sm text-[#4A2C1D] block">
                                    {{ $offer->title }}
                                </strong>
                                @if($offer->subtitle)
                                    <span class="text-[11px] text-stone-400 block truncate max-w-xs">{{ $offer->subtitle }}</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($offer->badge_text)
                                    <span class="text-[10px] font-mono text-stone-600 bg-stone-100 px-2 py-0.5 rounded border border-stone-200">
                                        {{ $offer->badge_text }}
                                    </span>
                                @else
                                    <span class="text-stone-300 italic text-[10px]">Default</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-lg">
                                    {{ (int) $offer->discount_percentage }}% OFF
                                </span>
                            </td>

                            <td class="py-3.5 px-4 font-mono text-[11px] text-stone-500 whitespace-nowrap">
                                {{ $offer->starts_at->format('d M y') }} - {{ $offer->ends_at->format('d M y') }}
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" @click="cloneOffer('{{ addslashes($offer->title) }}', '{{ addslashes($offer->subtitle) }}', '{{ $offer->discount_percentage }}', '{{ $offer->starts_at }}', '{{ $offer->ends_at }}', '{{ addslashes($offer->badge_text) }}')" class="px-3 py-1.5 bg-[#FAF7F0] hover:bg-[#D4AF6A] text-[#4A2C1D] hover:text-[#2E180E] border border-[#D4AF6A]/50 rounded-lg font-semibold text-[10px] transition cursor-pointer shadow-xs">
                                        <svg class="w-3.5 h-3.5 inline-block mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                        Reuse
                                    </button>
                                    
                                    <form action="{{ route('admin.offers.toggle', $offer->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 rounded-lg text-[10px] font-bold transition shadow-xs {{ $offer->is_active ? 'bg-amber-100 text-amber-800 hover:bg-amber-200 border border-amber-300' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200 border border-emerald-300' }}">
                                            {{ $offer->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this offer campaign?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg font-semibold text-[10px] transition shadow-xs">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-stone-400">
                                No timed category offers configured yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MOBILE RESPONSIVE CARDS VIEW (Under 768px - No horizontal scroll!) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($offers as $offer)
                <div class="p-4 space-y-3 bg-white hover:bg-stone-50/50 transition">
                    <!-- Top Status & Discount Row -->
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            @if(! $offer->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-stone-100 text-stone-600">Disabled</span>
                            @elseif($offer->is_live)
                                <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span><span>LIVE NOW</span>
                                </span>
                            @elseif($offer->is_upcoming)
                                <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span><span>Upcoming</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-600">Expired</span>
                            @endif
                        </div>

                        <span class="text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-lg shrink-0">
                            {{ (int) $offer->discount_percentage }}% OFF
                        </span>
                    </div>

                    <!-- Title & Tagline -->
                    <div>
                        <h4 class="font-serif-royal text-sm sm:text-base font-bold text-[#4A2C1D] leading-snug">
                            {{ $offer->title }}
                        </h4>
                        @if($offer->subtitle)
                            <p class="text-[11px] text-stone-500 mt-0.5">{{ $offer->subtitle }}</p>
                        @endif
                    </div>

                    <!-- Meta Information (Badge & Dates) -->
                    <div class="flex items-center justify-between text-[11px] text-stone-500 bg-stone-50 rounded-xl p-2.5 border border-stone-200/70 flex-wrap gap-2">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="font-mono text-[10px] sm:text-[11px]">{{ $offer->starts_at->format('d M') }} - {{ $offer->ends_at->format('d M y') }}</span>
                        </div>

                        @if($offer->badge_text)
                            <span class="text-[10px] font-mono text-stone-600 bg-white px-2 py-0.5 rounded border border-stone-200">
                                {{ $offer->badge_text }}
                            </span>
                        @endif
                    </div>

                    <!-- Mobile Action Buttons Row -->
                    <div class="pt-2 flex items-center gap-2">
                        <button type="button" 
                                @click="cloneOffer('{{ addslashes($offer->title) }}', '{{ addslashes($offer->subtitle) }}', '{{ $offer->discount_percentage }}', '{{ $offer->starts_at }}', '{{ $offer->ends_at }}', '{{ addslashes($offer->badge_text) }}')" 
                                class="flex-1 py-2 px-2.5 bg-[#FAF7F0] hover:bg-[#D4AF6A] text-[#4A2C1D] hover:text-[#2E180E] border border-[#D4AF6A]/60 rounded-xl font-bold text-xs transition flex items-center justify-center gap-1 cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                            <span>Reuse</span>
                        </button>
                        
                        <form action="{{ route('admin.offers.toggle', $offer->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" 
                                    class="w-full py-2 px-2.5 rounded-xl text-xs font-bold transition shadow-2xs {{ $offer->is_active ? 'bg-amber-100 text-amber-800 hover:bg-amber-200 border border-amber-300' : 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200 border border-emerald-300' }}">
                                {{ $offer->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST" onsubmit="return confirm('Delete this offer campaign?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="py-2 px-3 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl font-semibold text-xs transition shadow-2xs cursor-pointer"
                                    title="Delete Campaign">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-stone-400 text-xs">
                    No timed category offers configured yet.
                </div>
            @endforelse
        </div>
        @if($offers->hasPages())
            <div class="px-6 py-4 border-t border-stone-200">
                {{ $offers->links('pagination::tailwind') }}
            </div>
        @endif
    </div>

</div>
@endsection
