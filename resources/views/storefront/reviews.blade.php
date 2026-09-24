@extends('layouts.storefront')

@section('title', 'Verified Patron Reviews — Rayka Imitation Jewellery')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

    <div class="text-center max-w-xl mx-auto">
        <span class="text-xs uppercase font-bold tracking-[0.25em] text-[#996E2E]">Royal Testimonials</span>
        <h1 class="font-serif-royal text-3xl font-bold text-[#4A2C1D] mt-1">
            Site-Wide Patron Reviews
        </h1>
        <div class="rangoli-divider">
            <img src="{{ asset('images/motifs/rangoli-mandala.svg') }}" class="w-6 h-6 inline-block" alt="Rangoli">
        </div>
        <p class="text-xs text-stone-600">
            Read honest impressions from patrons across India who celebrate life's moments adorned in Rayka.
        </p>

        <!-- Rating Stat Box -->
        <div class="mt-6 inline-flex items-center space-x-4 bg-white px-6 py-3 rounded-full border border-[#D4AF6A]/40 shadow-xs">
            <div class="flex items-center space-x-1 text-[#F0B429] text-base">
                <svg class="w-5 h-5 text-[#F0B429]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-[#F0B429]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-[#F0B429]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-[#F0B429]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                <svg class="w-5 h-5 text-[#F0B429]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
            </div>
            <span class="font-bold text-base text-[#4A2C1D]">{{ number_format($averageRating, 1) }} / 5.0</span>
            <span class="text-stone-300">|</span>
            <span class="text-xs text-stone-500 font-medium">{{ $totalReviews }} Verified Reviews</span>
        </div>
    </div>

    <!-- Reviews Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($reviews as $rev)
            <div class="royal-card p-6 rounded-2xl bg-white border border-[#D4AF6A]/30 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center text-[#F0B429] text-xs">
                            @for($i = 0; $i < $rev->rating; $i++)
                                <span><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg></span>
                            @endfor
                        </div>
                        @if($rev->is_verified_purchase)
                            <span class="text-[10px] bg-emerald-50 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded-full font-semibold flex items-center gap-1">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Verified Buyer
                            </span>
                        @endif
                    </div>

                    @if($rev->product)
                        <a href="{{ route('product.show', $rev->product->slug) }}" class="text-[11px] font-bold text-[#996E2E] hover:underline block line-clamp-1">
                            Regarding: {{ $rev->product->name }}
                        </a>
                    @endif

                    @if($rev->title)
                        <h4 class="font-serif-royal text-base font-bold text-[#4A2C1D]">"{{ $rev->title }}"</h4>
                    @endif

                    <p class="text-xs text-stone-600 leading-relaxed italic">
                        "{{ $rev->comment }}"
                    </p>
                </div>

                <div class="pt-4 border-t border-[#D4AF6A]/20 mt-4 flex items-center justify-between text-xs">
                    <span class="font-bold text-[#4A2C1D]">{{ $rev->customer_name }}</span>
                    <span class="text-stone-400 text-[10px]">{{ $rev->created_at->format('d M, Y') }}</span>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-stone-500 text-xs">
                No approved reviews published yet.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $reviews->links() }}
    </div>

</div>
@endsection

