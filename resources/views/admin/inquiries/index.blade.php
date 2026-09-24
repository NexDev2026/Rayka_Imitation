@extends('admin.layouts.admin')

@section('title', 'Customer Inquiries')
@section('page_title', 'Customer Inquiries & Bridal Requests')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
        
        <!-- 1. MOBILE RESPONSIVE CARDS VIEW (md:hidden) -->
        <div class="block md:hidden divide-y divide-stone-100">
            @forelse($inquiries as $inq)
                <div class="p-4 space-y-3 hover:bg-stone-50/60 transition text-xs">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <strong class="text-stone-900 block font-semibold text-sm">{{ $inq->name }}</strong>
                            <span class="text-stone-400 text-[10px]">{{ $inq->created_at->format('d M, Y h:i A') }}</span>
                        </div>
                        <form action="{{ route('admin.inquiries.update', $inq->id) }}" method="POST">
                            @csrf
                            <select name="status" onchange="this.form.submit()" class="border rounded-lg px-2.5 py-1 text-[11px] font-bold {{ $inq->status === 'new' ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-emerald-100 text-emerald-900 border-emerald-300' }}">
                                <option value="new" {{ $inq->status === 'new' ? 'selected' : '' }}>New</option>
                                <option value="replied" {{ $inq->status === 'replied' ? 'selected' : '' }}>Replied</option>
                            </select>
                        </form>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 pt-1 border-t border-stone-100 text-[11px]">
                        <div>
                            <span class="text-stone-400 text-[10px] block">Email:</span>
                            <a href="mailto:{{ $inq->email }}" class="text-[#996E2E] hover:underline font-medium break-all">{{ $inq->email }}</a>
                        </div>
                        <div>
                            <span class="text-stone-400 text-[10px] block">Mobile:</span>
                            @if($inq->mobile)
                                <a href="tel:{{ $inq->mobile }}" class="text-stone-800 font-mono">{{ $inq->mobile }}</a>
                            @else
                                <span class="text-stone-400 italic">Not provided</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-stone-50 p-3 rounded-xl border border-stone-100 space-y-1">
                        <strong class="text-[#4A2C1D] block text-xs">{{ $inq->subject ?: 'General Inquiry' }}</strong>
                        <p class="text-stone-600 leading-relaxed text-[11px]">{{ $inq->message }}</p>
                        @if($inq->admin_notes)
                            <div class="pt-1.5 border-t border-stone-200/60 text-[10px] text-stone-500 italic">
                                <strong>Admin Note:</strong> {{ $inq->admin_notes }}
                            </div>
                        @endif
                    </div>

                    <div class="pt-1 border-t border-stone-100 text-right">
                        <form action="{{ route('admin.inquiries.destroy', $inq->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this inquiry?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl font-semibold text-xs transition">
                                Delete Inquiry
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-stone-400 text-xs">
                    No customer messages received yet.
                </div>
            @endforelse
        </div>

        <!-- 2. DESKTOP TABULAR VIEW (hidden md:block) -->
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 uppercase border-b border-stone-200 font-semibold tracking-wider">
                    <tr>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Subject & Message</th>
                        <th class="p-4">Date</th>
                        <th class="p-4">Status & Notes</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($inquiries as $inq)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 font-bold text-stone-900 whitespace-nowrap">
                                {{ $inq->name }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <a href="mailto:{{ $inq->email }}" class="text-[#996E2E] hover:underline block">{{ $inq->email }}</a>
                                <span class="text-stone-500 font-mono">{{ $inq->mobile ?: '—' }}</span>
                            </td>
                            <td class="p-4 max-w-md">
                                <strong class="text-[#4A2C1D] block mb-0.5">{{ $inq->subject ?: 'General Inquiry' }}</strong>
                                <p class="text-stone-600 leading-relaxed">{{ $inq->message }}</p>
                            </td>
                            <td class="p-4 text-stone-400 whitespace-nowrap">
                                {{ $inq->created_at->format('d M, Y') }}
                            </td>
                            <td class="p-4 whitespace-nowrap">
                                <form action="{{ route('admin.inquiries.update', $inq->id) }}" method="POST" class="space-y-1.5">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 text-[11px] font-semibold {{ $inq->status === 'new' ? 'bg-amber-100 text-amber-900 border-amber-300' : 'bg-emerald-100 text-emerald-900 border-emerald-300' }}">
                                        <option value="new" {{ $inq->status === 'new' ? 'selected' : '' }}>New</option>
                                        <option value="replied" {{ $inq->status === 'replied' ? 'selected' : '' }}>Replied</option>
                                    </select>
                                    @if($inq->admin_notes)
                                        <p class="text-[10px] text-stone-500 italic">Notes: {{ $inq->admin_notes }}</p>
                                    @endif
                                </form>
                            </td>
                            <td class="p-4 text-right whitespace-nowrap">
                                <form action="{{ route('admin.inquiries.destroy', $inq->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this inquiry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 font-semibold hover:underline cursor-pointer">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-400">No customer messages received yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <div>
        {{ $inquiries->links() }}
    </div>

</div>
@endsection
