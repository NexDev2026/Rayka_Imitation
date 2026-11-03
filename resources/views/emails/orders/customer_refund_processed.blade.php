@extends('emails.layouts.master', [
    'emailTitle' => 'Refund Processed — #' . $order->order_number . ' — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    'headerTitle' => 'Refund Processed',
    'headerSubtitle' => 'Refund Settled for Order #' . $order->order_number
])

@section('content')
  <!-- Compact Refund Hero Card -->
  <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(212, 175, 106, 0.12) 100%); border: 1.5px solid #10B981; border-radius: 16px; padding: 18px 20px; text-align: center; margin-bottom: 22px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
    <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: #10B981; font-weight: 800; margin-bottom: 4px;">Refund Amount Credited</div>
    <div style="font-family: monospace; font-size: 30px; font-weight: 900; color: #FFFFFF; line-height: 1.1; margin-bottom: 6px;">
      ₹{{ number_format((float)($order->refund_amount > 0 ? $order->refund_amount : $order->getRefundableAmount()), 2) }}
    </div>
    <div style="font-size: 11px; color: #A7F3D0; font-weight: 600;">
      Status: <span style="background: rgba(16, 185, 129, 0.25); color: #34D399; padding: 2px 10px; border-radius: 9999px; font-weight: 800; border: 1px solid #10B981;">Completed & Returned</span>
    </div>
  </div>

  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  <p class="message-text">
    Your refund of <strong style="color:#10B981;">₹{{ number_format((float)($order->refund_amount > 0 ? $order->refund_amount : $order->getRefundableAmount()), 2) }}</strong> for cancelled Order <strong>#{{ $order->order_number }}</strong> has been processed by our finance desk.
  </p>

  <!-- Compact Refund Details Box -->
  <div class="lg-box" style="margin-top:16px; border:1px solid rgba(212, 175, 106, 0.35); border-radius:14px; overflow:hidden;">
    <div style="background:#241610; border-bottom:1px solid rgba(212, 175, 106, 0.25); padding:9px 14px;">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="left" style="color:#E7C77B; font-size:10.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase;">
            Refund Transaction Details
          </td>
          <td align="right" style="color:#A8988B; font-size:10.5px; font-family:monospace;">
            #{{ $order->order_number }}
          </td>
        </tr>
      </table>
    </div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Refund Channel</td>
        <td class="val" style="color:#FAF7F0;">{{ $order->refund_method ?: 'UPI / Bank Reversal' }}</td>
      </tr>
      @if($order->refund_transaction_reference)
      <tr>
        <td class="lbl">Bank UTR / Ref</td>
        <td class="val font-mono" style="font-weight:800; color:#FFFFFF;">{{ $order->refund_transaction_reference }}</td>
      </tr>
      @endif
      <tr>
        <td class="lbl">Settlement Date</td>
        <td class="val">{{ $order->refunded_at ? $order->refunded_at->format('d M, Y \a\t h:i A') : now()->format('d M, Y \a\t h:i A') }} IST</td>
      </tr>
      @if($order->refund_note)
      <tr>
        <td class="lbl">Note</td>
        <td class="val" style="color:#D4AF6A; font-style:italic;">{{ $order->refund_note }}</td>
      </tr>
      @endif
    </table>
  </div>

  <!-- Primary CTA Button -->
  <div class="btn-wrap" style="margin-top:20px;">
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn">
      View Order Details →
    </a>
  </div>
@endsection
