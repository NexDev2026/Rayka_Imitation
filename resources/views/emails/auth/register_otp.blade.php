@extends('emails.layouts.master', [
    'emailTitle' => 'Verify Your Royal Account — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"></path></svg>',
    'headerTitle' => 'Welcome to Rayka',
    'headerSubtitle' => 'Email verification code'
])

@section('content')
  <div class="greeting">Welcome to Rayka Imitation Jewellery!</div>
  <p class="message-text">
    Thank you for creating an account with Rayka. To activate your royal account and safeguard your orders, please verify your email address with the verification code below:
  </p>

  <div class="otp-wrapper">
    <div class="otp-bg">
      <div class="otp-label">Registration Verification Code</div>
      <div class="otp-code">{{ $otp }}</div>
      <div class="otp-expiry">Valid for the next 15 minutes</div>
    </div>
  </div>

  <div class="alert-box alert-info">
    <strong>Royal Assurance:</strong> As a verified member of Rayka, you enjoy express checkout, personalized bridal recommendations, order tracking, and exclusive preview access to new 1-gram micro gold collections.
  </div>

  <p class="message-text" style="font-size:12.5px; color:#8C7E72; margin-bottom:0;">
    If you did not register for an account at Rayka, you can safely disregard this email.
  </p>
@endsection
