<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="color-scheme" content="light">
<title>Claim & Verify Your Business on {{ $site_name }}</title>
</head>
<body style="margin:0;padding:0;background:#ffffff;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ffffff;">
  <tr>
    <td align="center" style="padding:48px 16px;">
      <table role="presentation" width="520" cellpadding="0" cellspacing="0" style="max-width:520px;width:100%;">

        {{-- Wordmark --}}
        <tr>
          <td style="padding-bottom:40px;">
            <div style="font-size:15px;font-weight:800;color:#0f172a;letter-spacing:-.3px;">Go<span style="color:#e8a020;">Bazaar</span></div>
          </td>
        </tr>

        {{-- Headline --}}
        <tr>
          <td style="padding-bottom:20px;">
            <div style="font-size:22px;font-weight:700;color:#0f172a;line-height:1.4;">
              Is this your business?
            </div>
          </td>
        </tr>

        {{-- Body --}}
        <tr>
          <td style="padding-bottom:28px;">
            <div style="font-size:14.5px;color:#475569;line-height:1.75;">
              We found <strong style="color:#0f172a;">{{ $business_name }}</strong>{{ $business_city ? ' ('.$business_city.')' : '' }} listed on {{ $site_name }}. If you're the owner, verify your claim to manage the listing — update details, reply to messages, and get a verified badge.
            </div>
          </td>
        </tr>

        {{-- CTA --}}
        <tr>
          <td style="padding-bottom:8px;">
            <table role="presentation" cellpadding="0" cellspacing="0">
              <tr>
                <td style="border-radius:8px;background:#0f172a;">
                  <a href="{{ $claim_url }}" style="display:inline-block;padding:13px 28px;font-size:14px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
                    Claim this business →
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="padding-bottom:36px;">
            <div style="font-size:12px;color:#94a3b8;">Link expires in 14 days.</div>
          </td>
        </tr>

        {{-- Thin divider --}}
        <tr>
          <td style="border-top:1px solid #f1f5f9;padding-top:28px;padding-bottom:12px;">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.6px;">What's included</div>
          </td>
        </tr>

        {{-- Feature list — plain text rows, no card/box --}}
        <tr>
          <td style="padding-bottom:10px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:8px 0;font-size:13.5px;color:#334155;border-bottom:1px solid #f8fafc;">Edit your business profile, hours &amp; photos</td>
              </tr>
              <tr>
                <td style="padding:8px 0;font-size:13.5px;color:#334155;border-bottom:1px solid #f8fafc;">Verified badge on your listing</td>
              </tr>
              <tr>
                <td style="padding:8px 0;font-size:13.5px;color:#334155;border-bottom:1px solid #f8fafc;">Reply to customer chat messages</td>
              </tr>
              <tr>
                <td style="padding:8px 0;font-size:13.5px;color:#334155;">Post updates and offers</td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="padding-top:40px;">
            <div style="font-size:11.5px;color:#cbd5e1;line-height:1.7;">
              If you don't own this business, you can ignore this email.<br>
              <a href="{{ $claim_url }}" style="color:#94a3b8;">{{ $claim_url }}</a><br><br>
              © {{ date('Y') }} {{ $site_name }} — <a href="{{ config('app.url') }}" style="color:#94a3b8;">{{ str_replace(['https://','http://'], '', config('app.url')) }}</a>
            </div>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
