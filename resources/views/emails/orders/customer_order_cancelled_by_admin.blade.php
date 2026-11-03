@extends('emails.layouts.master', [
    'emailTitle' => 'Order #' . $order->order_number . ' ' . $actionType . ' — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
    'headerTitle' => 'Order ' . $actionType,
    'headerSubtitle' => 'Update regarding Order #' . $order->order_number
])

@section('content')
  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  
  @if($actionType === 'Rejected')
    <p class="message-text">
      Our accounts desk was unable to verify your payment proof for Order <strong>#{{ $order->order_number }}</strong>. As a result, this order has been marked as <strong>Rejected</strong>.
    </p>
  @else
    <p class="message-text">
      Your Order <strong>#{{ $order->order_number }}</strong> has been cancelled by our store administration.
    </p>
  @endif

  <div class="alert-box alert-danger" style="margin-bottom:18px;">
    <strong>Reason:</strong> {{ $reasonNote }}
  </div>

  <div class="lg-box" style="margin-top:14px; border:1px solid rgba(212, 175, 106, 0.35); border-radius:14px; overflow:hidden;">
    <div style="background:#241610; border-bottom:1px solid rgba(212, 175, 106, 0.25); padding:9px 14px;">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="left" style="color:#E7C77B; font-size:10.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase;">
            Order Overview
          </td>
          <td align="right" style="color:#EF4444; font-size:10.5px; font-weight:800; text-transform:uppercase;">
            {{ $actionType }}
          </td>
        </tr>
      </table>
    </div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">Order Amount</td>
        <td class="val">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
      @if($order->getRefundableAmount() > 0)
      <tr style="border-top:1px dashed rgba(212,175,106,0.3); background:rgba(212,175,106,0.06);">
        <td class="lbl" style="color:#E7C77B; font-weight:800;">Refundable Amount</td>
        <td class="val" style="color:#10B981; font-weight:900; font-family:monospace; font-size:14px;">₹{{ number_format($order->getRefundableAmount(), 2) }}</td>
      </tr>
      <tr>
        <td class="lbl">Refund Status</td>
        <td class="val" style="color:{{ $order->refund_status === 'Refunded' ? '#10B981' : '#F59E0B' }}; font-weight:700;">
          {{ $order->refund_status === 'Refunded' ? 'Refunded & Settled' : ($order->refund_status === 'Not Applicable' ? 'Not Applicable' : 'Queued for Reversal (Within 24–48 hrs)') }}
        </td>
      </tr>
      @endif
    </table>
  </div>

  <div class="btn-wrap" style="margin-top:20px;">
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn">
      View Order Details →
    </a>
  </div>
@endsection
