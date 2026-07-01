@extends('layouts.app')
@section('title', 'Advertise With Us — GoBazaar')
@section('description', 'Reach 50,000+ Indian-Canadians. Advertise your business on GoBazaar with banner ads, featured listings, sponsored posts, and more.')

@push('styles')
<style>
.page-hero{background:var(--primary);padding:52px 24px 48px;text-align:center}
.page-hero h1{font-family:var(--fh);font-size:36px;font-weight:800;color:#fff;margin-bottom:10px}
.page-hero p{font-size:15px;color:rgba(255,255,255,.75);max-width:560px;margin:0 auto}
.page-wrap{max-width:960px;margin:0 auto;padding:40px 24px}
.adv-packages{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin:32px 0}
.adv-pkg{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;position:relative}
.adv-pkg.popular{border-color:var(--primary);box-shadow:0 4px 20px rgba(26,58,143,.12)}
.adv-pkg .popular-badge{position:absolute;top:-11px;left:50%;transform:translateX(-50%);background:var(--primary);color:#fff;font-size:10px;font-weight:700;padding:3px 14px;border-radius:20px;white-space:nowrap;text-transform:uppercase;letter-spacing:.5px}
.adv-pkg h3{font-family:var(--fh);font-size:17px;font-weight:800;color:var(--text);margin-bottom:4px}
.adv-pkg .price{font-family:var(--fh);font-size:26px;font-weight:800;color:var(--primary);margin:10px 0 4px}
.adv-pkg .price small{font-size:13px;font-weight:400;color:var(--muted)}
.adv-pkg ul{list-style:none;padding:0;margin:14px 0 18px;display:flex;flex-direction:column;gap:8px}
.adv-pkg ul li{font-size:12.5px;color:var(--text);display:flex;align-items:flex-start;gap:8px}
.adv-pkg ul li i{color:#15803d;margin-top:2px;flex-shrink:0}
.adv-pkg .pkg-btn{display:block;width:100%;text-align:center;background:var(--primary);color:#fff;padding:10px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;transition:background .2s}
.adv-pkg.popular .pkg-btn{background:var(--primary)}
.adv-pkg .pkg-btn:hover{background:var(--primary-dark)}
.adv-pkg.popular .pkg-btn{box-shadow:0 4px 12px rgba(26,58,143,.3)}

.adv-formats{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin:24px 0}
.adv-fmt{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:18px;display:flex;gap:14px}
.adv-fmt-icon{width:42px;height:42px;border-radius:10px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.adv-fmt-icon i{color:var(--primary)}
.adv-fmt h4{font-size:13.5px;font-weight:700;color:var(--text);margin-bottom:4px}
.adv-fmt p{font-size:12px;color:var(--muted);line-height:1.5}
@media(max-width:720px){
  .adv-packages{grid-template-columns:1fr}
  .adv-formats{grid-template-columns:1fr}
  .page-hero h1{font-size:26px}
}
</style>
@endpush

@section('content')
<div class="page-hero">
  <h1>Advertise With <span style="color:var(--accent)">GoBazaar</span></h1>
  <p>Reach 50,000+ Indian-Canadians actively looking for products, services, and opportunities — right in your city.</p>
</div>

<div class="page-wrap">

  @if(session('success'))
    <div style="background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;padding:14px 18px;border-radius:var(--radius);margin-bottom:24px;font-size:13.5px;font-weight:500">
      <i class="fa-solid fa-circle-check" style="margin-right:7px"></i>{{ session('success') }}
    </div>
  @endif

  <div style="text-align:center;margin-bottom:8px">
    <div style="font-family:var(--fh);font-size:22px;font-weight:800;color:var(--text)">Why Advertise on GoBazaar?</div>
    <div style="font-size:13.5px;color:var(--muted);margin-top:6px">Hyper-targeted reach within the Indian-Canadian community</div>
  </div>

  <div class="adv-formats">
    @foreach([
      ['fa-rectangle-ad','Banner Ads','Premium banner placements on homepage, classifieds, directory, and events pages. High visibility, high impact.'],
      ['fa-star','Featured Listings','Your listing or business appears at the top of search results with a "Featured" badge — more clicks, more leads.'],
      ['fa-newspaper','Sponsored Blog Posts','Publish branded content or news on our Community Blog, reaching engaged readers.'],
      ['fa-envelope','Newsletter Sponsorship','Get your brand in front of our email subscribers every week with a dedicated newsletter slot.'],
      ['fa-map-pin','City Spotlight','Own the spotlight in a specific city — your banner on every page for users browsing that city.'],
      ['fa-chart-line','Analytics Dashboard','Track impressions, clicks, and conversions for every ad you run — real-time, transparent reporting.'],
    ] as [$icon,$title,$desc])
    <div class="adv-fmt">
      <div class="adv-fmt-icon"><i class="fa-solid {{ $icon }}"></i></div>
      <div>
        <h4>{{ $title }}</h4>
        <p>{{ $desc }}</p>
      </div>
    </div>
    @endforeach
  </div>

  <div style="text-align:center;margin:36px 0 12px">
    <div style="font-family:var(--fh);font-size:22px;font-weight:800;color:var(--text)">Advertising Packages</div>
    <div style="font-size:13.5px;color:var(--muted);margin-top:6px">Flexible plans for every budget. All prices in CAD/month.</div>
  </div>

  <div class="adv-packages">
    <div class="adv-pkg">
      <h3>Starter</h3>
      <div style="font-size:12.5px;color:var(--muted)">For small businesses &amp; solo entrepreneurs</div>
      <div class="price">$49 <small>/ month</small></div>
      <ul>
        <li><i class="fa-solid fa-check"></i> 1 Featured Listing</li>
        <li><i class="fa-solid fa-check"></i> Sidebar banner (homepage)</li>
        <li><i class="fa-solid fa-check"></i> 1 city targeting</li>
        <li><i class="fa-solid fa-check"></i> Basic analytics</li>
        <li><i class="fa-solid fa-check"></i> 30-day campaign</li>
      </ul>
      <a href="#enquiry" class="pkg-btn">Get Started</a>
    </div>
    <div class="adv-pkg popular">
      <div class="popular-badge">Most Popular</div>
      <h3>Growth</h3>
      <div style="font-size:12.5px;color:var(--muted)">For growing businesses looking for maximum reach</div>
      <div class="price">$149 <small>/ month</small></div>
      <ul>
        <li><i class="fa-solid fa-check"></i> 3 Featured Listings</li>
        <li><i class="fa-solid fa-check"></i> Homepage + inline banners</li>
        <li><i class="fa-solid fa-check"></i> Up to 3 cities</li>
        <li><i class="fa-solid fa-check"></i> Sponsored blog post (1/month)</li>
        <li><i class="fa-solid fa-check"></i> Advanced analytics dashboard</li>
        <li><i class="fa-solid fa-check"></i> Priority customer support</li>
      </ul>
      <a href="#enquiry" class="pkg-btn">Get Started</a>
    </div>
    <div class="adv-pkg">
      <h3>Enterprise</h3>
      <div style="font-size:12.5px;color:var(--muted)">For brands &amp; agencies running national campaigns</div>
      <div class="price" style="font-size:20px">Custom</div>
      <ul>
        <li><i class="fa-solid fa-check"></i> Unlimited featured placements</li>
        <li><i class="fa-solid fa-check"></i> All ad positions site-wide</li>
        <li><i class="fa-solid fa-check"></i> All cities / national reach</li>
        <li><i class="fa-solid fa-check"></i> Newsletter sponsorship</li>
        <li><i class="fa-solid fa-check"></i> Dedicated account manager</li>
        <li><i class="fa-solid fa-check"></i> Custom reporting &amp; invoicing</li>
      </ul>
      <a href="#enquiry" class="pkg-btn">Contact Us</a>
    </div>
  </div>

  {{-- Enquiry Form --}}
  <div id="enquiry" style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:30px;margin-top:32px">
    <div style="font-family:var(--fh);font-size:20px;font-weight:800;color:var(--text);margin-bottom:6px">Send an Enquiry</div>
    <div style="font-size:13px;color:var(--muted);margin-bottom:22px">Fill in your details and we'll get back to you within 24 hours with a custom proposal.</div>

    <form action="{{ route('advertise.store') }}" method="POST">
      @csrf
      @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px">
          {{ $errors->first() }}
        </div>
      @endif
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
        <div>
          <label style="font-size:12.5px;font-weight:600;color:var(--text);display:block;margin-bottom:5px">Your Name *</label>
          <input name="name" value="{{ old('name') }}" required placeholder="John Smith" style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb);box-sizing:border-box">
        </div>
        <div>
          <label style="font-size:12.5px;font-weight:600;color:var(--text);display:block;margin-bottom:5px">Email Address *</label>
          <input name="email" type="email" value="{{ old('email') }}" required placeholder="you@example.com" style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb);box-sizing:border-box">
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
        <div>
          <label style="font-size:12.5px;font-weight:600;color:var(--text);display:block;margin-bottom:5px">Business Name</label>
          <input name="business_name" value="{{ old('business_name') }}" placeholder="GoBazaar Pvt Ltd" style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb);box-sizing:border-box">
        </div>
        <div>
          <label style="font-size:12.5px;font-weight:600;color:var(--text);display:block;margin-bottom:5px">Phone Number</label>
          <input name="phone" value="{{ old('phone') }}" placeholder="+1 (416) 000-0000" style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb);box-sizing:border-box">
        </div>
      </div>
      <div style="margin-bottom:14px">
        <label style="font-size:12.5px;font-weight:600;color:var(--text);display:block;margin-bottom:5px">Package of Interest</label>
        <select name="package" style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb);background:#fff">
          <option value="">-- Select a package --</option>
          <option value="Starter" {{ old('package')=='Starter'?'selected':'' }}>Starter — $49/month</option>
          <option value="Growth" {{ old('package')=='Growth'?'selected':'' }}>Growth — $149/month</option>
          <option value="Enterprise" {{ old('package')=='Enterprise'?'selected':'' }}>Enterprise — Custom</option>
          <option value="Other" {{ old('package')=='Other'?'selected':'' }}>Other / Not sure yet</option>
        </select>
      </div>
      <div style="margin-bottom:20px">
        <label style="font-size:12.5px;font-weight:600;color:var(--text);display:block;margin-bottom:5px">Tell us about your campaign *</label>
        <textarea name="message" required rows="4" placeholder="What would you like to advertise? Which cities are you targeting? Any specific goals or budget range?" style="width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb);resize:vertical;box-sizing:border-box">{{ old('message') }}</textarea>
      </div>
      <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:12px 32px;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;transition:background .2s">
        <i class="fa-solid fa-paper-plane" style="margin-right:7px"></i>Send Enquiry
      </button>
    </form>
  </div>

</div>
@endsection
