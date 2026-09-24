@extends('emails.layouts.master', [
    'emailTitle' => 'Database Backup Completed — Rayka',
    'headerIcon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#D4AF6A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;display:inline-block;"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path><path d="M3 12c0 1.66 4 3 9 3s9-1.34 9-3"></path></svg>',
    'headerTitle' => 'Database Backup Success',
    'headerSubtitle' => 'Automated snapshot created and verified'
])

@section('content')
  <div class="greeting">Administrator Notice,</div>
  <p class="message-text">
    The automated database backup cron job has completed successfully. A complete snapshot of all tables and data has been compressed and stored securely in the backup repository.
  </p>

  <div class="lg-box">
    <div class="lg-box-header">Backup Execution Telemetry</div>
    <table class="detail-table">
      <tr>
        <td class="lbl">Archive Filename</td>
        <td class="val font-mono" style="font-size:12px; color:#D4AF6A;">{{ $data['filename'] }}</td>
      </tr>
      <tr>
        <td class="lbl">Archive Size</td>
        <td class="val" style="color:#10B981; font-weight:800;">{{ $data['size_readable'] }}</td>
      </tr>
      <tr>
        <td class="lbl">Tables Exported</td>
        <td class="val">{{ $data['tables'] }} tables</td>
      </tr>
      <tr>
        <td class="lbl">Rows Dumped</td>
        <td class="val">{{ number_format($data['rows']) }} rows</td>
      </tr>
      <tr>
        <td class="lbl">Execution Duration</td>
        <td class="val">{{ $data['duration_sec'] }} seconds</td>
      </tr>
      <tr>
        <td class="lbl">7-Day Rotated Files</td>
        <td class="val">{{ $data['rotated'] }} old files pruned</td>
      </tr>
      <tr>
        <td class="lbl">Timestamp</td>
        <td class="val">{{ date('d M, Y \a\t h:i:s A T') }}</td>
      </tr>
    </table>
  </div>

  <div class="alert-box alert-success">
    <strong>Storage Integrity:</strong> The backup archive was verified after writing to ensure a non-zero byte size and successful gzip compression.
  </div>

  <div class="btn-wrap">
    <a href="{{ route('admin.dashboard') }}" class="cta-btn">Open Admin Dashboard</a>
  </div>
@endsection
