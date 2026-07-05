@extends('layouts.app')
@section('title', 'About GoBazaar — Canada\'s #1 Indian Community Portal')
@section('description', 'Learn about GoBazaar — our mission, story, and commitment to connecting the Indian-Canadian community.')

@push('styles')
<style>
.page-hero{background:var(--primary);padding:52px 24px 48px;text-align:center}
.page-hero h1{font-family:var(--fh);font-size:36px;font-weight:800;color:#fff;margin-bottom:10px}
.page-hero p{font-size:15px;color:rgba(255,255,255,.75);max-width:560px;margin:0 auto}
.page-wrap{max-width:900px;margin:0 auto;padding:40px 24px}
.about-grid{display:grid;grid-template-columns:1fr 1fr;gap:28px;margin:36px 0}
.about-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:26px}
.about-card-icon{width:48px;height:48px;border-radius:12px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:14px}
.about-card-icon i{color:var(--primary)}
.about-card h3{font-family:var(--fh);font-size:16px;font-weight:700;color:var(--text);margin-bottom:8px}
.about-card p{font-size:13.5px;color:var(--muted);line-height:1.6}
.stat-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;background:var(--primary);border-radius:var(--radius);padding:30px 24px;margin:32px 0;text-align:center}
.stat-row .num{font-family:var(--fh);font-size:28px;font-weight:800;color:#fff}
.stat-row .lbl{font-size:12px;color:rgba(255,255,255,.65);margin-top:3px}
.team-section h2{font-family:var(--fh);font-size:22px;font-weight:800;color:var(--text);margin-bottom:6px}
.team-section p{font-size:14px;color:var(--muted);line-height:1.7;margin-bottom:14px}
@media(max-width:700px){
  .about-grid{grid-template-columns:1fr}
  .stat-row{grid-template-columns:repeat(2,1fr)}
  .page-hero h1{font-size:26px}
}
</style>
@endpush

@section('content')
<div class="page-hero">
  <h1>About <span style="color:var(--accent)">GoBazaar</span></h1>
  <p>Canada's #1 Indian Community Portal — connecting millions of Indians across Canada since 2020.</p>
</div>

<div class="page-wrap">

  <div class="team-section">
    <h2>Our Story</h2>
    <p>GoBazaar was born from a simple idea: every Indian who arrives in Canada deserves a trusted, one-stop platform to buy, sell, find jobs, discover events, connect with local businesses, and feel at home. What started as a small classifieds board has grown into Canada's largest Indian community portal — spanning Classifieds, Yellow Pages, Events, Jobs, Blog, and more.</p>
    <p>We are built by the community, for the community. Our team is passionate about making the Indian-Canadian experience richer, easier, and more connected — whether you're searching for a room to rent, a reliable immigration consultant, the best biryani in Brampton, or your life partner.</p>
  </div>

  <div class="stat-row">
    <div><div class="num">50K+</div><div class="lbl">Registered Users</div></div>
    <div><div class="num">120K+</div><div class="lbl">Listings Posted</div></div>
    <div><div class="num">8K+</div><div class="lbl">Businesses Listed</div></div>
    <div><div class="num">10+</div><div class="lbl">Cities Covered</div></div>
  </div>

  <div class="about-grid">
    <div class="about-card">
      <div class="about-card-icon"><i class="fa-solid fa-bullseye"></i></div>
      <h3>Our Mission</h3>
      <p>To empower the Indian-Canadian community with a trusted platform where every member can buy, sell, find work, celebrate culture, and connect with each other — all in one place.</p>
    </div>
    <div class="about-card">
      <div class="about-card-icon"><i class="fa-solid fa-eye"></i></div>
      <h3>Our Vision</h3>
      <p>To be the most trusted and beloved community platform for Indians across Canada — a digital home that reflects the warmth, vibrancy, and diversity of our community.</p>
    </div>
    <div class="about-card">
      <div class="about-card-icon"><i class="fa-solid fa-handshake"></i></div>
      <h3>Our Values</h3>
      <p>Trust, transparency, and community. We verify listings, moderate content, and provide a safe space where every member is treated with respect and every transaction is supported.</p>
    </div>
    <div class="about-card">
      <div class="about-card-icon"><i class="fa-solid fa-shield-halved"></i></div>
      <h3>Safety First</h3>
      <p>From verified badges to report tools and human moderation, we work around the clock to keep GoBazaar scam-free and trustworthy for every user.</p>
    </div>
  </div>

  <div class="team-section">
    <h2>What We Offer</h2>
    <p>GoBazaar is more than a classifieds site. Here's what you'll find:</p>
    <ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:10px;margin:0 0 24px">
      @foreach([
        ['fa-tag','Classifieds','Buy and sell anything — furniture, electronics, clothing, vehicles, and more.'],
        ['fa-building','Business Directory','Find Indian-owned restaurants, salons, doctors, lawyers, immigration consultants, grocery stores, and more.'],
        ['fa-calendar','Events','Discover Diwali celebrations, Navratri garba, cultural festivals, and community gatherings near you.'],
        ['fa-briefcase','Jobs','Post and find jobs in IT, healthcare, hospitality, and more within the Indian-Canadian community.'],
        ['fa-newspaper','Blog & News','Stay informed with community news, immigration updates, and lifestyle articles.'],
      ] as [$icon,$title,$desc])
      <li style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;display:flex;gap:14px;align-items:flex-start">
        <div style="width:38px;height:38px;border-radius:10px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0">
          <i class="fa-solid {{ $icon }}" style="color:var(--primary)"></i>
        </div>
        <div>
          <div style="font-size:13.5px;font-weight:700;color:var(--text);margin-bottom:3px">{{ $title }}</div>
          <div style="font-size:12.5px;color:var(--muted)">{{ $desc }}</div>
        </div>
      </li>
      @endforeach
    </ul>
  </div>

  <div style="background:var(--primary-light);border:1px solid #c7d4f0;border-radius:var(--radius);padding:22px 24px;text-align:center">
    <div style="font-family:var(--fh);font-size:17px;font-weight:700;color:var(--primary);margin-bottom:8px">Ready to join the community?</div>
    <div style="font-size:13px;color:var(--muted);margin-bottom:16px">Register free and start buying, selling, and connecting today.</div>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
      <a href="{{ route('register') }}" style="background:var(--primary);color:#fff;padding:10px 24px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">Register Free</a>
      <a href="{{ route('contact') }}" style="background:#fff;color:var(--primary);border:1.5px solid var(--primary);padding:10px 24px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">Contact Us</a>
    </div>
  </div>

</div>
@endsection
