@extends('layouts.app')
@section('title', 'GoBazzar — Indian Community Portal in Canada')
@section('description', "Canada's #1 Indian community portal — Classifieds, Yellow Pages, Events, Jobs, Blog, Matrimonial and more for Indians in Canada.")

@push('styles')
<style>
/* ── HERO ──────────────────────────────────────────────────────── */
.hero{background:var(--dark);padding:36px 20px;position:relative;overflow:hidden;background-image:url("https://images.unsplash.com/photo-1567360425852-ed03ee47df0d?w=1400&q=70&fit=crop");background-size:cover;background-position:center}
.hero::before{content:'';position:absolute;inset:0;background:rgba(15,5,4,.75);z-index:0;pointer-events:none}
.hero::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 80% at 70% 50%,rgba(192,57,43,.35) 0%,transparent 70%);pointer-events:none;z-index:0}
.hero-inner{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 420px;gap:40px;align-items:center;position:relative;z-index:1}
.hero-tag{display:inline-block;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.7);font-size:10.5px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;padding:4px 12px;border-radius:20px;margin-bottom:14px}
.hero h1{font-family:var(--fh);font-size:32px;font-weight:800;color:#fff;line-height:1.18;margin-bottom:10px}
.hero h1 span{color:var(--red2)}
.hero-desc{font-size:13.5px;color:rgba(255,255,255,.55);margin-bottom:22px;max-width:480px}
.hero-btns{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:22px}
.hero-stats{display:flex;gap:20px;border-top:1px solid rgba(255,255,255,.1);padding-top:18px;flex-wrap:wrap}
.hero-stat{font-size:11.5px;color:rgba(255,255,255,.5)}
.hero-stat b{color:rgba(255,255,255,.9);font-family:var(--fh);font-size:14px;display:block}
.hero-search-box{background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.1);border-radius:var(--rl);padding:22px;backdrop-filter:blur(8px)}
.hero-search-box h3{font-family:var(--fh);font-size:13px;font-weight:700;color:rgba(255,255,255,.8);margin-bottom:14px;text-transform:uppercase;letter-spacing:.8px}
.search-field{display:flex;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:var(--r);height:40px;align-items:center;padding:0 12px;gap:8px;margin-bottom:10px}
.search-field input,.search-field select{background:none;border:none;font-size:12.5px;color:#fff;flex:1;outline:none}
.search-field input::placeholder{color:rgba(255,255,255,.35)}
.search-field select option{background:var(--dark);color:#fff}
.quick-cats{display:grid;grid-template-columns:repeat(3,1fr);gap:6px}
.qcat{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:var(--r);padding:8px 6px;text-align:center;transition:all .15s;font-size:11px;color:rgba(255,255,255,.7);display:block;text-decoration:none}
.qcat:hover{background:var(--red);border-color:var(--red);color:#fff}
.qcat .icon{font-size:18px;display:block;margin-bottom:3px}
/* ── LAYOUT ────────────────────────────────────────────────────── */
.main-wrap{max-width:1200px;margin:0 auto;padding:20px;display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start}
.content{min-width:0}
.sidebar-right{display:flex;flex-direction:column;gap:0}
.sec-head{display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--dark);color:#fff;border-radius:var(--r) var(--r) 0 0}
.sec-head h2{font-family:var(--fh);font-size:12.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.sec-head a{font-size:11px;color:rgba(255,255,255,.6);transition:color .15s}
.sec-head a:hover{color:#fff}
.sec-box{background:var(--surface);border:1.5px solid var(--border);border-top:none;border-radius:0 0 var(--r) var(--r);margin-bottom:18px;overflow:hidden}
.empty-note{padding:24px;text-align:center;color:var(--muted);font-size:13px}
.empty-note a{color:var(--red)}
/* ── ROW-ITEM ──────────────────────────────────────────────────── */
.row-item{display:flex;align-items:flex-start;padding:9px 12px;border-bottom:1px solid var(--border);transition:background .12s;gap:10px;text-decoration:none;color:var(--text)}
.row-item:last-child{border-bottom:none}
.row-item:hover{background:var(--red-pale)}
.ri-body{flex:1;min-width:0}
.ri-title{font-size:12.5px;font-weight:500;line-height:1.4;color:var(--text)}
.ri-sub{font-size:11px;color:var(--muted);margin-top:2px}
.ri-loc{font-size:10.5px;color:var(--hint);margin-top:1px}
.ri-arr{color:var(--hint);font-size:11px;flex-shrink:0;align-self:center}
.ri-badge{font-size:9.5px;font-weight:700;padding:2px 7px;border-radius:20px;white-space:nowrap;flex-shrink:0;align-self:flex-start;margin-top:2px}
.badge-new{background:#DCFCE7;color:#15803D}
.badge-hot{background:#FEE2E2;color:var(--red)}
.badge-feat{background:#FEF9C3;color:#92400E}
/* ── AD CARDS ──────────────────────────────────────────────────── */
.ads-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;padding:12px}
.ad-card{border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;transition:all .18s;display:block;color:var(--text);text-decoration:none}
.ad-card:hover{border-color:var(--red2);box-shadow:var(--sh);transform:translateY(-2px)}
.ad-thumb{height:120px;display:flex;align-items:center;justify-content:center;font-size:36px;position:relative;overflow:hidden;background:var(--bg)}
.ad-thumb img{width:100%;height:100%;object-fit:cover}
.ad-body{padding:10px}
.ad-title{font-size:12px;font-weight:500;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:4px}
.ad-loc{font-size:10.5px;color:var(--muted);margin-bottom:4px}
.ad-price{font-family:var(--fh);font-size:15px;font-weight:700;color:var(--red)}
.ad-price small{font-size:10px;font-weight:400;color:var(--muted);font-family:var(--fb)}
.abs-badges{position:absolute;top:6px;left:6px;display:flex;gap:4px}
.vbadge{font-size:9.5px;font-weight:700;padding:2px 7px;border-radius:20px}
.vb-feat{background:var(--gold);color:#fff}
.vb-new{background:var(--blue);color:#fff}
/* ── BLOG CARDS ────────────────────────────────────────────────── */
.blog-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;padding:12px}
.blog-card{border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;transition:all .15s;display:block;color:var(--text);text-decoration:none}
.blog-card:hover{border-color:var(--red2);box-shadow:var(--sh);transform:translateY(-2px)}
.blog-thumb{height:100px;display:flex;align-items:center;justify-content:center;font-size:32px;overflow:hidden;background:var(--bg)}
.blog-thumb img{width:100%;height:100%;object-fit:cover}
.blog-body{padding:10px}
.blog-cat-tag{font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--red);margin-bottom:4px}
.blog-title{font-size:12px;font-weight:500;line-height:1.4;margin-bottom:4px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.blog-date{font-size:10px;color:var(--muted)}
.blog-read{font-size:10.5px;color:var(--red);font-weight:500;margin-top:5px}
/* ── EVENT ITEMS ───────────────────────────────────────────────── */
.ev-item{display:flex;gap:10px;padding:9px 12px;border-bottom:1px solid var(--border);transition:background .12s;text-decoration:none;color:var(--text)}
.ev-item:hover{background:var(--red-pale)}
.ev-item:last-child{border-bottom:none}
.ev-date-box{min-width:38px;text-align:center;background:var(--red-pale);border:1px solid var(--border2);border-radius:var(--r);padding:5px 4px;flex-shrink:0}
.ev-date-box .day{font-family:var(--fh);font-size:18px;font-weight:800;color:var(--red);line-height:1}
.ev-date-box .mon{font-size:9px;text-transform:uppercase;font-weight:700;color:var(--muted)}
.ev-body{flex:1;min-width:0}
.ev-title{font-size:12.5px;font-weight:500;line-height:1.35}
.ev-meta{font-size:11px;color:var(--muted);margin-top:2px}
.ev-badge{font-size:9px;font-weight:700;padding:2px 7px;border-radius:20px;background:var(--red-pale);color:var(--red);white-space:nowrap;flex-shrink:0;align-self:flex-start;margin-top:2px}
/* ── CATEGORY ICON BAR ─────────────────────────────────────────── */
.cat-icon-bar{display:grid;border-bottom:1px solid var(--border)}
.cat-icon-item{padding:12px 6px;text-align:center;border-right:1px solid var(--border);transition:background .12s;display:block;text-decoration:none;color:var(--text)}
.cat-icon-item:last-child{border-right:none}
.cat-icon-item:hover{background:var(--red-pale)}
.ci-icon{font-size:22px;margin-bottom:5px}
.ci-lbl{font-size:11px;font-weight:600}
/* ── JOB QUICK CATS ────────────────────────────────────────────── */
.job-cats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;padding:10px 12px;border-bottom:1px solid var(--border)}
.job-cat{padding:10px;border:1.5px solid var(--border);border-radius:var(--r);text-align:center;font-size:11.5px;font-weight:500;transition:border-color .15s;display:block;text-decoration:none;color:var(--text)}
.job-cat:hover{border-color:var(--red);color:var(--red)}
/* ── SIDEBAR WIDGETS ───────────────────────────────────────────── */
.widget{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;margin-bottom:16px}
.widget-head{background:var(--red);color:#fff;padding:8px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;display:flex;justify-content:space-between;align-items:center}
.widget-head a{font-size:10.5px;color:rgba(255,255,255,.7);font-family:var(--fb);font-weight:400;text-decoration:none}
.widget-head a:hover{color:#fff}
.widget-body{padding:12px}
.poll-opt{display:flex;align-items:center;gap:8px;margin-bottom:8px}
.poll-label{font-size:11.5px;flex:1}
.poll-bar-wrap{flex:2;background:var(--bg);border:1px solid var(--border2);border-radius:4px;height:16px;overflow:hidden;position:relative}
.poll-bar{height:100%;background:var(--red-pale)}
.poll-val{position:absolute;right:5px;top:50%;transform:translateY(-50%);font-size:9px;color:var(--muted);font-weight:600}
.poll-btn{width:100%;padding:8px;background:var(--red);color:#fff;border:none;border-radius:var(--r);font-size:12px;font-weight:600;margin-top:6px;cursor:pointer;transition:background .15s}
.poll-btn:hover{background:var(--red-dark)}
.qlink{display:flex;align-items:center;gap:8px;padding:8px 12px;border-bottom:1px solid var(--border);transition:all .12s;font-size:12.5px;text-decoration:none;color:var(--text)}
.qlink:last-child{border-bottom:none}
.qlink:hover{background:var(--red-pale);color:var(--red)}
.ql-icon{font-size:16px;width:24px;text-align:center}
.ql-arr{margin-left:auto;font-size:10px;color:var(--hint)}
.w-ad{display:block;width:100%;margin-bottom:8px;cursor:pointer;transition:transform .15s;text-decoration:none}
.w-ad:last-child{margin-bottom:0}
.w-ad:hover{transform:scale(1.01)}
.w-ad-inner{padding:14px;border:1.5px solid var(--border);border-radius:var(--r);background:var(--bg);text-align:center}
.w-ad-inner .icon{font-size:28px;margin-bottom:6px}
.w-ad-inner .title{font-size:12px;font-weight:600;margin-bottom:3px;color:var(--text)}
.w-ad-inner .sub{font-size:10.5px;color:var(--muted)}
.w-ad-inner .cta{display:inline-block;margin-top:8px;background:var(--red);color:#fff;font-size:10.5px;font-weight:600;padding:4px 12px;border-radius:20px}
.trend-item{display:flex;align-items:flex-start;gap:8px;padding:9px 12px;border-bottom:1px solid var(--border);text-decoration:none;color:var(--text);transition:background .12s}
.trend-item:hover{background:var(--red-pale)}
.trend-item:last-child{border-bottom:none}
.trend-num{font-family:var(--fh);font-size:20px;font-weight:800;color:var(--border2);line-height:1;width:24px;flex-shrink:0}
.trend-title{font-size:12px;font-weight:500;line-height:1.4}
.trend-cat{font-size:10px;color:var(--muted)}
.sub-form{padding:12px;display:flex;flex-direction:column;gap:8px}
.sub-form input{padding:8px 12px;border:1.5px solid var(--border2);border-radius:var(--r);font-size:12px;background:var(--bg);outline:none}
.sub-form button{padding:9px;background:var(--red);color:#fff;border-radius:var(--r);font-size:12.5px;font-weight:600;border:none;cursor:pointer}
.sub-form button:hover{background:var(--red-dark)}
/* ── FOOTER ────────────────────────────────────────────────────── */
.home-footer{background:var(--dark);padding:32px 20px 20px;margin-top:0}
.foot-inner{max-width:1200px;margin:0 auto}
.foot-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr;gap:24px;margin-bottom:24px}
.foot-brand p{font-size:12px;color:rgba(255,255,255,.45);line-height:1.7;margin-top:8px;max-width:260px}
.serving{font-size:11.5px;color:rgba(255,255,255,.55);font-style:italic;border-top:1px solid rgba(255,255,255,.07);padding-top:14px;margin-top:14px}
.foot-col h4{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.9px;color:rgba(255,255,255,.6);margin-bottom:12px}
.foot-col a{display:block;font-size:12px;color:rgba(255,255,255,.4);margin-bottom:7px;transition:color .15s}
.foot-col a:hover{color:rgba(255,255,255,.9)}
.foot-bottom{border-top:1px solid rgba(255,255,255,.07);padding-top:16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
.foot-bottom p{font-size:11px;color:rgba(255,255,255,.35)}
.foot-links{display:flex;gap:14px;flex-wrap:wrap}
.foot-links a{font-size:11px;color:rgba(255,255,255,.35);transition:color .15s}
.foot-links a:hover{color:rgba(255,255,255,.7)}
/* ── MOBILE HERO SEARCH ────────────────────────────────────────── */
.hero-mobile-search{display:none;margin-top:16px}
.hero-mobile-search .search-field{background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.2)}
.hero-mobile-search .search-field input,.hero-mobile-search .search-field select{color:#fff}
.hero-mobile-search .search-field select option{background:var(--dark)}
.hero-mobile-btn{width:100%;background:var(--red);color:#fff;border:none;border-radius:var(--r);padding:10px;font-size:13px;font-weight:600;cursor:pointer;margin-top:4px}

/* ── RESPONSIVE ────────────────────────────────────────────────── */
@media(max-width:900px){
  .main-wrap{grid-template-columns:1fr;padding:14px}
  .sidebar-right{display:none}
  .hero-inner{grid-template-columns:1fr}
  .hero-search-box{display:none}
  .hero-mobile-search{display:block}
  .hero{padding:24px 16px 28px}
  .hero h1{font-size:24px}
  .blog-grid,.ads-grid{grid-template-columns:repeat(2,1fr)}
  .foot-grid{grid-template-columns:1fr 1fr}
}
@media(max-width:600px){
  .hero h1{font-size:20px}
  .hero-btns{gap:8px}
  .hero-btns .btn{font-size:12px;padding:7px 12px}
  .hero-stats{gap:12px}
  .job-cats{grid-template-columns:1fr 1fr}
  .foot-grid{grid-template-columns:1fr}
  .ads-grid{grid-template-columns:1fr}
}
</style>
@endpush

@section('content')

{{-- ═══════════ HERO ═══════════ --}}
<div class="hero">
  <div class="hero-inner">
    <div>
      <span class="hero-tag">Indian Community in Canada — Since 2024</span>
      <h1>Canada's #1 Indian<br>Community <span>Portal</span></h1>
      @if(request('city') || request('province'))
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(192,57,43,.25);border:1px solid rgba(192,57,43,.5);border-radius:20px;padding:5px 14px;margin-bottom:12px">
          <span style="font-size:14px">📍</span>
          <span style="font-size:13px;color:#fff;font-weight:600">{{ request('city') ? request('city').', ' : '' }}{{ request('province') }}</span>
          <a href="{{ route('home') }}" style="color:rgba(255,255,255,.5);font-size:12px;margin-left:4px">✕</a>
        </div>
      @endif
      <p class="hero-desc">Classifieds • Yellow Pages • Events • Jobs • Blog • Matrimonial — everything the Indian-Canadian community needs, in one place.</p>
      <div class="hero-btns">
        @auth
          <a href="{{ route('post.create') }}" class="btn btn-red">+ Post Free Ad</a>
        @else
          <a href="{{ route('register') }}" class="btn btn-red">+ Post Free Ad</a>
        @endauth
        <a href="{{ route('directory.index') }}" class="btn btn-dark">🗂 Yellow Pages</a>
        <a href="{{ route('events.index') }}" class="btn btn-ghost" style="border-color:rgba(255,255,255,.2);color:rgba(255,255,255,.7)">📅 Events</a>
      </div>
      <div class="hero-stats">
        <div class="hero-stat"><b>{{ $stats['businesses'] > 0 ? number_format($stats['businesses']) : '400' }}+</b>Businesses Listed</div>
        <div class="hero-stat"><b>{{ $stats['listings'] > 0 ? number_format($stats['listings']) : '1,200' }}+</b>Free Ads Posted</div>
        <div class="hero-stat"><b>{{ $stats['events'] > 0 ? number_format($stats['events']) : '50' }}+</b>Events This Month</div>
        <div class="hero-stat"><b>1.6M+</b>Indian-Canadians</div>
      </div>

      {{-- Mobile-only search bar --}}
      <div class="hero-mobile-search">
        <form onsubmit="heroSubmit(event)">
          <div class="search-field">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><circle cx="6.5" cy="6.5" r="5" stroke="rgba(255,255,255,.4)" stroke-width="1.5"/><path d="M10.5 10.5L14 14" stroke="rgba(255,255,255,.4)" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input type="text" id="hero-search-m" placeholder="Search listings, businesses…" value="{{ request('search') }}">
          </div>
          <div class="search-field">
            <select id="hero-province-m" onchange="heroLoadCitiesM(this.value)">
              <option value="">All Provinces</option>
              @foreach($provinces as $prov)
                <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
              @endforeach
            </select>
          </div>
          <div class="search-field">
            <select id="hero-city-m">
              <option value="">All Cities</option>
              @foreach($cities as $city)
                <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="hero-mobile-btn">Search GoBazzar →</button>
        </form>
      </div>
    </div>
    <div class="hero-search-box">
      <h3>🔍 Find in GoBazzar</h3>
      <form id="hero-search-form" onsubmit="heroSubmit(event)">
        <div class="search-field">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><circle cx="6.5" cy="6.5" r="5" stroke="rgba(255,255,255,.4)" stroke-width="1.5"/><path d="M10.5 10.5L14 14" stroke="rgba(255,255,255,.4)" stroke-width="1.5" stroke-linecap="round"/></svg>
          <input type="text" id="hero-search" placeholder="Search listings, businesses…" value="{{ request('search') }}">
        </div>
        <div class="search-field">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><rect x="2" y="2" width="12" height="12" rx="2" stroke="rgba(255,255,255,.4)" stroke-width="1.5" fill="none"/><path d="M5 8h6M5 5h6M5 11h3" stroke="rgba(255,255,255,.4)" stroke-width="1.2" stroke-linecap="round"/></svg>
          <select id="hero-province" onchange="heroLoadCities(this.value)">
            <option value="">All Provinces</option>
            @foreach($provinces as $prov)
              <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
            @endforeach
          </select>
        </div>
        <div class="search-field" style="margin-bottom:12px">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><path d="M8 1C5.24 1 3 3.24 3 6c0 3.75 5 9 5 9s5-5.25 5-9c0-2.76-2.24-5-5-5z" stroke="rgba(255,255,255,.4)" stroke-width="1.5" fill="none"/><circle cx="8" cy="6" r="1.5" fill="rgba(255,255,255,.4)"/></svg>
          <select id="hero-city">
            <option value="">All Cities</option>
            @foreach($cities as $city)
              <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" style="width:100%;background:var(--red);color:#fff;border:none;border-radius:var(--r);padding:10px;font-size:13px;font-weight:600;cursor:pointer;margin-bottom:12px;transition:background .15s" onmouseover="this.style.background='var(--red-dark)'" onmouseout="this.style.background='var(--red)'">Search GoBazzar →</button>
      </form>
      @if(request('city') || request('province'))
        <div style="text-align:center;margin-bottom:10px;font-size:12px;color:rgba(255,255,255,.5)">
          Showing results for
          <strong style="color:rgba(255,255,255,.85)">{{ request('city') ?: request('province') }}</strong>
          <a href="{{ route('home') }}" style="color:var(--red2);margin-left:8px">✕ Clear</a>
        </div>
      @endif
      <div class="quick-cats">
        <a href="{{ route('classifieds.index', array_filter(['category' => $classifiedCategories->where('name','Real Estate')->first()?->id, 'city' => request('city'), 'province' => request('province')])) }}" class="qcat"><span class="icon">🏠</span>Housing</a>
        <a href="{{ route('jobs.index', array_filter(['city' => request('city'), 'province' => request('province')])) }}" class="qcat"><span class="icon">💼</span>Jobs</a>
        <a href="{{ route('classifieds.index', array_filter(['category' => $classifiedCategories->where('name','Autos')->first()?->id, 'city' => request('city'), 'province' => request('province')])) }}" class="qcat"><span class="icon">🚗</span>Autos</a>
        <a href="{{ route('directory.index', array_filter(['city' => request('city'), 'province' => request('province')])) }}" class="qcat"><span class="icon">🍛</span>Dining</a>
        <a href="{{ route('events.index', array_filter(['city' => request('city'), 'province' => request('province')])) }}" class="qcat"><span class="icon">🎉</span>Events</a>
        <a href="{{ route('matrimonial.index', array_filter(['city' => request('city'), 'province' => request('province')])) }}" class="qcat"><span class="icon">💍</span>Matrimony</a>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════ MAIN LAYOUT ═══════════ --}}
<div class="main-wrap">
<div class="content">

{{-- ── 1. COMMUNITY BLOG ────────────────────────────────────────── --}}
<div class="sec-head"><h2>📰 Community Blog</h2><a href="{{ route('blog.index') }}">View All</a></div>
<div class="sec-box">
  @if($blogPosts->isEmpty())
    <div class="blog-grid">
      @php
        $placeholders = [
          ['bg'=>'#FEF9C3','icon'=>'📜','title'=>'India-Canada Trade Relations: What it Means for the Diaspora in 2026','date'=>'Coming soon'],
          ['bg'=>'#EFF6FF','icon'=>'🛂','title'=>'Birthright Citizenship Debate and Impact on Indian Community in Canada','date'=>'Coming soon'],
          ['bg'=>'#FEF2F1','icon'=>'🎆','title'=>'Diwali 2025 Celebrations Across Canada — Highlights & Photos','date'=>'Coming soon'],
          ['bg'=>'#F0FDF4','icon'=>'🌸','title'=>'Navratri & Garba Events Across Toronto, Brampton & Calgary','date'=>'Coming soon'],
        ];
      @endphp
      @foreach($placeholders as $ph)
      <div class="blog-card" style="pointer-events:none">
        <div class="blog-thumb" style="background:{{ $ph['bg'] }}">{{ $ph['icon'] }}</div>
        <div class="blog-body">
          <div class="blog-title">{{ $ph['title'] }}</div>
          <div class="blog-date">{{ $ph['date'] }}</div>
        </div>
      </div>
      @endforeach
    </div>
    <div style="padding:10px 12px;text-align:center;border-top:1px solid var(--border)">
      <a href="/gobazzar-app/public/admin/blog-posts/create" style="font-size:12px;color:var(--red);font-weight:500">+ Add Blog Post from Admin →</a>
    </div>
  @else
    <div class="blog-grid">
      @foreach($blogPosts as $post)
      <a href="{{ route('blog.show', $post->slug) }}" class="blog-card">
        <div class="blog-thumb" @if(!$post->image) style="background:#FEF9C3" @endif>
          @if($post->image)
            <img src="{{ asset('storage/'.$post->image) }}" alt="{{ $post->title }}">
          @else 📰 @endif
        </div>
        <div class="blog-body">
          @if($post->category)<div class="blog-cat-tag">{{ $post->category }}</div>@endif
          <div class="blog-title">{{ $post->title }}</div>
          <div class="blog-date">Posted: {{ ($post->published_at ?? $post->created_at)->format('M j, Y') }}</div>
          <div class="blog-read">Read More →</div>
        </div>
      </a>
      @endforeach
    </div>
  @endif
</div>

{{-- ── 2. COMMUNITY CALENDAR ────────────────────────────────────── --}}
<div class="sec-head"><h2>📅 Community Calendar</h2><a href="{{ route('events.index') }}">Events & More</a></div>
<div class="sec-box">
  @if($upcomingEvents->isEmpty())
    <div class="empty-note">No upcoming events yet. <a href="{{ route('post.create') }}">Post an event →</a></div>
  @else
    @foreach($upcomingEvents as $event)
    <a href="{{ route('events.show', $event->slug) }}" class="ev-item">
      <div class="ev-date-box">
        <div class="day">{{ $event->start_date->format('j') }}</div>
        <div class="mon">{{ $event->start_date->format('M') }}</div>
      </div>
      <div class="ev-body">
        <div class="ev-title">{{ $event->title }}</div>
        <div class="ev-meta">
          @if($event->city)📍 {{ $event->city }}@endif
          @if($event->city && $event->venue) · @endif
          @if($event->venue){{ $event->venue }}@endif
        </div>
      </div>
      @if($event->price)
        <span class="ev-badge">{{ strtolower($event->price) === 'free' || $event->price === '0' ? 'Free' : $event->price }}</span>
      @endif
    </a>
    @endforeach
  @endif
</div>

{{-- ── 3. COMMUNITY NEWS (static anchor links) ─────────────────── --}}
<div class="sec-head"><h2>📢 Community News</h2><a href="{{ route('feed') }}">Community Feed</a></div>
<div class="sec-box">
  <a href="{{ route('blog.index') }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">New IRCC Processing Times for PR Applications — Faster in 2026?</div>
      <div class="ri-sub">Community · Immigration Updates</div>
    </div>
    <span class="ri-badge badge-hot">Hot</span>
    <span class="ri-arr">›</span>
  </a>
  <a href="{{ route('blog.index') }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">Best Indian grocery stores in Brampton area — community recommendations</div>
      <div class="ri-sub">Community · Shopping Guide</div>
    </div>
    <span class="ri-arr">›</span>
  </a>
  <a href="{{ route('blog.index') }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">India-Canada trade deal update — what it means for us</div>
      <div class="ri-sub">Blog · Business & Finance</div>
    </div>
    <span class="ri-arr">›</span>
  </a>
  <a href="{{ route('feed') }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">GoBazzar Community Feed — Share your thoughts with the community</div>
      <div class="ri-sub">Community Feed · Join the conversation</div>
    </div>
    <span class="ri-badge badge-new">New</span>
    <span class="ri-arr">›</span>
  </a>
</div>

{{-- ── 4. FREE CLASSIFIEDS ──────────────────────────────────────── --}}
<div class="sec-head"><h2>📋 Free Classifieds</h2><a href="{{ route('classifieds.index') }}">Post Free Ad</a></div>
<div class="sec-box">
  {{-- Dynamic category icon bar --}}
  @if($classifiedCategories->isNotEmpty())
  <div class="cat-icon-bar" style="grid-template-columns:repeat({{ min($classifiedCategories->count(), 4) }},1fr)">
    @foreach($classifiedCategories->take(4) as $cat)
    <a href="{{ route('classifieds.index', ['category' => $cat->id]) }}" class="cat-icon-item">
      <div class="ci-icon">{{ $cat->icon }}</div>
      <div class="ci-lbl">{{ $cat->name }}</div>
    </a>
    @endforeach
  </div>
  @endif

  @if($latestListings->isEmpty())
    <div class="empty-note">No classifieds yet. <a href="{{ route('post.create') }}">Post the first one →</a></div>
  @else
    <div class="ads-grid">
      @foreach($latestListings as $listing)
      <a href="{{ route('classifieds.show', $listing->slug) }}" class="ad-card">
        <div class="ad-thumb">
          @if($listing->is_featured)<div class="abs-badges"><span class="vbadge vb-feat">Featured</span></div>@endif
          @if($listing->image)
            <img src="{{ asset('storage/'.$listing->image) }}" alt="{{ $listing->title }}">
          @else
            {{ $listing->category->icon ?? '📦' }}
          @endif
        </div>
        <div class="ad-body">
          <div class="ad-title">{{ $listing->title }}</div>
          <div class="ad-loc">📍 {{ $listing->location }}</div>
          @if($listing->price)
            <div class="ad-price">{{ $listing->price }}<small>{{ $listing->price_unit }}</small></div>
          @endif
        </div>
      </a>
      @endforeach
    </div>
  @endif
  <div style="padding:10px 12px;text-align:center;border-top:1px solid var(--border)">
    <a href="{{ route('classifieds.index') }}" style="font-size:12px;color:var(--red);font-weight:500">Free Classifieds — More ›</a>
  </div>
</div>

{{-- ── 5. BUSINESS DIRECTORY ────────────────────────────────────── --}}
<div class="sec-head"><h2>🗂 Business Directory</h2><a href="{{ route('directory.index') }}">{{ $stats['businesses'] > 0 ? number_format($stats['businesses']).'+ Listings' : 'View All' }}</a></div>
<div class="sec-box">
  <div style="padding:10px 12px;background:var(--green-bg);border-bottom:1px solid var(--border);font-size:11.5px;color:var(--green);font-weight:500">
    ✅ All businesses verified by GoBazzar · Businesses, Organizations & more
  </div>
  @if($latestBusinesses->isEmpty())
    <div class="empty-note">No businesses listed yet. <a href="{{ route('post.create') }}">List your business →</a></div>
  @else
    <div class="ads-grid">
      @foreach($latestBusinesses as $biz)
      <a href="{{ route('directory.show', $biz->slug) }}" class="ad-card">
        <div class="ad-thumb" style="background:#ecfdf5">
          @if($biz->logo)
            <img src="{{ asset('storage/'.$biz->logo) }}" alt="{{ $biz->name }}">
          @elseif($biz->image)
            <img src="{{ asset('storage/'.$biz->image) }}" alt="{{ $biz->name }}">
          @else
            {{ $biz->category->icon ?? '🏢' }}
          @endif
        </div>
        <div class="ad-body">
          <div class="ad-title">{{ $biz->name }}</div>
          <div class="ad-loc">
            {{ $biz->category->icon ?? '' }} {{ $biz->category->name ?? '' }}
            @if($biz->city) · {{ $biz->city }}@endif
          </div>
          @if($biz->rating > 0)
            <div style="font-size:11px;color:var(--amber);margin-top:4px">⭐ {{ number_format($biz->rating, 1) }}</div>
          @endif
        </div>
      </a>
      @endforeach
    </div>
  @endif
  <div style="padding:8px 12px;text-align:center;border-top:1px solid var(--border);display:flex;justify-content:center;gap:14px">
    <a href="{{ route('jobs.index') }}" style="font-size:12px;color:var(--red);font-weight:500">💼 Find a Job</a>
    <a href="{{ route('classifieds.index', ['category' => $classifiedCategories->where('name','Roommates')->first()?->id]) }}" style="font-size:12px;color:var(--red);font-weight:500">🤝 Roommate</a>
    <a href="{{ route('classifieds.index', ['category' => $classifiedCategories->where('name','Autos')->first()?->id]) }}" style="font-size:12px;color:var(--red);font-weight:500">🚗 Car</a>
    <a href="{{ route('classifieds.index', ['category' => $classifiedCategories->where('name','Real Estate')->first()?->id]) }}" style="font-size:12px;color:var(--red);font-weight:500">🏠 Real Estate</a>
  </div>
</div>

{{-- ── 6. PROFESSIONAL SERVICES ─────────────────────────────────── --}}
@if($professionalServices->isNotEmpty())
<div class="sec-head"><h2>⚖️ Professional Services</h2><a href="{{ route('directory.index') }}">View All</a></div>
<div class="sec-box">
  @foreach($professionalServices as $biz)
  <a href="{{ route('directory.show', $biz->slug) }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">{{ $biz->name }}</div>
      <div class="ri-sub">{{ $biz->category->icon ?? '' }} {{ $biz->category->name ?? 'Services' }}@if($biz->city) · {{ $biz->city }}@endif</div>
    </div>
    @if($biz->is_featured)<span class="ri-badge badge-feat">Featured</span>@endif
    <span class="ri-arr">›</span>
  </a>
  @endforeach
</div>
@endif

{{-- ── 7. EDUCATION / SPORTS ────────────────────────────────────── --}}
@if($educationSports->isNotEmpty())
<div class="sec-head"><h2>🎓 Education / Sports</h2><a href="{{ route('directory.index') }}">View All</a></div>
<div class="sec-box">
  @foreach($educationSports as $biz)
  <a href="{{ route('directory.show', $biz->slug) }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">{{ $biz->name }}</div>
      <div class="ri-sub">{{ $biz->category->icon ?? '' }} {{ $biz->category->name ?? '' }}@if($biz->city) · {{ $biz->city }}@endif</div>
    </div>
    @if($biz->is_featured)<span class="ri-badge badge-feat">Featured</span>@endif
    <span class="ri-arr">›</span>
  </a>
  @endforeach
</div>
@endif

{{-- ── 8. MEDICAL / DENTAL ──────────────────────────────────────── --}}
@if($medicalDental->isNotEmpty())
<div class="sec-head"><h2>🏥 Medical / Dental</h2><a href="{{ route('directory.index') }}">View All</a></div>
<div class="sec-box">
  @foreach($medicalDental as $biz)
  <a href="{{ route('directory.show', $biz->slug) }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">{{ $biz->name }}</div>
      <div class="ri-sub">{{ $biz->category->icon ?? '' }} {{ $biz->category->name ?? '' }}@if($biz->city) · {{ $biz->city }}@endif</div>
    </div>
    @if($biz->is_featured)<span class="ri-badge badge-feat">Featured</span>@endif
    <span class="ri-arr">›</span>
  </a>
  @endforeach
</div>
@endif

{{-- ── 9. DINING / RESTAURANTS ─────────────────────────────────── --}}
@if($diningBusinesses->isNotEmpty())
<div class="sec-head"><h2>🍛 Dining / Restaurants</h2><a href="{{ route('directory.index') }}">View All</a></div>
<div class="sec-box">
  @foreach($diningBusinesses as $biz)
  <a href="{{ route('directory.show', $biz->slug) }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">{{ $biz->name }}</div>
      <div class="ri-sub">{{ $biz->category->icon ?? '' }} {{ $biz->category->name ?? 'Restaurant' }}@if($biz->city) · {{ $biz->city }}@endif@if($biz->rating > 0) · ⭐ {{ number_format($biz->rating, 1) }}@endif</div>
    </div>
    @if($biz->is_featured)<span class="ri-badge badge-feat">Featured</span>@endif
    <span class="ri-arr">›</span>
  </a>
  @endforeach
</div>
@endif

{{-- ── 10. SALON & SPA ───────────────────────────────────────────── --}}
@if($salonSpa->isNotEmpty())
<div class="sec-head"><h2>💅 Salon & Spa</h2><a href="{{ route('directory.index') }}">View All</a></div>
<div class="sec-box">
  @foreach($salonSpa as $biz)
  <a href="{{ route('directory.show', $biz->slug) }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">{{ $biz->name }}</div>
      <div class="ri-sub">{{ $biz->category->icon ?? '' }} {{ $biz->category->name ?? '' }}@if($biz->city) · {{ $biz->city }}@endif</div>
    </div>
    @if($biz->is_featured)<span class="ri-badge badge-feat">Featured</span>@endif
    <span class="ri-arr">›</span>
  </a>
  @endforeach
</div>
@endif

{{-- ── 11. FASHIONS ─────────────────────────────────────────────── --}}
@if($fashionBiz->isNotEmpty())
<div class="sec-head"><h2>👗 Fashions</h2><a href="{{ route('directory.index') }}">View All</a></div>
<div class="sec-box">
  @foreach($fashionBiz as $biz)
  <a href="{{ route('directory.show', $biz->slug) }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">{{ $biz->name }}</div>
      <div class="ri-sub">{{ $biz->category->icon ?? '' }} {{ $biz->category->name ?? '' }}@if($biz->city) · {{ $biz->city }}@endif</div>
    </div>
    @if($biz->is_featured)<span class="ri-badge badge-feat">Featured</span>@endif
    <span class="ri-arr">›</span>
  </a>
  @endforeach
</div>
@endif

{{-- ── 12. GROCERY STORES ───────────────────────────────────────── --}}
@if($groceryStores->isNotEmpty())
<div class="sec-head"><h2>🛒 Grocery Stores</h2><a href="{{ route('directory.index') }}">View All</a></div>
<div class="sec-box">
  @foreach($groceryStores as $biz)
  <a href="{{ route('directory.show', $biz->slug) }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">{{ $biz->name }}</div>
      <div class="ri-sub">{{ $biz->category->icon ?? '' }} {{ $biz->category->name ?? '' }}@if($biz->city) · {{ $biz->city }}@endif</div>
    </div>
    @if($biz->is_featured)<span class="ri-badge badge-feat">Featured</span>@endif
    <span class="ri-arr">›</span>
  </a>
  @endforeach
</div>
@endif

{{-- ── 13. JEWELRY ──────────────────────────────────────────────── --}}
@if($jewelryBiz->isNotEmpty())
<div class="sec-head"><h2>💎 Jewelry</h2><a href="{{ route('directory.index') }}">View All</a></div>
<div class="sec-box">
  @foreach($jewelryBiz as $biz)
  <a href="{{ route('directory.show', $biz->slug) }}" class="row-item">
    <div class="ri-body">
      <div class="ri-title">{{ $biz->name }}</div>
      <div class="ri-sub">{{ $biz->category->icon ?? '' }} {{ $biz->category->name ?? '' }}@if($biz->city) · {{ $biz->city }}@endif</div>
    </div>
    @if($biz->is_featured)<span class="ri-badge badge-feat">Featured</span>@endif
    <span class="ri-arr">›</span>
  </a>
  @endforeach
</div>
@endif

{{-- ── 14. COMMUNITY EVENTS ─────────────────────────────────────── --}}
<div class="sec-head"><h2>🎊 Community Events</h2><a href="{{ route('events.index') }}">View All</a></div>
<div class="sec-box">
  @if($communityEvents->isEmpty())
    <div class="empty-note">No upcoming community events. <a href="{{ route('post.create') }}">Post one →</a></div>
  @else
    @foreach($communityEvents as $event)
    <a href="{{ route('events.show', $event->slug) }}" class="ev-item">
      <div class="ev-date-box">
        <div class="day">{{ $event->start_date->format('j') }}</div>
        <div class="mon">{{ $event->start_date->format('M') }}</div>
      </div>
      <div class="ev-body">
        <div class="ev-title">{{ $event->title }}</div>
        <div class="ev-meta">
          @if($event->city)📍 {{ $event->city }}@endif
          @if($event->organizer) · {{ $event->organizer }}@endif
        </div>
      </div>
      @if($event->is_featured)<span class="ev-badge">Featured</span>@endif
    </a>
    @endforeach
  @endif
</div>

{{-- ── 15. JOBS & IT TRAINING ───────────────────────────────────── --}}
<div class="sec-head"><h2>💻 Jobs & IT Training / Placement</h2><a href="{{ route('jobs.index') }}">View All</a></div>
<div class="sec-box">
  <div class="job-cats">
    <a href="{{ route('jobs.index', ['type'=>'full-time']) }}" class="job-cat">💻 IT Training & Placement</a>
    <a href="{{ route('jobs.index', ['type'=>'contract']) }}" class="job-cat">🖥 IT & Technology Jobs</a>
    <a href="{{ route('jobs.index', ['type'=>'part-time']) }}" class="job-cat">🏢 Sales, Service & Office</a>
  </div>
  @if($latestJobs->isEmpty())
    <div class="row-item" style="pointer-events:none"><div class="ri-body"><div class="ri-title">Global Information Technology — Get Trained. Get Placed. Free Training for qualified applicants.</div><div class="ri-sub">IT Training · Toronto, ON</div></div><span class="ri-arr">›</span></div>
    <div class="row-item" style="pointer-events:none"><div class="ri-body"><div class="ri-title">Tata Consultancy Services — Multiple IT openings for experienced professionals</div><div class="ri-sub">IT Jobs · Toronto · Mississauga · Vancouver</div></div><span class="ri-badge badge-new">Apply Now</span><span class="ri-arr">›</span></div>
  @else
    @foreach($latestJobs as $job)
    <a href="{{ route('jobs.show', $job->slug) }}" class="row-item">
      <div class="ri-body">
        <div class="ri-title">{{ $job->title }}@if($job->company) — {{ $job->company }}@endif</div>
        <div class="ri-sub">{{ $job->job_type_label }}@if($job->city) · {{ $job->city }}@elseif($job->location) · {{ $job->location }}@endif</div>
      </div>
      @if($job->salary)<span style="font-size:11px;color:var(--green);font-weight:600;flex-shrink:0;white-space:nowrap">{{ $job->salary }}</span>@endif
      @if($job->is_featured)<span class="ri-badge badge-feat">Featured</span>@endif
      <span class="ri-arr">›</span>
    </a>
    @endforeach
  @endif
</div>

{{-- ── 16. BUSINESS PROMOTIONS ──────────────────────────────────── --}}
<div class="sec-head"><h2>📣 Business Promotions</h2><a href="{{ route('directory.index') }}">Advertise With Us</a></div>
<div class="sec-box">
  @php
    $promoItems = $featuredBusinesses->isNotEmpty() ? $featuredBusinesses : collect([]);
  @endphp
  @if($promoItems->isEmpty())
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;padding:12px">
      @foreach([['icon'=>'🛡️','title'=>'Anne Insurance Group','sub'=>'Auto · Home · Life · Business Insurance'],['icon'=>'🏠','title'=>'Koshy George — Realtor','sub'=>'Residential & Commercial · GTA'],['icon'=>'✈️','title'=>'Visitors Insurance','sub'=>'For parents & students visiting Canada'],['icon'=>'💎','title'=>'Manjil Designs Jewelry','sub'=>'Best Gold, Diamond & Silver · Brampton']] as $p)
      <div style="border:1.5px solid var(--border);border-radius:var(--rl);padding:14px;text-align:center">
        <div style="font-size:28px;margin-bottom:6px">{{ $p['icon'] }}</div>
        <div style="font-size:12px;font-weight:600">{{ $p['title'] }}</div>
        <div style="font-size:10.5px;color:var(--muted)">{{ $p['sub'] }}</div>
      </div>
      @endforeach
    </div>
  @else
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;padding:12px">
      @foreach($promoItems as $biz)
      <a href="{{ route('directory.show', $biz->slug) }}" style="border:1.5px solid var(--border);border-radius:var(--rl);padding:14px;text-align:center;display:block;text-decoration:none;color:var(--text);transition:border-color .15s" onmouseover="this.style.borderColor='var(--red)'" onmouseout="this.style.borderColor='var(--border)'">
        <div style="font-size:28px;margin-bottom:6px">{{ $biz->category->icon ?? '🏢' }}</div>
        <div style="font-size:12px;font-weight:600">{{ Str::limit($biz->name, 30) }}</div>
        <div style="font-size:10.5px;color:var(--muted)">{{ $biz->city }}</div>
        @if($biz->phone)<div style="margin-top:6px;display:inline-block;background:var(--red);color:#fff;font-size:10px;padding:3px 10px;border-radius:20px">Call Now</div>@endif
      </a>
      @endforeach
    </div>
  @endif
</div>

</div>{{-- /content --}}

{{-- ═══════════ RIGHT SIDEBAR ═══════════ --}}
<div class="sidebar-right">

  {{-- SUBSCRIBE --}}
  <div class="widget">
    <div class="widget-head">📧 Subscribe Newsletter <a href="#">Free</a></div>
    <div class="sub-form">
      <input type="email" placeholder="Your email address">
      <button type="button" id="sub-btn">Subscribe for Free Updates</button>
    </div>
  </div>

  {{-- QUICK LINKS --}}
  <div class="widget">
    <div class="widget-head">⚡ Quick Links</div>
    <a href="{{ route('matrimonial.index') }}" class="qlink"><span class="ql-icon">💒</span> Wedding Connections <span class="ql-arr">›</span></a>
    <a href="{{ route('classifieds.index', ['category' => $classifiedCategories->where('name','Roommates')->first()?->id]) }}" class="qlink"><span class="ql-icon">🏠</span> Find Roommate <span class="ql-arr">›</span></a>
    <a href="{{ route('classifieds.index', ['category' => $classifiedCategories->where('name','Autos')->first()?->id]) }}" class="qlink"><span class="ql-icon">🚗</span> Buy / Sell Cars <span class="ql-arr">›</span></a>
    <a href="{{ route('directory.index') }}" class="qlink"><span class="ql-icon">✈️</span> Travel Agents <span class="ql-arr">›</span></a>
    <a href="{{ route('blog.index') }}" class="qlink"><span class="ql-icon">📰</span> Community Blog <span class="ql-arr">›</span></a>
    <a href="{{ route('feed') }}" class="qlink"><span class="ql-icon">📲</span> Community Feed <span class="ql-arr">›</span></a>
    <a href="{{ route('jobs.index') }}" class="qlink"><span class="ql-icon">💼</span> Find a Job <span class="ql-arr">›</span></a>
  </div>

  {{-- MI POLL --}}
  <div class="widget">
    <div class="widget-head">📊 Mi Poll</div>
    <div class="widget-body">
      <p style="font-size:12px;font-weight:500;margin-bottom:12px">Do you expect the Canadian job market to improve for software programmers in 2026?</p>
      <div class="poll-opt">
        <span class="poll-label">Yes</span>
        <div class="poll-bar-wrap"><div class="poll-bar" style="width:62%"><span class="poll-val">62%</span></div></div>
      </div>
      <div class="poll-opt">
        <span class="poll-label">No</span>
        <div class="poll-bar-wrap"><div class="poll-bar" style="width:38%"><span class="poll-val">38%</span></div></div>
      </div>
      <button class="poll-btn" id="poll-btn">Vote</button>
    </div>
  </div>

  {{-- FEATURED ADS --}}
  <div class="widget">
    <div class="widget-head">⭐ Featured Ads <a href="{{ route('post.create') }}">Advertise</a></div>
    <div class="widget-body">
      @if($sidebarFeatured->isEmpty())
        <div class="w-ad"><div class="w-ad-inner"><div class="icon">🛡️</div><div class="title">Anne Insurance Group</div><div class="sub">Auto · Home · Life · Business Insurance</div><span class="cta">Get Quote</span></div></div>
        <div class="w-ad"><div class="w-ad-inner"><div class="icon">🏠</div><div class="title">Koshy George — Realtor</div><div class="sub">Residential & Commercial · GTA</div><span class="cta">Contact Now</span></div></div>
        <div class="w-ad"><div class="w-ad-inner" style="background:var(--amber-bg);border-color:#FDE68A"><div class="icon">💎</div><div class="title">Manjil Designs Jewelry</div><div class="sub">Best Gold, Diamond & Silver · Brampton</div><span class="cta">Shop Now</span></div></div>
      @else
        @foreach($sidebarFeatured as $listing)
        <a href="{{ route('classifieds.show', $listing->slug) }}" class="w-ad">
          <div class="w-ad-inner">
            <div class="icon">{{ $listing->category->icon ?? '📦' }}</div>
            <div class="title">{{ Str::limit($listing->title, 28) }}</div>
            <div class="sub">📍 {{ $listing->location }}</div>
            @if($listing->price)<span class="cta">{{ $listing->price }}</span>@endif
          </div>
        </a>
        @endforeach
      @endif
    </div>
  </div>

  {{-- TRENDING TODAY --}}
  <div class="widget">
    <div class="widget-head">🔥 Trending Today</div>
    @if($trendingListings->isEmpty())
      <div class="trend-item"><span class="trend-num">1</span><div><div class="trend-title">Rent Vintage Cars for Wedding & Special Events</div><div class="trend-cat">Directory · Toronto</div></div></div>
      <div class="trend-item"><span class="trend-num">2</span><div><div class="trend-title">New Job Listings — Apply Now</div><div class="trend-cat">Jobs · Multiple Cities</div></div></div>
      <div class="trend-item"><span class="trend-num">3</span><div><div class="trend-title">Key Revisions in Canada-India Trade Deal</div><div class="trend-cat">News · Community</div></div></div>
      <div class="trend-item"><span class="trend-num">4</span><div><div class="trend-title">Diwali Mela 2026 — Toronto Expo Centre</div><div class="trend-cat">Events · Oct 18</div></div></div>
    @else
      @foreach($trendingListings as $i => $listing)
      <a href="{{ route('classifieds.show', $listing->slug) }}" class="trend-item">
        <span class="trend-num">{{ $i + 1 }}</span>
        <div>
          <div class="trend-title">{{ Str::limit($listing->title, 48) }}</div>
          <div class="trend-cat">{{ $listing->category->name ?? 'Classifieds' }} · {{ $listing->location }}</div>
        </div>
      </a>
      @endforeach
    @endif
  </div>

  {{-- LATEST BUSINESSES --}}
  <div class="widget">
    <div class="widget-head">🆕 Latest in Canada <a href="{{ route('directory.index') }}">View All</a></div>
    @if($latestSidebarBiz->isEmpty())
      <div class="row-item" style="pointer-events:none"><div class="ri-body"><div class="ri-title">Motor City Vintage Rentals</div><div class="ri-sub">Toronto, ON</div></div><span class="ri-badge badge-new">New</span></div>
      <div class="row-item" style="pointer-events:none"><div class="ri-body"><div class="ri-title">AM Rental Property Management</div><div class="ri-sub">GTA, ON</div></div></div>
      <div class="row-item" style="pointer-events:none"><div class="ri-body"><div class="ri-title">GoBazzar Digital Display Network</div><div class="ri-sub">Canada-wide</div></div><span class="ri-badge badge-feat">Featured</span></div>
      <div class="row-item" style="pointer-events:none"><div class="ri-body"><div class="ri-title">Nazaare Photography — Beyond Lenses</div><div class="ri-sub">Brampton, ON</div></div></div>
      <div class="row-item" style="pointer-events:none"><div class="ri-body"><div class="ri-title">LifeLab Kids — Developmental Therapy</div><div class="ri-sub">Mississauga, ON</div></div></div>
    @else
      @foreach($latestSidebarBiz as $biz)
      <a href="{{ route('directory.show', $biz->slug) }}" class="row-item">
        <div class="ri-body">
          <div class="ri-title">{{ $biz->name }}</div>
          <div class="ri-sub">{{ $biz->city ?? ($biz->category->name ?? '') }}</div>
        </div>
        @if($biz->is_featured)<span class="ri-badge badge-feat">Featured</span>@elseif($biz->created_at->gte(now()->subDays(7)))<span class="ri-badge badge-new">New</span>@endif
      </a>
      @endforeach
    @endif
  </div>

  {{-- ADVERTISE WITH US --}}
  <div class="widget" style="background:var(--dark)">
    <div style="padding:16px;text-align:center">
      <div style="font-family:var(--fh);font-size:14px;font-weight:800;color:#fff;margin-bottom:6px">Advertise With Us</div>
      <p style="font-size:11.5px;color:rgba(255,255,255,.55);line-height:1.6;margin-bottom:14px">Reach thousands of Indian-Canadians. Customize your ad by need & budget.</p>
      <a href="mailto:info@gobazzar.ca" class="btn btn-red" style="width:100%;justify-content:center;display:flex">Click Here for Info</a>
    </div>
  </div>

</div>{{-- /sidebar-right --}}
</div>{{-- /main-wrap --}}

@endsection

@push('scripts')
<script>
function heroLoadCities(province) {
  var citySel = document.getElementById('hero-city');
  if (!citySel) return;
  citySel.innerHTML = '<option value="">Loading…</option>';
  fetch('{{ route('locations.cities') }}?province=' + encodeURIComponent(province))
    .then(function(r){ return r.json(); })
    .then(function(cities) {
      citySel.innerHTML = '<option value="">All Cities</option>';
      cities.forEach(function(c) {
        var o = document.createElement('option');
        o.value = c; o.textContent = c;
        citySel.appendChild(o);
      });
    });
}
function heroLoadCitiesM(province) {
  var citySel = document.getElementById('hero-city-m');
  if (!citySel) return;
  citySel.innerHTML = '<option value="">Loading…</option>';
  fetch('{{ route('locations.cities') }}?province=' + encodeURIComponent(province))
    .then(function(r){ return r.json(); })
    .then(function(cities) {
      citySel.innerHTML = '<option value="">All Cities</option>';
      cities.forEach(function(c) {
        var o = document.createElement('option');
        o.value = c; o.textContent = c;
        citySel.appendChild(o);
      });
    });
}
function heroSubmit(e) {
  e.preventDefault();
  var isMobile = window.innerWidth <= 900;
  var search   = document.getElementById(isMobile ? 'hero-search-m' : 'hero-search')?.value.trim() || '';
  var province = document.getElementById(isMobile ? 'hero-province-m' : 'hero-province')?.value || '';
  var city     = document.getElementById(isMobile ? 'hero-city-m' : 'hero-city')?.value || '';

  var params = new URLSearchParams();
  if (province) params.set('province', province);
  if (city)     params.set('city', city);

  if (search) {
    params.set('search', search);
    window.location.href = '{{ route('classifieds.index') }}?' + params.toString();
  } else {
    window.location.href = '{{ route('home') }}?' + params.toString();
  }
}

document.getElementById('poll-btn')?.addEventListener('click', function(){
  this.textContent = '✅ Vote Recorded!';
  this.disabled = true;
  this.style.background = 'var(--green)';
});
document.getElementById('sub-btn')?.addEventListener('click', function(){
  const input = this.previousElementSibling;
  if(input.value.includes('@')){
    this.textContent = '✅ Subscribed!';
    this.disabled = true;
    this.style.background = 'var(--green)';
    input.disabled = true;
  } else {
    input.style.borderColor = 'var(--red)';
    input.placeholder = 'Enter a valid email…';
  }
});
</script>
@endpush
