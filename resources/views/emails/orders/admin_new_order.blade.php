@extends('emails.layouts.master', [
    'emailTitle' => 'New Order Alert #' . $order->order_number . ' — Rayka Admin',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>',
    'headerTitle' => 'New Order Received',
    'headerSubtitle' => 'Order #' . $order->order_number . ' requires payment verification'
])

@section('content')
  <div class="greeting">Administrator Alert,</div>
  <p class="message-text">
    A new order has been placed on the storefront. Please review the customer's payment proof and verify bank credit.
  </p>

  <div class="lg-box" style="border:1px solid rgba(212, 175, 106, 0.35); border-radius:14px; overflow:hidden;">
    <div style="background:#241610; border-bottom:1px solid rgba(212, 175, 106, 0.25); padding:9px 14px;">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="left" style="color:#E7C77B; font-size:10.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase;">
            Order & Payment Overview
          </td>
          <td align="right" style="color:#D4AF6A; font-size:10.5px; font-family:monospace; font-weight:700;">
            #{{ $order->order_number }}
          </td>
        </tr>
      </table>
    </div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Customer</td>
        <td class="val">{{ $order->address?->name ?? $order->user?->name ?? 'Guest' }} ({{ $order->address?->mobile }})</td>
      </tr>
      <tr>
        <td class="lbl">Payment Type</td>
        <td class="val" style="font-weight:700; color:{{ $order->isCod() ? '#D4AF6A' : '#10B981' }};">
          {{ $order->isCod() ? 'Cash on Delivery (' . $order->advance_percentage . '% Advance Token)' : 'Full Online UPI' }}
        </td>
      </tr>
      @if($order->isCod())
      <tr>
        <td class="lbl">Advance to Verify</td>
        <td class="val" style="color:#10B981; font-weight:800; font-size:14px; font-family:monospace;">₹{{ number_format($order->advance_amount, 2) }}</td>
      </tr>
      <tr>
        <td class="lbl">Balance on Delivery</td>
        <td class="val" style="color:#FFFFFF; font-weight:700; font-family:monospace;">₹{{ number_format($order->balance_amount, 2) }}</td>
      </tr>
      @endif
      <tr style="border-top:1px solid rgba(212,175,106,0.2); background:#241610;">
        <td class="lbl" style="color:#ffffff; font-weight:800;">Total Order Value</td>
        <td class="val" style="color:#D4AF6A; font-weight:900; font-family:monospace; font-size:15px;">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
      @if($order->payment?->transaction_reference)
      <tr>
        <td class="lbl">UTR / Ref</td>
        <td class="val font-mono">{{ $order->payment->transaction_reference }}</td>
      </tr>
      @endif
      <tr>
        <td class="lbl">Payment Proof</td>
        <td class="val">
          @if($order->payment?->screenshot_path)
            <span style="color:#10B981; font-weight:700;">Screenshot Attached</span>
          @else
            <span style="color:#EF4444; font-weight:700;">No Proof</span>
          @endif
        </td>
      </tr>
    </table>
  </div>

  <div class="btn-wrap" style="margin-top:20px;">
    <a href="{{ route('admin.orders.show', $order->id) }}" class="cta-btn">
      Review & Verify in Admin Portal →
    </a>
  </div>
@endsection
