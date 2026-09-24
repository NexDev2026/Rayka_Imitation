@extends('admin.layouts.admin')

@section('title', 'Customer Directory')

@section('page_title', 'Customer Directory')

@section('content')
<div class="space-y-5">

    {{-- ── STATS CARDS ── --}}
    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-5 shadow-sm flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-[#4A2C1D]/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#4A2C1D]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs font-semibold text-stone-400 uppercase tracking-wider truncate">Total Users</p>
                <p class="text-xl sm:text-3xl font-bold text-[#2E180E]">{{ number_format($stats['total']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-5 shadow-sm flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs font-semibold text-stone-400 uppercase tracking-wider truncate">Customers</p>
                <p class="text-xl sm:text-3xl font-bold text-emerald-700">{{ number_format($stats['customers']) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-5 shadow-sm flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] sm:text-xs font-semibold text-stone-400 uppercase tracking-wider truncate">Admins</p>
                <p class="text-xl sm:text-3xl font-bold text-amber-700">{{ number_format($stats['admins']) }}</p>
            </div>
        </div>
    </div>

    {{-- ── SEARCH & FILTER BAR ── --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3 p-4">
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-stone-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email or mobile…"
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-stone-200 text-sm text-stone-700 placeholder-stone-400 focus:outline-none focus:ring-2 focus:ring-[#D4AF6A]/50 focus:border-[#D4AF6A] transition">
            </div>
            <select name="role" class="px-3 py-2.5 rounded-xl border border-stone-200 text-sm text-stone-700 focus:outline-none focus:ring-2 focus:ring-[#D4AF6A]/50 focus:border-[#D4AF6A] transition bg-white cursor-pointer">
                <option value="">All Roles</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="super-admin" {{ request('role') === 'super-admin' ? 'selected' : '' }}>Super Admin</option>
            </select>
            <button type="submit" class="px-5 py-2.5 bg-[#4A2C1D] hover:bg-[#3a2117] text-white text-sm font-semibold rounded-xl transition shrink-0 cursor-pointer">
                Search
            </button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-600 text-sm font-semibold rounded-xl transition text-center shrink-0 cursor-pointer">Clear</a>
            @endif
        </form>
    </div>

    {{-- ── USERS TABLE (Desktop) / CARDS (Mobile) ── --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">

        {{-- Results header --}}
        <div class="px-4 sm:px-6 py-3 border-b border-stone-100 flex items-center justify-between gap-2 flex-wrap">
            <p class="text-xs text-stone-500">
                Showing <span class="font-semibold text-stone-700">{{ $users->firstItem() ?? 0 }}</span>–<span class="font-semibold text-stone-700">{{ $users->lastItem() ?? 0 }}</span>
                of <span class="font-semibold text-stone-700">{{ $users->total() }}</span> users
            </p>
            <span class="text-[10px] bg-stone-100 text-stone-500 px-2.5 py-1 rounded-full font-medium">Page {{ $users->currentPage() }} / {{ $users->lastPage() }}</span>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-stone-50 text-stone-500 text-xs uppercase tracking-wider border-b border-stone-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold">User</th>
                        <th class="text-left px-5 py-3 font-semibold">Contact</th>
                        <th class="text-center px-4 py-3 font-semibold">Role</th>
                        <th class="text-center px-4 py-3 font-semibold">Orders</th>
                        <th class="text-right px-5 py-3 font-semibold">Total Spend</th>
                        <th class="text-center px-4 py-3 font-semibold">Joined</th>
                        <th class="text-center px-4 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-stone-50/60 transition-colors group">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#4A2C1D] to-[#7A4A2A] flex items-center justify-center shrink-0 shadow-sm">
                                    <span class="text-[#E7C77B] font-bold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-stone-800">{{ $user->name }}</p>
                                    <p class="text-xs text-stone-400">#{{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-stone-700 text-xs">{{ $user->email }}</p>
                            @if($user->mobile)
                                <p class="text-xs text-stone-400 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                    {{ $user->mobile }}
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if(in_array($user->role, ['admin', 'super-admin']))
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                    {{ ucfirst($user->role) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Customer
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="font-semibold text-stone-700">{{ $user->orders_count }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <span class="font-bold text-[#4A2C1D]">₹{{ number_format($user->orders_sum_total_amount ?? 0, 2) }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-xs text-stone-500">{{ $user->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-semibold text-[#4A2C1D] bg-[#FAF7F0] hover:bg-[#D4AF6A]/20 border border-[#D4AF6A]/40 rounded-lg transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    View
                                </a>
                                @if(!$user->isAdmin())
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                      onsubmit="return confirm('Permanently delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-[11px] font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-stone-400">
                                <svg class="w-12 h-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                <p class="font-semibold text-sm">No users found</p>
                                <p class="text-xs">Try adjusting your search or filter criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-stone-100">
            @forelse($users as $user)
            <div class="p-4 hover:bg-stone-50/50 transition-colors">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#4A2C1D] to-[#7A4A2A] flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                        <span class="text-[#E7C77B] font-bold text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <p class="font-semibold text-stone-800 text-sm truncate">{{ $user->name }}</p>
                            @if(in_array($user->role, ['admin', 'super-admin']))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 shrink-0">
                                    <svg class="w-3 h-3 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                    {{ ucfirst($user->role) }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Customer</span>
                            @endif
                        </div>
                        <p class="text-xs text-stone-400 truncate mt-0.5">{{ $user->email }}</p>
                        @if($user->mobile)
                            <p class="text-xs text-stone-400 mt-0.5 flex items-center gap-1">
                                <svg class="w-3 h-3 text-stone-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                {{ $user->mobile }}
                            </p>
                        @endif

                        <div class="flex items-center gap-3 mt-2 flex-wrap">
                            <span class="text-xs text-stone-500">
                                <span class="font-semibold text-stone-700">{{ $user->orders_count }}</span> orders
                            </span>
                            <span class="text-xs font-bold text-[#4A2C1D]">₹{{ number_format($user->orders_sum_total_amount ?? 0, 2) }}</span>
                            <span class="text-xs text-stone-400">{{ $user->created_at->format('d M Y') }}</span>
                        </div>

                        <div class="flex items-center gap-2 mt-3">
                            <a href="{{ route('admin.users.show', $user->id) }}"
                               class="flex-1 text-center px-3 py-2 text-xs font-semibold text-[#4A2C1D] bg-[#FAF7F0] hover:bg-[#D4AF6A]/20 border border-[#D4AF6A]/40 rounded-lg transition cursor-pointer">
                                View Profile
                            </a>
                            @if(!$user->isAdmin())
                            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition cursor-pointer">
                                    Delete
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-16 text-center text-stone-400">
                <p class="font-semibold text-sm">No users found</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-stone-100 bg-stone-50/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
