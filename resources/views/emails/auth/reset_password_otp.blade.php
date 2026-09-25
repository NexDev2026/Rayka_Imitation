@extends('emails.layouts.master', [
    'emailTitle' => $otp . ' – your Rayka password reset code',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>',
    'headerTitle' => 'Password Reset Request',
    'headerSubtitle' => 'Secure account verification code'
])

@section('schema_markup')
<script type="application/ld+json">
{
  "{{ '@context' }}": "https://schema.org",
  "{{ '@type' }}": "EmailMessage",
  "description": "Your Rayka password reset code is {{ $otp }}"
}
</script>
@endsection

@section('content')
  <div class="greeting">Hi there,</div>
  <p class="message-text">
    We received a request to reset your password for your <strong>Rayka Imitation Jewellery</strong> account. Enter this code to verify your identity and set a new password:
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
      <a href="{{ route('password.forgot') }}" class="cta-btn" style="padding:10px 28px; font-size:12px; letter-spacing:1px;">
        Reset Password →
      </a>
    </div>

    <p style="font-size:11.5px; color:#A8988B; margin-top:14px;">
      This code is valid for <strong>15 minutes</strong>.
    </p>
  </div>

  <div class="alert-box alert-warning">
    <strong>Security Notice:</strong> If you did not request a password reset, please change your password immediately or contact our support team.
  </div>
@endsection
