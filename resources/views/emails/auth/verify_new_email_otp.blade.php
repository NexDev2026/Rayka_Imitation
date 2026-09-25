@extends('emails.layouts.master', [
    'emailTitle' => $otp . ' – your Rayka email verification code',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M22 13V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h9"></path><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path><path d="m16 19 2 2 4-4"></path></svg>',
    'headerTitle' => 'Verify New Email',
    'headerSubtitle' => 'Confirm your email change request'
])

@section('schema_markup')
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "EmailMessage",
  "description": "Your Rayka email verification code is {{ $otp }}"
}
</script>
@endsection

@section('content')
  <div class="greeting">Hi there,</div>
  <p class="message-text">
    We received a request to update your account email to this address. Enter this verification code to confirm:
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

    <p style="font-size:11.5px; color:#A8988B; margin-top:14px;">
      This code is valid for <strong>15 minutes</strong>.
    </p>
  </div>

  <div class="alert-box alert-warning">
    <strong>Security Notice:</strong> If you did not initiate this change, please ignore this email and your email address will remain unchanged.
  </div>
@endsection