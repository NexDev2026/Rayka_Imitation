@extends('emails.layouts.master', [
    'emailTitle' => 'Order Booked: #' . $order->order_number . ' — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>',
    'headerTitle' => 'Order Booked Successfully',
    'headerSubtitle' => 'Order #' . $order->order_number
])

@section('content')
  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  <p class="message-text">
    Thank you for choosing <strong>Rayka Imitation Jewellery</strong>. Your royal order has been recorded and is currently in verification. Below are your order summary and itemized details:
  </p>

  <!-- Order Info Card -->
  <div class="lg-box">
    <div class="lg-box-header">Order Information</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono" style="color:#D4AF6A; font-weight:800;">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">Order Date</td>
        <td class="val">{{ \Carbon\Carbon::parse($order->created_at)->format('d M, Y \a\t h:i A') }} IST</td>
      </tr>
      <tr>
        <td class="lbl">Order Status</td>
        <td class="val"><span style="color:#F59E0B; font-weight:700;">{{ $order->status }}</span></td>
      </tr>
      <tr>
        <td class="lbl">Payment Method</td>
        <td class="val">{{ $order->payment?->payment_method ?: 'Static UPI QR' }}</td>
      </tr>
      @if($order->payment?->transaction_reference)
      <tr>
        <td class="lbl">UTR / Ref Number</td>
        <td class="val font-mono">{{ $order->payment->transaction_reference }}</td>
      </tr>
      @endif
    </table>
  </div>

  <!-- Itemized Creations Table -->
  <div class="lg-box">
    <div class="lg-box-header">Ordered Creations ({{ $order->items->count() }})</div>
    <table class="order-items-table">
      <thead>
        <tr>
          <th>Creation Details</th>
          <th style="text-align:center; width:45px; white-space:nowrap;">Qty</th>
          <th style="text-align:right; width:90px; white-space:nowrap;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $item)
          <tr>
            <td style="vertical-align:middle;">
              <div class="item-title" style="font-size:12px;">{{ $item->product_name }}</div>
              <div class="item-meta">
                @if($item->variant_info)
                  Variant: {{ $item->variant_info }} |
                @endif
                <span style="font-family:monospace;">{{ $item->product_sku }}</span>
              </div>
            </td>
            <td style="text-align:center; font-weight:700; vertical-align:middle; white-space:nowrap;">{{ $item->quantity }}</td>
            <td style="text-align:right; font-weight:700; color:#F8F4EE; vertical-align:middle; white-space:nowrap; font-family:monospace;">₹{{ number_format($item->subtotal, 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <table class="detail-table" style="background:rgba(0,0,0,0.25); border-top:1px solid rgba(212,175,106,0.15);">
      <tr>
        <td class="lbl">Subtotal</td>
        <td class="val">₹{{ number_format($order->subtotal, 2) }}</td>
      </tr>
      @if($order->coupon_discount > 0)
      <tr>
        <td class="lbl">Coupon ({{ $order->coupon_code }})</td>
        <td class="val" style="color:#10B981;">− ₹{{ number_format($order->coupon_discount, 2) }}</td>
      </tr>
      @endif
      <tr>
        <td class="lbl">Shipping Fee</td>
        <td class="val">{{ $order->shipping_fee == 0 ? 'FREE' : '₹' . number_format($order->shipping_fee, 2) }}</td>
      </tr>
      <tr style="border-top:1px solid rgba(212,175,106,0.3);">
        <td class="lbl" style="font-size:12px; color:#D4AF6A; font-weight:800;">Total Payable</td>
        <td class="val" style="font-size:16px; color:#D4AF6A; font-weight:900;">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
    </table>
  </div>

  <!-- Delivery Address -->
  @if($order->address)
  <div class="lg-box">
    <div class="lg-box-header">Delivery Destination</div>
    <div style="padding:14px 20px; font-size:13px; line-height:1.7; color:#E2D9D0;">
      <strong style="color:#FFFFFF;">{{ $order->address->name }}</strong><br>
      {{ $order->address->formatted_address }}<br>
      <span style="color:#A8988B;">Phone: {{ $order->address->mobile }} | Email: {{ $order->address->email }}</span>
    </div>
  </div>
  @endif

  <div class="alert-box alert-info">
    <strong>Verification in Progress:</strong> Our accounts team is verifying your payment screenshot against our bank settlement ledger. You will receive an instant notification along with your official Tax Invoice as soon as your payment is approved!
  </div>

  <div class="btn-wrap">
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn">Track Order Status</a>
  </div>
@endsection
