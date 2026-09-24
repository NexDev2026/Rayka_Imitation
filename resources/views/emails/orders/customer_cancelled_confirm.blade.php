@extends('emails.layouts.master', [
    'emailTitle' => 'Cancellation Confirmation: Order #' . $order->order_number . ' — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
    'headerTitle' => 'Order Cancellation Confirmed',
    'headerSubtitle' => 'Order #' . $order->order_number . ' has been cancelled'
])

@section('content')
  <div class="greeting">Dear {{ $order->address?->name ?? $order->user?->name ?? 'Valued Patron' }},</div>
  <p class="message-text">
    As per your request, your Order <strong>#{{ $order->order_number }}</strong> has been successfully cancelled. The reserved jewelry pieces have been removed from your booking.
  </p>

  <div class="alert-box alert-warning">
    <strong>Cancellation Reason:</strong><br>
    {{ $reason }}
  </div>

  <div class="lg-box">
    <div class="lg-box-header">Cancelled Order Summary</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Order Number</td>
        <td class="val font-mono">#{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td class="lbl">Total Value</td>
        <td class="val">₹{{ number_format($order->total_amount, 2) }}</td>
      </tr>
      <tr>
        <td class="lbl">Status</td>
        <td class="val" style="color:#EF4444; font-weight:700;">Cancelled</td>
      </tr>
    </table>
  </div>

  <p class="message-text">
    If you made an advance UPI transfer that requires refund reconciliation, our accounts team will verify and initiate the process within 24–48 hours. Please contact our concierge if you have questions.
  </p>

  <div class="btn-wrap">
    <a href="{{ route('home') }}" class="cta-btn">Browse New Collections</a>
  </div>
@endsection
