@extends('emails.layouts.master', [
    'emailTitle' => 'New Patron Registration — Rayka Admin',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
    'headerTitle' => 'New Customer Registration',
    'headerSubtitle' => 'A new user has registered on the platform'
])

@section('content')
  <div class="greeting">Administrator Alert,</div>
  <p class="message-text">
    A new customer has successfully registered and verified their account on Rayka Imitation Jewellery.
  </p>

  <div class="lg-box">
    <div class="lg-box-header">Customer Profile Information</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Customer Name</td>
        <td class="val">{{ $user->name }}</td>
      </tr>
      <tr>
        <td class="lbl">Email Address</td>
        <td class="val">{{ $user->email }}</td>
      </tr>
      <tr>
        <td class="lbl">Registration IP</td>
        <td class="val font-mono">{{ $ip }}</td>
      </tr>
      <tr>
        <td class="lbl">Registered At</td>
        <td class="val">{{ $timestamp }}</td>
      </tr>
    </table>
  </div>

  <div class="btn-wrap">
    <a href="{{ route('admin.dashboard') }}" class="cta-btn">View Admin Portal</a>
  </div>
@endsection
