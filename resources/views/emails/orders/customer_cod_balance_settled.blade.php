@extends('emails.layouts.master', [
    'emailTitle' => 'Full Payment Completed — #' . $order->order_number . ' — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    'headerTitle' => 'Full Payment Completed',
    'headerSubtitle' => 'Order #' . $order->order_number . ' • 100% Settled & Closed'
])

@section('content')
  <!-- Compact Luxury Hero Card -->
  <div style="background:linear-gradient(135deg, #062e22 0%, #041f17 100%); border:1px solid #10b981; border-radius:16px; padding:18px 20px; margin-bottom:22px; box-shadow:0 10px 30px rgba(0,0,0,0.65);">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top">
          <div style="display:inline-block; background:#059669; color:#ffffff; font-size:10px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:3px 10px; border-radius:20px; margin-bottom:8px;">
            100% Full Payment Settled
          </div>
          <div style="font-size:11.5px; color:#a7f3d0; margin-bottom:4px; font-weight:500;">
            Order #{{ $order->order_number }} • Rayka Imitation Jewellery
          </div>
          <div style="font-size:10.5px; color:#cbd5e1; text-transform:uppercase; letter-spacing:1px; font-weight:700;">
            Total Order Value (Paid in Full)
          </div>
          <div style="font-size:26px; font-weight:900; color:#ffffff; font-family:monospace; letter-spacing:-0.5px; line-height:1.1;">
            ₹{{ number_format($order->total_amount, 2) }}
          </div>
          <div style="font-size:11px; color:#6ee7b7; margin-top:4px; font-weight:600;">
            Advance: ₹{{ number_format($order->advance_amount, 2) }} + Balance: ₹{{ number_format($order->balance_amount, 2) }} ({{ $order->balance_status }})
          </div>
        </td>
        <td align="right" valign="top" style="width:50px;">
          <div style="width:42px; height:42px; background:#059669; border:2px solid #10B981; border-radius:50%; text-align:center; line-height:38px; display:inline-block; font-size:20px; color:#ffffff !important; font-weight:900; font-family:Arial,Helvetica,sans-serif;">
            &#10004;
          </div>
        </td>
      </tr>
    </table>
  </div>

  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  <p class="message-text">
    The remaining delivery balance of <strong>₹{{ number_format($order->balance_amount, 2) }}</strong> for Order <strong>#{{ $order->order_number }}</strong> has been verified. Your order is now <strong>100% fully paid</strong>. Your official Tax Invoice (PDF) is attached to this email.
  </p>

  <!-- Compact Settlement Breakdown Box -->
  <div class="lg-box" style="margin-top:16px; border:1px solid #10b981; border-radius:14px; overflow:hidden;">
    <div style="background:#062e22; border-bottom:1px solid rgba(16,185,129,0.3); padding:9px 14px;">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="left" style="color:#a7f3d0; font-size:10.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase;">
            Payment Settlement Summary
          </td>
          <td align="right" style="color:#34d399; font-size:10px; font-weight:800; text-transform:uppercase;">
            Closed
          </td>
        </tr>
      </table>
    </div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono" style="color:#ffffff;">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">1. Advance Token (Online)</td>
        <td class="val" style="color:#E7C77B; font-weight:700;">₹{{ number_format($order->advance_amount, 2) }} ({{ $order->advance_percentage }}%)</td>
      </tr>
      <tr>
        <td class="lbl">2. Balance on Delivery</td>
        <td class="val" style="color:#34d399; font-weight:700;">₹{{ number_format($order->balance_amount, 2) }} (Settled via {{ $order->balance_status }})</td>
      </tr>
      @if($order->delivery_transaction_reference)
      <tr>
        <td class="lbl">Ref / UTR</td>
        <td class="val font-mono">{{ $order->delivery_transaction_reference }}</td>
      </tr>
      @endif
      <tr style="border-top:1px solid rgba(16,185,129,0.3); background:#062e22;">
        <td class="lbl" style="color:#ffffff; font-weight:800;">Total Settled</td>
        <td class="val" style="color:#34d399; font-weight:900; font-size:15px; font-family:monospace;">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
    </table>
  </div>

  <!-- Primary CTA Button -->
  <div class="btn-wrap" style="margin-top:20px;">
    <a href="{{ route('order.track.invoice', $order->order_number) }}" class="cta-btn">
      Download Official Tax Invoice (PDF)
    </a>
  </div>
@endsection
