@extends('emails.layouts.master', [
    'emailTitle' => 'COD Balance Settled — Order #' . $order->order_number . ' — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    'headerTitle' => 'COD Balance Payment Settled',
    'headerSubtitle' => 'Order #' . $order->order_number . ' • Consignment Balance Closed'
])

@section('content')
  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  <p class="message-text">
    We are pleased to inform you that the remaining Cash on Delivery balance for your Order <strong>#{{ $order->order_number }}</strong> has been officially confirmed, verified, and settled in our accounts ledger!
  </p>

  <!-- Big Amount Pill -->
  <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(212, 175, 106, 0.12) 100%); border: 1.5px solid #10B981; border-radius: 14px; padding: 20px 22px; text-align: center; margin: 20px 0 24px 0;">
    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #065F46; font-weight: 700; margin-bottom: 6px;">Remaining Balance Collected & Settled</div>
    <div style="font-family: 'Playfair Display', Georgia, serif; font-size: 32px; font-weight: 900; color: #064E3B; line-height: 1;">
      ₹{{ number_format((float)$order->balance_amount, 2) }}
    </div>
    <div style="font-size: 11px; color: #047857; margin-top: 8px; font-weight: 600;">
      Collection Mode: <span style="background: #D1FAE5; color: #065F46; padding: 2px 10px; border-radius: 9999px; font-weight: 800; border: 1px solid #A7F3D0;">{{ $order->balance_status }}</span>
    </div>
  </div>

  <div class="lg-box">
    <div class="lg-box-header">Payment & Settlement Breakdown</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">Total Order Value</td>
        <td class="val font-mono">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
      <tr>
        <td class="lbl">1. Advance Token (Paid Online)</td>
        <td class="val" style="color:#10B981; font-weight:700;">₹{{ number_format($order->advance_amount, 2) }} ({{ $order->advance_percentage }}%)</td>
      </tr>
      <tr>
        <td class="lbl">2. Balance on Delivery (Now Settled)</td>
        <td class="val" style="color:#10B981; font-weight:700;">₹{{ number_format($order->balance_amount, 2) }}</td>
      </tr>
      <tr style="border-top:1px dashed rgba(212,175,106,0.3); background:rgba(16,185,129,0.06);">
        <td class="lbl" style="color:#065F46; font-weight:800;">Total Settlement Status</td>
        <td class="val" style="color:#10B981; font-weight:900; font-size:13px;">
          100% Fully Paid & Closed
        </td>
      </tr>
      @if($order->delivery_transaction_reference)
      <tr>
        <td class="lbl">Delivery UTR / Cash Slip Ref</td>
        <td class="val font-mono" style="font-weight:800; color:#1F2937;">{{ $order->delivery_transaction_reference }}</td>
      </tr>
      @endif
      <tr>
        <td class="lbl">Settlement Verified At</td>
        <td class="val">{{ $order->delivery_paid_at ? $order->delivery_paid_at->format('d M, Y \a\t h:i A') : now()->format('d M, Y \a\t h:i A') }} IST</td>
      </tr>
      @php
        $note = $adminNote ?? ($order->payment?->admin_note ?? null);
      @endphp
      @if($note)
      <tr>
        <td class="lbl">Accounts Verification Note</td>
        <td class="val" style="color:#4A2C1D; font-style:italic;">{{ $note }}</td>
      </tr>
      @endif
    </table>
  </div>

  @if(!empty($order->delivery_payment_screenshot))
    <div class="alert-box" style="background:#ECFDF5; border:1px solid #A7F3D0; color:#065F46; padding:12px 16px; border-radius:10px; margin:16px 0; font-size:12px;">
      <strong>Payment Receipt Attached:</strong> A copy of the confirmed balance transaction receipt has been attached to this email for your records.
    </div>
  @endif

  <div class="alert-box" style="background:#FAF7F0; border:1px solid rgba(212,175,106,0.4); color:#4A2C1D; padding:12px 16px; border-radius:10px; margin:16px 0; font-size:12px; line-height:1.6;">
    <strong>Order Settlement Complete:</strong> No further amount is due for this consignment. We sincerely appreciate your trust in Rayka Imitation Jewellery. We hope our royal creations bring you enduring joy and grace!
  </div>

  <div class="btn-wrap">
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn">View Order Consignment</a>
  </div>
@endsection