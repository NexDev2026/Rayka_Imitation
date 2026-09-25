<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <title>{{ $emailTitle ?? config('app.name', 'Rayka Imitation Jewellery') }}</title>
  @yield('schema_markup')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,600&display=swap" rel="stylesheet">
  <style>
    /* Reset */
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      margin:0!important; padding:0!important; width:100%!important;
      background-color:#0D0704;
      font-family:'Outfit',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;
      -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
      color:#E2D9D0; line-height:1.6;
    }
    table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
    img { border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic; }
    a { text-decoration:none; }

    /* Outer Wrapper */
    .email-wrapper {
      width:100%;
      background:radial-gradient(ellipse at top, #23130B 0%, #0D0704 70%);
      padding:36px 12px;
    }

    /* Container Card (max 600px) */
    .email-container {
      max-width:600px;
      margin:0 auto;
      background:linear-gradient(180deg, #1C0F08 0%, #140A05 100%);
      border:1px solid rgba(212, 175, 106, 0.35);
      border-radius:24px;
      overflow:hidden;
      box-shadow:0 25px 60px rgba(0,0,0,0.85), inset 0 1px 0 rgba(255,255,255,0.08);
    }

    /* Top Glowing Bar */
    .top-gold-bar {
      height:4px;
      width:100%;
      background:linear-gradient(90deg, #996E2E 0%, #D4AF6A 50%, #F5E6BE 75%, #996E2E 100%);
    }

    /* Brand Header */
    .email-header {
      padding:36px 32px 28px;
      text-align:center;
      border-bottom:1px solid rgba(212, 175, 106, 0.15);
      background:linear-gradient(180deg, rgba(212, 175, 106, 0.08) 0%, transparent 100%);
      position:relative;
    }
    .brand-tag {
      display:inline-block;
      font-family:'Outfit',sans-serif;
      font-size:10px;
      font-weight:800;
      letter-spacing:4px;
      text-transform:uppercase;
      color:#D4AF6A;
      margin-bottom:12px;
      text-shadow:0 0 12px rgba(212, 175, 106, 0.4);
    }
    .brand-title {
      font-family:'Playfair Display',Georgia,serif;
      font-size:26px;
      font-weight:700;
      color:#FFFFFF;
      letter-spacing:0.5px;
      line-height:1.2;
      margin-bottom:6px;
    }
    .brand-subtitle {
      font-size:12px;
      color:#A8988B;
      font-weight:400;
      letter-spacing:0.4px;
    }

    /* Header Icon Badge / Brand Logo Emblem */
    .header-icon-badge {
      width:64px;
      height:64px;
      border-radius:50%;
      margin:0 auto 16px;
      text-align:center;
      background:linear-gradient(135deg, #2E180E 0%, #150904 100%);
      border:2px solid #D4AF6A;
      padding:6px;
      box-sizing:border-box;
      box-shadow:0 0 25px rgba(212, 175, 106, 0.3);
      display:inline-block;
    }

    /* Email Body */
    .email-body {
      padding:32px 32px 28px;
    }
    .greeting {
      font-family:'Playfair Display',Georgia,serif;
      font-size:18px;
      color:#F8F4EE;
      font-weight:700;
      margin-bottom:12px;
    }
    .message-text {
      font-size:14px;
      color:#C4B5A5;
      line-height:1.75;
      margin-bottom:24px;
    }

    /* Liquid Glass Box */
    .lg-box {
      background:linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.01) 100%);
      border:1px solid rgba(212, 175, 106, 0.2);
      border-radius:16px;
      overflow:hidden;
      margin-bottom:24px;
      box-shadow:0 6px 20px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .lg-box-header {
      font-size:11px;
      font-weight:800;
      letter-spacing:2px;
      text-transform:uppercase;
      color:#D4AF6A;
      padding:12px 20px;
      background:linear-gradient(90deg, rgba(212, 175, 106, 0.12) 0%, transparent 100%);
      border-bottom:1px solid rgba(212, 175, 106, 0.15);
    }

    /* Detail Table */
    .detail-table {
      width:100%;
      border-collapse:collapse;
    }
    .detail-table td {
      padding:12px 20px;
      font-size:13px;
      border-bottom:1px solid rgba(255, 255, 255, 0.04);
      vertical-align:middle;
    }
    .detail-table tr:last-child td {
      border-bottom:none;
    }
    .detail-table td.lbl {
      color:#9C8C7E;
      font-weight:600;
      width:40%;
      font-size:11px;
      text-transform:uppercase;
      letter-spacing:0.8px;
    }
    .detail-table td.val {
      color:#F8F4EE;
      font-weight:600;
      text-align:right;
    }

    /* Itemized Order Table */
    .order-items-table {
      width:100%;
      border-collapse:collapse;
    }
    .order-items-table th {
      background:rgba(212, 175, 106, 0.1);
      color:#D4AF6A;
      font-size:10.5px;
      font-weight:700;
      text-transform:uppercase;
      letter-spacing:1px;
      padding:10px 14px;
      text-align:left;
      border-bottom:1px solid rgba(212, 175, 106, 0.2);
    }
    .order-items-table td {
      padding:12px 14px;
      border-bottom:1px solid rgba(255, 255, 255, 0.05);
      font-size:12.5px;
      color:#E2D9D0;
      vertical-align:middle;
    }
    .order-items-table tr:last-child td {
      border-bottom:none;
    }
    .item-title {
      font-weight:700;
      color:#FFFFFF;
      margin-bottom:2px;
    }
    .item-meta {
      font-size:11px;
      color:#9C8C7E;
    }

    /* OTP Code Box */
    .otp-wrapper {
      text-align:center;
      margin:28px 0;
    }
    .otp-bg {
      background:linear-gradient(135deg, rgba(212, 175, 106, 0.12) 0%, rgba(74, 44, 29, 0.25) 100%);
      border:1px solid rgba(212, 175, 106, 0.4);
      border-radius:18px;
      padding:24px 20px 18px;
      display:inline-block;
      min-width:280px;
      box-shadow:0 0 30px rgba(212, 175, 106, 0.15);
    }
    .otp-label {
      font-size:10px;
      font-weight:800;
      letter-spacing:2px;
      text-transform:uppercase;
      color:#D4AF6A;
      margin-bottom:8px;
    }
    .otp-code {
      font-family:'Outfit',monospace;
      font-size:42px;
      font-weight:900;
      letter-spacing:10px;
      color:#FFFFFF;
      text-shadow:0 0 20px rgba(212, 175, 106, 0.6);
      line-height:1.2;
    }
    .otp-expiry {
      font-size:11px;
      color:#A8988B;
      margin-top:8px;
    }

    /* CTA Button */
    .btn-wrap {
      text-align:center;
      margin:28px 0 12px;
    }
    .cta-btn {
      display:inline-block;
      padding:14px 38px;
      background:linear-gradient(135deg, #D4AF6A 0%, #996E2E 100%);
      color:#1F0F08!important;
      font-family:'Outfit',sans-serif;
      font-weight:800;
      font-size:13.5px;
      letter-spacing:1px;
      text-transform:uppercase;
      border-radius:50px;
      text-decoration:none;
      box-shadow:0 10px 25px rgba(212, 175, 106, 0.35);
      border:1px solid rgba(255, 255, 255, 0.3);
    }

    /* Alert Boxes */
    .alert-box {
      border-radius:12px;
      padding:14px 18px;
      font-size:13px;
      line-height:1.6;
      margin-bottom:22px;
    }
    .alert-info {
      background:rgba(212, 175, 106, 0.08);
      border-left:4px solid #D4AF6A;
      color:#E2D9D0;
    }
    .alert-success {
      background:rgba(16, 185, 129, 0.08);
      border-left:4px solid #10B981;
      color:#D1FAE5;
    }
    .alert-danger {
      background:rgba(239, 68, 68, 0.08);
      border-left:4px solid #EF4444;
      color:#FEE2E2;
    }
    .alert-warning {
      background:rgba(245, 158, 11, 0.08);
      border-left:4px solid #F59E0B;
      color:#FEF3C7;
    }

    /* Divider */
    .divider {
      height:1px;
      background:linear-gradient(90deg, transparent 0%, rgba(212, 175, 106, 0.2) 50%, transparent 100%);
      margin:24px 0;
      border:none;
    }

    /* Footer */
    .email-footer {
      padding:24px 32px;
      text-align:center;
      border-top:1px solid rgba(212, 175, 106, 0.15);
      background:rgba(10, 5, 2, 0.85);
    }
    .footer-brand {
      font-family:'Playfair Display',Georgia,serif;
      font-size:14px;
      font-weight:700;
      color:#D4AF6A;
      letter-spacing:1px;
      margin-bottom:6px;
    }
    .footer-text {
      font-size:11px;
      color:#7E6F62;
      line-height:1.6;
      margin-bottom:8px;
    }
    .footer-links a {
      color:#D4AF6A;
      font-size:11px;
      font-weight:600;
      margin:0 8px;
    }

    /* Mobile Responsive Rules (< 600px) */
    @media only screen and (max-width:600px) {
      .email-wrapper { padding:12px 6px!important; }
      .email-container { border-radius:16px!important; width:100%!important; }
      .email-header, .email-body { padding-left:18px!important; padding-right:18px!important; }
      .email-footer { padding:20px 16px!important; }
      .brand-title { font-size:22px!important; }
      .otp-code { font-size:34px!important; letter-spacing:6px!important; }
      .otp-bg { min-width:100%!important; padding:18px 12px 14px!important; }
      .cta-btn { padding:13px 26px!important; font-size:12.5px!important; width:100%!important; }
      .detail-table, .detail-table tbody, .detail-table tr, .detail-table td {
        display:block!important;
        width:100%!important;
      }
      .detail-table tr { border-bottom:1px solid rgba(255,255,255,0.06)!important; }
      .detail-table tr:last-child { border-bottom:none!important; }
      .detail-table td { border-bottom:none!important; }
      .detail-table td.lbl { padding:10px 14px 2px!important; text-align:left!important; width:100%!important; }
      .detail-table td.val { padding:2px 14px 10px!important; text-align:left!important; width:100%!important; }
    }
  </style>
</head>
<body>
  <div class="email-wrapper">
    <div class="email-container">
      <div class="top-gold-bar"></div>

      <!-- Brand Header -->
      <div class="email-header">
        <div class="header-icon-badge">
          @php
            $appUrl = config('app.url', '');
            $isLocal = str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1') || empty($appUrl);
            $logoUrl = $isLocal ? 'https://files.catbox.moe/96dzwb.png' : url('images/rayka-logo.png');
          @endphp
          <img src="{{ $logoUrl }}" alt="{{ config('app.name', 'Rayka') }}" width="48" height="48" border="0" style="width:48px; max-width:48px; height:48px; max-height:48px; object-fit:contain; border-radius:50%; display:block; margin:0 auto; border:none; outline:none; text-decoration:none;">
        </div>
        <span class="brand-tag">Royal Heritage Jewellery</span>
        <h1 class="brand-title">{{ $headerTitle ?? 'Rayka Imitation' }}</h1>
        @if(!empty($headerSubtitle))
          <p class="brand-subtitle">{{ $headerSubtitle }}</p>
        @endif
      </div>

      <!-- Email Main Content -->
      <div class="email-body">
        @yield('content')
      </div>

      <!-- Brand Footer -->
      <div class="email-footer">
        <div class="footer-brand">{{ \App\Models\StoreSetting::get('store_name', config('app.name', 'Rayka Imitation Jewellery')) }}</div>
        <p class="footer-text">
          1 Gram Micro Gold Plated Heritage Jewellery & Royal Bridal Creations.<br>
          For concierge assistance: <a href="mailto:{{ \App\Models\StoreSetting::get('store_email', config('mail.from.address', 'care@raykajewellery.com')) }}" style="color:#D4AF6A;">{{ \App\Models\StoreSetting::get('store_email', config('mail.from.address', 'care@raykajewellery.com')) }}</a>
        </p>
        <p class="footer-text" style="font-size:10px; color:#5D5146;">
          © {{ date('Y') }} {{ \App\Models\StoreSetting::get('store_name', config('app.name', 'Rayka Imitation Jewellery')) }}. All rights reserved. Handcrafted with royal precision.
        </p>
      </div>

    </div>
  </div>
</body>
</html>
