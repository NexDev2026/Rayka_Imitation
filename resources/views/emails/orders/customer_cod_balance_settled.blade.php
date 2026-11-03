@extends('emails.layouts.master', [
    'emailTitle' => 'Full Payment Completed (COD Balance Settled) — Order #' . $order->order_number . ' — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    'headerTitle' => 'Full Payment Completed (COD)',
    'headerSubtitle' => 'Order #' . $order->order_number . ' • 100% Fully Settled & Closed'
])

@section('schema_markup')
<script type="application/ld+json">
{
  "{{ '@context' }}": "https://schema.org",
  "{{ '@type' }}": "Invoice",
  "accountId": "{{ $order->order_number }}",
  "name": "Rayka COD Settlement Bill",
  "description": "Full Payment Completed for COD Order #{{ $order->order_number }}",
  "paymentDue": "{{ \Carbon\Carbon::parse($order->created_at)->toIso8601String() }}",
  "paymentStatus": "PaymentAutomaticallyApplied",
  "provider": {
    "{{ '@type' }}": "Organization",
    "name": "Rayka Imitation Jewellery",
    "url": "https://raykaimitation.com"
  },
  "totalPaymentDue": {
    "{{ '@type' }}": "PriceSpecification",
    "price": "{{ number_format($order->total_amount, 2, '.', '') }}",
    "priceCurrency": "INR"
  },
  "customer": {
    "{{ '@type' }}": "Person",
    "name": "{{ addslashes($order->address?->name ?? $order->user?->name ?? 'Valued Patron') }}"
  }
}
</script>
@endsection

@section('content')
  <!-- SaaS 100% Payment Completion Card -->
  <div style="background:linear-gradient(135deg, #062e22 0%, #041f17 100%); border:1px solid #10b981; border-radius:18px; padding:22px 24px; margin-bottom:28px; box-shadow:0 12px 35px rgba(0,0,0,0.55);">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top">
          <div style="display:inline-block; background:#059669; color:#ffffff; font-size:10.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:4px 12px; border-radius:20px; margin-bottom:10px;">
            100% Full Payment Settled
          </div>
          <div style="font-size:13px; color:#a7f3d0; margin-bottom:6px; font-weight:500;">
            Rayka Imitation Jewellery • Order #{{ $order->order_number }}
          </div>
          <div style="font-size:11px; color:#cbd5e1; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px; font-weight:700;">
            Total Order Value (100% Paid)
          </div>
          <div style="font-size:34px; font-weight:900; color:#ffffff; font-family:ui-monospace,Menlo,Consolas,monospace; letter-spacing:-0.5px; line-height:1.1; margin-bottom:10px;">
            ₹{{ number_format($order->total_amount, 2) }}
          </div>
          <div style="font-size:11px; color:#6ee7b7; font-weight:600; line-height:1.4;">
            Advance: ₹{{ number_format($order->advance_amount, 2) }} ({{ $order->advance_percentage }}%) + Delivery Balance: ₹{{ number_format($order->balance_amount, 2) }} (Settled via {{ $order->balance_status }})
          </div>
        </td>
        <td align="right" valign="middle" style="width:60px;">
          <div style="width:48px; height:48px; background:#059669; border:2px solid #10B981; border-radius:50%; text-align:center; line-height:44px; display:inline-block; box-shadow:0 0 20px rgba(16, 185, 129, 0.45); font-size:24px; color:#ffffff !important; font-weight:900; font-family:Arial,Helvetica,sans-serif;">
            &#10004;
          </div>
        </td>
      </tr>
    </table>
  </div>

  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  <p class="message-text">
    We are pleased to inform you that the remaining Cash on Delivery balance of <strong style="color:#10b981; font-size:14px;">₹{{ number_format($order->balance_amount, 2) }}</strong> for Order <strong>#{{ $order->order_number }}</strong> has been verified and settled! Your entire order is now <strong>100% fully paid</strong> and complete in our royal accounts ledger.
  </p>

  <!-- Attached Invoice Alert -->
  <div class="alert-box alert-success" style="margin-bottom:20px; background:rgba(16, 185, 129, 0.12); border:1px solid #10b981; color:#a7f3d0; padding:14px 18px; border-radius:12px; font-size:12px; line-height:1.6;">
    <strong style="color:#34d399; display:block; margin-bottom:4px; font-size:13px;">Official GST Tax Invoice Attached:</strong>
    Your computer-generated official Tax Invoice (PDF) has been generated and attached directly to this email. You can also download or view it anytime using the button below.
  </div>

  <!-- Settlement Breakdown Card -->
  <div class="lg-box" style="margin-top:16px; border:1px solid #10b981; border-radius:16px; overflow:hidden;">
    <div style="background:#062e22; border-bottom:1px solid rgba(16,185,129,0.3); padding:10px 16px;">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="left" valign="middle" style="color:#a7f3d0; font-size:11px; font-weight:800; letter-spacing:1px; text-transform:uppercase;">
            COD FINAL SETTLEMENT BREAKDOWN
          </td>
          <td align="right" valign="middle" style="white-space:nowrap; padding-left:8px;">
            <span style="font-size:10px; font-weight:800; color:#34d399; background:rgba(52,211,153,0.15); border:1px solid #34d399; padding:3px 8px; border-radius:12px; display:inline-block; white-space:nowrap; text-transform:uppercase;">
              100% PAID & CLOSED
            </span>
          </td>
        </tr>
      </table>
    </div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono" style="font-weight:700; color:#ffffff;">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">Total Order Value</td>
        <td class="val font-mono" style="font-weight:800; color:#ffffff;">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
      <tr>
        <td class="lbl">1. Advance Token (Paid Online)</td>
        <td class="val" style="color:#E7C77B; font-weight:700;">₹{{ number_format($order->advance_amount, 2) }} ({{ $order->advance_percentage }}%)</td>
      </tr>
      <tr>
        <td class="lbl">2. Balance on Delivery (Settled)</td>
        <td class="val" style="color:#34d399; font-weight:700;">₹{{ number_format($order->balance_amount, 2) }}</td>
      </tr>
      <tr style="border-top:1px dashed rgba(16,185,129,0.3); background:rgba(16,185,129,0.08);">
        <td class="lbl" style="color:#a7f3d0; font-weight:800;">Settlement Mode</td>
        <td class="val" style="color:#ffffff; font-weight:800; font-size:12px;">
          <span style="background:rgba(16,185,129,0.25); color:#34d399; padding:2px 10px; border-radius:12px; border:1px solid #10b981; font-weight:800;">{{ $order->balance_status }}</span>
        </td>
      </tr>
      @if($order->delivery_transaction_reference)
      <tr>
        <td class="lbl">Delivery UTR / Cash Ref</td>
        <td class="val font-mono" style="font-weight:800; color:#ffffff;">{{ $order->delivery_transaction_reference }}</td>
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
        <td class="val" style="color:#D4AF6A; font-style:italic;">{{ $note }}</td>
      </tr>
      @endif
    </table>
  </div>

  @if(!empty($order->delivery_payment_screenshot))
    <div class="alert-box" style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.3); color:#a7f3d0; padding:12px 16px; border-radius:10px; margin:16px 0; font-size:12px;">
      <strong style="color:#34d399;">Delivery Proof Attached:</strong> A copy of the confirmed balance transaction receipt has been attached to this email for your financial records.
    </div>
  @endif

  <div class="alert-box" style="background:#FAF7F0; border:1px solid rgba(212,175,106,0.4); color:#4A2C1D; padding:12px 16px; border-radius:10px; margin:16px 0; font-size:12px; line-height:1.6;">
    <strong>Order Settlement Complete:</strong> No further amount is due for this consignment. We sincerely appreciate your patronage of Rayka Imitation Jewellery. We hope our handcrafted ornaments bring you elegance and enduring beauty!
  </div>

  <!-- Action Buttons -->
  <div class="btn-wrap" style="margin-top:24px;">
    <a href="{{ route('order.track.invoice', $order->order_number) }}" class="cta-btn" style="background:linear-gradient(135deg, #D4AF6A 0%, #996E2E 100%); color:#1C100B!important; box-shadow:0 4px 15px rgba(212,175,106,0.35); margin-right:8px; margin-bottom:8px;">
      Download Official Tax Invoice (PDF)
    </a>
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn" style="background:rgba(212,175,106,0.15); color:#E7C77B!important; border:1px solid #D4AF6A; margin-bottom:8px;">
      View Consignment Details
    </a>
  </div>
@endsection
