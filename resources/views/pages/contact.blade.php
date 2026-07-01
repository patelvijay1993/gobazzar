@extends('layouts.app')
@section('title', 'Contact Us — GoBazaar')
@section('description', 'Get in touch with the GoBazaar team. We\'re here to help with listings, accounts, advertising, and community support.')

@push('styles')
<style>
.page-hero{background:var(--primary);padding:52px 24px 48px;text-align:center}
.page-hero h1{font-family:var(--fh);font-size:36px;font-weight:800;color:#fff;margin-bottom:10px}
.page-hero p{font-size:15px;color:rgba(255,255,255,.75);max-width:520px;margin:0 auto}
.page-wrap{max-width:960px;margin:0 auto;padding:40px 24px}
.contact-grid{display:grid;grid-template-columns:1fr 380px;gap:28px;align-items:start}
.contact-form-wrap{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:28px}
.contact-form-wrap h2{font-family:var(--fh);font-size:18px;font-weight:800;color:var(--text);margin-bottom:4px}
.contact-info{display:flex;flex-direction:column;gap:14px}
.ci-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:18px;display:flex;gap:13px;align-items:flex-start}
.ci-icon{width:40px;height:40px;border-radius:10px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.ci-icon i{color:var(--primary);font-size:17px}
.ci-label{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px}
.ci-value{font-size:13.5px;font-weight:600;color:var(--text)}
.ci-sub{font-size:11.5px;color:var(--muted);margin-top:2px}
.faq-item{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:16px 18px;margin-bottom:10px}
.faq-q{font-size:13.5px;font-weight:700;color:var(--text);margin-bottom:6px}
.faq-a{font-size:12.5px;color:var(--muted);line-height:1.6}
@media(max-width:780px){
  .contact-grid{grid-template-columns:1fr}
  .page-hero h1{font-size:26px}
}
</style>
@endpush

@section('content')
<div class="page-hero">
  <h1>Contact <span style="color:var(--accent)">Us</span></h1>
  <p>Have a question, suggestion, or need help? We'd love to hear from you.</p>
</div>

<div class="page-wrap">

  @if(session('success'))
    <div style="background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;padding:14px 18px;border-radius:var(--radius);margin-bottom:24px;font-size:13.5px;font-weight:500">
      <i class="fa-solid fa-circle-check" style="margin-right:7px"></i>{{ session('success') }}
    </div>
  @endif

  <div class="contact-grid">
    {{-- Form --}}
    <div class="contact-form-wrap">
      <h2>Send us a Message</h2>
      <div style="font-size:13px;color:var(--muted);margin-bottom:22px">We typically respond within 1–2 business days.</div>

      <form action="{{ route('contact.submit') }}" method="POST">
        @csrf
        @if($errors->any())
          <div style="background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px">
            {{ $errors->first() }}
          </div>
        @endif
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
          <div>
            <label style="font-size:12.5px;font-weight:600;color:var(--text);display:block;margin-bottom:5px">Your Name *</label>
            <input name="name" value="{{ old('name') }}" required placeholder="John Smith" style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb);box-sizing:border-box">
          </div>
          <div>
            <label style="font-size:12.5px;font-weight:600;color:var(--text);display:block;margin-bottom:5px">Email Address *</label>
            <input name="email" type="email" value="{{ old('email') }}" required placeholder="you@example.com" style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb);box-sizing:border-box">
          </div>
        </div>
        <div style="margin-bottom:12px">
          <label style="font-size:12.5px;font-weight:600;color:var(--text);display:block;margin-bottom:5px">Subject *</label>
          <select name="subject" required style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb);background:#fff">
            <option value="">-- Select a topic --</option>
            <option value="General Inquiry" {{ old('subject')=='General Inquiry'?'selected':'' }}>General Inquiry</option>
            <option value="Account Help" {{ old('subject')=='Account Help'?'selected':'' }}>Account Help</option>
            <option value="Listing Issue" {{ old('subject')=='Listing Issue'?'selected':'' }}>Listing Issue</option>
            <option value="Report a Scam" {{ old('subject')=='Report a Scam'?'selected':'' }}>Report a Scam / Fraud</option>
            <option value="Advertising" {{ old('subject')=='Advertising'?'selected':'' }}>Advertising &amp; Partnerships</option>
            <option value="Technical Issue" {{ old('subject')=='Technical Issue'?'selected':'' }}>Technical Issue / Bug</option>
            <option value="Feedback" {{ old('subject')=='Feedback'?'selected':'' }}>Feedback &amp; Suggestions</option>
            <option value="Other" {{ old('subject')=='Other'?'selected':'' }}>Other</option>
          </select>
        </div>
        <div style="margin-bottom:20px">
          <label style="font-size:12.5px;font-weight:600;color:var(--text);display:block;margin-bottom:5px">Message *</label>
          <textarea name="message" required rows="5" placeholder="Describe your question or issue in detail..." style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb);resize:vertical;box-sizing:border-box">{{ old('message') }}</textarea>
        </div>
        <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:12px 28px;border-radius:8px;font-size:13.5px;font-weight:700;cursor:pointer;transition:background .2s;display:flex;align-items:center;gap:7px">
          <i class="fa-solid fa-paper-plane"></i> Send Message
        </button>
      </form>
    </div>

    {{-- Info cards --}}
    <div class="contact-info">
      <div class="ci-card">
        <div class="ci-icon"><i class="fa-solid fa-envelope"></i></div>
        <div>
          <div class="ci-label">Email</div>
          <div class="ci-value">support@gobazaar.ca</div>
          <div class="ci-sub">For general queries &amp; support</div>
        </div>
      </div>
      <div class="ci-card">
        <div class="ci-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
        <div>
          <div class="ci-label">Advertising</div>
          <div class="ci-value">ads@gobazaar.ca</div>
          <div class="ci-sub">For advertising &amp; sponsorships</div>
        </div>
      </div>
      <div class="ci-card">
        <div class="ci-icon"><i class="fa-brands fa-whatsapp"></i></div>
        <div>
          <div class="ci-label">WhatsApp</div>
          <div class="ci-value">+1 (437) 000-0000</div>
          <div class="ci-sub">Mon–Fri, 9am–6pm EST</div>
        </div>
      </div>
      <div class="ci-card">
        <div class="ci-icon"><i class="fa-solid fa-clock"></i></div>
        <div>
          <div class="ci-label">Response Time</div>
          <div class="ci-value">1–2 Business Days</div>
          <div class="ci-sub">We try to respond faster when possible</div>
        </div>
      </div>

      <div style="background:var(--primary-light);border:1px solid #c7d4f0;border-radius:var(--radius);padding:16px">
        <div style="font-size:12.5px;font-weight:700;color:var(--primary);margin-bottom:10px"><i class="fa-solid fa-circle-question" style="margin-right:5px"></i>Quick FAQ</div>
        <div style="display:flex;flex-direction:column;gap:10px">
          @foreach([
            ['How do I delete my listing?','Log in → My Account → find the listing → click Delete.'],
            ['How do I report a scam?','Click "Report" on any listing, or email support@gobazaar.ca'],
            ['How do I upgrade my plan?','Go to Pricing and click Upgrade next to your desired plan.'],
          ] as [$q,$a])
          <div style="border-bottom:1px solid #c7d4f0;padding-bottom:9px">
            <div style="font-size:12px;font-weight:700;color:var(--text);margin-bottom:3px">{{ $q }}</div>
            <div style="font-size:11.5px;color:var(--muted)">{{ $a }}</div>
          </div>
          @endforeach
          <div style="font-size:11.5px;color:var(--muted)">More questions? <a href="{{ route('contact') }}" style="color:var(--primary);font-weight:600">Send us a message</a></div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
