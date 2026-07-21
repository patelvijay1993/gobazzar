@extends('layouts.app')
@section('title', 'Privacy Policy — GoBazaar')
@section('description', 'GoBazaar Privacy Policy — how we collect, use, and protect your personal information under PIPEDA and CASL.')

@push('styles')
<style>
.page-hero{background:var(--primary);padding:52px 24px 48px;text-align:center}
.page-hero h1{font-family:var(--fh);font-size:36px;font-weight:800;color:#fff;margin-bottom:10px}
.page-hero p{font-size:15px;color:rgba(255,255,255,.75);max-width:520px;margin:0 auto}
.legal-wrap{max-width:760px;margin:0 auto;padding:44px 24px}
.legal-wrap h2{font-family:var(--fh);font-size:17px;font-weight:800;color:var(--text);margin:30px 0 8px;padding-top:8px;border-top:1px solid var(--border)}
.legal-wrap h2:first-of-type{border-top:none;margin-top:0}
.legal-wrap p{font-size:13.5px;color:var(--text);line-height:1.75;margin-bottom:12px}
.legal-wrap ul{padding-left:20px;margin-bottom:14px;display:flex;flex-direction:column;gap:6px}
.legal-wrap ul li{font-size:13.5px;color:var(--text);line-height:1.6}
.legal-meta{background:#f5f7fb;border:1px solid var(--border);border-radius:var(--radius);padding:14px 18px;font-size:12.5px;color:var(--muted);margin-bottom:28px}
.cookie-table{width:100%;border-collapse:collapse;font-size:13px;margin-bottom:16px}
.cookie-table th,.cookie-table td{padding:8px 12px;border:1px solid var(--border);text-align:left;vertical-align:top}
.cookie-table th{background:#f5f7fb;font-weight:700}
</style>
@endpush

@section('content')
<div class="page-hero">
  <h1>Privacy <span style="color:var(--accent)">Policy</span></h1>
  <p>How GoBazaar collects, uses, and protects your personal information.</p>
</div>

<div class="legal-wrap">
  <div class="legal-meta">
    <i class="fa-solid fa-calendar" style="margin-right:6px"></i><strong>Last Updated:</strong> July 21, 2026 &nbsp;·&nbsp;
    <i class="fa-solid fa-location-dot" style="margin-right:6px"></i>Applicable to users in Canada &nbsp;·&nbsp;
    Compliant with PIPEDA and CASL
  </div>

  <h2>1. About Us</h2>
  <p>GoBazaar is an online community marketplace operated in Canada. Our platform is available at <strong>gobazaar.ca</strong>. For privacy matters, you may contact our Privacy Officer at <a href="mailto:privacy@gobazaar.ca" style="color:var(--primary)">privacy@gobazaar.ca</a>.</p>

  <h2>2. Information We Collect</h2>
  <p>We collect personal information in the following ways:</p>
  <ul>
    <li><strong>Account Registration:</strong> Name, email address, phone number, and password when you create an account.</li>
    <li><strong>Social Login:</strong> If you sign in via Google, we receive your name, email, and profile picture from Google.</li>
    <li><strong>Listings &amp; Posts:</strong> Content you submit, including text, images, location, and pricing.</li>
    <li><strong>Communications:</strong> Messages you send through our chat system, contact forms, or support requests.</li>
    <li><strong>Usage Data:</strong> Pages visited, search queries, clicks, time on site, and device/browser information.</li>
    <li><strong>Cookies:</strong> Session cookies for login state, preference cookies for your location, and analytics cookies (Google Analytics) only if you consent.</li>
    <li><strong>Payment:</strong> If you upgrade to a paid plan, payment is processed by Stripe. We do not store your credit card numbers.</li>
  </ul>

  <h2>3. How We Use Your Information</h2>
  <p>We use your information to:</p>
  <ul>
    <li>Provide, operate, and maintain our platform and services (<em>legal basis: contract performance</em>)</li>
    <li>Process your listings, business registrations, job posts, and events (<em>contract performance</em>)</li>
    <li>Send you important account notifications and service updates (<em>contract performance / legitimate interest</em>)</li>
    <li>Respond to your support requests and enquiries (<em>legitimate interest</em>)</li>
    <li>Detect, prevent, and address fraud, spam, and abuse (<em>legitimate interest</em>)</li>
    <li>Analyze usage patterns to improve the platform — only with your consent via Google Analytics (<em>consent</em>)</li>
    <li>Send promotional communications only where you have provided express consent under CASL (<em>consent</em>)</li>
  </ul>

  <h2>4. Third-Party Service Providers</h2>
  <p>We do not sell your personal data. We share information only with the following trusted service providers:</p>
  <ul>
    <li><strong>Amazon Web Services (AWS / S3)</strong> — cloud file storage (Canada/US regions)</li>
    <li><strong>Stripe</strong> — payment processing</li>
    <li><strong>Google Analytics</strong> — website traffic analysis (only loaded with your consent; data is anonymized via IP anonymization)</li>
    <li><strong>Google (OAuth)</strong> — social sign-in, if you choose to use it</li>
    <li><strong>Resend / SMTP provider</strong> — transactional email delivery</li>
    <li><strong>Groq API</strong> — AI-powered features (no personal data is sent to Groq)</li>
    <li><strong>Legal Requirements:</strong> We may disclose information when required by law, court order, or government authority.</li>
    <li><strong>Business Transfer:</strong> In a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction.</li>
  </ul>

  <h2>5. Cookies</h2>
  <p>We use cookies in three categories:</p>
  <table class="cookie-table">
    <tr><th>Category</th><th>Purpose</th><th>Consent Required</th></tr>
    <tr><td><strong>Essential</strong></td><td>Login session, CSRF security token, location preference</td><td>No — required for the site to function</td></tr>
    <tr><td><strong>Analytics</strong></td><td>Google Analytics — understand how visitors use GoBazaar (anonymized)</td><td>Yes — only loaded after you click "Accept All"</td></tr>
    <tr><td><strong>Functional</strong></td><td>Remember dismissed banners, dark/light mode preference</td><td>No — stored in localStorage, not transmitted</td></tr>
  </table>
  <p>You can change your cookie preference at any time by clearing your browser's localStorage or cookies.</p>

  <h2>6. Marketing Communications (CASL)</h2>
  <p>We send promotional emails only where you have provided <strong>express consent</strong> as required by Canada's Anti-Spam Legislation (CASL). Each promotional email includes an unsubscribe link. You may also withdraw consent at any time by emailing <a href="mailto:privacy@gobazaar.ca" style="color:var(--primary)">privacy@gobazaar.ca</a>.</p>

  <h2>7. Data Retention</h2>
  <ul>
    <li><strong>Account data:</strong> Retained while your account is active. Deleted within 30 days of account deletion request, except where legal retention is required.</li>
    <li><strong>Chat messages:</strong> Retained for 12 months after last activity in the conversation.</li>
    <li><strong>Activity logs &amp; analytics:</strong> Retained for 12 months, then anonymized or deleted.</li>
    <li><strong>Payment records:</strong> Retained for 7 years as required by Canadian tax law.</li>
  </ul>

  <h2>8. Public Listings</h2>
  <p>Information you include in public listings (title, description, price, location, images, and contact details you choose to display) is visible to all visitors of GoBazaar. Please be mindful of what personal information you share in your listings.</p>

  <h2>9. Your Rights (PIPEDA)</h2>
  <p>Under Canada's Personal Information Protection and Electronic Documents Act (PIPEDA), you have the right to:</p>
  <ul>
    <li>Access the personal information we hold about you</li>
    <li>Request correction of inaccurate data</li>
    <li>Request deletion of your personal data</li>
    <li>Withdraw consent for non-essential data processing</li>
    <li>Lodge a complaint with the <a href="https://www.priv.gc.ca" target="_blank" rel="noopener noreferrer" style="color:var(--primary)">Office of the Privacy Commissioner of Canada</a></li>
  </ul>
  <p>To exercise any of these rights, contact our Privacy Officer at <a href="mailto:privacy@gobazaar.ca" style="color:var(--primary)">privacy@gobazaar.ca</a>. We will respond within 30 days.</p>

  <h2>10. Security</h2>
  <p>We implement industry-standard security measures including HTTPS/TLS encryption, bcrypt-hashed passwords, CSRF protection, Content Security Policy headers, and access controls. However, no method of internet transmission is 100% secure.</p>

  <h2>11. Children's Privacy</h2>
  <p>GoBazaar is not intended for users under the age of 18. We do not knowingly collect personal information from minors. If you believe a minor has provided us with personal information, contact us immediately at <a href="mailto:privacy@gobazaar.ca" style="color:var(--primary)">privacy@gobazaar.ca</a>.</p>

  <h2>12. Third-Party Links</h2>
  <p>Our platform may contain links to third-party websites. We are not responsible for the privacy practices of those sites and encourage you to review their privacy policies.</p>

  <h2>13. Changes to This Policy</h2>
  <p>We may update this Privacy Policy from time to time. We will notify you of significant changes by posting the updated policy on this page and updating the "Last Updated" date above. Continued use of GoBazaar after changes constitutes your acceptance of the updated policy.</p>

  <h2>14. Contact Us</h2>
  <p>If you have questions about this Privacy Policy or how we handle your data, please contact us:</p>
  <ul>
    <li>Email: <a href="mailto:privacy@gobazaar.ca" style="color:var(--primary)">privacy@gobazaar.ca</a></li>
    <li>Website: <a href="{{ route('contact') }}" style="color:var(--primary)">gobazaar.ca/contact</a></li>
  </ul>

</div>
@endsection
