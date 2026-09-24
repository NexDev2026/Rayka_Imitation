@extends('emails.layouts.master', [
    'emailTitle' => 'Welcome to Rayka Imitation Jewellery',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
    'headerTitle' => 'Welcome to Royal Heritage',
    'headerSubtitle' => 'Your royal account is now active'
])

@section('content')
  <div class="greeting">Welcome, {{ $user->name }}!</div>
  <p class="message-text">
    It is our utmost honor to welcome you to <strong>Rayka Imitation Jewellery</strong>. We specialize in authentic royal craftsmanship, bridal couture ornaments, and 1-gram micro gold plated heirloom creations.
  </p>

  <div class="lg-box">
    <div class="lg-box-header">Your Member Privileges</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Account Name</td>
        <td class="val">{{ $user->name }}</td>
      </tr>
      <tr>
        <td class="lbl">Registered Email</td>
        <td class="val">{{ $user->email }}</td>
      </tr>
      <tr>
        <td class="lbl">Membership Tier</td>
        <td class="val" style="color:#D4AF6A; font-weight:700;">Royal Patron</td>
      </tr>
    </table>
  </div>

  <div class="btn-wrap">
    <a href="{{ route('home') }}" class="cta-btn">Explore Collections</a>
  </div>

  <div class="alert-box alert-info" style="margin-top:24px;">
    <strong>Concierge Support:</strong> Need assistance choosing a bridal set or custom chain length? Our master stylists are available to assist you.
  </div>
@endsection
