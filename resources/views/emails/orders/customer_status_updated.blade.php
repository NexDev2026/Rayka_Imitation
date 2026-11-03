@php
  $currentStatus = (string) ($status ?? '');
  $isCod = $order->isCod();
  $statusIcon = match($currentStatus) {
    'Confirmed' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
    'Processing' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
    'Shipped' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>',
    'Delivered' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>',
    default => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
  };

  $emailTitle = ($currentStatus === 'Confirmed')
    ? ($isCod 
        ? ('Order Confirmed (COD Advance Verified) — #' . $order->order_number . ' — Rayka') 
        : ('Payment Confirmed — Order #' . $order->order_number . ' — Rayka'))
    : ('Order Status: ' . $currentStatus . ' — #' . $order->order_number . ' — Rayka');

  $headerTitle = ($currentStatus === 'Confirmed')
    ? ($isCod ? 'Order Confirmed' : 'Payment Confirmed')
    : ('Order Status: ' . $currentStatus);

  $headerSubtitle = ($currentStatus === 'Confirmed')
    ? ($isCod 
        ? ('Order #' . $order->order_number . ' • ' . $order->advance_percentage . '% Token Verified') 
        : ('Tax Invoice & Receipt — Order #' . $order->order_number))
    : ('Latest progress on Order #' . $order->order_number);
@endphp

@extends('emails.layouts.master', [
    'emailTitle' => $emailTitle,
    'headerIcon' => $statusIcon,
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
            {{ $status === 'Confirmed' ? ($isCod ? 'Advance Verified' : 'Payment Confirmed') : ('Status: ' . $status) }}
          </div>
          <div style="font-size:11.5px; color:#A8988B; margin-bottom:4px; font-weight:500;">
            Order #{{ $order->order_number }} • {{ \Carbon\Carbon::parse($order->created_at)->format('d M, Y') }}
          </div>
          @if($status === 'Confirmed' && $isCod)
            <div style="font-size:10.5px; color:#D4AF6A; text-transform:uppercase; letter-spacing:1px; font-weight:700;">
              Advance Paid Online: <span style="font-size:18px; color:#FFFFFF; font-weight:900; font-family:monospace;">₹{{ number_format($order->advance_amount, 2) }}</span>
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
          <div style="width:42px; height:42px; background:#059669; border:2px solid #10B981; border-radius:50%; text-align:center; line-height:38px; display:inline-block; font-size:20px; color:#ffffff !important; font-weight:900; font-family:Arial,Helvetica,sans-serif;">
            &#10004;
          </div>
        </td>
      </tr>
    </table>
  </div>

  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  
  @if($status === 'Confirmed')
    @if($isCod)
      <p class="message-text">
        Your <strong>{{ $order->advance_percentage }}% advance token (₹{{ number_format($order->advance_amount, 2) }})</strong> has been verified. Your order is confirmed and currently queued for hallmark inspection and royal packaging. The remaining balance of <strong>₹{{ number_format($order->balance_amount, 2) }}</strong> will be collected upon doorstep delivery via Cash or QR.
      </p>
    @else
      <p class="message-text">
        Your payment of <strong style="color:#D4AF6A;">₹{{ number_format($order->total_amount, 2) }}</strong> has been verified. Your order is confirmed and being prepared for royal dispatch. Your official Tax Invoice (PDF) is attached.
      </p>
    @endif
  @elseif($status === 'Processing')
    <p class="message-text">
      Your creations for Order <strong>#{{ $order->order_number }}</strong> are now in our master workshop undergoing 1-gram micro gold hallmark finishing and packaging.
    </p>
  @elseif($status === 'Shipped')
    <p class="message-text">
      Your package has been dispatched via <strong>{{ $order->tracking_carrier ?: 'Express Courier' }}</strong> (AWB: <strong style="font-family:monospace;">{{ $order->tracking_number ?: 'In Transit' }}</strong>).
      @if($isCod)
        Please keep the remaining balance of <strong>₹{{ number_format($order->balance_amount, 2) }}</strong> ready for delivery.
      @endif
    </p>
  @elseif($status === 'Delivered')
    <p class="message-text">
      Your order <strong>#{{ $order->order_number }}</strong> has been successfully delivered. We hope you cherish wearing your royal creations!
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
    @if($status === 'Confirmed' && !$order->isCod())
      <a href="{{ route('order.track.invoice', $order->order_number) }}" class="cta-btn">
        Download Tax Invoice (PDF)
      </a>
    @else
      <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="cta-btn">
        Track Order Status →
      </a>
    @endif
  </div>
@endsection
