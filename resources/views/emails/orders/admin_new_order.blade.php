@extends('emails.layouts.master', [
    'emailTitle' => 'New Order Alert #' . $order->order_number . ' — Rayka Admin',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>',
    'headerTitle' => 'New Customer Order Received',
    'headerSubtitle' => 'Order #' . $order->order_number . ' requires payment verification'
])

@section('content')
  <div class="greeting">Administrator Alert,</div>
  <p class="message-text">
    A new customer order has been booked on the storefront. Please review the customer's payment proof and verify the bank credit.
  </p>

  <div class="lg-box">
    <div class="lg-box-header">Order & Payment Overview</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono" style="color:#D4AF6A; font-weight:800;">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">Customer Name</td>
        <td class="val">{{ $order->address?->name ?? $order->user?->name ?? 'Guest Patron' }}</td>
      </tr>
      <tr>
        <td class="lbl">Customer Contact</td>
        <td class="val">{{ $order->address?->mobile }} | {{ $order->address?->email ?? $order->user?->email }}</td>
      </tr>
      <tr>
        <td class="lbl">Payable Amount</td>
        <td class="val" style="color:#10B981; font-weight:800; font-size:15px;">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
      <tr>
        <td class="lbl">UTR / Ref</td>
        <td class="val font-mono">{{ $order->payment?->transaction_reference ?: 'Not Provided' }}</td>
      </tr>
      <tr>
        <td class="lbl">Payment Proof</td>
        <td class="val">
          @if($order->payment?->screenshot_path)
            <span style="color:#10B981; font-weight:700;">✓ Screenshot Attached</span>
          @else
            <span style="color:#EF4444; font-weight:700;">✕ No Proof Uploaded</span>
          @endif
        </td>
      </tr>
    </table>
  </div>

  <div class="lg-box">
    <div class="lg-box-header">Ordered Items ({{ $order->items->count() }})</div>
    <table class="detail-table">
      @foreach($order->items as $item)
      <tr>
        <td class="lbl" style="width:65%;">
          <strong style="color:#FFFFFF;">{{ $item->product_name }}</strong><br>
          <span style="font-size:10px; color:#A8988B;">SKU: {{ $item->product_sku }} | Qty: {{ $item->quantity }}</span>
        </td>
        <td class="val">₹{{ number_format($item->subtotal, 2) }}</td>
      </tr>
      @endforeach
    </table>
  </div>

  <div class="btn-wrap">
    <a href="{{ route('admin.orders.show', $order->id) }}" class="cta-btn">Review & Verify in Admin Portal</a>
  </div>
@endsection
