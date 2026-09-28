<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="color-scheme" content="light">
<title>Claim & Verify Your Business on {{ $site_name }}</title>
</head>
<body style="margin:0;padding:0;background:#eef1f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef1f7;padding:32px 16px;">
  <tr>
    <td align="center">
      <table role="presentation" width="580" cellpadding="0" cellspacing="0" style="max-width:580px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">

        {{-- Small wordmark bar --}}
        <tr>
          <td style="padding:20px 32px;border-bottom:1px solid #f1f5f9;">
            <div style="font-size:14px;font-weight:800;color:#0f172a;">Go<span style="color:#e8a020;">Bazaar</span></div>
          </td>
        </tr>

        {{-- Business photo banner --}}
        <tr>
          <td>
            @if($business_image)
              <img src="{{ $business_image }}" alt="{{ $business_name }}" width="580" style="display:block;width:100%;max-height:220px;object-fit:cover;">
            @else
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#eef2ff 0%,#e0e7ff 100%);background-color:#eef2ff;">
                <tr><td style="height:140px;text-align:center;vertical-align:middle;font-size:44px;">🏢</td></tr>
              </table>
            @endif
          </td>
        </tr>

        {{-- Business name card, overlapping-style label --}}
        <tr>
          <td style="padding:24px 32px 0;">
            @if($business_category)
              <div style="font-size:11px;font-weight:700;color:#1a3a8f;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">{{ $business_category }}</div>
            @endif
            <div style="font-size:21px;font-weight:800;color:#0f172a;line-height:1.3;">{{ $business_name }}</div>
            @if($business_city)
              <div style="font-size:13px;color:#94a3b8;margin-top:4px;">📍 {{ $business_city }}</div>
            @endif
          </td>
        </tr>

        {{-- Message --}}
        <tr>
          <td style="padding:18px 32px 0;">
            <div style="font-size:14px;color:#475569;line-height:1.75;">
              This business is listed on {{ $site_name }} but hasn't been claimed by its owner yet. If that's you, verify your ownership to unlock full control of the listing.
            </div>
          </td>
        </tr>

        {{-- CTA --}}
        <tr>
          <td style="padding:26px 32px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center" style="border-radius:10px;background:#1a3a8f;">
                  <a href="{{ $claim_url }}" style="display:block;padding:15px 24px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px;">
                    Yes, this is my business →
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Two-column benefit grid --}}
        <tr>
          <td style="padding:28px 32px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="50%" style="vertical-align:top;padding-right:8px;padding-bottom:12px;">
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;">
                    <tr><td style="padding:16px;">
                      <div style="font-size:18px;margin-bottom:6px;">✓</div>
                      <div style="font-size:12.5px;font-weight:700;color:#0f172a;">Verified Badge</div>
                      <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Build customer trust</div>
                    </td></tr>
                  </table>
                </td>
                <td width="50%" style="vertical-align:top;padding-left:8px;padding-bottom:12px;">
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;">
                    <tr><td style="padding:16px;">
                      <div style="font-size:18px;margin-bottom:6px;">✏️</div>
                      <div style="font-size:12.5px;font-weight:700;color:#0f172a;">Full Control</div>
                      <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Edit info, hours &amp; photos</div>
                    </td></tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td width="50%" style="vertical-align:top;padding-right:8px;">
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;">
                    <tr><td style="padding:16px;">
                      <div style="font-size:18px;margin-bottom:6px;">💬</div>
                      <div style="font-size:12.5px;font-weight:700;color:#0f172a;">Direct Replies</div>
                      <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Chat with customers</div>
                    </td></tr>
                  </table>
                </td>
                <td width="50%" style="vertical-align:top;padding-left:8px;">
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;">
                    <tr><td style="padding:16px;">
                      <div style="font-size:18px;margin-bottom:6px;">📣</div>
                      <div style="font-size:12.5px;font-weight:700;color:#0f172a;">Post Updates</div>
                      <div style="font-size:11.5px;color:#94a3b8;margin-top:2px;">Share offers &amp; news</div>
                    </td></tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Fallback link --}}
        <tr>
          <td style="padding:24px 32px 0;">
            <div style="font-size:11.5px;color:#94a3b8;text-align:center;word-break:break-all;">
              Or copy this link: <a href="{{ $claim_url }}" style="color:#1a3a8f;">{{ $claim_url }}</a>
            </div>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="padding:24px 32px 28px;">
            <hr style="border:none;border-top:1px solid #f1f5f9;margin:0 0 16px;">
            <div style="font-size:11.5px;color:#cbd5e1;text-align:center;line-height:1.7;">
              This link expires in 14 days. Not your business? You can ignore this email.<br>
              © {{ date('Y') }} {{ $site_name }} &middot; <a href="{{ config('app.url') }}" style="color:#94a3b8;">Visit Website</a>
            </div>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
