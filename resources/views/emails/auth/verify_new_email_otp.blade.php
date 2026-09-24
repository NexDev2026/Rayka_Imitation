@extends('emails.layouts.master', [
    'emailTitle' => 'Verify New Email Address — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>',
    'headerTitle' => 'Email Address Verification',
    'headerSubtitle' => 'Confirm your new email address'
])

@section('content')
  <div class="greeting">Confirm Your New Email Address</div>
  <p class="message-text">
    You have requested to change the primary email address for your Rayka royal account. To confirm ownership and finalize this update, please enter the verification code below:
  </p>

  <div class="otp-wrapper">
    <div class="otp-bg">
      <div class="otp-label">Verification Code</div>
      <div class="otp-code">{{ $otp }}</div>
      <div class="otp-expiry">Valid for the next 15 minutes</div>
    </div>
  </div>

  <div class="alert-box alert-info">
    <strong>Security Alert:</strong> Once confirmed, all future order confirmations, dispatch updates, and royal communications will be delivered to this email address.
  </div>

  <p class="message-text" style="font-size:12.5px; color:#8C7E72; margin-bottom:0;">
    If you did not initiate this request from your Rayka account settings, please sign in to your existing account immediately to review your account security.
  </p>
@endsection