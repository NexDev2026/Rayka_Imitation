@extends('admin.layouts.admin')

@section('title', "Order #{$order->order_number}")
@section('page_title', "Order #{$order->order_number} Details & Verification")

@section('content')
<div class="space-y-8 max-w-5xl" x-data="{ 
    proofModal: false,
    reasonModal: false,
    modalAction: 'reject',
    selectedReason: 'Payment screenshot invalid or illegible',
    customComment: '',
    presetReasons: {
        'reject': [
            'Payment screenshot invalid or illegible',
            'Payment amount mismatch with bank statement',
            'Transaction UTR not found in bank ledger',
            'Duplicate or expired payment proof',
            'Other / Custom Reason'
        ],
        'cancel': [
            'Customer requested cancellation via call or WhatsApp',
            'Creation out of stock or failed quality check',
            'Delivery destination pin code unserviceable',
            'Suspected fraudulent or duplicate booking',
            'Other / Custom Reason'
        ]
    },
    openReasonModal(action) {
        this.modalAction = action;
        this.selectedReason = this.presetReasons[action][0];
        this.customComment = '';
        this.reasonModal = true;
    }
}">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-800">
            ← Back to Orders Queue
        </a>

        @if($order->isConfirmed())
            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="px-4 py-2 bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] text-xs font-semibold rounded-lg transition flex items-center space-x-1">
                <span><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg></span>
                <span>Download Tax Invoice (PDF)</span>
            </a>
        @endif
    </div>

    <!-- 1. STATIC QR CODE PAYMENT SCREENSHOT PROOF VERIFICATION CARD -->
    <div class="bg-gradient-to-r from-[#FAF7F0] via-white to-[#FAF7F0] rounded-2xl border-2 border-[#D4AF6A] p-4 sm:p-8 shadow-md">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between pb-6 border-b border-[#D4AF6A]/40 gap-4">
            <div>
                <span class="bg-[#4A2C1D] text-[#E7C77B] text-[10px] uppercase font-bold tracking-widest px-3 py-1 rounded-full">
                    <svg class="w-5 h-5 inline-block text-current shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M11.7 2.805a.75.75 0 01.6 0A60.65 60.65 0 0122.83 8.72a.75.75 0 01-.231 1.337 49.949 49.949 0 00-9.902 3.912l-.003.002-.34.18a.75.75 0 01-.707 0A50.009 50.009 0 002.5 9.77a.75.75 0 01-.233-1.335A60.61 60.61 0 0111.7 2.805z" /><path d="M13.06 15.473a48.45 48.45 0 017.666-3.282c.134 1.414.22 2.843.251 4.284a.75.75 0 01-.46.711 47.87 47.87 0 00-8.105 4.342.75.75 0 01-.824 0 47.87 47.87 0 00-8.105-4.342.75.75 0 01-.46-.711c.03-1.441.117-2.87.251-4.284a48.5 48.5 0 017.666 3.282c.31.144.664.144.972 0z" /></svg> Step 1: Payment Verification Portal
                </span>
                <h3 class="font-serif-royal text-xl font-bold text-[#4A2C1D] mt-2">
                    Customer UPI Payment Proof Review
                </h3>
                <p class="text-xs text-[#996E2E] font-medium mt-0.5">
                    Order Booked: {{ $order->created_at->format('d M, Y \a\t h:i A') }} IST
                </p>
                <p class="text-xs text-stone-500 mt-0.5">
                    Verify whether the customer's uploaded screenshot matches ₹{{ number_format($order->total_amount) }} in Rayka's bank account.
                </p>
            </div>

            @if($order->isCancelledByCustomer())
                <span class="text-xs font-bold uppercase px-4 py-1.5 rounded-full border bg-stone-100 text-stone-800 border-stone-300">
                    Cancelled by User
                </span>
            @elseif($order->isCancelledByAdmin())
                <span class="text-xs font-bold uppercase px-4 py-1.5 rounded-full border bg-rose-100 text-rose-800 border-rose-300">
                    Cancelled by Admin
                </span>
            @else
                <span class="text-xs font-bold uppercase px-4 py-1.5 rounded-full border {{ $order->status_badge_class }}">
                    {{ $order->status }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 sm:gap-8 mt-6 items-center">
            
            <!-- Screenshot Preview Box (4 cols) -->
            <div class="md:col-span-5 text-center">
                @if($order->payment && $order->payment->screenshot_path)
                    <div class="p-2 bg-white rounded-xl border border-[#D4AF6A] shadow-xs inline-block cursor-pointer group" @click="proofModal = true">
                        <img src="{{ asset($order->payment->screenshot_path) }}" alt="Customer Proof" class="max-h-56 max-w-full rounded-lg object-contain group-hover:scale-105 transition">
                        <span class="text-[11px] text-[#996E2E] font-semibold mt-2 block underline">
                            <svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg> Click to Enlarge / Inspect
                        </span>
                    </div>
                @else
                    <div class="p-8 bg-stone-100 rounded-xl text-stone-400 text-xs">
                        No payment proof uploaded.
                    </div>
                @endif
            </div>

            <!-- Verification Action Form (7 cols) -->
            <div class="md:col-span-7 space-y-4 text-xs">
                <div class="bg-white p-4 rounded-xl border border-stone-200 space-y-1">
                    <p><strong>Expected Payable:</strong> <span class="font-serif-royal text-base font-bold text-[#4A2C1D]">₹{{ number_format($order->total_amount) }}</span></p>
                    <p><strong>Payment Method:</strong> {{ $order->payment?->payment_method ?: 'Static QR / UPI' }}</p>
                    <p><strong>Transaction Ref / UTR:</strong> <span class="font-mono">{{ $order->payment?->transaction_reference ?: 'Not provided' }}</span></p>
                    @if($order->payment?->verified_at)
                        <p class="text-emerald-700 font-semibold">Verified on: {{ $order->payment->verified_at->format('d M, Y h:i A') }}</p>
                    @endif
                </div>

                @if($order->isCancelledByCustomer())
                    @php $cancelData = $order->getCancellationDetails(); @endphp
                    <!-- User Cancelled Locked State -->
                    <div class="p-4 bg-stone-50 rounded-xl border border-stone-300 text-xs space-y-3">
                        <div class="flex items-center justify-between text-stone-800 font-bold">
                            <span class="flex items-center gap-1.5 text-stone-800">
                                <svg class="w-5 h-5 text-stone-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                Order Cancelled by User
                            </span>
                            <span class="text-[10px] text-stone-600 bg-stone-200 font-semibold px-2.5 py-0.5 rounded-full border border-stone-300">User Initiated</span>
                        </div>
                        <div class="text-stone-600 text-[11px] bg-white p-3.5 rounded-lg border border-stone-200 leading-relaxed space-y-2">
                            <div class="p-2.5 bg-rose-50/70 rounded-md border border-rose-200/80">
                                <span class="font-bold text-rose-900 block text-[10px] uppercase tracking-wider mb-0.5">User's Stated Reason:</span>
                                <p class="text-stone-800 font-medium text-xs break-words">
                                    {{ $cancelData['reason'] ?? 'Order cancelled by user.' }}
                                </p>
                            </div>
                            <div class="space-y-1 text-[10.5px] text-stone-500 pt-1">
                                <p><strong>Cancellation Source:</strong> User requested cancellation directly from customer account portal.</p>
                                <p><strong>Inventory Status:</strong> Reserved creations were automatically restored to inventory.</p>
                                @if($order->payment?->admin_note)
                                    <p class="pt-1 border-t border-stone-100 text-[10px]">
                                        <strong>Log Record:</strong> {{ $order->payment->admin_note }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <p class="text-[11px] text-stone-500 italic">
                            ✓ Payment re-approval and dispatch updates are disabled because this order was cancelled by the user.
                        </p>
                    </div>
                @else
                    <!-- One-Click Confirm or Reject -->
                    <form action="{{ route('admin.orders.verify_payment', $order->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block font-semibold text-stone-700 mb-1">Admin Verification Notes (Optional):</label>
                            <input type="text" name="admin_note" value="{{ $order->payment?->admin_note }}" {{ $order->status !== 'Pending Verification' ? 'readonly' : '' }} placeholder="e.g. Transaction confirmed in ICICI account ₹{{ number_format($order->total_amount) }}." class="w-full border rounded-lg p-2 text-xs">
                        </div>

                        @if($order->status === 'Pending Verification')
                            <div class="flex flex-col sm:flex-row items-center gap-2.5 pt-1">
                                <button type="submit" name="action" value="confirm" class="w-full sm:flex-1 py-3 bg-emerald-700 text-white rounded-xl font-bold uppercase tracking-wider hover:bg-emerald-800 transition shadow-sm text-center cursor-pointer">
                                    Confirm Payment (Approve)
                                </button>
                                <button type="button" @click="openReasonModal('reject')" class="w-full sm:flex-1 py-3 bg-rose-700 text-white rounded-xl font-bold uppercase tracking-wider hover:bg-rose-800 transition shadow-sm text-center cursor-pointer">
                                    ✕ Reject Payment
                                </button>
                            </div>
                        @else
                            @if($order->status === 'Rejected' || $order->payment?->status === 'Rejected')
                                <div class="p-3.5 bg-rose-50 rounded-xl border border-rose-200 text-xs space-y-2">
                                    <div class="flex items-center justify-between text-rose-800 font-bold">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            Payment & Order Rejected
                                        </span>
                                        <span class="text-[10px] text-rose-700 bg-rose-100 font-semibold px-2 py-0.5 rounded border border-rose-300">Status: Rejected</span>
                                    </div>
                                    @if($order->payment?->admin_note)
                                        <p class="text-rose-800 text-[11px] bg-white p-2 rounded border border-rose-200">
                                            <strong>Rejection Reason:</strong> {{ $order->payment->admin_note }}
                                        </p>
                                    @endif
                                    <div class="pt-1 flex items-center justify-between gap-2 border-t border-rose-200/60">
                                        <span class="text-[11px] text-stone-500">Need to change decision?</span>
                                        <button type="submit" name="action" value="confirm" class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold text-[11px] transition shadow-xs cursor-pointer">
                                            ✓ Re-Approve / Confirm Payment
                                        </button>
                                    </div>
                                </div>
                            @elseif($order->isCancelledByAdmin())
                                @php $adminCancelData = $order->getCancellationDetails(); @endphp
                                <div class="p-3.5 bg-rose-50 rounded-xl border border-rose-200 text-xs space-y-2">
                                    <div class="flex items-center justify-between text-rose-800 font-bold">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            Order Cancelled by Admin
                                        </span>
                                        <span class="text-[10px] text-rose-700 bg-rose-100 font-semibold px-2 py-0.5 rounded border border-rose-300">Admin Decision</span>
                                    </div>
                                    <p class="text-rose-800 text-[11px] bg-white p-2 rounded border border-rose-200">
                                        <strong>Cancellation Reason:</strong> {{ $adminCancelData['reason'] }}
                                    </p>
                                    <div class="pt-1 flex items-center justify-between gap-2 border-t border-rose-200/60">
                                        <span class="text-[11px] text-stone-500">Need to reinstate or approve?</span>
                                        <button type="submit" name="action" value="confirm" class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold text-[11px] transition shadow-xs cursor-pointer">
                                            ✓ Re-Approve / Confirm Payment
                                        </button>
                                    </div>
                                </div>
                            @elseif($order->isConfirmed() || $order->payment?->status === 'Confirmed')
                                <div class="p-3.5 bg-emerald-50 rounded-xl border border-emerald-200 text-xs space-y-2">
                                    <div class="flex items-center justify-between text-emerald-800 font-bold">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Payment Verified & Approved
                                        </span>
                                        <span class="text-[10px] text-emerald-700 bg-emerald-100 font-semibold px-2 py-0.5 rounded border border-emerald-300">Active</span>
                                    </div>
                                    @if($order->payment?->admin_note)
                                        <p class="text-emerald-800 text-[11px] bg-white p-2 rounded border border-emerald-200">
                                            <strong>Note:</strong> {{ $order->payment->admin_note }}
                                        </p>
                                    @endif
                                    <div class="pt-1 flex items-center justify-between gap-2 border-t border-emerald-200/60">
                                        <span class="text-[11px] text-stone-500">Found an issue with proof?</span>
                                        <button type="submit" name="action" value="reject" onclick="return confirm('Change status to REJECTED? Customer tracking will reflect payment verification failed.');" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold text-[11px] transition shadow-xs cursor-pointer">
                                            ✕ Reject Payment
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-center space-x-2 pt-2 pb-1 text-amber-700 font-bold bg-amber-50 rounded-xl py-3 border border-amber-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    <span>Payment Pending Verification</span>
                                </div>
                            @endif
                        @endif
                    </form>
                @endif
            </div>

        </div>
    </div>

    <!-- 2. SHIPPING & DISPATCH STATUS CONTROLLER -->
    @if($order->isCancelledByCustomer())
        @php $cancelData = $order->getCancellationDetails(); @endphp
        <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-6 shadow-xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-stone-200 gap-2">
                <h3 class="font-serif-royal text-base font-bold text-stone-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Dispatch Workflow & Logistics Tracking (Locked)</span>
                </h3>
                <span class="text-[11px] font-bold text-stone-700 bg-stone-100 px-3 py-1 rounded-full border border-stone-300 self-start sm:self-auto">
                    Cancelled by User
                </span>
            </div>
            <div class="p-4 bg-stone-50 rounded-xl border border-stone-200 text-xs text-stone-600 flex items-start gap-3">
                <svg class="w-5 h-5 text-stone-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="space-y-1.5">
                    <p class="font-semibold text-stone-800">Dispatch Actions are Disabled</p>
                    <p>This order was cancelled by the user. Reason: <span class="font-semibold text-rose-900 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 inline-block my-0.5">{{ $cancelData['reason'] ?? 'Order cancelled by user.' }}</span></p>
                    <p class="text-[11px] text-stone-500">Items have been returned to available stock. In accordance with professional e-commerce SaaS standards, user-cancelled orders cannot be dispatched.</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                    Dispatch Workflow & Logistics Tracking
                </h3>
                @if($order->isCancelledByAdmin())
                    @php $adminCancelData = $order->getCancellationDetails(); @endphp
                    <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200">
                        Status: Cancelled by Admin
                    </span>
                @elseif($order->isRejected())
                    <span class="text-[11px] font-bold text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200">
                        Status: Rejected
                    </span>
                @endif
            </div>

            @if($order->tracking_number && in_array($order->status, ['Processing', 'Shipped', 'Delivered']) && !$order->isCancelled() && !$order->isRejected())
                <div class="p-3 bg-indigo-50/70 rounded-xl border border-indigo-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse"></span>
                        <span class="text-indigo-950 font-bold">Active Logistics:</span>
                        <span class="text-stone-700 font-semibold">{{ $order->tracking_carrier ?: 'Express Courier' }}</span>
                        <span class="font-mono font-bold text-indigo-900 bg-white px-2 py-0.5 rounded border border-indigo-200">{{ $order->tracking_number }}</span>
                    </div>
                    @if($order->tracking_url)
                        <a href="{{ $order->tracking_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-700 hover:text-indigo-950 font-bold underline flex items-center gap-1">
                            <span>Open Live Courier Tracking</span>
                            <span>↗</span>
                        </a>
                    @endif
                </div>
            @endif

            <form action="{{ route('admin.orders.update_status', $order->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ (!in_array($order->status, ['Cancelled', 'Rejected', 'Delivered']) && !$order->isCancelledByCustomer()) ? '5' : '4' }} gap-3 sm:gap-4 text-xs items-end">
                @csrf
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Update Status</label>
                    <select name="status" class="w-full border rounded-lg p-2.5 bg-stone-50 font-semibold focus:border-[#D4AF6A] focus:ring-1 focus:ring-[#D4AF6A]">
                        @if(!in_array($order->status, ['Pending Verification', 'Confirmed', 'Processing', 'Shipped', 'Delivered']))
                            <option value="{{ $order->status }}" selected disabled>{{ $order->status }}</option>
                        @endif
                        @foreach(['Pending Verification', 'Confirmed', 'Processing', 'Shipped', 'Delivered'] as $st)
                            <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Carrier Name</label>
                    <input type="text" name="tracking_carrier" value="{{ $order->tracking_carrier }}" placeholder="e.g. Blue Dart / Delhivery" class="w-full border rounded-lg p-2.5">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Airway Bill / Tracking #</label>
                    <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="e.g. BLUEDART-88291039" class="w-full border rounded-lg p-2.5 font-mono">
                </div>

                <div>
                    <button type="submit" class="w-full py-2.5 bg-[#4A2C1D] text-[#E7C77B] rounded-lg font-bold uppercase tracking-wider hover:bg-[#2E180E] transition cursor-pointer">
                        Update Dispatch
                    </button>
                </div>

                @if(!in_array($order->status, ['Cancelled', 'Rejected', 'Delivered']) && !$order->isCancelledByCustomer())
                <div>
                    <button type="button" @click="openReasonModal('cancel')" class="w-full py-2.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg font-bold uppercase tracking-wider hover:bg-rose-100 transition cursor-pointer">
                        ✕ Cancel Order
                    </button>
                </div>
                @endif
            </form>
        </div>
    @endif

    <!-- 3. ORDER ITEMS & CUSTOMER ADDRESS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 text-xs">
        
        <!-- Ordered Items -->
        <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-6 shadow-xs space-y-3">
            <h4 class="font-serif-royal text-sm font-bold text-[#4A2C1D] pb-2 border-b border-stone-200">
                Ordered Creations ({{ $order->items->count() }})
            </h4>

            <div class="divide-y divide-stone-100">
                @foreach($order->items as $item)
                    <div class="py-2.5 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset($item->product_image ?: 'images/products/p1-a.svg') }}" class="w-10 h-10 rounded object-cover border">
                            <div>
                                <p class="font-bold text-stone-800">{{ $item->product_name }}</p>
                                <p class="text-stone-400 font-mono text-[10px]">{{ $item->product_sku }} | Qty: {{ $item->quantity }}</p>
                            </div>
                        </div>
                        <span class="font-bold text-stone-900">₹{{ number_format($item->subtotal) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-stone-200 space-y-1 text-stone-600">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span>₹{{ number_format($order->subtotal) }}</span>
                </div>
                @if($order->coupon_discount > 0)
                    <div class="flex justify-between text-emerald-700 font-semibold">
                        <span>Coupon Discount ({{ $order->coupon_code }}):</span>
                        <span>− ₹{{ number_format($order->coupon_discount) }}</span>
                    </div>
                @endif
                <div class="flex justify-between">
                    <span>Shipping:</span>
                    <span>{{ $order->shipping_fee == 0 ? 'FREE' : '₹' . number_format($order->shipping_fee) }}</span>
                </div>
                <div class="flex justify-between font-bold text-sm text-[#4A2C1D] pt-2 border-t">
                    <span>Total Amount:</span>
                    <span class="font-serif-royal">₹{{ number_format($order->total_amount) }}</span>
                </div>
            </div>
        </div>

        <!-- Customer Address -->
        <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-6 shadow-xs space-y-3">
            <h4 class="font-serif-royal text-sm font-bold text-[#4A2C1D] pb-2 border-b border-stone-200">
                Customer & Shipping Destination
            </h4>

            <div class="space-y-2">
                <p><strong>Name:</strong> {{ $order->address?->name }}</p>
                <p><strong>Mobile:</strong> <a href="tel:{{ $order->address?->mobile }}" class="text-[#996E2E] font-bold underline">{{ $order->address?->mobile }}</a></p>
                <p><strong>Email:</strong> {{ $order->address?->email }}</p>
                <p><strong>Full Address:</strong> {{ $order->address?->formatted_address }}</p>
                @if($order->notes)
                    <p class="pt-2 text-stone-500 italic bg-[#FAF7F0] p-2.5 rounded border border-[#D4AF6A]/30">
                        "{{ $order->notes }}"
                    </p>
                @endif
            </div>
        </div>

    </div>

    <!-- ENLARGED PROOF INSPECTION MODAL -->
    <template x-if="proofModal">
        <div class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-0 sm:p-4 backdrop-blur-xs overflow-hidden"
             @click.self="proofModal = false"
             @keydown.escape.window="proofModal = false">
            <div class="bg-white rounded-none sm:rounded-2xl w-full h-full sm:h-auto sm:max-w-3xl sm:max-h-[92dvh] text-center shadow-2xl relative flex flex-col overflow-hidden">
                <!-- Header -->
                <div class="flex justify-between items-center p-3 sm:p-4 border-b border-stone-200 shrink-0 bg-stone-50">
                    <h4 class="font-bold text-stone-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Payment Screenshot Proof
                    </h4>
                    <button type="button" @click="proofModal = false" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-600 hover:text-stone-900 font-bold flex items-center justify-center text-lg leading-none cursor-pointer transition">✕</button>
                </div>
                <!-- Image Area: fills remaining space, scrollable if very tall -->
                @if($order->payment && $order->payment->screenshot_path)
                    <div class="flex-1 overflow-auto bg-stone-100 flex items-center justify-center p-2 sm:p-4 min-h-0">
                        <img src="{{ asset($order->payment->screenshot_path) }}" 
                             alt="Payment Screenshot Proof" 
                             class="max-w-full max-h-full w-auto h-auto object-contain rounded-lg shadow-md"
                             style="max-height: calc(100dvh - 130px);">
                    </div>
                @else
                    <div class="flex-1 flex items-center justify-center text-stone-500 text-sm p-8">
                        No screenshot uploaded.
                    </div>
                @endif
                <!-- Footer -->
                <div class="p-3 sm:p-4 border-t border-stone-200 bg-stone-50 shrink-0">
                    <button type="button" @click="proofModal = false" class="w-full sm:w-auto px-6 py-2 bg-stone-800 hover:bg-stone-900 text-white rounded-lg text-xs font-semibold cursor-pointer transition">
                        Close Preview
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- INTERACTIVE REASON MODAL (REJECTION / CANCELLATION) -->
    <template x-if="reasonModal">
        <div class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-2.5 sm:p-4 backdrop-blur-xs overflow-y-auto"
             @click.self="reasonModal = false"
             @keydown.escape.window="reasonModal = false">
            <div class="bg-white rounded-2xl max-w-lg w-full my-auto max-h-[92dvh] sm:max-h-[90vh] flex flex-col shadow-2xl relative border-2 border-[#D4AF6A] text-xs overflow-hidden">
                
                <!-- Modal Header (Pinned) -->
                <div class="flex justify-between items-start p-3.5 sm:p-5 border-b border-stone-200 shrink-0 bg-[#FAF7F0]/50">
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-full bg-rose-100 border border-rose-300 flex items-center justify-center shrink-0 text-rose-700 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm sm:text-base text-[#4A2C1D]" x-text="modalAction === 'reject' ? 'Reject Payment & Order' : 'Cancel Order as Administrator'"></h4>
                            <p class="text-[11px] text-stone-500 mt-0.5">Please specify the official reason. This note will be permanently recorded and emailed to the customer.</p>
                        </div>
                    </div>
                    <button type="button" @click="reasonModal = false" class="text-stone-400 hover:text-stone-800 font-bold p-1 text-lg leading-none cursor-pointer shrink-0 ml-2">✕</button>
                </div>

                <!-- Modal Body with Smooth Inner Scroll -->
                <form :action="modalAction === 'reject' ? '{{ route('admin.orders.verify_payment', $order->id) }}' : '{{ route('admin.orders.update_status', $order->id) }}'" method="POST" class="flex flex-col flex-1 overflow-hidden min-h-0">
                    @csrf
                    <input type="hidden" :name="modalAction === 'reject' ? 'action' : 'status'" :value="modalAction === 'reject' ? 'reject' : 'Cancelled'">

                    <div class="overflow-y-auto p-3.5 sm:p-5 space-y-3 sm:space-y-4 flex-1 overscroll-contain">
                        <div>
                            <label class="block font-bold text-stone-800 mb-1.5">Select Primary Reason:</label>
                            <div class="space-y-1.5 max-h-36 sm:max-h-44 overflow-y-auto pr-1">
                                <template x-for="r in presetReasons[modalAction]" :key="r">
                                    <label class="flex items-start gap-2.5 p-2 rounded-lg border cursor-pointer transition hover:bg-stone-50"
                                           :class="selectedReason === r ? 'border-[#D4AF6A] bg-[#FAF7F0]' : 'border-stone-200'">
                                        <input type="radio" :name="modalAction === 'reject' ? 'rejection_reason' : 'cancellation_reason'" :value="r" x-model="selectedReason" class="mt-0.5 text-[#4A2C1D] focus:ring-[#D4AF6A]">
                                        <span class="font-medium text-stone-800 text-[11.5px] leading-snug" x-text="r"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-stone-800 mb-1">Additional Details / Note (Optional):</label>
                            <textarea :name="modalAction === 'reject' ? 'rejection_comment' : 'cancellation_comment'" x-model="customComment" rows="2" placeholder="e.g. Bank settlement ref #998371 does not reflect credited amount." class="w-full border border-stone-300 rounded-xl p-2.5 text-xs focus:border-[#D4AF6A] focus:ring-1 focus:ring-[#D4AF6A]"></textarea>
                        </div>

                        <div class="p-2.5 sm:p-3 bg-rose-50 rounded-xl border border-rose-200 text-rose-800 text-[10.5px] sm:text-[11px] leading-relaxed">
                            <strong>Customer Email Alert:</strong> An automated, royal-styled notification will be dispatched to the customer's email explaining this decision with your reason note.
                        </div>
                    </div>

                    <!-- Modal Footer (Always Visible & Accessible) -->
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2 p-3 sm:p-4 border-t border-stone-200 bg-stone-50/90 shrink-0">
                        <button type="button" @click="reasonModal = false" class="w-full sm:w-auto px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-semibold rounded-lg text-xs cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-rose-700 hover:bg-rose-800 text-white font-bold rounded-lg text-xs tracking-wider uppercase transition shadow-sm cursor-pointer">
                            Confirm & Send Email Notice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
@endsection
