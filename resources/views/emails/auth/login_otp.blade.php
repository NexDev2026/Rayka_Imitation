@extends('emails.layouts.master', [
    'emailTitle' => 'Your Verification Code — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><circle cx="7.5" cy="15.5" r="5.5"></circle><path d="m21 2-9.6 9.6"></path><path d="m15.5 7.5 3 3L22 7l-3-3"></path></svg>',
    'headerTitle' => 'Royal Sign-In Verification',
    'headerSubtitle' => 'One-Time Password for secure access'
])

@section('content')
  <div class="greeting">Greetings, Valued Patron,</div>
  <p class="message-text">
    To securely access your Rayka royal account, please enter the 6-digit One-Time Password (OTP) provided below into the verification prompt:
  </p>

  <div class="otp-wrapper">
    <div class="otp-bg">
      <div class="otp-label">One-Time Verification Code</div>
      <div class="otp-code">{{ $otp }}</div>
      <div class="otp-expiry">Valid for the next 15 minutes only</div>
    </div>
  </div>

  <div class="alert-box alert-warning">
    <strong>Security Notice:</strong> Never share this verification code with anyone. Rayka concierges or support executives will never ask for your OTP.
  </div>

  <p class="message-text" style="font-size:12.5px; color:#8C7E72; margin-bottom:0;">
    If you did not request this verification code, please ignore this email or contact our support if you believe your account has been compromised.
  </p>
@endsection
