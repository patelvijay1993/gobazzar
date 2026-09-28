<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="color-scheme" content="light">
<title>Claim & Verify Your Business on {{ $site_name }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:28px 16px;">
  <tr>
    <td align="center">
      <table role="presentation" width="580" cellpadding="0" cellspacing="0" style="max-width:580px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">

        {{-- Top accent bar --}}
        <tr>
          <td style="background:#e8a020;height:6px;line-height:6px;font-size:1px;">&nbsp;</td>
        </tr>

        {{-- Header --}}
        <tr>
          <td style="padding:30px 36px 0;">
            <div style="font-size:15px;font-weight:800;color:#0f172a;">Go<span style="color:#e8a020;">Bazaar</span></div>
          </td>
        </tr>

        {{-- Urgency badge --}}
        <tr>
          <td style="padding:24px 36px 0;">
            <table role="presentation" cellpadding="0" cellspacing="0">
              <tr>
                <td style="background:#fef2f2;border-radius:20px;padding:6px 14px;">
                  <span style="font-size:11.5px;font-weight:700;color:#dc2626;">⚡ ACTION NEEDED — Unclaimed Listing</span>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Headline --}}
        <tr>
          <td style="padding:18px 36px 0;">
            <div style="font-size:24px;font-weight:800;color:#0f172a;line-height:1.3;">
              Someone could be managing<br>"{{ $business_name }}" — <span style="color:#1a3a8f;">it should be you.</span>
            </div>
          </td>
        </tr>

        <tr>
          <td style="padding:14px 36px 0;">
            <div style="font-size:14px;color:#475569;line-height:1.7;">
              Your business is live on {{ $site_name }}{{ $business_city ? ' in '.$business_city : '' }}, but nobody's verified ownership yet. Customers are seeing it right now — claim it before someone else does.
            </div>
          </td>
        </tr>

        {{-- Big CTA --}}
        <tr>
          <td style="padding:28px 36px 0;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td align="center" style="border-radius:12px;background:#dc2626;">
                  <a href="{{ $claim_url }}" style="display:block;padding:18px 24px;font-size:16px;font-weight:800;color:#ffffff;text-decoration:none;border-radius:12px;">
                    🔓 Claim My Business Now
                  </a>
                </td>
              </tr>
            </table>
            <div style="font-size:11.5px;color:#94a3b8;text-align:center;margin-top:10px;">
              Takes 60 seconds &middot; 100% free &middot; Link expires in 14 days
            </div>
          </td>
        </tr>

        {{-- 3-step process --}}
        <tr>
          <td style="padding:32px 36px 0;">
            <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:16px;">How it works</div>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width="36" style="vertical-align:top;padding-bottom:16px;">
                  <div style="width:26px;height:26px;background:#1a3a8f;border-radius:50%;color:#fff;font-size:12px;font-weight:800;text-align:center;line-height:26px;">1</div>
                </td>
                <td style="vertical-align:top;padding-bottom:16px;padding-left:4px;">
                  <div style="font-size:13.5px;font-weight:700;color:#0f172a;">Click the button above</div>
                  <div style="font-size:12.5px;color:#94a3b8;margin-top:2px;">You'll go straight to the claim page</div>
                </td>
              </tr>
              <tr>
                <td width="36" style="vertical-align:top;padding-bottom:16px;">
                  <div style="width:26px;height:26px;background:#1a3a8f;border-radius:50%;color:#fff;font-size:12px;font-weight:800;text-align:center;line-height:26px;">2</div>
                </td>
                <td style="vertical-align:top;padding-bottom:16px;padding-left:4px;">
                  <div style="font-size:13.5px;font-weight:700;color:#0f172a;">Sign in or create a free account</div>
                  <div style="font-size:12.5px;color:#94a3b8;margin-top:2px;">Takes less than a minute</div>
                </td>
              </tr>
              <tr>
                <td width="36" style="vertical-align:top;">
                  <div style="width:26px;height:26px;background:#16a34a;border-radius:50%;color:#fff;font-size:12px;font-weight:800;text-align:center;line-height:26px;">✓</div>
                </td>
                <td style="vertical-align:top;padding-left:4px;">
                  <div style="font-size:13.5px;font-weight:700;color:#0f172a;">Confirm the claim — done!</div>
                  <div style="font-size:12.5px;color:#94a3b8;margin-top:2px;">Your listing is now yours to manage</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Fallback link --}}
        <tr>
          <td style="padding:28px 36px 0;">
            <div style="font-size:11.5px;color:#94a3b8;background:#f8fafc;border-radius:8px;padding:12px 14px;word-break:break-all;">
              Button not working? <a href="{{ $claim_url }}" style="color:#1a3a8f;">{{ $claim_url }}</a>
            </div>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="padding:28px 36px 28px;">
            <div style="font-size:11.5px;color:#cbd5e1;text-align:center;">
              If you don't own this business, you can safely ignore this email.<br><br>
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
