@extends('admin.layouts.admin')

@section('title', "Order #{$order->order_number}")
@section('page_title', "Order #{$order->order_number} Details & Verification")

@section('content')
<div class="space-y-8 max-w-5xl" x-data="{ 
    proofModal: false,
    deliveryProofModal: false,
    refundProofModal: false,
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

        <div class="flex items-center gap-2">
            @if($order->isConfirmed())
                <a href="{{ route('admin.orders.invoice', $order->id) }}" class="px-4 py-2 bg-[#FAF7F0] border border-[#D4AF6A] text-[#4A2C1D] hover:bg-[#D4AF6A] hover:text-[#2E180E] text-xs font-semibold rounded-lg transition flex items-center space-x-1">
                    <span><svg class="w-5 h-5 inline-block text-current shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg></span>
                    <span>Download Tax Invoice (PDF)</span>
                </a>
            @endif

            @if($order->canBeDeleted())
                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Permanently delete Order #{{ $order->order_number }}? All records, items, and proofs will be removed permanently.');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3.5 py-2 bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 hover:border-rose-300 text-xs font-semibold rounded-lg transition inline-flex items-center gap-1.5 cursor-pointer" title="Delete Order Permanently">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Delete Order</span>
                    </button>
                </form>
            @else
                <button type="button" onclick="alert('Order #{{ $order->order_number }} is currently active ({{ $order->status }}). For store integrity, orders can only be deleted once they have been Cancelled or Rejected.');" class="px-3.5 py-2 bg-stone-100 border border-stone-200 text-stone-400 hover:bg-stone-200 hover:text-stone-600 text-xs font-semibold rounded-lg transition inline-flex items-center gap-1.5 cursor-pointer" title="Protected: Order must be Cancelled or Rejected before deletion">
                    <svg class="w-4 h-4 shrink-0 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Protected ({{ $order->status }})</span>
                </button>
            @endif
        </div>
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
                    @if($order->isCod())
                        • <span class="bg-amber-100 text-amber-900 px-2 py-0.5 rounded font-bold border border-amber-300">Cash on Delivery (Advance {{ $order->advance_percentage }}%)</span>
                    @endif
                </p>
                <p class="text-xs text-stone-500 mt-0.5">
                    @if($order->isCod())
                        Verify whether the customer's uploaded advance payment screenshot matches ₹{{ number_format($order->advance_amount) }} ({{ $order->advance_percentage }}% Advance Token) in Rayka's bank account.
                    @else
                        Verify whether the customer's uploaded screenshot matches ₹{{ number_format($order->total_amount) }} in Rayka's bank account.
                    @endif
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
                <div class="bg-white p-4 rounded-xl border border-stone-200 space-y-1.5">
                    @if($order->isCod())
                        <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 space-y-1 text-xs mb-2">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-emerald-800">1. Advance Token ({{ $order->advance_percentage }}%):</span>
                                <span class="font-serif-royal font-bold text-emerald-800 text-sm">₹{{ number_format($order->advance_amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-[#4A2C1D]">2. Balance Due on Delivery:</span>
                                <span class="font-serif-royal font-bold text-[#4A2C1D]">₹{{ number_format($order->balance_amount) }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-1 border-t border-amber-200/70 text-[11px]">
                                <span class="text-stone-600">Total Order Amount:</span>
                                <span class="font-bold text-stone-900 font-serif-royal">₹{{ number_format($order->total_amount) }}</span>
                            </div>
                        </div>
                    @else
                        <p><strong>Expected Payable:</strong> <span class="font-serif-royal text-base font-bold text-[#4A2C1D]">₹{{ number_format($order->total_amount) }}</span></p>
                    @endif
                    <p><strong>Payment Method:</strong> {{ $order->payment?->payment_method ?: ($order->isCod() ? 'Cash on Delivery (Advance Paid Online)' : 'Static QR / UPI') }}</p>
                    <p><strong>Advance Ref / UTR:</strong> <span class="font-mono">{{ $order->payment?->transaction_reference ?: 'Not provided' }}</span></p>
                    @if($order->payment?->verified_at)
                        <p class="text-emerald-700 font-semibold">Advance Verified on: {{ $order->payment->verified_at->format('d M, Y h:i A') }}</p>
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
                            Payment re-approval and dispatch updates are disabled because this order was cancelled by the user.
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
                                    Reject Payment
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
                                            Re-Approve / Confirm Payment
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
                                            Re-Approve / Confirm Payment
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
                                            Reject Payment
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
    @elseif($order->isCancelledByAdmin())
        @php $adminCancelData = $order->getCancellationDetails(); @endphp
        <div class="bg-white rounded-2xl border border-rose-200 p-4 sm:p-6 shadow-xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-rose-200 gap-2">
                <h3 class="font-serif-royal text-base font-bold text-rose-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Dispatch Workflow & Logistics Tracking (Locked)</span>
                </h3>
                <span class="text-[11px] font-bold text-rose-800 bg-rose-100 px-3 py-1 rounded-full border border-rose-300 self-start sm:self-auto">
                    Cancelled by Administrator
                </span>
            </div>
            <div class="p-4 bg-rose-50/70 rounded-xl border border-rose-200 text-xs text-rose-900 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="space-y-1.5">
                    <p class="font-semibold text-rose-950">Dispatch Actions are Disabled</p>
                    <p>This order was cancelled by the administrator. Reason: <span class="font-semibold text-rose-950 bg-white px-2 py-0.5 rounded border border-rose-300 inline-block my-0.5">{{ $adminCancelData['reason'] ?? 'Order cancelled by admin.' }}</span></p>
                    <p class="text-[11px] text-rose-700">Items have been returned to available stock.</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <h3 class="font-serif-royal text-base font-bold text-[#4A2C1D]">
                    Dispatch Workflow & Logistics Tracking
                </h3>
                @if($order->isRejected())
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
                        Cancel Order
                    </button>
                </div>
                @endif
            </form>
        </div>
    @endif

    <!-- REFUND & SETTLEMENT ENGINE (STRICTLY SHOWN ONLY WHEN ORDER IS CANCELLED) -->
    @if($order->isCancelled())
        <div class="bg-gradient-to-r from-amber-50/70 via-white to-amber-50/70 rounded-2xl border-2 border-[#D4AF6A] p-4 sm:p-6 shadow-sm space-y-5"
             x-data="{
                refundScreenshotPreview: null,
                previewRefundFile(e) {
                    let file = e.target.files[0];
                    if (file) {
                        if (file.size > 10 * 1024 * 1024) {
                            alert('Receipt image exceeds 10MB limit.');
                            e.target.value = '';
                            return;
                        }
                        if (file.type.startsWith('image/')) {
                            let reader = new FileReader();
                            reader.onload = (ev) => { this.refundScreenshotPreview = ev.target.result; };
                            reader.readAsDataURL(file);
                        } else {
                            this.refundScreenshotPreview = null;
                        }
                    }
                }
             }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-amber-200 gap-3">
                <div>
                    <span class="bg-[#4A2C1D] text-[#E7C77B] text-[10px] uppercase font-bold tracking-widest px-3 py-1 rounded-full">
                        Cancellation Settlement Engine
                    </span>
                    <h3 class="font-serif-royal text-lg font-bold text-[#4A2C1D] mt-2 flex items-center gap-2">
                        <span>Payment Refund & Settlement (₹{{ number_format($order->getRefundableAmount(), 2) }} {{ $order->isCod() ? 'Advance Token' : 'Total Prepaid' }})</span>
                    </h3>
                    <p class="text-xs text-stone-500 mt-0.5">
                        @if($order->isCod())
                            Customer placed order via Cash on Delivery with {{ $order->advance_percentage }}% advance token online (₹{{ number_format($order->advance_amount, 2) }}). Uncollected balance of ₹{{ number_format($order->balance_amount, 2) }} is voided.
                        @else
                            Customer paid total amount online via UPI (₹{{ number_format($order->total_amount, 2) }}).
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if($order->isRefunded())
                        <span class="text-xs font-bold px-3 py-1.5 rounded-full border bg-emerald-100 text-emerald-800 border-emerald-300">
                            Refund Settled & Completed
                        </span>
                    @elseif($order->isRefundPending())
                        <span class="text-xs font-bold px-3 py-1.5 rounded-full border bg-amber-100 text-amber-800 border-amber-300 animate-pulse">
                            Action Required: Refund Pending
                        </span>
                    @elseif($order->refund_status === 'Not Applicable')
                        <span class="text-xs font-bold px-3 py-1.5 rounded-full border bg-stone-100 text-stone-600 border-stone-300">
                            No Refund Applicable
                        </span>
                    @else
                        <span class="text-xs font-bold px-3 py-1.5 rounded-full border {{ $order->refund_status_badge_class }}">
                            Refund Status: {{ $order->refund_status }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start text-xs">
                <!-- Left: Customer Payment Summary & Direct WhatsApp/Call Actions (5 cols) -->
                <div class="lg:col-span-5 bg-white p-4 rounded-xl border border-stone-200 space-y-4">
                    <div class="flex items-center justify-between border-b pb-2">
                        <span class="font-bold text-stone-800">Refund Target & Customer Details</span>
                        <span class="text-[10px] text-stone-400 font-mono">Order #{{ $order->order_number }}</span>
                    </div>

                    @php
                        $custName = $order->address?->name ?: ($order->user?->name ?: 'Customer');
                        $custPhone = $order->address?->mobile ?: ($order->user?->mobile ?: '');
                        $cleanCustPhone = preg_replace('/[^0-9]/', '', (string)$custPhone);
                        if (strlen($cleanCustPhone) === 10) {
                            $cleanCustPhone = '91' . $cleanCustPhone;
                        }
                        $refundable = $order->getRefundableAmount();
                    @endphp

                    <div class="p-3 bg-stone-50 rounded-xl space-y-2 border border-stone-200/80">
                        <div class="flex items-center justify-between">
                            <span class="text-stone-500">Customer:</span>
                            <strong class="text-stone-900 font-semibold">{{ $custName }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-stone-500">Phone / WhatsApp:</span>
                            <span class="font-mono font-bold text-stone-800">{{ $custPhone ?: 'Not provided' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-stone-500">Payment Channel:</span>
                            <span class="font-bold {{ $order->isCod() ? 'text-amber-800' : 'text-indigo-800' }}">{{ $order->payment_method }}</span>
                        </div>
                        @if($order->isCod())
                            <div class="flex items-center justify-between text-emerald-800 bg-emerald-50/70 px-2 py-1 rounded">
                                <span class="font-semibold">Advance Token Paid:</span>
                                <strong class="font-mono text-sm">₹{{ number_format($order->advance_amount, 2) }}</strong>
                            </div>
                            <div class="flex items-center justify-between text-stone-500 px-2 py-0.5">
                                <span>Voided Delivery Balance:</span>
                                <span class="font-mono line-through">₹{{ number_format($order->balance_amount, 2) }}</span>
                            </div>
                        @else
                            <div class="flex items-center justify-between text-emerald-800 bg-emerald-50/70 px-2 py-1 rounded">
                                <span class="font-semibold">Full Total Paid:</span>
                                <strong class="font-mono text-sm">₹{{ number_format($order->total_amount, 2) }}</strong>
                            </div>
                        @endif

                        @if($order->payment?->transaction_reference)
                            <div class="flex items-center justify-between pt-1 border-t border-stone-200 text-[11px]">
                                <span class="text-stone-500">Customer UTR / Ref:</span>
                                <span class="font-mono font-bold text-stone-700">{{ $order->payment->transaction_reference }}</span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($cleanCustPhone))
                        <div class="flex items-center gap-2">
                            <a href="https://wa.me/{{ $cleanCustPhone }}?text={{ urlencode("Hello {$custName}, this is Rayka Imitation Jewellery regarding your cancelled Order #{$order->order_number}. We are processing your ₹" . number_format($refundable, 2) . " refund. Kindly share your UPI ID (Google Pay / PhonePe / Paytm) to complete the reversal:") }}" target="_blank" class="flex-1 py-2 px-3 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold text-[11px] flex items-center justify-center gap-1.5 transition">
                                <svg class="w-3.5 h-3.5 fill-current shrink-0" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span>Request UPI on WhatsApp</span>
                            </a>
                            <a href="tel:{{ $custPhone }}" class="py-2 px-3 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg font-bold text-[11px] transition">
                                Call Customer
                            </a>
                        </div>
                    @endif

                    @if($order->isRefunded())
                        <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 space-y-1.5">
                            <div class="flex items-center justify-between text-emerald-900 font-bold">
                                <span>Refund Processed</span>
                                <span class="font-mono text-xs">₹{{ number_format($order->refund_amount, 2) }}</span>
                            </div>
                            <div class="text-[11px] text-emerald-800 space-y-0.5">
                                <p><strong>Method:</strong> {{ $order->refund_method ?: 'UPI / Bank Reversal' }}</p>
                                @if($order->refund_transaction_reference)
                                    <p><strong>Bank Ref / UTR:</strong> <span class="font-mono font-bold">{{ $order->refund_transaction_reference }}</span></p>
                                @endif
                                <p><strong>Settled on:</strong> {{ $order->refunded_at?->format('d M, Y \a\t h:i A') ?? 'Recently' }}</p>
                                @if($order->refund_note)
                                    <p class="pt-1 text-stone-600 italic">"{{ $order->refund_note }}"</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($order->refund_screenshot)
                        <div class="pt-2 border-t border-stone-200">
                            <span class="font-bold text-stone-800 block mb-2">Attached Refund Transfer Proof / Slip</span>
                            <div class="p-2 bg-stone-50 rounded-lg border border-stone-200 text-center cursor-pointer group" @click="refundProofModal = true">
                                <img src="{{ asset($order->refund_screenshot) }}" alt="Refund Proof" class="max-h-36 max-w-full mx-auto rounded object-contain group-hover:scale-105 transition">
                                <span class="text-[11px] text-[#996E2E] font-semibold mt-1.5 block underline">
                                    Click to Enlarge Refund Proof
                                </span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right: Admin Refund Action Form (7 cols) -->
                <div class="lg:col-span-7 bg-white p-4 rounded-xl border border-stone-200 space-y-4">
                    <form action="{{ route('admin.orders.update_refund', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-stone-700 mb-1">Refund Settlement Status *</label>
                                <select name="refund_status" class="w-full border rounded-lg p-2.5 bg-stone-50 font-semibold focus:border-[#D4AF6A] focus:ring-1 focus:ring-[#D4AF6A]">
                                    <option value="Pending" {{ $order->refund_status === 'Pending' ? 'selected' : '' }}>Pending (Refund Queued / Awaiting)</option>
                                    <option value="Refunded" {{ $order->refund_status === 'Refunded' ? 'selected' : '' }}>Refunded (Payment Returned to Client)</option>
                                    <option value="Not Applicable" {{ $order->refund_status === 'Not Applicable' ? 'selected' : '' }}>Not Applicable (No Refund Due)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-stone-700 mb-1">Refund Amount (₹) *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-stone-400 font-bold">₹</span>
                                    <input type="number" step="0.01" name="refund_amount" value="{{ $order->refund_amount > 0 ? $order->refund_amount : $order->getRefundableAmount() }}" required class="w-full border rounded-lg pl-7 pr-3 py-2.5 font-bold font-mono text-stone-900 bg-stone-50 focus:border-[#D4AF6A] focus:ring-1 focus:ring-[#D4AF6A]">
                                </div>
                                <span class="text-[10px] text-stone-400 block mt-0.5">
                                    Recommended: ₹{{ number_format($order->getRefundableAmount(), 2) }} ({{ $order->isCod() ? 'Advance Token' : 'Prepaid Total' }})
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-stone-700 mb-1">Refund Method</label>
                                <select name="refund_method" class="w-full border rounded-lg p-2.5 bg-stone-50 text-xs focus:border-[#D4AF6A] focus:ring-1 focus:ring-[#D4AF6A]">
                                    <option value="UPI (Google Pay / PhonePe / Paytm / BHIM)" {{ ($order->refund_method ?? '') === 'UPI (Google Pay / PhonePe / Paytm / BHIM)' ? 'selected' : '' }}>UPI (Google Pay / PhonePe / Paytm / BHIM)</option>
                                    <option value="Direct Bank Transfer (IMPS / NEFT)" {{ ($order->refund_method ?? '') === 'Direct Bank Transfer (IMPS / NEFT)' ? 'selected' : '' }}>Direct Bank Transfer (IMPS / NEFT)</option>
                                    <option value="Cash Return Handover" {{ ($order->refund_method ?? '') === 'Cash Return Handover' ? 'selected' : '' }}>Cash Return Handover</option>
                                    <option value="Store Credit Voucher" {{ ($order->refund_method ?? '') === 'Store Credit Voucher' ? 'selected' : '' }}>Store Credit Voucher</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-stone-700 mb-1">Bank UTR / Transaction Ref</label>
                                <input type="text" name="refund_transaction_reference" value="{{ $order->refund_transaction_reference }}" placeholder="e.g. 428901839210 or BANK-REF-01" class="w-full border rounded-lg p-2.5 font-mono text-xs">
                            </div>
                        </div>

                        <!-- Drag & Drop / File Chooser with Live Preview -->
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Upload Refund Receipt / Screenshot (Optional)</label>
                            <label class="relative flex flex-col items-center justify-center p-3 sm:p-4 border-2 border-dashed border-stone-300 rounded-xl bg-stone-50 hover:bg-stone-100/70 cursor-pointer transition">
                                <input type="file" name="refund_screenshot" accept="image/*,.pdf" @change="previewRefundFile($event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                                
                                <template x-if="refundScreenshotPreview">
                                    <div class="space-y-1.5 text-center">
                                        <img :src="refundScreenshotPreview" class="max-h-24 mx-auto rounded shadow-sm border border-stone-200">
                                        <p class="text-[10px] text-emerald-700 font-bold">New receipt selected (Click or drag to change)</p>
                                    </div>
                                </template>

                                <template x-if="!refundScreenshotPreview">
                                    <div class="space-y-1 text-center">
                                        <svg class="w-6 h-6 mx-auto text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <p class="text-xs font-semibold text-stone-700">Click to upload or drag & drop refund receipt</p>
                                        <p class="text-[10px] text-stone-400">PNG, JPG, WEBP, PDF up to 10MB</p>
                                    </div>
                                </template>
                            </label>
                        </div>

                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Admin Audit Note / Customer Reversal Note</label>
                            <textarea name="refund_note" rows="2" placeholder="e.g. ₹{{ number_format($order->getRefundableAmount(), 2) }} transferred back to customer Google Pay account at mobile {{ $custPhone }}." class="w-full border rounded-lg p-2.5 text-xs">{{ $order->refund_note }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-3 bg-[#4A2C1D] hover:bg-[#2E180E] text-[#E7C77B] rounded-xl font-bold uppercase tracking-wider text-xs transition shadow-sm cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-[#E7C77B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Save Refund Settlement Status</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- 2.5. CASH ON DELIVERY BALANCE SETTLEMENT & DELIVERY RECEIPT CARD (ONLY FOR ACTIVE COD ORDERS) -->
    @if($order->isCod() && !$order->isCancelled() && !$order->isRejected())
        <div class="bg-gradient-to-r from-amber-50/70 via-white to-amber-50/70 rounded-2xl border-2 border-amber-400/80 p-4 sm:p-6 shadow-sm space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-amber-200 gap-3">
                <div>
                    <span class="bg-[#4A2C1D] text-[#E7C77B] text-[10px] uppercase font-bold tracking-widest px-3 py-1 rounded-full">
                        Step 2.5: COD Balance Settlement
                    </span>
                    <h3 class="font-serif-royal text-lg font-bold text-[#4A2C1D] mt-2 flex items-center gap-2">
                        <span>Remaining Balance Collection (₹{{ number_format($order->balance_amount) }})</span>
                    </h3>
                    <p class="text-xs text-stone-500 mt-0.5">
                        Track, collect, or verify courier cash / after-delivery online payment for this order.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold px-3 py-1.5 rounded-full border {{ $order->balance_status_badge_class }}">
                        Status: {{ $order->balance_status }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start text-xs">
                <!-- Left: Delivery Proof or Upload Status (5 cols) -->
                <div class="lg:col-span-5 bg-white p-4 rounded-xl border border-stone-200 space-y-3">
                    <div class="flex items-center justify-between border-b pb-2">
                        <span class="font-bold text-stone-800">Customer Delivery Payment Proof</span>
                        @if($order->delivery_paid_at)
                            <span class="text-[10px] text-emerald-700 font-semibold">{{ $order->delivery_paid_at->format('d M, h:i A') }}</span>
                        @endif
                    </div>

                    @if($order->delivery_payment_screenshot)
                        <div class="text-center">
                            <div class="p-2 bg-stone-50 rounded-lg border border-stone-200 inline-block cursor-pointer group" @click="deliveryProofModal = true">
                                <img src="{{ asset($order->delivery_payment_screenshot) }}" alt="Delivery Proof" class="max-h-44 max-w-full rounded object-contain group-hover:scale-105 transition">
                                <span class="text-[11px] text-[#996E2E] font-semibold mt-1.5 block underline">
                                    Click to Enlarge Proof
                                </span>
                            </div>
                        </div>
                        @if($order->delivery_transaction_reference)
                            <div class="p-2 bg-emerald-50 rounded-lg border border-emerald-200 text-[11px]">
                                <strong>Delivery UTR / Ref:</strong> <span class="font-mono font-bold">{{ $order->delivery_transaction_reference }}</span>
                            </div>
                        @endif
                    @else
                        <div class="p-6 bg-stone-50 rounded-lg text-center text-stone-400 space-y-1">
                            <svg class="w-8 h-8 mx-auto text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="font-medium text-xs text-stone-500">No Delivery Proof Attached Yet</p>
                            <p class="text-[10px] text-stone-400">Customer can upload via tracking link, or admin can mark cash collection below.</p>
                        </div>
                    @endif
                </div>

                <!-- Right: Admin Balance Update Action Form (7 cols) -->
                <div class="lg:col-span-7 bg-white p-4 rounded-xl border border-stone-200 space-y-4"
                     x-data="{
                        balanceProofPreview: null,
                        previewBalanceFile(e) {
                            let file = e.target.files[0];
                            if (file) {
                                if (file.size > 10 * 1024 * 1024) {
                                    alert('Receipt image exceeds 10MB limit.');
                                    e.target.value = '';
                                    this.balanceProofPreview = null;
                                    return;
                                }
                                if (file.type.startsWith('image/')) {
                                    let reader = new FileReader();
                                    reader.onload = (ev) => { this.balanceProofPreview = ev.target.result; };
                                    reader.readAsDataURL(file);
                                } else {
                                    this.balanceProofPreview = null;
                                }
                            }
                        }
                     }">
                    <form action="{{ route('admin.orders.settle_balance', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-stone-700 mb-1">Update Balance Status *</label>
                                <select name="balance_status" class="w-full border rounded-lg p-2.5 bg-stone-50 font-semibold focus:border-[#D4AF6A] focus:ring-1 focus:ring-[#D4AF6A]">
                                    <option value="Pending" {{ $order->balance_status === 'Pending' ? 'selected' : '' }}>Pending (Uncollected)</option>
                                    <option value="Collected Cash" {{ $order->balance_status === 'Collected Cash' ? 'selected' : '' }}>Collected Cash (Courier Collected)</option>
                                    <option value="Paid Online" {{ $order->balance_status === 'Paid Online' ? 'selected' : '' }}>Paid Online (Delivery QR Verified)</option>
                                    <option value="Waived" {{ $order->balance_status === 'Waived' ? 'selected' : '' }}>Waived / Exception</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-stone-700 mb-1">Delivery UTR / Cash Slip Ref</label>
                                <input type="text" name="delivery_transaction_reference" value="{{ $order->delivery_transaction_reference }}" placeholder="e.g. CASH-RECV-01 or UTR 4289..." class="w-full border rounded-lg p-2.5 font-mono">
                            </div>
                        </div>

                        <!-- Drag & Drop Luxury File Chooser -->
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Upload Receipt / Proof (Optional)</label>
                            <label class="relative flex flex-col items-center justify-center p-3 sm:p-4 border-2 border-dashed border-stone-300 rounded-xl bg-stone-50 hover:bg-stone-100/70 cursor-pointer transition">
                                <input type="file" name="delivery_payment_screenshot" accept="image/*,.heic,.heif,.pdf" @change="previewBalanceFile($event)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                                
                                <template x-if="balanceProofPreview">
                                    <div class="space-y-1.5 text-center">
                                        <img :src="balanceProofPreview" class="max-h-24 mx-auto rounded shadow-sm border border-stone-200">
                                        <p class="text-[10px] text-emerald-700 font-bold">New receipt selected (Click or drag to change)</p>
                                    </div>
                                </template>

                                <template x-if="!balanceProofPreview">
                                    <div class="space-y-1 text-center">
                                        <svg class="w-6 h-6 mx-auto text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <p class="text-xs font-semibold text-stone-700">Click to upload or drag & drop proof</p>
                                        <p class="text-[10px] text-stone-400">PNG, JPG, WEBP, PDF up to 10MB</p>
                                    </div>
                                </template>
                            </label>
                        </div>

                        <div>
                            <label class="block font-bold text-stone-700 mb-1">Admin Audit Note (Optional)</label>
                            <input type="text" name="admin_balance_note" placeholder="e.g. Courier agent deposited cash ₹{{ number_format($order->balance_amount) }} in branch." class="w-full border rounded-lg p-2.5">
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-[#4A2C1D] hover:bg-[#2E180E] text-[#E7C77B] rounded-xl font-bold uppercase tracking-wider text-xs transition shadow-sm cursor-pointer flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4 text-[#E7C77B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Save COD Balance Settlement Status</span>
                        </button>
                    </form>
                </div>
            </div>
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
                @if($order->cod_extra_charge > 0)
                    <div class="flex justify-between text-[#996E2E] font-medium">
                        <span>COD Courier Handling Fee:</span>
                        <span>+ ₹{{ number_format($order->cod_extra_charge) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-sm text-[#4A2C1D] pt-2 border-t">
                    <span>Total Order Amount:</span>
                    <span class="font-serif-royal">₹{{ number_format($order->total_amount) }}</span>
                </div>
                @if($order->isCod())
                    <div class="pt-2 border-t border-amber-200/70 space-y-1 text-xs">
                        <div class="flex justify-between text-emerald-800 font-bold">
                            <span>1. Advance Paid ({{ $order->advance_percentage }}%):</span>
                            <span>₹{{ number_format($order->advance_amount) }}</span>
                        </div>
                        <div class="flex justify-between text-[#4A2C1D] font-bold">
                            <span>2. Balance Payable on Delivery:</span>
                            <span>₹{{ number_format($order->balance_amount) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-stone-600 pt-1">
                            <span>Balance Status:</span>
                            <span class="font-bold px-2 py-0.5 rounded-full border {{ $order->balance_status_badge_class }}">{{ $order->balance_status }}</span>
                        </div>
                    </div>
                @endif
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
                    <button type="button" @click="proofModal = false" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-600 hover:text-stone-900 font-bold flex items-center justify-center cursor-pointer transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
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

    <!-- ENLARGED DELIVERY PROOF MODAL -->
    @if($order->isCod() && $order->delivery_payment_screenshot)
    <template x-if="deliveryProofModal">
        <div class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-0 sm:p-4 backdrop-blur-xs overflow-hidden"
             @click.self="deliveryProofModal = false"
             @keydown.escape.window="deliveryProofModal = false">
            <div class="bg-white rounded-none sm:rounded-2xl w-full h-full sm:h-auto sm:max-w-3xl sm:max-h-[92dvh] text-center shadow-2xl relative flex flex-col overflow-hidden">
                <div class="flex justify-between items-center p-3 sm:p-4 border-b border-stone-200 shrink-0 bg-stone-50">
                    <h4 class="font-bold text-stone-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#996E2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        Customer Delivery / Balance Payment Proof
                    </h4>
                    <button type="button" @click="deliveryProofModal = false" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-600 hover:text-stone-900 font-bold flex items-center justify-center cursor-pointer transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-auto bg-stone-100 flex items-center justify-center p-2 sm:p-4 min-h-0">
                    <img src="{{ asset($order->delivery_payment_screenshot) }}" 
                         alt="Delivery Payment Proof" 
                         class="max-w-full max-h-full w-auto h-auto object-contain rounded-lg shadow-md"
                         style="max-height: calc(100dvh - 130px);">
                </div>
                <div class="p-3 sm:p-4 border-t border-stone-200 bg-stone-50 shrink-0 flex items-center justify-between">
                    <span class="text-xs text-stone-600">Balance: <strong>₹{{ number_format($order->balance_amount) }}</strong> • Status: <strong>{{ $order->balance_status }}</strong></span>
                    <button type="button" @click="deliveryProofModal = false" class="px-6 py-2 bg-stone-800 hover:bg-stone-900 text-white rounded-lg text-xs font-semibold cursor-pointer transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </template>
    @endif

    <!-- ENLARGED REFUND PROOF MODAL -->
    @if($order->refund_screenshot)
    <template x-if="refundProofModal">
        <div class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-0 sm:p-4 backdrop-blur-xs overflow-hidden"
             @click.self="refundProofModal = false"
             @keydown.escape.window="refundProofModal = false">
            <div class="bg-white rounded-none sm:rounded-2xl w-full h-full sm:h-auto sm:max-w-3xl sm:max-h-[92dvh] text-center shadow-2xl relative flex flex-col overflow-hidden">
                <div class="flex justify-between items-center p-3 sm:p-4 border-b border-stone-200 shrink-0 bg-stone-50">
                    <h4 class="font-bold text-stone-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Official Bank / UPI Refund Transfer Receipt
                    </h4>
                    <button type="button" @click="refundProofModal = false" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 text-stone-600 hover:text-stone-900 font-bold flex items-center justify-center cursor-pointer transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-auto bg-stone-100 flex items-center justify-center p-2 sm:p-4 min-h-0">
                    <img src="{{ asset($order->refund_screenshot) }}" 
                         alt="Refund Transfer Proof" 
                         class="max-w-full max-h-full w-auto h-auto object-contain rounded-lg shadow-md"
                         style="max-height: calc(100dvh - 130px);">
                </div>
                <div class="p-3 sm:p-4 border-t border-stone-200 bg-stone-50 shrink-0 flex items-center justify-between">
                    <span class="text-xs text-stone-600">Refunded: <strong>₹{{ number_format($order->refund_amount, 2) }}</strong> • Ref: <strong>{{ $order->refund_transaction_reference ?? 'N/A' }}</strong></span>
                    <button type="button" @click="refundProofModal = false" class="px-6 py-2 bg-stone-800 hover:bg-stone-900 text-white rounded-lg text-xs font-semibold cursor-pointer transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </template>
    @endif

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
                    <button type="button" @click="reasonModal = false" class="text-stone-400 hover:text-stone-800 font-bold p-1 cursor-pointer shrink-0 ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
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
