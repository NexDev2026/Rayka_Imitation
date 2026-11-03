@php
  $paymentStatus = (string) ($order->payment?->status ?? 'Pending');
  $paymentMethod = (string) ($order->payment?->payment_method ?? ($order->payment_method ?: 'Static QR / UPI'));
  $isPaid = in_array(strtolower($paymentStatus), ['confirmed', 'success', 'paid']);
  $isCod = $order->isCod();

  $emailTitle = $isPaid 
    ? ('Payment Confirmed — Order #' . $order->order_number . ' — Rayka')
    : ($isCod 
        ? ('COD Order Received (Advance Verification Pending) — Order #' . $order->order_number . ' — Rayka')
        : ('Order Received (Payment Verification Pending) — Order #' . $order->order_number . ' — Rayka'));

  $headerTitle = $isPaid 
    ? 'Order & Payment Confirmed' 
    : ($isCod 
        ? 'COD Order Placed (Verification Pending)' 
        : 'Order Received — Verification Pending');

  $headerSubtitle = $isPaid 
    ? ('Payment Receipt — Order #' . $order->order_number) 
    : ($isCod 
        ? ('Order #' . $order->order_number . ' • ' . $order->advance_percentage . '% Advance Token Verification in Progress') 
        : ('Order #' . $order->order_number . ' • Awaiting Admin Verification'));

  $headerIcon = $isPaid
    ? '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
    : '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
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
  "{{ '@type' }}": "Invoice",
  "accountId": "{{ $order->order_number }}",
  "name": "Rayka Imitation Jewellery bill",
  "description": "Rayka Order #{{ $order->order_number }}",
  "paymentDue": "{{ \Carbon\Carbon::parse($order->created_at)->toIso8601String() }}",
  "paymentStatus": "{{ $isPaid ? 'PaymentAutomaticallyApplied' : 'PaymentDue' }}",
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
  },
  "referencesOrder": {
    "{{ '@type' }}": "Order",
    "merchant": {
      "{{ '@type' }}": "Organization",
      "name": "Rayka Imitation Jewellery"
    },
    "orderNumber": "{{ $order->order_number }}",
    "priceCurrency": "INR",
    "price": "{{ number_format($order->total_amount, 2, '.', '') }}",
    "orderDate": "{{ \Carbon\Carbon::parse($order->created_at)->toIso8601String() }}",
    "orderStatus": "https://schema.org/Order{{ $isPaid ? 'Processing' : 'PaymentDue' }}",
    "potentialAction": {
      "{{ '@type' }}": "ViewAction",
      "name": "Track Order",
      "target": "{{ route('order.track', ['order_number' => $order->order_number]) }}"
    }
  }
}
</script>
@endsection

@section('content')
  @if($isPaid)
    <!-- Paid: Royal Velvet Dark Chocolate & Gold Card -->
    <div style="background:linear-gradient(180deg, #24140C 0%, #170B06 100%); border:1px solid rgba(212, 175, 106, 0.45); border-radius:18px; padding:22px 24px; margin-bottom:28px; box-shadow:0 14px 40px rgba(0,0,0,0.6);">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top">
            <div style="display:inline-block; background:rgba(16,185,129,0.15); border:1px solid #10B981; color:#10B981; font-size:10px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:4px 12px; border-radius:20px; margin-bottom:10px;">
              Paid on {{ \Carbon\Carbon::parse($order->payment?->verified_at ?? $order->created_at)->format('d M') }}
            </div>
            <div style="font-size:13px; color:#A8988B; margin-bottom:6px; font-weight:500;">
              Rayka Imitation Jewellery bill • Order #{{ $order->order_number }}
            </div>
            <div style="font-size:11px; color:#D4AF6A; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px; font-weight:700;">
              Amount Paid (Full Payment)
            </div>
            <div style="font-size:36px; font-weight:900; color:#ffffff; font-family:ui-monospace,Menlo,Consolas,monospace; letter-spacing:-0.5px; line-height:1.1;">
              ₹{{ number_format($order->total_amount, 2) }}
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
  @elseif($isCod)
    <!-- COD: Royal Velvet Dark Chocolate & Gold Card with Advance Token & Balance Due -->
    <div style="background:linear-gradient(180deg, #24140C 0%, #170B06 100%); border:1px solid rgba(212, 175, 106, 0.45); border-radius:18px; padding:22px 24px; margin-bottom:28px; box-shadow:0 14px 40px rgba(0,0,0,0.6);">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td valign="top">
            <div style="display:inline-block; background:rgba(212, 175, 106, 0.15); border:1px solid #D4AF6A; color:#E7C77B; font-size:10px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:4px 12px; border-radius:20px; margin-bottom:10px;">
              ● {{ $isPaid ? 'Cash on Delivery (Advance Token Confirmed)' : 'Cash on Delivery (Advance Verification Pending)' }}
            </div>
            <div style="font-size:13px; color:#A8988B; margin-bottom:8px; font-weight:500;">
              Rayka Imitation Jewellery • Order #{{ $order->order_number }}
            </div>
            <div style="font-size:11px; color:#E7C77B; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px; font-weight:700;">
              1. Advance Token ({{ $order->advance_percentage }}% Online)
            </div>
            <div style="font-size:30px; font-weight:900; color:#FFFFFF; font-family:ui-monospace,Menlo,Consolas,monospace; letter-spacing:-0.5px; line-height:1.1; margin-bottom:10px;">
              ₹{{ number_format($order->advance_amount, 2) }}
            </div>
            <div style="font-size:11px; color:#C4B5A5; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px; font-weight:700;">
              2. Remaining Balance Payable on Delivery
            </div>
            <div style="font-size:22px; font-weight:900; color:#E7C77B; font-family:ui-monospace,Menlo,Consolas,monospace; line-height:1.1;">
              ₹{{ number_format($order->balance_amount, 2) }}
            </div>
          </td>
          <td align="right" valign="top" style="width:70px; padding-left:10px;">
            <div style="display:inline-block; background:rgba(212, 175, 106, 0.12); border:1.5px solid #D4AF6A; border-radius:12px; padding:6px 12px; text-align:center;">
              <span style="color:#E7C77B; font-size:14px; font-weight:900; letter-spacing:1px; display:block; white-space:nowrap; line-height:1.2;">COD</span>
            </div>
          </td>
        </tr>
      </table>
    </div>
  @else
    <!-- Pending Verification: Royal Champagne Gold Luxury Order Card -->
    <div style="background:linear-gradient(180deg, #1C110A 0%, #120905 100%); border:1px solid rgba(212, 175, 106, 0.45); border-radius:18px; padding:24px 26px; margin-bottom:28px; box-shadow:0 14px 40px rgba(0,0,0,0.6), inset 0 1px 0 rgba(212, 175, 106, 0.15);">
      <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
          <td valign="middle">
            <div style="display:inline-block; background:rgba(212, 175, 106, 0.12); border:1px solid rgba(212, 175, 106, 0.45); color:#E7C77B; font-size:10px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; padding:4px 14px; border-radius:30px;">
              ● Verification in Progress
            </div>
          </td>
          <td align="right" valign="middle">
            <span style="font-family:ui-monospace,Menlo,Consolas,monospace; font-size:11px; color:#A8988B; letter-spacing:0.5px;">Order #{{ $order->order_number }}</span>
          </td>
        </tr>
      </table>
      <div style="font-size:11px; color:#D4AF6A; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:4px; font-weight:700;">
        Total Order Value
      </div>
      <div style="font-size:38px; font-weight:900; color:#FFFFFF; font-family:'Outfit',ui-monospace,-apple-system,sans-serif; letter-spacing:-0.5px; line-height:1.15; margin-bottom:14px;">
        ₹{{ number_format($order->total_amount, 2) }}
      </div>
      <div style="padding-top:12px; border-top:1px solid rgba(212, 175, 106, 0.15); font-size:12px; color:#C4B5A5;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td valign="middle" style="font-size:12px; color:#A8988B;">
              Payment Method: <strong style="color:#FAF7F0;">{{ $paymentMethod }}</strong>
            </td>
            <td align="right" valign="middle" style="font-size:11px; color:#E7C77B; font-weight:600; letter-spacing:0.5px;">
              Accounts Concierge Desk
            </td>
          </tr>
        </table>
      </div>
    </div>
  @endif

  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  
  @if($isPaid)
    <p class="message-text">
      Thank you for choosing <strong>Rayka Imitation Jewellery</strong>. Your royal order <strong>#{{ $order->order_number }}</strong> and payment have been confirmed. Below is your official receipt summary:
    </p>
  @elseif($isCod)
    <p class="message-text">
      Thank you for choosing <strong>Rayka Imitation Jewellery</strong>. Your order <strong>#{{ $order->order_number }}</strong> has been booked for Cash on Delivery. Your mandatory <strong>{{ $order->advance_percentage }}% advance token payment (₹{{ number_format($order->advance_amount, 2) }})</strong> has been received online. The remaining balance of <strong>₹{{ number_format($order->balance_amount, 2) }}</strong> will be payable upon delivery in Cash or via courier Delivery UPI QR.
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
            <span style="color:#10B981; font-weight:700;">Verified & Confirmed</span>
          @elseif($isCod)
            <span style="color:#E7C77B; font-weight:700; background:rgba(212,175,106,0.12); padding:3px 10px; border-radius:4px; border:1px solid rgba(212,175,106,0.35); font-size:11px;">Advance Token Verification in Progress</span>
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
        <td class="lbl" style="color:#10B981;">Coupon ({{ $order->coupon_code }})</td>
        <td class="val" style="color:#10B981; font-weight:700;">− ₹{{ number_format($order->coupon_discount, 2) }}</td>
      </tr>
      @endif
      <tr>
        <td class="lbl">Shipping Fee</td>
        <td class="val">{{ $order->shipping_fee == 0 ? 'FREE' : '₹' . number_format($order->shipping_fee, 2) }}</td>
      </tr>
      @if($order->cod_extra_charge > 0)
      <tr>
        <td class="lbl" style="color:#D4AF6A;">COD Courier Handling Fee</td>
        <td class="val" style="color:#D4AF6A; font-weight:700;">+ ₹{{ number_format($order->cod_extra_charge, 2) }}</td>
      </tr>
      @endif
      <tr style="border-top:1px solid rgba(212,175,106,0.3);">
        <td class="lbl" style="font-size:12px; color:#D4AF6A; font-weight:800;">
          Total Order Value
        </td>
        <td class="val" style="font-size:18px; color:#D4AF6A; font-weight:900; font-family:monospace;">
          ₹{{ number_format($order->total_amount, 2) }}
        </td>
      </tr>
      @if($isCod)
      <tr style="background:rgba(212,175,106,0.12); border-top:1px dashed #D4AF6A;">
        <td class="lbl" style="color:#E7C77B; font-weight:700;">1. Advance Token Paid ({{ $order->advance_percentage }}%)</td>
        <td class="val" style="color:#E7C77B; font-weight:800; font-family:monospace;">₹{{ number_format($order->advance_amount, 2) }}</td>
      </tr>
      <tr style="background:rgba(212,175,106,0.18);">
        <td class="lbl" style="color:#ffffff; font-weight:800;">2. Remaining Due on Delivery</td>
        <td class="val" style="color:#ffffff; font-weight:900; font-size:16px; font-family:monospace;">₹{{ number_format($order->balance_amount, 2) }}</td>
      </tr>
      @endif
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
    <div class="alert-box" style="background:rgba(212, 175, 106, 0.08); border-left:4px solid #D4AF6A; border:1px solid rgba(212, 175, 106, 0.25); border-left-width:4px; color:#FAF7F0; padding:15px 18px; border-radius:10px; margin:22px 0;">
      <strong style="color:#E7C77B; font-size:13px; display:block; margin-bottom:4px; font-weight:800;">Advance Token Verification in Progress:</strong>
      <span style="font-size:12.5px; color:#E2D9D0; line-height:1.6;">We have recorded your booking and submitted advance token payment proof (<strong style="color:#FFFFFF;">₹{{ number_format($order->advance_amount, 2) }}</strong>). Our accounts concierge is cross-verifying this transaction with our official bank ledger. As soon as the advance is verified, your order will be Confirmed and prepared for dispatch. The remaining balance of <strong style="color:#FFFFFF;">₹{{ number_format($order->balance_amount, 2) }}</strong> will be payable upon doorstep delivery via Cash or QR.</span>
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
