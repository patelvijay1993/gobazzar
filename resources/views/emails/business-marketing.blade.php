<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $subject_line }}</title>
<style>
  body{margin:0;padding:0;background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif}
  .wrap{max-width:580px;margin:32px auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.08)}
  .header{background:linear-gradient(135deg,#1a56db 0%,#0e3fa8 100%);padding:28px 32px;text-align:center}
  .logo{color:#fff;font-size:24px;font-weight:800;letter-spacing:-0.5px}
  .logo span{color:#fbbf24}
  .body{padding:32px}
  .greeting{font-size:16px;font-weight:600;color:#1e293b;margin-bottom:16px}
  .message{font-size:14.5px;color:#475569;line-height:1.75;white-space:pre-line}
  .divider{border:none;border-top:1px solid #e2e8f0;margin:24px 0}
  .footer{background:#f8fafc;padding:20px 32px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0}
  .footer a{color:#1a56db;text-decoration:none}
  .badge{display:inline-block;background:#eff6ff;color:#1a56db;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;margin-bottom:16px}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo">Go<span>Bazaar</span></div>
  </div>
  <div class="body">
    <div class="badge">Message from {{ $site_name }} Team</div>
    <div class="greeting">Hello, {{ $business_name }}!</div>
    <div class="message">{{ $body }}</div>
    <hr class="divider">
    <p style="font-size:13px;color:#94a3b8;margin:0">
      This message was sent because your business is listed on {{ $site_name }}.
    </p>
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} {{ $site_name }} &middot;
    <a href="{{ config('app.url') }}">Visit Website</a>
  </div>
</div>
</body>
</html>
