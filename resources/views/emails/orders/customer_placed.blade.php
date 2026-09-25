@extends('emails.layouts.master', [
    'emailTitle' => 'Payment successful for Rayka Imitation Jewellery - Order #' . $order->order_number,
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>',
    'headerTitle' => 'Order & Payment Confirmed',
    'headerSubtitle' => 'Order #' . $order->order_number
])

@section('schema_markup')
<script type="application/ld+json">
{
  "{{ '@context' }}": "https://schema.org",
  "{{ '@type' }}": "Order",
  "merchant": {
    "{{ '@type' }}": "Organization",
    "name": "Rayka Imitation Jewellery"
  },
  "orderNumber": "{{ $order->order_number }}",
  "priceCurrency": "INR",
  "price": "{{ number_format($order->total_amount, 2, '.', '') }}",
  "orderDate": "{{ \Carbon\Carbon::parse($order->created_at)->toIso8601String() }}",
  "acceptedOffer": [
    @foreach($order->items as $idx => $item)
    {
      "{{ '@type' }}": "Offer",
      "itemOffered": {
        "{{ '@type' }}": "Product",
        "name": "{{ addslashes($item->product_name) }}"
      },
      "price": "{{ number_format($item->subtotal, 2, '.', '') }}",
      "priceCurrency": "INR",
      "eligibleQuantity": {
        "{{ '@type' }}": "QuantitativeValue",
        "value": "{{ $item->quantity }}"
      }
    }{{ $loop->last ? '' : ',' }}
    @endforeach
  ],
  "orderStatus": "http://schema.org/OrderProcessing",
  "potentialAction": {
    "{{ '@type' }}": "ViewAction",
    "name": "Track Order",
    "target": "{{ route('order.track', ['order_number' => $order->order_number]) }}"
  }
}
</script>
@endsection

@section('content')
  <!-- Razorpay-Style SaaS Payment Card -->
  <div style="background:linear-gradient(135deg, #152544 0%, #0d172a 100%); border:1px solid #3b82f6; border-radius:18px; padding:22px 24px; margin-bottom:28px; box-shadow:0 12px 35px rgba(0,0,0,0.55);">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top">
          <div style="display:inline-block; background:#2563eb; color:#ffffff; font-size:10.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:4px 12px; border-radius:20px; margin-bottom:10px;">
            Paid on {{ \Carbon\Carbon::parse($order->created_at)->format('d M') }}
          </div>
          <div style="font-size:13px; color:#94a3b8; margin-bottom:6px; font-weight:500;">
            Rayka Imitation Jewellery bill
          </div>
          <div style="font-size:11px; color:#cbd5e1; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px; font-weight:700;">
            Amount paid
          </div>
          <div style="font-size:36px; font-weight:900; color:#ffffff; font-family:ui-monospace,Menlo,Consolas,monospace; letter-spacing:-0.5px; line-height:1.1;">
            ₹{{ number_format($order->total_amount, 2) }}
          </div>
        </td>
        <td align="right" valign="middle" style="width:65px;">
          <div style="width:54px; height:54px; background:#16a34a; border-radius:50%; text-align:center; line-height:54px; display:inline-block; box-shadow:0 0 25px rgba(34, 197, 94, 0.45);">
            <span style="color:#ffffff; font-size:28px; font-weight:900; line-height:54px; display:block;">✓</span>
          </div>
        </td>
      </tr>
    </table>
  </div>

  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  <p class="message-text">
    Thank you for choosing <strong>Rayka Imitation Jewellery</strong>. Your royal order <strong>#{{ $order->order_number }}</strong> has been recorded and is currently in verification. Below is your official receipt summary:
  </p>

  <!-- Order Details Box -->
  <div class="lg-box">
    <div class="lg-box-header">Order Summary</div>
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
          <th style="text-align:right; width:95px; white-space:nowrap;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $item)
          <tr>
            <td style="vertical-align:middle;">
              <div class="item-title" style="font-size:12.5px;">{{ $item->product_name }}</div>
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
        <td class="lbl" style="font-size:12px; color:#D4AF6A; font-weight:800;">Total Paid</td>
        <td class="val" style="font-size:18px; color:#D4AF6A; font-weight:900; font-family:monospace;">₹{{ number_format($order->total_amount, 2) }}</td>
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
    <strong>Verification in Progress:</strong> Our accounts desk is confirming your payment screenshot against our bank ledger. You will receive an instant notification along with your official Tax Invoice as soon as your payment is approved!
  </div>

  <div class="btn-wrap">
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn">Track Order Status →</a>
  </div>
@endsection
