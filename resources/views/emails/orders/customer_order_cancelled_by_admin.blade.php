@extends('emails.layouts.master', [
    'emailTitle' => 'Order #' . $order->order_number . ' ' . $actionType . ' — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
    'headerTitle' => 'Order ' . $actionType . ' Notice',
    'headerSubtitle' => 'Update regarding Order #' . $order->order_number
])

@section('content')
  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  
  @if($actionType === 'Rejected')
    <p class="message-text">
      We regret to inform you that our accounts concierge was unable to verify your payment proof for Order <strong>#{{ $order->order_number }}</strong>. As a result, this order has been marked as <strong>Rejected</strong>.
    </p>
  @else
    <p class="message-text">
      We are writing to notify you that your Order <strong>#{{ $order->order_number }}</strong> has been cancelled by our store administration.
    </p>
  @endif

  <!-- Highlighted Reason Note -->
  <div class="alert-box alert-danger">
    <strong style="display:block; text-transform:uppercase; font-size:11px; letter-spacing:1px; margin-bottom:6px; color:#EF4444;">
      Reason for {{ $actionType }}:
    </strong>
    <p style="font-size:14px; font-weight:700; color:#FFFFFF; line-height:1.6; margin-bottom:0;">
      {{ $reasonNote }}
    </p>
  </div>

  <div class="lg-box">
    <div class="lg-box-header">Order Summary</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">Order Amount</td>
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
        <td class="val" style="color:#EF4444; font-weight:700;">{{ $actionType }}</td>
      </tr>
      @if($order->payment?->transaction_reference)
      <tr>
        <td class="lbl">Transaction Ref</td>
        <td class="val font-mono">{{ $order->payment->transaction_reference }}</td>
      </tr>
      @endif
    </table>
  </div>

  <p class="message-text" style="font-size:13px;">
    <strong>What happens next?</strong><br>
    If your money was debited or if you have any questions regarding this decision, please reply directly to this email or contact our support concierge with your transaction UTR number. We will assist you promptly.
  </p>

  <div class="btn-wrap">
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn">View Order Details</a>
  </div>
@endsection
