@php
  $paymentStatus = (string) ($order->payment?->status ?? 'Pending');
  $paymentMethod = (string) ($order->payment?->payment_method ?? ($order->payment_method ?: 'Static QR / UPI'));
  $isPaid = in_array(strtolower($paymentStatus), ['confirmed', 'success', 'paid']);
  $isCod = $order->isCod();

  $emailTitle = $isPaid 
    ? ('Order Confirmed — #' . $order->order_number . ' — Rayka')
    : ($isCod 
        ? ('COD Order Received — #' . $order->order_number . ' — Rayka')
        : ('Order Received — #' . $order->order_number . ' — Rayka'));

  $headerTitle = $isPaid 
    ? 'Order Confirmed' 
    : ($isCod 
        ? 'COD Order Booked' 
        : 'Order Received');

  $headerSubtitle = $isPaid 
    ? ('Payment Receipt — Order #' . $order->order_number) 
    : ($isCod 
        ? ('Order #' . $order->order_number . ' • ' . $order->advance_percentage . '% Token Advance') 
        : ('Order #' . $order->order_number . ' • Verification Pending'));

  $headerIcon = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
@endphp

@extends('emails.layouts.master', [
    'emailTitle' => $emailTitle,
    'headerIcon' => $headerIcon,
    'headerTitle' => $headerTitle,
    'headerSubtitle' => $headerSubtitle
])

@section('content')
  <!-- Compact Luxury Hero Card -->
  <div style="background:linear-gradient(135deg, #24140C 0%, #170B06 100%); border:1px solid rgba(212, 175, 106, 0.45); border-radius:16px; padding:18px 20px; margin-bottom:22px; box-shadow:0 10px 30px rgba(0,0,0,0.65);">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top">
          <div style="display:inline-block; background:rgba(212, 175, 106, 0.16); border:1px solid #D4AF6A; color:#E7C77B; font-size:10px; font-weight:800; letter-spacing:1px; text-transform:uppercase; padding:3px 10px; border-radius:20px; margin-bottom:8px;">
            {{ $isPaid ? 'Payment Confirmed' : ($isCod ? 'Cash on Delivery Booked' : 'Verification in Progress') }}
          </div>
          <div style="font-size:11.5px; color:#A8988B; margin-bottom:4px; font-weight:500;">
            Order #{{ $order->order_number }} • {{ \Carbon\Carbon::parse($order->created_at)->format('d M, Y') }}
          </div>
          @if($isCod)
            <div style="font-size:10.5px; color:#D4AF6A; text-transform:uppercase; letter-spacing:1px; font-weight:700;">
              Advance Token Paid Online: <span style="font-size:18px; color:#FFFFFF; font-weight:900; font-family:monospace;">₹{{ number_format($order->advance_amount, 2) }}</span>
            </div>
            <div style="font-size:11px; color:#E7C77B; margin-top:4px; font-weight:600;">
              Doorstep Balance Due: <strong style="color:#FFFFFF; font-family:monospace; font-size:14px;">₹{{ number_format($order->balance_amount, 2) }}</strong>
            </div>
          @else
            <div style="font-size:10.5px; color:#D4AF6A; text-transform:uppercase; letter-spacing:1px; font-weight:700;">
              Total Order Value
            </div>
            <div style="font-size:26px; font-weight:900; color:#FFFFFF; font-family:monospace; letter-spacing:-0.5px; line-height:1.1;">
              ₹{{ number_format($order->total_amount, 2) }}
            </div>
          @endif
        </td>
        <td align="right" valign="top" style="width:50px;">
          <div style="display:inline-block; background:rgba(212, 175, 106, 0.12); border:1.5px solid #D4AF6A; border-radius:12px; padding:6px 12px; text-align:center;">
            <span style="color:#E7C77B; font-size:13px; font-weight:900; letter-spacing:1px; display:block; white-space:nowrap; line-height:1.2;">{{ $isCod ? 'COD' : 'UPI' }}</span>
          </div>
        </td>
      </tr>
    </table>
  </div>

  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  
  @if($isPaid)
    <p class="message-text">
      Thank you for choosing <strong>Rayka Imitation Jewellery</strong>. Your order <strong>#{{ $order->order_number }}</strong> has been received and confirmed.
    </p>
  @elseif($isCod)
    <p class="message-text">
      Thank you for your order! Your Cash on Delivery booking for Order <strong>#{{ $order->order_number }}</strong> has been recorded. Our concierge is verifying your advance token payment proof (<strong>₹{{ number_format($order->advance_amount, 2) }}</strong>). The remaining balance of <strong>₹{{ number_format($order->balance_amount, 2) }}</strong> will be payable upon doorstep delivery.
    </p>
  @else
    <p class="message-text">
      Thank you for your order! Your Order <strong>#{{ $order->order_number }}</strong> has been recorded. Our accounts desk is reviewing your payment proof against bank credits and will confirm dispatch shortly.
    </p>
  @endif

  <!-- Compact Order & Financial Summary Box -->
  <div class="lg-box" style="margin-top:16px; border:1px solid rgba(212, 175, 106, 0.35); border-radius:14px; overflow:hidden;">
    <div style="background:#241610; border-bottom:1px solid rgba(212, 175, 106, 0.25); padding:9px 14px;">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="left" style="color:#E7C77B; font-size:10.5px; font-weight:800; letter-spacing:1px; text-transform:uppercase;">
            Order Summary ({{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }})
          </td>
          <td align="right" style="color:#A8988B; font-size:10.5px; font-family:monospace;">
            #{{ $order->order_number }}
          </td>
        </tr>
      </table>
    </div>

    <!-- Items List -->
    <table class="order-items-table">
      @foreach($order->items as $item)
        <tr>
          <td style="padding:10px 14px;">
            <div style="font-weight:700; color:#FFFFFF; font-size:12px;">{{ $item->product_name }}</div>
            <div style="font-size:10.5px; color:#A8988B;">
              @if($item->variant_info) {{ $item->variant_info }} • @endif Qty: {{ $item->quantity }}
            </div>
          </td>
          <td align="right" style="padding:10px 14px; font-weight:700; color:#F8F4EE; font-family:monospace; white-space:nowrap; font-size:12px;">
            ₹{{ number_format($item->subtotal, 2) }}
          </td>
        </tr>
      @endforeach
    </table>

    <!-- Totals Table -->
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
        <td class="lbl" style="color:#D4AF6A;">COD Handling Fee</td>
        <td class="val" style="color:#D4AF6A; font-weight:700;">+ ₹{{ number_format($order->cod_extra_charge, 2) }}</td>
      </tr>
      @endif
      <tr style="border-top:1px solid rgba(212,175,106,0.3); background:#241610;">
        <td class="lbl" style="font-size:12px; font-weight:800; color:#FFFFFF;">Total Order Value</td>
        <td class="val" style="font-size:15px; color:#D4AF6A; font-weight:900; font-family:monospace;">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
      @if($isCod)
      <tr style="background:rgba(212,175,106,0.12); border-top:1px dashed #D4AF6A;">
        <td class="lbl" style="color:#E7C77B; font-weight:700;">Advance Token Paid ({{ $order->advance_percentage }}%)</td>
        <td class="val" style="color:#E7C77B; font-weight:800; font-family:monospace;">₹{{ number_format($order->advance_amount, 2) }}</td>
      </tr>
      <tr style="background:rgba(212,175,106,0.18);">
        <td class="lbl" style="color:#ffffff; font-weight:800;">Balance Due on Delivery</td>
        <td class="val" style="color:#ffffff; font-weight:900; font-size:14px; font-family:monospace;">₹{{ number_format($order->balance_amount, 2) }}</td>
      </tr>
      @endif
    </table>

    <!-- Destination Short Note -->
    @if($order->address)
    <div style="padding:10px 14px; border-top:1px solid rgba(212,175,106,0.15); font-size:11px; color:#A8988B; background:rgba(0,0,0,0.15);">
      Deliver to: <strong style="color:#FAF7F0;">{{ $order->address->name }}</strong>, {{ $order->address->city }}, {{ $order->address->state }} ({{ $order->address->pincode }})
    </div>
    @endif
  </div>

  <!-- Primary CTA Button -->
  <div class="btn-wrap" style="margin-top:20px;">
    <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn">
      Track Order Status →
    </a>
  </div>
@endsection
