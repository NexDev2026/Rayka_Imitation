@extends('emails.layouts.master', [
    'emailTitle' => $otp . ' – your Rayka registration code',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"></path></svg>',
    'headerTitle' => 'Welcome to Rayka',
    'headerSubtitle' => 'Verify your email to activate your account'
])

@section('schema_markup')
<script type="application/ld+json">
{
  "{{ '@context' }}": "https://schema.org",
  "{{ '@type' }}": "EmailMessage",
  "description": "Your Rayka registration code is {{ $otp }}",
  "potentialAction": {
    "{{ '@type' }}": "ConfirmAction",
    "name": "Copy code"
  }
}
</script>
@endsection

@section('content')
  <!-- Gmail NLP Trigger -->
  <div style="display:none; font-size:1px; line-height:1px; max-height:0px; max-width:0px; opacity:0; overflow:hidden;">
    Your Rayka registration code is {{ $otp }}. Enter this code to continue.
  </div>

  <div class="greeting">Hi {{ !empty($userName) ? $userName : 'there' }},</div>
  <p class="message-text">
    Welcome to <strong>Rayka Imitation Jewellery</strong>! Enter this verification code to confirm your email address and complete your registration:
  </p>

  <!-- Spotify-Style SaaS Code Box -->
  <div style="text-align:center; margin:32px 0 28px;">
    <div style="font-size:11px; text-transform:uppercase; letter-spacing:2.5px; color:#D4AF6A; font-weight:800; margin-bottom:14px;">
      Code Requested
    </div>
    
    <table align="center" border="0" cellpadding="0" cellspacing="6" style="margin:0 auto;">
      <tr>
        @foreach(str_split((string)$otp) as $digit)
          <td align="center" valign="middle" style="width:48px; height:58px; background:#23130B; border:1.5px solid #D4AF6A; border-radius:12px; font-family:ui-monospace,Menlo,Consolas,monospace; font-size:32px; font-weight:900; color:#FFFFFF; text-align:center; box-shadow:0 6px 16px rgba(0,0,0,0.5);">
            {{ $digit }}
          </td>
        @endforeach
      </tr>
    </table>

    <div style="margin-top:20px;">
      <a href="{{ route('register') }}" class="cta-btn" style="padding:10px 28px; font-size:12px; letter-spacing:1px;">
        Complete Registration →
      </a>
    </div>

    <p style="font-size:11.5px; color:#A8988B; margin-top:14px;">
      This code is valid for <strong>15 minutes</strong>.
    </p>
  </div>

  <div class="alert-box alert-info">
    <strong>Royal Member Benefits:</strong> Enjoy express checkout, order tracking, and exclusive preview access to new 1-gram micro gold collections.
  </div>

  <p class="message-text" style="font-size:12px; color:#7E6F62; margin-bottom:0;">
    If you did not register for an account at Rayka, you can safely disregard this email.
  </p>
@endsection
