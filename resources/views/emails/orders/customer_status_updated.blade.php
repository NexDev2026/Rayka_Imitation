@php
  $currentStatus = (string) ($status ?? '');
  $statusIcon = match($currentStatus) {
    'Confirmed' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    'Processing' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
    'Shipped' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#38BDF8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>',
    'Delivered' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>',
    default => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
  };
@endphp

@extends('emails.layouts.master', [
    'emailTitle' => ($status === 'Confirmed' ? 'Payment Confirmed & Tax Invoice — Order #' : 'Order Status: ' . $status . ' — #') . $order->order_number . ' — Rayka',
    'headerIcon' => $statusIcon,
    'headerTitle' => $status === 'Confirmed' ? 'Payment Confirmed' : 'Order Status: ' . $status,
    'headerSubtitle' => $status === 'Confirmed' ? 'Tax Invoice & Payment Receipt — Order #' . $order->order_number : 'Latest progress on Order #' . $order->order_number
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
  "orderStatus": "http://schema.org/Order{{ $status === 'Delivered' ? 'Delivered' : ($status === 'Shipped' ? 'InTransit' : 'Processing') }}",
  "potentialAction": {
    "{{ '@type' }}": "ViewAction",
    "name": "Track Order",
    "target": "{{ route('order.track', ['order_number' => $order->order_number]) }}"
  }
}
</script>
@endsection

@section('content')
  @if($status === 'Confirmed')
  <!-- Razorpay-Style SaaS Payment Card -->
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
  @endif

  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  
  @if($status === 'Confirmed')
    <p class="message-text">
      We are delighted to confirm that your payment of <strong style="color:#D4AF6A; font-size:15px;">₹{{ number_format($order->total_amount, 2) }}</strong> for Order <strong>#{{ $order->order_number }}</strong> has been verified and settled by our accounts concierge!
    </p>

    <!-- Attached Invoice Alert -->
    <div class="alert-box alert-success" style="margin-bottom:20px;">
      <strong>Official Tax Invoice Attached:</strong> Your computer-generated GST Tax Invoice (PDF) has been generated and attached directly to this email. You can also download or print it anytime using the button below.
    </div>

    <!-- Official In-Email Tax Invoice Card -->
    <div class="lg-box" style="margin-top:16px; border:1px solid #D4AF6A;">
      <div class="lg-box-header" style="background:#241610; color:#E7C77B; display:flex; justify-content:space-between; align-items:center;">
        <span>TAX INVOICE & RECEIPT</span>
        <span style="font-size:10px; font-weight:700; color:#10B981; background:rgba(16,185,129,0.15); padding:2px 8px; border-radius:4px; border:1px solid #10B981;">✓ VERIFIED & PAID</span>
      </div>

      <!-- Invoice Meta Grid -->
      <table class="detail-table" style="border-bottom:1px solid #332017;">
        <tr>
          <td class="lbl">Invoice Number</td>
          <td class="val font-mono" style="color:#FFFFFF; font-weight:700;">#{{ $order->order_number }}</td>
        </tr>
        <tr>
          <td class="lbl">Payment Date</td>
          <td class="val">{{ $order->payment?->verified_at ? $order->payment->verified_at->format('d M, Y \a\t h:i A') : now()->format('d M, Y \a\t h:i A') }} IST</td>
        </tr>
        <tr>
          <td class="lbl">Payment Method</td>
          <td class="val" style="color:#D4AF6A;">{{ $order->payment?->payment_method ?: 'UPI QR Code' }}</td>
        </tr>
        @if($order->payment?->transaction_reference)
        <tr>
          <td class="lbl">UTR / Ref No.</td>
          <td class="val font-mono">{{ $order->payment->transaction_reference }}</td>
        </tr>
        @endif
        <tr>
          <td class="lbl">HSN Classification</td>
          <td class="val">7117 (Imitation Jewellery — 3% GST Included)</td>
        </tr>
      </table>

      <!-- Billing & Shipping Information -->
      @if($order->address)
      <div style="padding:14px 18px; border-bottom:1px solid #332017; font-size:12px; line-height:1.6; color:#D8CDC2;">
        <span style="font-size:10px; text-transform:uppercase; font-weight:700; color:#D4AF6A; letter-spacing:1px; display:block; margin-bottom:4px;">Billed & Shipped To:</span>
        <strong style="color:#FFFFFF; font-size:13px;">{{ $order->address->name }}</strong><br>
        {{ $order->address->formatted_address }}<br>
        <span style="color:#A8988B;">Phone: {{ $order->address->mobile }} | Email: {{ $order->address->email }}</span>
      </div>
      @endif

      <!-- Itemized Table -->
      <div style="padding:10px 14px 4px;">
        <div style="font-size:10px; text-transform:uppercase; font-weight:700; color:#D4AF6A; letter-spacing:1px; margin-bottom:8px;">Itemized Jewellery Breakdown:</div>
        <table style="width:100%; border-collapse:collapse; font-size:11.5px; color:#E2D9D0;">
          <thead>
            <tr style="border-bottom:1px solid #3D261B; text-align:left; color:#D4AF6A; font-size:10px; text-transform:uppercase;">
              <th style="padding:6px 4px;">Creation Details</th>
              <th style="padding:6px 4px; text-align:right;">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $item)
            <tr style="border-bottom:1px solid #291811;">
              <td style="padding:8px 4px; vertical-align:middle;">
                <strong style="color:#FFFFFF; font-size:12px; display:block;">{{ $item->product_name }}</strong>
                @if($item->variant_info)
                  <div style="font-size:10px; color:#A8988B;">Variant: {{ $item->variant_info }}</div>
                @endif
                <div style="font-size:10px; color:#A8988B; margin-top:2px;">
                  ₹{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}
                </div>
              </td>
              <td style="padding:8px 4px; text-align:right; font-family:monospace; font-weight:700; color:#FFFFFF; font-size:13px; vertical-align:middle; white-space:nowrap;">
                ₹{{ number_format($item->subtotal, 2) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Financial Totals -->
      <table class="detail-table" style="margin-top:6px;">
        <tr>
          <td class="lbl">Subtotal</td>
          <td class="val">₹{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if($order->coupon_discount > 0)
        <tr>
          <td class="lbl" style="color:#10B981;">Coupon Discount ({{ $order->coupon_code }})</td>
          <td class="val" style="color:#10B981; font-weight:700;">− ₹{{ number_format($order->coupon_discount, 2) }}</td>
        </tr>
        @endif
        <tr>
          <td class="lbl">Shipping & Handling</td>
          <td class="val">{{ $order->shipping_fee == 0 ? 'FREE' : '₹' . number_format($order->shipping_fee, 2) }}</td>
        </tr>
        <tr style="background:#241610;">
          <td class="lbl" style="font-size:13px; font-weight:800; color:#FFFFFF; padding-top:10px; padding-bottom:10px;">Grand Total (Paid)</td>
          <td class="val" style="font-size:16px; color:#D4AF6A; font-weight:900; padding-top:10px; padding-bottom:10px;">₹{{ number_format($order->total_amount, 2) }}</td>
        </tr>
      </table>
    </div>

    <!-- Download Invoice Button -->
    <div class="btn-wrap" style="margin-top:24px;">
      <a href="{{ route('order.track.invoice', $order->order_number) }}" class="cta-btn" style="background:linear-gradient(135deg, #D4AF6A 0%, #996E2E 100%); color:#1C100B!important; box-shadow:0 4px 15px rgba(212,175,106,0.35);">
        📥 Download Official Tax Invoice (PDF)
      </a>
    </div>

  @elseif($status === 'Processing')
    <p class="message-text">
      Your royal creations for Order <strong>#{{ $order->order_number }}</strong> are now in our master workshop. Each piece is undergoing 1-gram micro gold quality inspection, polishing, and bespoke velvet jewelry packaging.
    </p>

    <!-- Order Details Box -->
    <div class="lg-box" style="margin-top:20px;">
      <div class="lg-box-header">Order Summary</div>
      <table class="detail-table">
        <tr>
          <td class="lbl">Order Number</td>
          <td class="val font-mono">#{{ $order->order_number }}</td>
        </tr>
        <tr>
          <td class="lbl">Items Count</td>
          <td class="val">{{ $order->items->sum('quantity') }} creations</td>
        </tr>
        <tr>
          <td class="lbl">Total Value</td>
          <td class="val" style="color:#D4AF6A; font-weight:800;">₹{{ number_format($order->total_amount, 2) }}</td>
        </tr>
        @if($order->address)
        <tr>
          <td class="lbl">Shipping Destination</td>
          <td class="val" style="font-size:11px;">{{ $order->address->city }}, {{ $order->address->state }} ({{ $order->address->pincode }})</td>
        </tr>
        @endif
      </table>
    </div>

  @elseif($status === 'Shipped')
    <p class="message-text">
      Exciting news! Your royal ornaments have been packed securely in tamper-evident velvet cases and handed over to our premium courier partner.
    </p>

    <div class="lg-box" style="margin-top:20px;">
      <div class="lg-box-header">Logistics & Tracking Details</div>
      <table class="detail-table">
        <tr>
          <td class="lbl">Courier Partner</td>
          <td class="val" style="color:#D4AF6A; font-weight:700;">{{ $order->tracking_carrier ?: 'Express Logistics' }}</td>
        </tr>
        <tr>
          <td class="lbl">Airway Bill (AWB)</td>
          <td class="val font-mono" style="font-size:14px; font-weight:800; color:#FFFFFF;">{{ $order->tracking_number ?: 'In Transit' }}</td>
        </tr>
        <tr>
          <td class="lbl">Estimated Delivery</td>
          <td class="val">3 to 5 Business Days</td>
        </tr>
      </table>
    </div>

    @if($order->tracking_url)
    <div class="btn-wrap">
      <a href="{{ $order->tracking_url }}" class="cta-btn">Track Consignment Live</a>
    </div>
    @endif

  @elseif($status === 'Delivered')
    <p class="message-text">
      Your package for Order <strong>#{{ $order->order_number }}</strong> has been successfully delivered! We hope you cherish wearing these royal handcrafted creations as much as we enjoyed crafting them.
    </p>

    <div class="alert-box alert-success">
      <strong>Royal Craftsmanship Guarantee:</strong> Your creations feature our 1-gram micro gold plating guarantee. For care instructions or inquiries, our concierge is always at your service.
    </div>

    <div class="btn-wrap">
      <a href="{{ route('account.order.detail', $order->order_number) }}" class="cta-btn">Review Your Creations</a>
    </div>

  @else
    <p class="message-text">
      Your Order <strong>#{{ $order->order_number }}</strong> status has been updated to <strong>{{ $status }}</strong>.
    </p>
  @endif

  @if(!empty($customNote))
  <div class="alert-box alert-info" style="margin-top:20px;">
    <strong>Message from Boutique Concierge:</strong><br>
    {{ $customNote }}
  </div>
  @endif

  <div class="btn-wrap" style="margin-top:24px;">
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn" style="background:transparent; color:#D4AF6A!important; border:1px solid #D4AF6A; box-shadow:none;">View Full Order Tracking</a>
  </div>
@endsection
