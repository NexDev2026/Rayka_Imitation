@extends('emails.layouts.master', [
    'emailTitle' => 'Cancellation Confirmation: Order #' . $order->order_number . ' — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
    'headerTitle' => 'Order Cancellation Confirmed',
    'headerSubtitle' => 'Order #' . $order->order_number . ' has been cancelled'
])

@section('content')
  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  <p class="message-text">
    As per your request, your Order <strong>#{{ $order->order_number }}</strong> has been successfully cancelled. The reserved jewelry pieces have been removed from your booking.
  </p>

  <div class="alert-box alert-warning">
    <strong>Cancellation Reason:</strong><br>
    {{ $reason }}
  </div>

  <div class="lg-box">
    <div class="lg-box-header">Cancelled Order Summary</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">Total Order Value</td>
        <td class="val">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
      @if($order->isCod())
      <tr>
        <td class="lbl">Advance Token Paid Online</td>
        <td class="val" style="color:#10B981; font-weight:700;">₹{{ number_format($order->advance_amount, 2) }} ({{ $order->advance_percentage }}%)</td>
      </tr>
      <tr>
        <td class="lbl">Delivery Balance</td>
        <td class="val" style="color:#A8988B; text-decoration:line-through;">₹{{ number_format($order->balance_amount, 2) }} (Voided)</td>
      </tr>
      @endif
      @if($order->getRefundableAmount() > 0)
      <tr style="border-top:1px dashed rgba(212,175,106,0.3); background:rgba(212,175,106,0.06);">
        <td class="lbl" style="color:#E7C77B; font-weight:800;">Refundable Amount</td>
        <td class="val" style="color:#E7C77B; font-weight:900; font-family:monospace; font-size:15px;">₹{{ number_format($order->getRefundableAmount(), 2) }}</td>
      </tr>
      <tr>
        <td class="lbl">Refund Status</td>
        <td class="val" style="color:{{ $order->refund_status === 'Refunded' ? '#10B981' : '#F59E0B' }}; font-weight:700;">
          {{ $order->refund_status === 'Refunded' ? 'Refunded & Settled' : ($order->refund_status === 'Not Applicable' ? 'Not Applicable' : 'Queued for Reversal (Within 24–48 hrs)') }}
        </td>
      </tr>
      @endif
      <tr>
        <td class="lbl">Status</td>
        <td class="val" style="color:#EF4444; font-weight:700;">Cancelled</td>
      </tr>
    </table>
  </div>

  @if($order->getRefundableAmount() > 0)
    <div class="alert-box alert-warning">
      <strong>Refund Reassurance:</strong> Our finance desk has recorded your payment of <strong>₹{{ number_format($order->getRefundableAmount(), 2) }}</strong> for reversal. We will return this amount to your UPI or bank account within 24–48 business hours and update the official bank UTR reference.
    </div>
  @endif

  <div class="btn-wrap">
    <a href="{{ route('home') }}" class="cta-btn">Browse New Collections</a>
  </div>
@endsection
