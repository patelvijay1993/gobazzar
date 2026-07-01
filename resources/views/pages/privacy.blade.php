@extends('layouts.app')
@section('title', 'Privacy Policy — GoBazaar')
@section('description', 'GoBazaar Privacy Policy — how we collect, use, and protect your personal information.')

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
</style>
@endpush

@section('content')
<div class="page-hero">
  <h1>Privacy <span style="color:var(--accent)">Policy</span></h1>
  <p>How GoBazaar collects, uses, and protects your personal information.</p>
</div>

<div class="legal-wrap">
  <div class="legal-meta">
    <i class="fa-solid fa-calendar" style="margin-right:6px"></i><strong>Last Updated:</strong> June 1, 2025 &nbsp;·&nbsp;
    <i class="fa-solid fa-location-dot" style="margin-right:6px"></i>Applicable to users in Canada
  </div>

  <h2>1. Introduction</h2>
  <p>Welcome to GoBazaar ("we," "us," or "our"). We are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website gobazaar.ca and use our services. Please read this policy carefully. If you disagree with its terms, please discontinue use of our site.</p>

  <h2>2. Information We Collect</h2>
  <p>We may collect personal information in the following ways:</p>
  <ul>
    <li><strong>Account Registration:</strong> Name, email address, phone number, and password when you create an account.</li>
    <li><strong>Listings &amp; Posts:</strong> Content you submit, including text, images, location, and pricing.</li>
    <li><strong>Communications:</strong> Messages you send through our chat, contact forms, or support system.</li>
    <li><strong>Usage Data:</strong> Pages visited, search queries, clicks, time on site, and device/browser information.</li>
    <li><strong>Cookies:</strong> Session cookies for login state and preference cookies to remember your location and settings.</li>
    <li><strong>Payment:</strong> If you upgrade to a paid plan, payment is processed by our payment partner (Stripe). We do not store credit card numbers.</li>
  </ul>

  <h2>3. How We Use Your Information</h2>
  <p>We use your information to:</p>
  <ul>
    <li>Provide, operate, and maintain our platform and services</li>
    <li>Process your listings, business registrations, job posts, and events</li>
    <li>Send you important account notifications and service updates</li>
    <li>Respond to your support requests and enquiries</li>
    <li>Detect, prevent, and address fraud, spam, and abuse</li>
    <li>Analyze usage patterns to improve the platform</li>
    <li>Send promotional communications (only with your consent)</li>
  </ul>

  <h2>4. Sharing Your Information</h2>
  <p>We do not sell your personal data. We may share information with:</p>
  <ul>
    <li><strong>Service Providers:</strong> Trusted third-party vendors (e.g., AWS for storage, Stripe for payments, email providers) who assist in operating our platform.</li>
    <li><strong>Legal Requirements:</strong> When required by law, court order, or government authority.</li>
    <li><strong>Safety:</strong> To protect the rights, safety, or property of GoBazaar, our users, or the public.</li>
    <li><strong>Business Transfer:</strong> In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction.</li>
  </ul>

  <h2>5. Public Listings</h2>
  <p>Information you include in public listings (title, description, price, location, images, and contact details you choose to display) is visible to all visitors of GoBazaar. Please be mindful of what personal information you share in your listings.</p>

  <h2>6. Cookies</h2>
  <p>We use cookies to maintain your login session, remember your city/province preference, and analyze site traffic via analytics tools. You can control cookie settings in your browser; however, disabling cookies may affect some site functionality.</p>

  <h2>7. Data Retention</h2>
  <p>We retain your personal information for as long as your account is active or as needed to provide services. If you delete your account, we will remove your personal data within 30 days, except where we are required to retain it for legal or fraud-prevention purposes.</p>

  <h2>8. Your Rights</h2>
  <p>Depending on your location, you may have the right to:</p>
  <ul>
    <li>Access the personal data we hold about you</li>
    <li>Request correction of inaccurate data</li>
    <li>Request deletion of your data ("right to be forgotten")</li>
    <li>Opt out of marketing communications at any time</li>
    <li>Lodge a complaint with a data protection authority</li>
  </ul>
  <p>To exercise any of these rights, contact us at <strong>privacy@gobazaar.ca</strong>.</p>

  <h2>9. Security</h2>
  <p>We implement industry-standard security measures including HTTPS encryption, hashed passwords, access controls, and regular security reviews. However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.</p>

  <h2>10. Children's Privacy</h2>
  <p>GoBazaar is not intended for users under the age of 18. We do not knowingly collect personal information from minors. If you believe a minor has provided us with personal information, please contact us immediately.</p>

  <h2>11. Third-Party Links</h2>
  <p>Our platform may contain links to third-party websites. We are not responsible for the privacy practices of those sites and encourage you to review their privacy policies.</p>

  <h2>12. Changes to This Policy</h2>
  <p>We may update this Privacy Policy from time to time. We will notify you of significant changes by posting the new policy on this page and updating the "Last Updated" date. Continued use of GoBazaar after changes constitutes your acceptance of the updated policy.</p>

  <h2>13. Contact Us</h2>
  <p>If you have questions about this Privacy Policy or how we handle your data, please contact us:</p>
  <ul>
    <li>Email: <a href="mailto:privacy@gobazaar.ca" style="color:var(--primary)">privacy@gobazaar.ca</a></li>
    <li>Website: <a href="{{ route('contact') }}" style="color:var(--primary)">gobazaar.ca/contact</a></li>
  </ul>

</div>
@endsection
