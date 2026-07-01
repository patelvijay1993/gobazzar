@extends('layouts.app')
@section('title', 'Terms of Use — GoBazaar')
@section('description', 'GoBazaar Terms of Use — rules and guidelines for using our platform.')

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
  <h1>Terms of <span style="color:var(--accent)">Use</span></h1>
  <p>Please read these terms carefully before using GoBazaar.</p>
</div>

<div class="legal-wrap">
  <div class="legal-meta">
    <i class="fa-solid fa-calendar" style="margin-right:6px"></i><strong>Last Updated:</strong> June 1, 2025 &nbsp;·&nbsp;
    <i class="fa-solid fa-location-dot" style="margin-right:6px"></i>Governing Law: Ontario, Canada
  </div>

  <h2>1. Acceptance of Terms</h2>
  <p>By accessing or using GoBazaar ("Site", "Platform", "we", "us"), you agree to be bound by these Terms of Use. If you do not agree to these terms, you may not use our platform. These terms apply to all visitors, registered users, and businesses who use GoBazaar.</p>

  <h2>2. Eligibility</h2>
  <p>You must be at least 18 years of age to create an account or post content on GoBazaar. By using the platform, you represent that you meet this requirement. GoBazaar is intended for use by residents of and businesses operating in Canada.</p>

  <h2>3. User Accounts</h2>
  <ul>
    <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
    <li>You are responsible for all activities that occur under your account.</li>
    <li>You must provide accurate and up-to-date information when registering.</li>
    <li>You may not share, sell, or transfer your account to another person.</li>
    <li>GoBazaar reserves the right to suspend or terminate accounts that violate these terms.</li>
  </ul>

  <h2>4. Prohibited Content & Conduct</h2>
  <p>You agree NOT to post, share, or engage in any of the following:</p>
  <ul>
    <li>Fraudulent, misleading, or scam listings</li>
    <li>Illegal goods or services (counterfeit items, drugs, weapons, etc.)</li>
    <li>Adult content, nudity, or sexually explicit material</li>
    <li>Hate speech, discrimination, or harassment based on race, religion, gender, or other protected characteristics</li>
    <li>Spam, duplicate listings, or fake accounts</li>
    <li>Content that infringes on intellectual property rights of others</li>
    <li>Personal information of others without their consent (doxxing)</li>
    <li>Pyramid schemes, multi-level marketing spam, or get-rich-quick scams</li>
    <li>Content that violates any applicable Canadian federal or provincial law</li>
  </ul>

  <h2>5. Listings & Posts</h2>
  <ul>
    <li>All listings must accurately describe the item or service being offered.</li>
    <li>Prices must be clearly stated in Canadian dollars (CAD) unless otherwise specified.</li>
    <li>You may not post the same listing multiple times to artificially boost visibility.</li>
    <li>GoBazaar reserves the right to remove any listing that violates our policies without notice.</li>
    <li>Listings expire according to your plan's post duration. Expired listings are automatically removed.</li>
  </ul>

  <h2>6. Transactions & Safety</h2>
  <p>GoBazaar is a platform that connects buyers, sellers, and service providers. We are not a party to any transaction that occurs between users. We strongly recommend:</p>
  <ul>
    <li>Meeting in public places for in-person transactions</li>
    <li>Using secure payment methods (never wire money or send gift cards)</li>
    <li>Verifying identities before sharing personal information</li>
    <li>Reporting suspicious users or listings to our support team immediately</li>
  </ul>
  <p>GoBazaar is not responsible for the quality, safety, legality, or delivery of items listed on the platform.</p>

  <h2>7. Intellectual Property</h2>
  <p>All content on GoBazaar — including the logo, design, code, and platform features — is owned by GoBazaar and protected by Canadian copyright and trademark law. You may not copy, reproduce, or distribute our content without written permission.</p>
  <p>By posting content (text, images, etc.) on GoBazaar, you grant us a non-exclusive, worldwide, royalty-free license to display, distribute, and promote that content in connection with our services.</p>

  <h2>8. Paid Plans & Refunds</h2>
  <ul>
    <li>Paid plan fees are charged in advance and are generally non-refundable.</li>
    <li>If your account is suspended for violating these terms, no refund will be issued.</li>
    <li>GoBazaar reserves the right to change plan pricing with 30 days' notice.</li>
    <li>For billing issues, contact us at <strong>billing@gobazaar.ca</strong>.</li>
  </ul>

  <h2>9. Disclaimer of Warranties</h2>
  <p>GoBazaar is provided "as is" and "as available" without warranties of any kind. We do not guarantee that the platform will be error-free, uninterrupted, or free of viruses. We are not responsible for any losses arising from your use of the platform.</p>

  <h2>10. Limitation of Liability</h2>
  <p>To the fullest extent permitted by law, GoBazaar shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the platform or any transactions conducted through it.</p>

  <h2>11. Governing Law</h2>
  <p>These Terms of Use are governed by and construed in accordance with the laws of the Province of Ontario and the federal laws of Canada applicable therein. Any disputes shall be resolved in the courts of Ontario.</p>

  <h2>12. Changes to Terms</h2>
  <p>We reserve the right to modify these terms at any time. We will notify users of material changes by posting the updated terms on this page. Continued use of GoBazaar after any changes constitutes acceptance of the new terms.</p>

  <h2>13. Contact Us</h2>
  <p>If you have questions about these Terms of Use, please contact us:</p>
  <ul>
    <li>Email: <a href="mailto:legal@gobazaar.ca" style="color:var(--primary)">legal@gobazaar.ca</a></li>
    <li>Website: <a href="{{ route('contact') }}" style="color:var(--primary)">gobazaar.ca/contact</a></li>
  </ul>

</div>
@endsection
