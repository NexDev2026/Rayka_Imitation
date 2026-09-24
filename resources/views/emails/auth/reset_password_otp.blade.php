@extends('emails.layouts.master', [
    'emailTitle' => 'Password Reset Verification — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>',
    'headerTitle' => 'Password Reset Request',
    'headerSubtitle' => 'Authorization code for password update'
])

@section('content')
  <div class="greeting">Password Reset Authorization</div>
  <p class="message-text">
    We received a request to reset the password for your Rayka account. Please enter the one-time verification code below into the password reset screen:
  </p>

  <div class="otp-wrapper">
    <div class="otp-bg">
      <div class="otp-label">Password Reset Code</div>
      <div class="otp-code">{{ $otp }}</div>
      <div class="otp-expiry">Valid for the next 15 minutes</div>
    </div>
  </div>

  <div class="alert-box alert-danger">
    <strong>Crucial Security Notice:</strong> If you did not request a password reset, please contact our concierge team immediately or verify that your email account credentials are secure.
  </div>

  <p class="message-text" style="font-size:12.5px; color:#8C7E72; margin-bottom:0;">
    This authorization code is unique to your account and should never be disclosed to anyone.
  </p>
@endsection
