@extends('layouts.app')
@section('title', $user->name . ' — Seller Profile')

@push('styles')
<style>
/* ── Hero Banner ── */
.sp-hero{position:relative;background:linear-gradient(135deg,#0f2d6b 0%,#1a4db8 55%,#1565c0 100%);padding:0;overflow:visible}
.sp-hero-bg{position:absolute;inset:0;opacity:.08;overflow:hidden;pointer-events:none}
.sp-hero-bg svg{width:100%;height:100%}
.sp-hero-inner{position:relative;z-index:2;max-width:1100px;margin:0 auto;padding:28px 16px 24px}

/* Avatar */
.sp-av-wrap{position:relative;display:inline-block;flex-shrink:0}
.sp-av{width:88px;height:88px;border-radius:50%;object-fit:cover;border:4px solid rgba(255,255,255,.9);box-shadow:0 4px 20px rgba(0,0,0,.25);display:block}
.sp-av-init{width:88px;height:88px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);border:4px solid rgba(255,255,255,.9);box-shadow:0 4px 20px rgba(0,0,0,.25);display:flex;align-items:center;justify-content:center;font-size:34px;font-weight:800;color:#fff}
.sp-ver-dot{position:absolute;bottom:4px;right:4px;width:22px;height:22px;border-radius:50%;background:#22c55e;border:3px solid #fff;display:flex;align-items:center;justify-content:center}
.sp-ver-dot i{font-size:9px;color:#fff}

/* Info row */
.sp-info{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.sp-info-text{flex:1;min-width:0}
.sp-name{font-size:24px;font-weight:800;color:#fff;margin-bottom:5px;line-height:1.2}
.sp-meta{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.sp-meta-item{display:flex;align-items:center;gap:4px;font-size:12px;color:rgba(255,255,255,.78);font-weight:500}
.sp-meta-item i{font-size:11px;opacity:.8}
.sp-stat{background:rgba(255,255,255,.12);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.15);border-radius:12px;padding:10px 18px;text-align:center;min-width:80px;flex-shrink:0}
.sp-stat-num{font-size:22px;font-weight:800;color:#fff;line-height:1}
.sp-stat-lbl{font-size:10px;color:rgba(255,255,255,.65);margin-top:3px;text-transform:uppercase;letter-spacing:.05em}
/* Mobile stat inline badge */
.sp-stat-badge{display:none;align-items:center;gap:5px;font-size:11.5px;font-weight:700;background:rgba(255,255,255,.15);color:#fff;border-radius:20px;padding:3px 10px;margin-top:6px}

/* Tab bar */
.sp-tabs{background:#fff;border-bottom:1.5px solid #e8edf4;box-shadow:0 2px 8px rgba(0,0,0,.06);position:sticky;top:0;z-index:50}
.sp-tabs-inner{max-width:1100px;margin:0 auto;padding:0 16px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.sp-tab{font-size:13px;font-weight:600;color:var(--muted);padding:13px 4px;border-bottom:2.5px solid transparent;text-decoration:none;white-space:nowrap}
.sp-tab.active{color:var(--primary);border-color:var(--primary)}
.sp-share{display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:var(--muted);background:#f4f6fa;border:none;border-radius:8px;padding:6px 12px;cursor:pointer;white-space:nowrap}

/* Content */
.sp-wrap{max-width:1100px;margin:0 auto;padding:20px 16px 56px;display:grid;grid-template-columns:1fr 260px;gap:20px;align-items:start}

/* Sidebar card */
.sp-side-card{background:#fff;border-radius:14px;border:1.5px solid #e8edf4;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05)}
.sp-side-head{padding:14px 16px;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;border-bottom:1px solid #f0f2f6}
.sp-side-body{padding:16px}
.sp-side-row{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f8f9fb;font-size:13px;color:var(--text)}
.sp-side-row:last-child{border-bottom:none}
.sp-side-row i{width:18px;color:var(--primary);font-size:13px;flex-shrink:0}
.sp-contact-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:11px;border-radius:10px;font-size:13.5px;font-weight:700;text-decoration:none;transition:.18s;margin-top:10px;border:none;cursor:pointer}
.sp-btn-primary{background:var(--primary);color:#fff}
.sp-btn-primary:hover{opacity:.9}

/* Listing grid */
.sp-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:14px}
.sp-card{background:#fff;border-radius:13px;overflow:hidden;border:1.5px solid #e8edf4;box-shadow:0 1px 5px rgba(0,0,0,.05);text-decoration:none;color:inherit;display:block;transition:transform .16s,box-shadow .16s,border-color .16s}
.sp-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.1);border-color:#c8d6f0}
.sp-card-img{position:relative;height:150px;background:#f3f6fb;display:flex;align-items:center;justify-content:center;overflow:hidden}
.sp-card-img img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.sp-card:hover .sp-card-img img{transform:scale(1.04)}
.sp-card-no-img{font-size:40px;opacity:.5}
.sp-card-body{padding:11px 12px 12px}
.sp-card-price{font-size:16px;font-weight:800;color:#d92d20;margin-bottom:2px;line-height:1.1}
.sp-card-price small{font-size:11px;font-weight:500;color:#94a3b8}
.sp-card-title{font-size:12.5px;font-weight:600;color:#1e293b;margin-bottom:6px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.sp-card-tags{display:flex;align-items:center;gap:5px;flex-wrap:wrap;margin-bottom:6px}
.sp-card-cat{font-size:10px;font-weight:600;color:#64748b;background:#f1f5f9;border-radius:6px;padding:2px 7px}
.sp-card-ver{display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;background:#dcfce7;color:#15803d;padding:2px 7px;border-radius:20px}
.sp-card-feat{position:absolute;top:8px;left:8px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-size:9px;font-weight:800;padding:3px 9px;border-radius:5px;text-transform:uppercase;letter-spacing:.5px;box-shadow:0 2px 6px rgba(0,0,0,.2)}
.sp-card-foot{display:flex;justify-content:space-between;font-size:10px;color:#94a3b8}

/* Section header */
.sp-sec-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.sp-sec-title{font-size:15px;font-weight:800;color:#1e293b}
.sp-sec-count{font-size:12px;color:#94a3b8;background:#f1f5f9;padding:3px 10px;border-radius:20px}

/* Empty */
.sp-empty{text-align:center;padding:48px 20px;color:#94a3b8}
.sp-empty-icon{font-size:48px;margin-bottom:12px;opacity:.5}

/* ── MOBILE ── */
@media(max-width:860px){
  .sp-wrap{grid-template-columns:1fr}
  /* sidebar goes below listings on mobile */
}
@media(max-width:600px){
  .sp-hero-inner{padding:20px 14px 20px}
  .sp-info{gap:12px;flex-wrap:nowrap;align-items:flex-start}
  .sp-av,.sp-av-init{width:68px;height:68px;font-size:26px}
  .sp-name{font-size:18px;margin-bottom:4px}
  .sp-meta{gap:8px}
  .sp-meta-item{font-size:11px}
  .sp-stat{display:none}
  .sp-stat-badge{display:inline-flex}
  .sp-grid{grid-template-columns:repeat(2,1fr);gap:10px}
  .sp-card-img{height:120px}
  .sp-card-body{padding:9px 10px 10px}
  .sp-card-price{font-size:14px}
  .sp-card-title{font-size:12px}
  .sp-wrap{padding:14px 12px 64px}
  .sp-sec-head{margin-bottom:10px}
  .sp-tabs-inner{padding:0 12px}
  /* Sidebar compact on mobile */
  .sp-side-card + .sp-side-card{margin-top:12px}
}
</style>
@endpush

@section('content')
@php $totalCount = \App\Models\Listing::where('user_id',$user->id)->where('status','active')->count(); @endphp

{{-- ── HERO ── --}}
<div class="sp-hero">
  {{-- Background pattern --}}
  <div class="sp-hero-bg">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 200" preserveAspectRatio="xMidYMid slice">
      <circle cx="700" cy="30" r="120" fill="white"/><circle cx="100" cy="180" r="80" fill="white"/>
      <circle cx="400" cy="-20" r="100" fill="white"/><circle cx="550" cy="160" r="60" fill="white"/>
    </svg>
  </div>
  <div class="sp-hero-inner">
    <div class="sp-info">
      {{-- Avatar --}}
      <div class="sp-av-wrap">
        @if($user->avatar_url)
          <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="sp-av">
        @else
          <div class="sp-av-init">{{ strtoupper(substr($user->name,0,1)) }}</div>
        @endif
        @if($user->is_verified ?? false)
          <div class="sp-ver-dot"><i class="fa-solid fa-check"></i></div>
        @endif
      </div>

      {{-- Name + meta --}}
      <div class="sp-info-text">
        <div class="sp-name">{{ $user->name }}</div>
        <div class="sp-meta">
          <span class="sp-meta-item"><i class="fa-regular fa-calendar"></i> Member since {{ $user->created_at->format('M Y') }}</span>
          @if($user->city)<span class="sp-meta-item"><i class="fa-solid fa-location-dot"></i> {{ $user->city }}</span>@endif
          @if($user->is_verified ?? false)
            <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;background:rgba(34,197,94,.2);color:#86efac;padding:3px 10px;border-radius:20px;border:1px solid rgba(134,239,172,.3)">
              <i class="fa-solid fa-circle-check" style="font-size:10px"></i> Verified
            </span>
          @endif
        </div>
        {{-- Mobile-only stat badge --}}
        <div class="sp-stat-badge">
          <i class="fa-solid fa-tag" style="font-size:10px;opacity:.8"></i>
          {{ $totalCount }} {{ Str::plural('Listing',$totalCount) }}
        </div>
      </div>

      {{-- Desktop stat pill --}}
      <div class="sp-stat">
        <div class="sp-stat-num">{{ $totalCount }}</div>
        <div class="sp-stat-lbl">{{ Str::plural('Listing',$totalCount) }}</div>
      </div>
    </div>
  </div>
</div>

{{-- ── TAB BAR ── --}}
<div class="sp-tabs">
  <div class="sp-tabs-inner">
    <div style="display:flex;gap:20px">
      <a href="#" class="sp-tab active">All Listings <span style="font-size:11px;background:#e8f0fe;color:var(--primary);border-radius:20px;padding:1px 7px;margin-left:4px">{{ $totalCount }}</span></a>
    </div>
    <button class="sp-share" onclick="navigator.share ? navigator.share({title:'{{ $user->name }}',url:window.location.href}) : navigator.clipboard.writeText(window.location.href)">
      <i class="fa-solid fa-share-nodes"></i> Share
    </button>
  </div>
</div>

{{-- ── MAIN CONTENT ── --}}
<div class="sp-wrap">

  {{-- Listings grid --}}
  <div>
    <div class="sp-sec-head">
      <div class="sp-sec-title">Active Listings</div>
      <div class="sp-sec-count">{{ $listings->total() }} total</div>
    </div>

    @if($listings->count())
      <div class="sp-grid">
        @foreach($listings as $listing)
        <a href="{{ route('classifieds.show', $listing->slug) }}" class="sp-card">
          <div class="sp-card-img">
            @if($listing->image_url)
              <img src="{{ $listing->image_url }}" alt="{{ $listing->title }}" loading="lazy">
            @else
              <div class="sp-card-no-img">
                @php $icons = ['🚗'=>'Autos','🏠'=>'Real Estate','📱'=>'Buy & Sell','🛠'=>'Services','🛋'=>'General','✈️'=>'Travel','🤝'=>'Roommates']; $icon = array_search($listing->category->name ?? '', $icons) ?: ($listing->category->icon ?? '📦'); @endphp
                {{ $icon }}
              </div>
            @endif
            @if($listing->is_featured)<div class="sp-card-feat"><i class="fa-solid fa-star" style="font-size:8px"></i> Featured</div>@endif
          </div>
          <div class="sp-card-body">
            @if($listing->price && (float)$listing->price > 0)
              <div class="sp-card-price">{{ $listing->formatted_price }}@if($listing->price_unit)<small> /{{ $listing->price_unit }}</small>@endif</div>
            @endif
            <div class="sp-card-title">{{ $listing->title }}</div>
            <div class="sp-card-tags">
              @if($listing->category)<span class="sp-card-cat">{{ $listing->category->name }}</span>@endif
              @if($listing->is_verified)<span class="sp-card-ver"><i class="fa-solid fa-circle-check" style="font-size:9px"></i> Verified</span>@endif
            </div>
            <div class="sp-card-foot">
              <span>@if($listing->location)<i class="fa-solid fa-location-dot" style="margin-right:2px"></i>{{ Str::limit($listing->location,20) }}@endif</span>
              <span>{{ $listing->created_at->diffForHumans() }}</span>
            </div>
          </div>
        </a>
        @endforeach
      </div>

      @if($listings->hasPages())
        <div style="margin-top:28px;display:flex;justify-content:center">{{ $listings->links() }}</div>
      @endif

    @else
      <div class="sp-empty">
        <div class="sp-empty-icon">📭</div>
        <div style="font-size:16px;font-weight:700;color:#475569;margin-bottom:6px">No active listings</div>
        <div style="font-size:13px">This seller has no active listings at the moment.</div>
      </div>
    @endif
  </div>

  {{-- Sidebar --}}
  <div>
    <div class="sp-side-card">
      <div class="sp-side-head">Seller Info</div>
      <div class="sp-side-body">
        {{-- Avatar + name --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #f0f2f6">
          @if($user->avatar_url)
            <img src="{{ $user->avatar_url }}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid #e8edf4">
          @else
            <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:#fff;flex-shrink:0">{{ strtoupper(substr($user->name,0,1)) }}</div>
          @endif
          <div>
            <div style="font-weight:700;font-size:14px;color:#1e293b">{{ $user->name }}</div>
            @if($user->is_verified ?? false)
              <div style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;color:#15803d;background:#dcfce7;padding:2px 7px;border-radius:20px;margin-top:3px"><i class="fa-solid fa-circle-check" style="font-size:9px"></i> Verified</div>
            @endif
          </div>
        </div>

        <div class="sp-side-row"><i class="fa-solid fa-list"></i> {{ $totalCount }} active {{ Str::plural('listing',$totalCount) }}</div>
        <div class="sp-side-row"><i class="fa-regular fa-calendar"></i> Since {{ $user->created_at->format('M Y') }}</div>
        @if($user->city)<div class="sp-side-row"><i class="fa-solid fa-location-dot"></i> {{ $user->city }}</div>@endif

        @auth
          @if(auth()->id() !== $user->id)
            @php $firstListing = $listings->first(); @endphp
            @if($firstListing)
              <a href="{{ route('chat.show', $firstListing) }}" class="sp-contact-btn sp-btn-primary" style="margin-top:16px">
                <i class="fa-solid fa-comments"></i> Send Message
              </a>
            @else
              <a href="{{ route('chat.inbox') }}" class="sp-contact-btn sp-btn-primary" style="margin-top:16px">
                <i class="fa-solid fa-comments"></i> Send Message
              </a>
            @endif
          @endif
        @else
          <a href="{{ route('login') }}" class="sp-contact-btn sp-btn-primary" style="margin-top:16px">
            <i class="fa-solid fa-comments"></i> Contact Seller
          </a>
        @endauth
      </div>
    </div>

    {{-- Safety tips --}}
    <div class="sp-side-card" style="margin-top:14px">
      <div class="sp-side-head">Safety Tips</div>
      <div class="sp-side-body" style="padding:14px 16px">
        @foreach(['Meet in a safe, public place','Check the item before paying','Don\'t pay in advance','Trust your instincts'] as $tip)
          <div style="display:flex;align-items:flex-start;gap:8px;font-size:12px;color:#475569;margin-bottom:9px;line-height:1.4">
            <i class="fa-solid fa-shield-halved" style="color:#22c55e;margin-top:1px;flex-shrink:0;font-size:11px"></i> {{ $tip }}
          </div>
        @endforeach
      </div>
    </div>
  </div>

</div>
@endsection
