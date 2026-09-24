@extends('emails.layouts.master', [
    'emailTitle' => 'Order #' . $order->order_number . ' Cancelled by Customer — Rayka Admin',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
    'headerTitle' => 'Order Cancelled by Customer',
    'headerSubtitle' => 'Customer initiated cancellation for Order #' . $order->order_number
])

@section('content')
  <div class="greeting">Administrator Alert,</div>
  <p class="message-text">
    A customer has requested cancellation for Order <strong>#{{ $order->order_number }}</strong> via their account portal. The reserved jewelry items have been automatically restored to available stock.
  </p>

  <!-- Cancellation Reason Card -->
  <div class="alert-box alert-warning">
    <strong style="display:block; text-transform:uppercase; font-size:11px; letter-spacing:1px; margin-bottom:4px; color:#F59E0B;">
      Customer's Stated Reason:
    </strong>
    <p style="font-size:14px; font-weight:700; color:#FFFFFF; margin-bottom:4px;">
      {{ $reason }}
    </p>
    @if(!empty($comment))
      <p style="font-size:12px; color:#E2D9D0; font-style:italic;">
        "{{ $comment }}"
      </p>
    @endif
  </div>

  <div class="lg-box">
    <div class="lg-box-header">Order & Customer Information</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono" style="color:#D4AF6A; font-weight:800;">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">Customer Name</td>
        <td class="val">{{ $order->address?->name ?? $order->user?->name }}</td>
      </tr>
      <tr>
        <td class="lbl">Customer Contact</td>
        <td class="val">{{ $order->address?->mobile }} | {{ $order->address?->email ?? $order->user?->email }}</td>
      </tr>
      <tr>
        <td class="lbl">Order Amount</td>
        <td class="val" style="color:#D4AF6A; font-weight:800;">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
      <tr>
        <td class="lbl">Payment Method</td>
        <td class="val">{{ $order->payment?->payment_method ?: 'UPI QR' }}</td>
      </tr>
      <tr>
        <td class="lbl">Inventory Status</td>
        <td class="val" style="color:#10B981; font-weight:700;">✓ Automatically Restored</td>
      </tr>
    </table>
  </div>

  <div class="btn-wrap">
    <a href="{{ route('admin.orders.show', $order->id) }}" class="cta-btn">View Order in Admin Panel</a>
  </div>
@endsection
