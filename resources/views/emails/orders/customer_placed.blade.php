@php
  $paymentStatus = (string) ($order->payment?->status ?? 'Pending');
  $paymentMethod = (string) ($order->payment?->payment_method ?? 'Static QR / UPI');
  $isPaid = in_array(strtolower($paymentStatus), ['confirmed', 'success', 'paid']);
  $isCod = str_contains(strtolower($paymentMethod), 'cod') || str_contains(strtolower($paymentMethod), 'cash');

  $emailTitle = $isPaid 
    ? ('Payment Confirmed — Order #' . $order->order_number . ' — Rayka')
    : ($isCod 
        ? ('Order Booked (Cash on Delivery) — Order #' . $order->order_number . ' — Rayka')
        : ('Order Received (Payment Verification Pending) — Order #' . $order->order_number . ' — Rayka'));

  $headerTitle = $isPaid 
    ? 'Order & Payment Confirmed' 
    : ($isCod 
        ? 'Order Booked (Cash on Delivery)' 
        : 'Order Received — Verification Pending');

  $headerSubtitle = $isPaid 
    ? ('Payment Receipt — Order #' . $order->order_number) 
    : ($isCod 
        ? ('Order #' . $order->order_number . ' • Pay on Delivery') 
        : ('Order #' . $order->order_number . ' • Awaiting Admin Approval'));

  $headerIcon = $isPaid
    ? '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
    : ($isCod 
        ? '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#38BDF8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>'
        : '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>');
@endphp

@extends('emails.layouts.master', [
    'emailTitle' => $emailTitle,
    'headerIcon' => $headerIcon,
    'headerTitle' => $headerTitle,
    'headerSubtitle' => $headerSubtitle
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
  "orderStatus": "http://schema.org/Order{{ $isPaid ? 'Processing' : 'PaymentDue' }}",
  "potentialAction": {
    "{{ '@type' }}": "ViewAction",
    "name": "Track Order",
    "target": "{{ route('order.track', ['order_number' => $order->order_number]) }}"
  }
}
</script>
@endsection

@section('content')
  @if($isPaid)
    <!-- Paid: Razorpay-Style SaaS Payment Card -->
    <div style="background:linear-gradient(135deg, #152544 0%, #0d172a 100%); border:1px solid #3b82f6; border-radius:18px; padding:22px 24px; margin-bottom:28px; box-shadow:0 12px 35px rgba(0,0,0,0.55);">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top">
            <div style="display:inline-block; background:#2563eb; color:#ffffff; font-size:10.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:4px 12px; border-radius:20px; margin-bottom:10px;">
              Paid on {{ \Carbon\Carbon::parse($order->payment?->verified_at ?? $order->created_at)->format('d M') }}
            </div>
            <div style="font-size:13px; color:#94a3b8; margin-bottom:6px; font-weight:500;">
              Rayka Imitation Jewellery bill • Order #{{ $order->order_number }}
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
  @elseif($isCod)
    <!-- COD: Sky Blue Luxury Card -->
    <div style="background:linear-gradient(135deg, #0c2d3a 0%, #081a24 100%); border:1px solid #0284c7; border-radius:18px; padding:22px 24px; margin-bottom:28px; box-shadow:0 12px 35px rgba(0,0,0,0.55);">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top">
            <div style="display:inline-block; background:#0284c7; color:#ffffff; font-size:10.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:4px 12px; border-radius:20px; margin-bottom:10px;">
              🚚 Cash on Delivery
            </div>
            <div style="font-size:13px; color:#94a3b8; margin-bottom:6px; font-weight:500;">
              Rayka Imitation Jewellery • Order #{{ $order->order_number }}
            </div>
            <div style="font-size:11px; color:#cbd5e1; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px; font-weight:700;">
              Amount Payable at Delivery
            </div>
            <div style="font-size:36px; font-weight:900; color:#ffffff; font-family:ui-monospace,Menlo,Consolas,monospace; letter-spacing:-0.5px; line-height:1.1;">
              ₹{{ number_format($order->total_amount, 2) }}
            </div>
          </td>
          <td align="right" valign="middle" style="width:65px;">
            <div style="width:54px; height:54px; background:#0284c7; border-radius:50%; text-align:center; line-height:54px; display:inline-block; box-shadow:0 0 25px rgba(2, 132, 199, 0.45);">
              <span style="color:#ffffff; font-size:26px; font-weight:900; line-height:54px; display:block;">📦</span>
            </div>
          </td>
        </tr>
      </table>
    </div>
  @else
    <!-- Pending Verification: Royal Obsidian & Champagne Gold SaaS Status Card -->
    <div style="background:linear-gradient(145deg, #1C120C 0%, #120A06 100%); border:1px solid rgba(212, 175, 106, 0.45); border-radius:18px; padding:22px 24px; margin-bottom:28px; box-shadow:0 12px 35px rgba(0,0,0,0.55);">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top">
            <div style="display:inline-block; background:rgba(212, 175, 106, 0.12); border:1px solid rgba(212, 175, 106, 0.4); color:#E7C77B; font-size:10px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:4px 12px; border-radius:20px; margin-bottom:10px;">
              ● Verification in Progress
            </div>
            <div style="font-size:12.5px; color:#A8988B; margin-bottom:6px; font-weight:500;">
              Rayka Imitation Jewellery • Order #{{ $order->order_number }}
            </div>
            <div style="font-size:11px; color:#D4AF6A; text-transform:uppercase; letter-spacing:1px; margin-bottom:3px; font-weight:700;">
              Total Order Value (Awaiting Confirmation)
            </div>
            <div style="font-size:36px; font-weight:900; color:#FAF7F0; font-family:ui-monospace,Menlo,Consolas,monospace; letter-spacing:-0.5px; line-height:1.1;">
              ₹{{ number_format($order->total_amount, 2) }}
            </div>
          </td>
          <td align="right" valign="middle" style="width:60px;">
            <div style="width:48px; height:48px; background:rgba(212, 175, 106, 0.1); border:1px solid rgba(212, 175, 106, 0.35); border-radius:50%; text-align:center; display:inline-block; line-height:48px;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#E7C77B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
            </div>
          </td>
        </tr>
      </table>
    </div>
  @endif

  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  
  @if($isPaid)
    <p class="message-text">
      Thank you for choosing <strong>Rayka Imitation Jewellery</strong>. Your royal order <strong>#{{ $order->order_number }}</strong> and payment have been confirmed. Below is your official receipt summary:
    </p>
  @elseif($isCod)
    <p class="message-text">
      Thank you for choosing <strong>Rayka Imitation Jewellery</strong>. Your order <strong>#{{ $order->order_number }}</strong> has been booked for Cash on Delivery. Please keep <strong>₹{{ number_format($order->total_amount, 2) }}</strong> ready when our courier partner arrives.
    </p>
  @else
    <p class="message-text">
      Thank you for choosing <strong>Rayka Imitation Jewellery</strong>. Your royal order <strong>#{{ $order->order_number }}</strong> has been recorded and your payment screenshot proof has been submitted to our concierge desk for bank verification. Below is your order summary:
    </p>
  @endif

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
        <td class="val">{{ $paymentMethod }}</td>
      </tr>
      <tr>
        <td class="lbl">Payment Status</td>
        <td class="val">
          @if($isPaid)
            <span style="color:#10B981; font-weight:700;">✓ Verified & Confirmed</span>
          @elseif($isCod)
            <span style="color:#38BDF8; font-weight:700;">🚚 Pay on Delivery</span>
          @else
            <span style="color:#E7C77B; font-weight:700; background:rgba(212,175,106,0.12); padding:3px 10px; border-radius:4px; border:1px solid rgba(212,175,106,0.35); font-size:11px;">Verification in Progress</span>
          @endif
        </td>
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
        <td class="lbl" style="font-size:12px; color:#D4AF6A; font-weight:800;">
          {{ $isPaid ? 'Total Paid' : ($isCod ? 'Total Payable on Delivery' : 'Total Order Value') }}
        </td>
        <td class="val" style="font-size:18px; color:{{ $isPaid ? '#10B981' : '#D4AF6A' }}; font-weight:900; font-family:monospace;">
          ₹{{ number_format($order->total_amount, 2) }}
        </td>
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

  @if($isPaid)
    <div class="alert-box alert-success">
      <strong>Payment Approved:</strong> Your order is now queued for careful hallmark inspection, royal packaging, and dispatch. You will receive tracking details once your parcel is handed to the courier.
    </div>
  @elseif($isCod)
    <div class="alert-box alert-info">
      <strong>Order Confirmed for Dispatch:</strong> Your order is being processed for shipping. Payment will be collected in cash or UPI by the delivery agent at your doorstep.
    </div>
  @else
    <div class="alert-box" style="background:rgba(212,175,106,0.06); border-left:4px solid #D4AF6A; border:1px solid rgba(212,175,106,0.2); border-left-width:4px; color:#FAF7F0; padding:15px 18px; border-radius:8px; margin:22px 0;">
      <strong style="color:#E7C77B; font-size:13px; display:block; margin-bottom:4px;">Next Step — Accounts Verification in Progress:</strong>
      <span style="font-size:12.5px; color:#E2D9D0; line-height:1.6;">Our accounts concierge is currently reviewing your submitted payment proof against our bank ledger. As soon as the payment is approved, you will receive your official <strong>Payment Confirmation Email with your attached GST Tax Invoice</strong>, and your jewellery will proceed to dispatch.</span>
    </div>
  @endif

  <div class="btn-wrap">
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn">Track Order Status →</a>
  </div>
@endsection
