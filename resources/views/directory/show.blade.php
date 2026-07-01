@extends('layouts.app')
@section('title', $business->name)

@push('styles')
<style>
/* Legacy var bridge */
body{--red:#1a3a8f;--red2:#e74c3c;--red-dark:#122970;--red-pale:#e8edf7;--border2:#e2e0db;--surface:#fff;--bg:#f9fafb;--hint:#9ca3af;--rl:14px;--r:8px;--amber:#92400e;--amber-bg:#fef9c3;--amber-light:#fef9c3;--dark:#1a3a8f;--dark2:#122970;--gold:#e8a020;--blue:#1d4ed8;--blue-bg:#eff6ff;--green:#16a34a;--green-bg:#dcfce7;}
.show-wrap{max-width:1200px;margin:24px auto;padding:0 20px;display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start}
/* Business posts */
.biz-posts{border-top:1px solid var(--border);padding:20px 24px}
.biz-posts-head{font-family:var(--fh);font-size:15px;font-weight:800;color:var(--text);margin-bottom:14px;display:flex;align-items:center;gap:8px}
.biz-posts-head i{color:var(--primary)}
.biz-posts-count{background:var(--primary-light);color:var(--primary);font-size:11px;font-weight:700;padding:1px 9px;border-radius:20px;margin-left:2px}
.bp-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
@media(max-width:600px){.bp-grid{grid-template-columns:1fr 1fr}}
.bp-card{background:#fff;border:1px solid var(--border);border-radius:var(--r);overflow:hidden;text-decoration:none;color:var(--text);transition:all .15s;display:block}
.bp-card:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:0 4px 12px rgba(26,58,143,.1)}
.bp-img{height:110px;background:#f5f0ec;display:flex;align-items:center;justify-content:center;font-size:30px;overflow:hidden}
.bp-img img{width:100%;height:100%;object-fit:cover}
.bp-body{padding:9px 11px}
.bp-price{font-family:var(--fh);font-size:15px;font-weight:800;color:var(--primary);margin-bottom:2px}
.bp-price small{font-size:10px;font-weight:400;color:var(--muted)}
.bp-title{font-size:12.5px;font-weight:600;line-height:1.3;margin-bottom:3px}
.bp-cat{font-size:10.5px;color:var(--muted)}
@media(max-width:768px){.show-wrap{grid-template-columns:1fr;padding:0 14px}.biz-title{font-size:20px}.biz-banner{height:160px}}
.biz-main{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden}
.biz-banner{height:220px;overflow:hidden;position:relative;background:#f3f3f3;display:flex;align-items:center;justify-content:center;font-size:60px}
.biz-logo-lg{width:72px;height:72px;border-radius:14px;background:var(--surface);border:3px solid var(--surface);box-shadow:var(--sh);display:flex;align-items:center;justify-content:center;font-size:32px;position:absolute;bottom:-24px;left:24px;overflow:hidden}
.biz-logo-lg img{width:100%;height:100%;object-fit:cover}
.biz-body{padding:36px 24px 24px}
.biz-title{font-family:var(--fh);font-size:24px;font-weight:800;margin-bottom:4px}
.biz-cat-label{font-size:12px;color:var(--red);font-weight:600;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px}
.biz-desc{font-size:13.5px;line-height:1.7;color:var(--text);margin:14px 0}
.biz-desc img{max-width:100%;height:auto;border-radius:8px;margin:8px 0;display:block}
.info-row{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;font-size:13px}
.info-icon{font-size:16px;flex-shrink:0;margin-top:1px}
.stars{color:var(--gold)}
.badge-ver{background:var(--green);color:#fff;font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px}
.badge-feat{background:var(--gold);color:#fff;font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px}
.tag{background:var(--bg);border:1px solid var(--border2);color:var(--muted);font-size:10px;padding:3px 9px;border-radius:20px;margin:2px 3px 2px 0;display:inline-block}
.sidebar-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;margin-bottom:16px}
.sidebar-head{background:var(--dark);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.sidebar-body{padding:16px}
.btn-block{display:block;width:100%;text-align:center;padding:11px;border-radius:var(--r);font-size:13px;font-weight:600;margin-bottom:8px;transition:all .15s}
.biz-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:11px;border-radius:9px;font-size:13.5px;font-weight:700;margin-bottom:9px;text-decoration:none;transition:all .18s}
.biz-btn-primary{background:var(--primary);color:#fff}
.biz-btn-primary:hover{background:var(--primary-dark)}
.biz-btn-outline{background:#fff;border:1.5px solid var(--primary);color:var(--primary)}
.biz-btn-outline:hover{background:var(--primary-light)}
.rel-item{display:flex;gap:10px;padding:8px 0;border-bottom:1px solid var(--border);align-items:center}
.rel-item:last-child{border-bottom:none}
.rel-icon{width:38px;height:38px;border-radius:8px;background:var(--bg);border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;overflow:hidden}
.rel-icon img{width:100%;height:100%;object-fit:cover}
</style>
@endpush

@section('content')
<div class="container" style="padding-top:8px">
  <div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a><span>›</span>
    <a href="{{ route('directory.index') }}">Directory</a><span>›</span>
    @if($business->category)
      @php $parentCat = $business->category->parent; @endphp
      @if($parentCat)
        <a href="{{ route('directory.category', $parentCat->slug) }}">{{ $parentCat->name }}</a><span>›</span>
      @endif
      <a href="{{ route('directory.index', ['category' => $business->category_id]) }}">{{ $business->category->name }}</a><span>›</span>
    @endif
    {{ $business->name }}
  </div>
</div>

<div class="show-wrap">
  <div class="biz-main">
    @php $bizImages = $business->images ?? ($business->image ? [$business->image] : []); @endphp
    <div style="position:relative">
      @if(count($bizImages))
        <x-image-slider :images="$bizImages" :alt="$business->name" height="260px" />
      @else
        <div class="biz-banner">{{ $business->category->icon ?? '🏢' }}</div>
      @endif
      <div class="biz-logo-lg">
        @if($business->logo)<img src="{{ $business->logo_url }}" alt="">
        @else {{ $business->category->icon ?? '🏢' }} @endif
      </div>
    </div>
    <div class="biz-body">
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <h1 class="biz-title">{{ $business->name }}</h1>
        @if($business->is_verified)<span class="badge-ver">✓ Verified</span>@endif
        @if($business->is_featured)<span class="badge-feat">★ Featured</span>@endif
      </div>
      <div class="biz-cat-label">{{ $business->category->name ?? '' }}</div>

      @if($business->rating > 0)
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
          <span class="stars" style="font-size:16px">★★★★★</span>
          <span style="font-family:var(--fh);font-size:16px;font-weight:700">{{ number_format($business->rating,1) }}</span>
          <span style="color:var(--muted);font-size:13px">({{ $business->review_count }} reviews)</span>
        </div>
      @endif

      @if($business->description)
        <div class="biz-desc">{!! $business->description !!}</div>
      @endif

      @if($business->address || $business->city)
        <div class="info-row"><span class="info-icon">📍</span><span>{{ $business->address }}{{ $business->city ? ', '.$business->city : '' }}{{ $business->province ? ', '.$business->province : '' }}</span></div>
      @endif
      @if($business->hours)
        <div class="info-row"><span class="info-icon">🕐</span><span>{{ $business->hours }}</span></div>
      @endif
      @if($business->website)
        <div class="info-row"><span class="info-icon">🌐</span><a href="{{ $business->website }}" target="_blank" style="color:var(--blue)">{{ $business->website }}</a></div>
      @endif

      @if($business->tags)
        <div style="margin-top:14px">@foreach($business->tags as $tag)<span class="tag">{{ $tag }}</span>@endforeach</div>
      @endif
    </div>

    {{-- Business Posts (products / services) --}}
    @if($posts->isNotEmpty())
    <div class="biz-posts">
      <div class="biz-posts-head">
        <i class="fa-solid fa-box-open"></i> Posts from {{ $business->name }}
        <span class="biz-posts-count">{{ $posts->count() }}</span>
      </div>
      <div class="bp-grid">
        @foreach($posts as $post)
          <a href="{{ route('directory.post', [$business->slug, $post->slug]) }}" class="bp-card">
            <div class="bp-img">
              @if($post->image_url)<img src="{{ $post->image_url }}" alt="{{ $post->title }}">@else 📦 @endif
            </div>
            <div class="bp-body">
              @if($post->price)<div class="bp-price">{{ $post->price }}<small>{{ $post->price_unit }}</small></div>@endif
              <div class="bp-title">{{ $post->title }}</div>
              @if($post->category)<div class="bp-cat">{{ $post->category->name }}</div>@endif
            </div>
          </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  <div>
    <div class="sidebar-card">
      <div class="sidebar-head">Contact</div>
      <div class="sidebar-body">
        @auth
          @if(Auth::id() !== $business->user_id)
            <a href="{{ route('chat.business', $business) }}" class="biz-btn biz-btn-primary" style="background:var(--green);margin-bottom:8px;display:flex;align-items:center;justify-content:center;gap:8px">
              <i class="fa-solid fa-comments"></i> Chat with Business
            </a>
          @endif
        @else
          <a href="{{ route('login') }}" class="biz-btn biz-btn-primary" style="background:var(--green);margin-bottom:8px;display:flex;align-items:center;justify-content:center;gap:8px">
            <i class="fa-solid fa-comments"></i> Chat with Business
          </a>
        @endauth
        @if($business->phone)
          <a href="tel:{{ $business->phone }}" class="biz-btn biz-btn-primary"><i class="fa-solid fa-phone"></i> {{ $business->phone }}</a>
        @endif
        @if($business->email)
          <a href="mailto:{{ $business->email }}" class="biz-btn biz-btn-outline"><i class="fa-solid fa-envelope"></i> Send Email</a>
        @endif
        @if($business->website)
          <a href="{{ $business->website }}" target="_blank" class="biz-btn biz-btn-outline"><i class="fa-solid fa-globe"></i> Visit Website</a>
        @endif
        @if(!$business->phone && !$business->email && !$business->website)
          <p style="color:var(--muted);font-size:12px;text-align:center">No contact info available</p>
        @endif
      </div>
    </div>

    @if($related->count())
    <div class="sidebar-card">
      <div class="sidebar-head">Similar Businesses</div>
      <div class="sidebar-body" style="padding:8px 14px">
        @foreach($related as $rel)
          <a href="{{ route('directory.show', $rel) }}" class="rel-item" style="display:flex;text-decoration:none;color:var(--text)">
            <div class="rel-icon">{{ $rel->category->icon ?? '🏢' }}</div>
            <div>
              <div style="font-size:12.5px;font-weight:500">{{ $rel->name }}</div>
              @if($rel->city)<div style="font-size:11px;color:var(--muted)">{{ $rel->city }}</div>@endif
            </div>
          </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>

@endsection
