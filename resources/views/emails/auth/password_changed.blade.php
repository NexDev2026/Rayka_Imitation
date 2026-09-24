@extends('emails.layouts.master', [
    'emailTitle' => 'Security Alert: Password Changed — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>',
    'headerTitle' => 'Security Notice: Password Updated',
    'headerSubtitle' => 'Your Rayka account password was recently changed'
])

@section('content')
  <div class="greeting">Hello {{ $user->name }},</div>
  <p class="message-text">
    This email confirms that the password for your Rayka account (<strong>{{ $user->email }}</strong>) was successfully changed on <strong>{{ $timestamp }}</strong>.
  </p>

  <div class="lg-box">
    <div class="lg-box-header">Security Event Details</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Account Email</td>
        <td class="val">{{ $user->email }}</td>
      </tr>
      <tr>
        <td class="lbl">IP Address</td>
        <td class="val font-mono">{{ $ip }}</td>
      </tr>
      <tr>
        <td class="lbl">Timestamp</td>
        <td class="val">{{ $timestamp }}</td>
      </tr>
      @if(!empty($userAgent))
      <tr>
        <td class="lbl">Browser / Client</td>
        <td class="val" style="font-size:11px; word-break:break-all;">{{ \Illuminate\Support\Str::limit($userAgent, 40) }}</td>
      </tr>
      @endif
    </table>
  </div>

  <div class="alert-box alert-danger">
    <strong>Did not make this change?</strong> If you did not perform this password reset, your account credentials may be compromised. Please use the "Forgot Password" link immediately to re-secure your account or contact our concierge helpline.
  </div>

  <div class="btn-wrap">
    <a href="{{ route('password.forgot') }}" class="cta-btn">Secure My Account</a>
  </div>
@endsection
