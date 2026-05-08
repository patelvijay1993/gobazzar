@extends('layouts.app')
@section('title', $business->name)

@push('styles')
<style>
.show-wrap{max-width:1200px;margin:24px auto;padding:0 20px;display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start}
@media(max-width:768px){.show-wrap{grid-template-columns:1fr;padding:0 14px}.biz-title{font-size:20px}.biz-banner{height:160px}}
.biz-main{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden}
.biz-banner{height:220px;overflow:hidden;position:relative;background:#f3f3f3;display:flex;align-items:center;justify-content:center;font-size:60px}
.biz-logo-lg{width:72px;height:72px;border-radius:14px;background:var(--surface);border:3px solid var(--surface);box-shadow:var(--sh);display:flex;align-items:center;justify-content:center;font-size:32px;position:absolute;bottom:-24px;left:24px;overflow:hidden}
.biz-logo-lg img{width:100%;height:100%;object-fit:cover}
.biz-body{padding:36px 24px 24px}
.biz-title{font-family:var(--fh);font-size:24px;font-weight:800;margin-bottom:4px}
.biz-cat-label{font-size:12px;color:var(--red);font-weight:600;text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px}
.biz-desc{font-size:13.5px;line-height:1.7;color:var(--text);margin:14px 0}
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
    @if($business->category)<a href="{{ route('directory.index', ['category' => $business->category_id]) }}">{{ $business->category->name }}</a><span>›</span>@endif
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
        @if($business->logo)<img src="{{ asset('storage/'.$business->logo) }}" alt="">
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
  </div>

  <div>
    <div class="sidebar-card">
      <div class="sidebar-head">Contact</div>
      <div class="sidebar-body">
        @if($business->phone)
          <a href="tel:{{ $business->phone }}" class="btn btn-red btn-block">📞 {{ $business->phone }}</a>
        @endif
        @if($business->email)
          <a href="mailto:{{ $business->email }}" class="btn btn-ghost btn-block">✉ Send Email</a>
        @endif
        @if($business->website)
          <a href="{{ $business->website }}" target="_blank" class="btn btn-ghost btn-block">🌐 Visit Website</a>
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
