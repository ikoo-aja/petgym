<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{ $title }} &mdash; PetGym</title>
  <x-dynamic-favicon />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: #0b1120;
      color: #f8fafc;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }
    .maintenance-card {
      background: #111827;
      border: 1px solid #1e293b;
      border-radius: 16px;
      max-width: 560px;
      width: 100%;
      padding: 40px 32px;
      text-align: center;
      box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }
    .gear-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 24px;
      background: rgba(244, 63, 94, 0.12);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #f43f5e;
      font-size: 40px;
    }
    .btn-support {
      background-color: #10b981;
      border-color: #10b981;
      color: #ffffff;
      font-weight: 700;
      border-radius: 8px;
      padding: 10px 24px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
      transition: all 0.2s ease;
    }
    .btn-support:hover {
      background-color: #059669;
      color: #ffffff;
      transform: translateY(-1px);
    }
  </style>
</head>
<body>
  <div class="maintenance-card">
    <div class="gear-icon">
      <span class="icon-refresh"></span>
    </div>
    <span class="badge badge-warning text-dark font-weight-bold px-3 py-2 mb-3" style="border-radius: 20px; font-size: 12px; letter-spacing: 0.5px;">
      <i class="icon-warning mr-1"></i> Mode Pemeliharaan Global
    </span>
    <h3 class="font-weight-bold text-white mb-3" style="font-size: 22px; line-height: 1.4;">
      {{ $title }}
    </h3>
    <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6; color: #94a3b8 !important;">
      {{ $message }}
    </p>

    @if(!empty($estimatedEnd))
    <div class="bg-dark p-3 rounded mb-4" style="background-color: #1e293b !important; border-radius: 10px;">
      <small class="text-muted d-block text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 0.5px;">Informasi Waktu</small>
      <span class="text-white font-weight-bold" style="font-size: 15px;">{{ $estimatedEnd }}</span>
    </div>
    @endif

    <div class="pt-2 border-top" style="border-color: #1e293b !important;">
      <small class="text-muted d-block mb-3" style="font-size: 12px;">Butuh bantuan mendesak selama masa pemeliharaan?</small>
      <div class="d-flex justify-content-center flex-wrap" style="gap: 12px;">
        @if(!empty($supportWhatsapp))
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $supportWhatsapp) }}?text=Halo%20Tim%20Support%20PetGym,%20saya%20ingin%20menanyakan%20status%20pemeliharaan%20platform." target="_blank" class="btn-support">
          <span class="icon-phone"></span> WhatsApp Helpdesk
        </a>
        @endif
        @if(!empty($supportEmail))
        <a href="mailto:{{ $supportEmail }}" class="btn btn-outline-light btn-sm font-weight-bold py-2 px-3" style="border-radius: 8px;">
          <span class="icon-envelope mr-1"></span> Hubungi Email
        </a>
        @endif
      </div>
    </div>

    <div class="mt-4 pt-3 text-center">
      <small style="color: #64748b; font-size: 11px;">
        &copy; {{ date('Y') }} {{ config('app.name', 'PetGym') }} Platform. Hak cipta dilindungi.
      </small>
    </div>
  </div>
</body>
</html>
