@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col items-center justify-center my-8 select-none">
        
        {{-- Mobile View (< 640px) : Compact Royal Bar (Never Wraps or Breaks) --}}
        <div class="sm:hidden w-full max-w-sm mx-auto px-2">
            <div class="flex items-center justify-between p-2 rounded-2xl bg-white border border-[#D4AF6A]/50 shadow-sm">
                {{-- Previous Link --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center h-9 px-3 rounded-xl text-stone-300 bg-stone-50 border border-stone-200 cursor-not-allowed text-xs font-medium space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Prev</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center h-9 px-3 rounded-xl text-[#4A2C1D] bg-[#FAF7F0] border border-[#D4AF6A]/40 hover:bg-[#D4AF6A] hover:text-[#2E180E] active:scale-95 transition text-xs font-bold shadow-2xs space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Prev</span>
                    </a>
                @endif

                {{-- Center Page Counter Pill --}}
                <div class="flex items-center space-x-1.5 text-xs">
                    <span class="text-stone-500 font-medium">Page</span>
                    <span class="inline-flex items-center justify-center min-w-[28px] h-7 px-2 rounded-lg bg-gradient-to-br from-[#4A2C1D] to-[#2E180E] text-[#E7C77B] font-bold shadow-2xs border border-[#D4AF6A]/50 text-xs">
                        {{ $paginator->currentPage() }}
                    </span>
                    <span class="text-stone-400">/</span>
                    <span class="text-stone-700 font-semibold">{{ $paginator->lastPage() }}</span>
                </div>

                {{-- Next Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center h-9 px-3 rounded-xl text-[#4A2C1D] bg-[#FAF7F0] border border-[#D4AF6A]/40 hover:bg-[#D4AF6A] hover:text-[#2E180E] active:scale-95 transition text-xs font-bold shadow-2xs space-x-1">
                        <span>Next</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center h-9 px-3 rounded-xl text-stone-300 bg-stone-50 border border-stone-200 cursor-not-allowed text-xs font-medium space-x-1">
                        <span>Next</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                @endif
            </div>

            {{-- Quick Jump Shortcuts for Long Pages --}}
            @if($paginator->lastPage() > 2)
                <div class="flex items-center justify-center space-x-2 mt-2.5 text-[11px]">
                    @if($paginator->currentPage() > 2)
                        <a href="{{ $paginator->url(1) }}" class="inline-flex items-center space-x-1 px-3 py-1 rounded-full bg-white border border-[#D4AF6A]/40 text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] shadow-2xs transition font-semibold">
                            <span>« First Page</span>
                        </a>
                    @endif
                    @if($paginator->currentPage() < $paginator->lastPage() - 1)
                        <a href="{{ $paginator->url($paginator->lastPage()) }}" class="inline-flex items-center space-x-1 px-3 py-1 rounded-full bg-white border border-[#D4AF6A]/40 text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] shadow-2xs transition font-semibold">
                            <span>Last Page ({{ $paginator->lastPage() }}) »</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Tablet & Desktop View (>= 640px) : Full Numbered Royal Strip --}}
        <div class="hidden sm:flex items-center space-x-1 sm:space-x-1.5 p-1.5 rounded-2xl bg-white border border-[#D4AF6A]/50 shadow-sm flex-nowrap overflow-x-auto max-w-full">
            
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-stone-300 bg-stone-50 border border-stone-200 cursor-not-allowed text-xs shrink-0" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-[#4A2C1D] bg-[#FAF7F0] border border-[#D4AF6A]/40 hover:bg-[#D4AF6A] hover:text-[#2E180E] transition text-xs font-bold shadow-2xs shrink-0" aria-label="@lang('pagination.previous')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-8 h-9 text-stone-400 text-xs font-semibold select-none shrink-0">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-[#4A2C1D] to-[#2E180E] text-[#E7C77B] border border-[#D4AF6A] text-xs font-bold shadow-xs shrink-0">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-[#4A2C1D] bg-[#FAF7F0] border border-[#D4AF6A]/30 hover:bg-[#D4AF6A] hover:text-[#2E180E] hover:border-[#D4AF6A] transition text-xs font-medium shrink-0" aria-label="@lang('pagination.goto_page', ['page' => $page])">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-[#4A2C1D] bg-[#FAF7F0] border border-[#D4AF6A]/40 hover:bg-[#D4AF6A] hover:text-[#2E180E] transition text-xs font-bold shadow-2xs shrink-0" aria-label="@lang('pagination.next')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-stone-300 bg-stone-50 border border-stone-200 cursor-not-allowed text-xs shrink-0" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif

        </div>
    </nav>
@endif
