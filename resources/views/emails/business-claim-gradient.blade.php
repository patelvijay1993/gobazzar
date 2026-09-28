<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="color-scheme" content="light">
<title>Claim & Verify Your Business on {{ $site_name }}</title>
</head>
<body style="margin:0;padding:0;background:#f0f2f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f0f2f8;padding:32px 16px;">
  <tr>
    <td align="center">
      <table role="presentation" width="580" cellpadding="0" cellspacing="0" style="max-width:580px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(26,58,143,.10);">

        {{-- Header --}}
        <tr>
          <td style="background:linear-gradient(135deg,#1a3a8f 0%,#122970 100%);background-color:#1a3a8f;padding:36px 32px;text-align:center;">
            <div style="color:#ffffff;font-size:26px;font-weight:800;letter-spacing:-.5px;">Go<span style="color:#e8a020;">Bazaar</span></div>
            <div style="color:rgba(255,255,255,.7);font-size:12px;margin-top:6px;letter-spacing:.4px;text-transform:uppercase;">Canada's #1 Community Marketplace</div>
          </td>
        </tr>

        {{-- Icon + Headline --}}
        <tr>
          <td style="padding:40px 36px 8px;text-align:center;">
            <div style="width:64px;height:64px;background:#eef2ff;border-radius:50%;display:inline-block;line-height:64px;font-size:28px;margin-bottom:18px;">📍</div>
            <div style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:10px;line-height:1.35;">
              Is <span style="color:#1a3a8f;">{{ $business_name }}</span> your business?
            </div>
            <div style="font-size:14px;color:#64748b;line-height:1.7;max-width:420px;margin:0 auto;">
              We found this business listed on {{ $site_name }}. Claim it now to take control of your profile and start getting more customers.
            </div>
          </td>
        </tr>

        {{-- CTA --}}
        <tr>
          <td style="padding:28px 36px 8px;text-align:center;">
            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
              <tr>
                <td style="border-radius:10px;background:#1a3a8f;">
                  <a href="{{ $claim_url }}" style="display:inline-block;padding:15px 36px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:10px;">
                    Claim &amp; Verify Your Business →
                  </a>
                </td>
              </tr>
            </table>
            <div style="font-size:11px;color:#94a3b8;margin-top:12px;">
              Free &middot; Takes less than a minute
            </div>
          </td>
        </tr>

        {{-- Benefits --}}
        <tr>
          <td style="padding:24px 36px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:12px;">
              <tr>
                <td style="padding:22px 24px;">
                  <div style="font-size:12px;font-weight:700;color:#1a3a8f;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">What you'll get</div>
                  <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="24" style="vertical-align:top;padding-bottom:12px;color:#16a34a;font-size:14px;">✓</td>
                      <td style="vertical-align:top;padding-bottom:12px;font-size:13.5px;color:#334155;line-height:1.5;">Full control to edit your info, hours &amp; photos</td>
                    </tr>
                    <tr>
                      <td width="24" style="vertical-align:top;padding-bottom:12px;color:#16a34a;font-size:14px;">✓</td>
                      <td style="vertical-align:top;padding-bottom:12px;font-size:13.5px;color:#334155;line-height:1.5;">A blue ✓ Verified badge on your listing</td>
                    </tr>
                    <tr>
                      <td width="24" style="vertical-align:top;padding-bottom:12px;color:#16a34a;font-size:14px;">✓</td>
                      <td style="vertical-align:top;padding-bottom:12px;font-size:13.5px;color:#334155;line-height:1.5;">Direct replies to customer chat messages</td>
                    </tr>
                    <tr>
                      <td width="24" style="vertical-align:top;color:#16a34a;font-size:14px;">✓</td>
                      <td style="vertical-align:top;font-size:13.5px;color:#334155;line-height:1.5;">Ability to post offers &amp; updates</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Fallback link --}}
        <tr>
          <td style="padding:24px 36px 0;">
            <div style="font-size:11.5px;color:#94a3b8;text-align:center;word-break:break-all;">
              Button not working? Paste this link into your browser:<br>
              <a href="{{ $claim_url }}" style="color:#1a3a8f;">{{ $claim_url }}</a>
            </div>
          </td>
        </tr>

        {{-- Divider + expiry note --}}
        <tr>
          <td style="padding:24px 36px 32px;">
            <hr style="border:none;border-top:1px solid #e2e8f0;margin:0 0 16px;">
            <div style="font-size:12px;color:#94a3b8;text-align:center;">
              This link expires in 14 days. If you don't own this business, you can safely ignore this email.
            </div>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="background:#f8fafc;padding:20px 32px;text-align:center;border-top:1px solid #e2e8f0;">
            <div style="font-size:11.5px;color:#94a3b8;">
              &copy; {{ date('Y') }} {{ $site_name }} &middot;
              <a href="{{ config('app.url') }}" style="color:#1a3a8f;text-decoration:none;">Visit Website</a>
            </div>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
