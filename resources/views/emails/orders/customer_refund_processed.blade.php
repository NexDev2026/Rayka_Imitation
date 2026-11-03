@extends('emails.layouts.master', [
    'emailTitle' => 'Payment Refund Processed — Order #' . $order->order_number . ' — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    'headerTitle' => 'Payment Refund Processed',
    'headerSubtitle' => 'Refund Settled for Order #' . $order->order_number
])

@section('content')
  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  <p class="message-text">
    We are pleased to inform you that the refund settlement for your cancelled order <strong>#{{ $order->order_number }}</strong> has been officially processed and returned to your account by our finance desk.
  </p>

  <!-- Big Amount Pill -->
  <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(212, 175, 106, 0.12) 100%); border: 1.5px solid #10B981; border-radius: 16px; padding: 20px 22px; text-align: center; margin: 20px 0 24px 0; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #10B981; font-weight: 800; margin-bottom: 6px;">Total Refund Amount Credited</div>
    <div style="font-family: 'Outfit', ui-monospace, sans-serif; font-size: 34px; font-weight: 900; color: #FFFFFF; line-height: 1.1; margin-bottom: 8px;">
      ₹{{ number_format((float)($order->refund_amount > 0 ? $order->refund_amount : $order->getRefundableAmount()), 2) }}
    </div>
    <div style="font-size: 11px; color: #A7F3D0; font-weight: 600;">
      Settlement Status: <span style="background: rgba(16, 185, 129, 0.25); color: #34D399; padding: 2px 10px; border-radius: 9999px; font-weight: 800; border: 1px solid #10B981;">Completed & Returned</span>
    </div>
  </div>

  <div class="lg-box">
    <div class="lg-box-header">Refund Transaction Details</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">Payment Type</td>
        <td class="val">{{ $order->isCod() ? 'Cash on Delivery (' . $order->advance_percentage . '% Advance Token)' : 'Full Prepaid Online (UPI)' }}</td>
      </tr>
      @if($order->isCod())
      <tr>
        <td class="lbl">Advance Token Paid</td>
        <td class="val" style="color:#10B981; font-weight:700;">₹{{ number_format($order->advance_amount, 2) }}</td>
      </tr>
      <tr>
        <td class="lbl">Delivery Balance (Voided)</td>
        <td class="val" style="color:#A8988B; text-decoration:line-through;">₹{{ number_format($order->balance_amount, 2) }}</td>
      </tr>
      @endif
      <tr style="border-top:1px dashed rgba(212,175,106,0.3); background:rgba(212,175,106,0.06);">
        <td class="lbl" style="color:#E7C77B; font-weight:800;">Refunded Amount</td>
        <td class="val" style="color:#10B981; font-weight:900; font-family:monospace; font-size:16px;">
          ₹{{ number_format((float)($order->refund_amount > 0 ? $order->refund_amount : $order->getRefundableAmount()), 2) }}
        </td>
      </tr>
      @if($order->refund_method)
      <tr>
        <td class="lbl">Refund Channel / Mode</td>
        <td class="val" style="font-weight:700; color:#FAF7F0;">{{ $order->refund_method }}</td>
      </tr>
      @endif
      @if($order->refund_transaction_reference)
      <tr>
        <td class="lbl">Bank UTR / Transaction Ref</td>
        <td class="val font-mono" style="font-weight:800; color:#FFFFFF; letter-spacing:0.5px;">{{ $order->refund_transaction_reference }}</td>
      </tr>
      @endif
      <tr>
        <td class="lbl">Settlement Date</td>
        <td class="val">{{ $order->refunded_at ? $order->refunded_at->format('d M, Y \a\t h:i A') : now()->format('d M, Y \a\t h:i A') }} IST</td>
      </tr>
      @if($order->refund_note)
      <tr>
        <td class="lbl">Finance Desk Note</td>
        <td class="val" style="color:#D4AF6A; font-style:italic;">{{ $order->refund_note }}</td>
      </tr>
      @endif
    </table>
  </div>

  @if(!empty($order->refund_screenshot))
    <div class="alert-box alert-success" style="padding:14px 18px; border-radius:10px; margin:16px 0; font-size:12.5px; line-height:1.6;">
      <strong>Payment Proof Attached:</strong> An official transaction confirmation screenshot or receipt has been attached to this email for your financial records. You may also view it online anytime from your order tracking page.
    </div>
  @endif

  <div class="alert-box alert-info" style="padding:14px 18px; border-radius:10px; margin:16px 0; font-size:12.5px; line-height:1.6;">
    <strong style="color:#E7C77B; display:block; margin-bottom:4px; font-size:13px; font-weight:800;">Bank Credit Timing Note:</strong>
    UPI and IMPS reversals usually reflect in your bank account immediately or within a few minutes. If it was processed via NEFT or card gateway reversal, please allow 1–2 business days depending on your bank's clearance cycles.
  </div>

  <div class="btn-wrap">
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn">View Live Order Tracking</a>
  </div>
@endsection
