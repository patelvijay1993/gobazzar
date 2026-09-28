<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Claim & Verify Your Business on {{ $site_name }}</title>
<style>
  body{margin:0;padding:0;background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif}
  .wrap{max-width:580px;margin:32px auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.08)}
  .header{background:linear-gradient(135deg,#1a56db 0%,#0e3fa8 100%);padding:28px 32px;text-align:center}
  .logo{color:#fff;font-size:24px;font-weight:800;letter-spacing:-0.5px}
  .logo span{color:#fbbf24}
  .body{padding:32px}
  .greeting{font-size:16px;font-weight:600;color:#1e293b;margin-bottom:16px}
  .message{font-size:14.5px;color:#475569;line-height:1.75}
  .cta-wrap{text-align:center;margin:28px 0}
  .cta-btn{display:inline-block;background:#1a56db;color:#fff !important;font-size:15px;font-weight:700;padding:14px 32px;border-radius:10px;text-decoration:none}
  .cta-btn:hover{background:#0e3fa8}
  .benefits{background:#f8fafc;border-radius:10px;padding:18px 20px;margin:20px 0}
  .benefits ul{margin:0;padding-left:18px;color:#475569;font-size:13.5px;line-height:1.9}
  .divider{border:none;border-top:1px solid #e2e8f0;margin:24px 0}
  .footer{background:#f8fafc;padding:20px 32px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0}
  .footer a{color:#1a56db;text-decoration:none}
  .badge{display:inline-block;background:#eff6ff;color:#1a56db;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;margin-bottom:16px}
  .link-fallback{font-size:11.5px;color:#94a3b8;word-break:break-all;margin-top:8px}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <div class="logo">Go<span>Bazaar</span></div>
  </div>
  <div class="body">
    <div class="badge">📍 Claim Your Listing</div>
    <div class="greeting">Is this your business, {{ $business_name }}?</div>
    <div class="message">
      We found <strong>{{ $business_name }}</strong> listed on {{ $site_name }}. If this is your business, claim it now to manage your profile, respond to customer messages, and get a verified badge.
    </div>

    <div class="cta-wrap">
      <a href="{{ $claim_url }}" class="cta-btn">Claim & Verify Your Business →</a>
      <div class="link-fallback">Or copy this link: {{ $claim_url }}</div>
    </div>

    <div class="benefits">
      <strong style="font-size:13px;color:#1e293b">Once claimed, you can:</strong>
      <ul>
        <li>Edit your business info, hours, and photos</li>
        <li>Get a ✓ Verified badge on your listing</li>
        <li>Reply to customer chat messages directly</li>
        <li>Add posts, offers, and updates to your profile</li>
      </ul>
    </div>

    <hr class="divider">
    <p style="font-size:13px;color:#94a3b8;margin:0">
      This link expires in 14 days. If you don't own this business, you can safely ignore this email.
    </p>
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} {{ $site_name }} &middot;
    <a href="{{ config('app.url') }}">Visit Website</a>
  </div>
</div>
</body>
</html>
