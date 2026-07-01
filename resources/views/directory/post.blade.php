@extends('layouts.app')
@section('title', $post->title . ' — ' . $business->name)

@push('styles')
<style>
.pp-wrap{max-width:1100px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start}
@media(max-width:768px){.pp-wrap{grid-template-columns:1fr;padding:0 14px}}
.pp-breadcrumb{max-width:1100px;margin:8px auto 0;padding:0 20px;font-size:12.5px;color:var(--muted);display:flex;align-items:center;gap:7px;flex-wrap:wrap}
.pp-breadcrumb a{color:var(--primary);text-decoration:none}
.pp-breadcrumb a:hover{text-decoration:underline}
.pp-breadcrumb i{font-size:9px;color:#bbb}
.pp-main{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-lg);overflow:hidden}
.pp-body{padding:22px 24px}
.pp-title{font-family:var(--fh);font-size:24px;font-weight:800;color:var(--text);margin-bottom:8px}
.pp-price{font-family:var(--fh);font-size:26px;font-weight:800;color:var(--primary);margin-bottom:14px}
.pp-price small{font-size:14px;font-weight:400;color:var(--muted)}
.pp-desc{font-size:14px;color:var(--text);line-height:1.7;margin-bottom:16px}
.pp-desc img{max-width:100%;height:auto;border-radius:8px;margin:8px 0;display:block}
.pp-meta{font-size:12px;color:var(--muted);display:flex;gap:16px;flex-wrap:wrap;border-top:1px solid var(--border);padding-top:14px}
.pp-specs{border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:16px}
.pp-spec-row{display:flex;padding:10px 14px;font-size:13px;border-bottom:1px solid var(--border)}
.pp-spec-row:last-child{border-bottom:none}
.pp-spec-row:nth-child(odd){background:#fafbfd}
.pp-spec-label{flex:0 0 42%;color:var(--muted);font-weight:600}
.pp-spec-val{flex:1;color:var(--text);font-weight:500}

.pp-side-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;margin-bottom:16px}
.pp-side-head{background:var(--primary);color:#fff;padding:12px 16px;font-family:var(--fh);font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.6px}
.pp-side-body{padding:16px}
.pp-biz-row{display:flex;align-items:center;gap:12px;margin-bottom:14px}
.pp-biz-logo{width:48px;height:48px;border-radius:12px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;overflow:hidden}
.pp-biz-logo img{width:100%;height:100%;object-fit:cover}
.pp-biz-name{font-family:var(--fh);font-size:15px;font-weight:800;color:var(--text)}
.pp-biz-loc{font-size:12px;color:var(--muted)}
.pp-btn{display:flex;align-items:center;justify-content:center;gap:7px;width:100%;padding:10px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;margin-bottom:8px;transition:all .15s}
.pp-btn-primary{background:var(--primary);color:#fff}
.pp-btn-primary:hover{background:var(--primary-dark)}
.pp-btn-outline{border:1.5px solid var(--border);color:var(--text)}
.pp-btn-outline:hover{border-color:var(--primary);color:var(--primary)}

.more-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.more-card{background:#fff;border:1px solid var(--border);border-radius:8px;overflow:hidden;text-decoration:none;color:var(--text);transition:all .15s}
.more-card:hover{border-color:var(--primary)}
.more-img{height:80px;background:#f5f0ec;display:flex;align-items:center;justify-content:center;font-size:24px;overflow:hidden}
.more-img img{width:100%;height:100%;object-fit:cover}
.more-body{padding:7px 9px}
.more-title{font-size:11.5px;font-weight:600;line-height:1.3}
.more-price{font-size:12px;font-weight:800;color:var(--primary);margin-top:2px}
</style>
@endpush

@section('content')

{{-- Breadcrumb: Home › Category › Sub › Business › Post --}}
<div class="pp-breadcrumb">
  <a href="{{ route('home') }}">Home</a>
  <i class="fa-solid fa-chevron-right"></i>
  <a href="{{ route('directory.index') }}">Directory</a>
  <i class="fa-solid fa-chevron-right"></i>
  @if($business->category)
    @php $parentCat = $business->category->parent; @endphp
    @if($parentCat)
      <a href="{{ route('directory.category', $parentCat->slug) }}">{{ $parentCat->name }}</a>
      <i class="fa-solid fa-chevron-right"></i>
    @endif
    <a href="{{ route('directory.index', ['category' => $business->category_id]) }}">{{ $business->category->name }}</a>
    <i class="fa-solid fa-chevron-right"></i>
  @endif
  <a href="{{ route('directory.show', $business->slug) }}">{{ $business->name }}</a>
  <i class="fa-solid fa-chevron-right"></i>
  <span>{{ Str::limit($post->title, 30) }}</span>
</div>

<div class="pp-wrap">

  {{-- MAIN --}}
  <div class="pp-main">
    @php $ppImages = $post->images ?? ($post->image ? [$post->image] : []); @endphp
    @if(count($ppImages))
      <x-image-slider :images="$ppImages" :alt="$post->title" height="320px" />
    @else
      <div style="height:240px;background:#f5f0ec;display:flex;align-items:center;justify-content:center;font-size:56px">📦</div>
    @endif
    <div class="pp-body">
      <h1 class="pp-title">{{ $post->title }}</h1>
      @if($post->price)
        <div class="pp-price">{{ $post->price }}<small>{{ $post->price_unit }}</small></div>
      @endif
      @if($post->description)
        <div class="pp-desc">{!! $post->description !!}</div>
      @endif

      @if(!empty($post->custom_fields))
        <div class="pp-specs">
          @foreach($post->custom_fields as $key => $val)
            @if($val !== null && $val !== '')
            <div class="pp-spec-row">
              <span class="pp-spec-label">{{ $fieldLabels[$key] ?? Str::headline($key) }}</span>
              <span class="pp-spec-val">{{ is_array($val) ? implode(', ', $val) : $val }}</span>
            </div>
            @endif
          @endforeach
        </div>
      @endif

      <div class="pp-meta">
        @if($post->category)<span><i class="fa-solid fa-tag"></i> {{ $post->category->name }}</span>@endif
        <span><i class="fa-regular fa-eye"></i> {{ $post->views }} views</span>
        <span><i class="fa-regular fa-clock"></i> {{ $post->created_at->diffForHumans() }}</span>
      </div>
    </div>
  </div>

  {{-- SIDEBAR --}}
  <div>
    <div class="pp-side-card">
      <div class="pp-side-head">Offered by</div>
      <div class="pp-side-body">
        <div class="pp-biz-row">
          <div class="pp-biz-logo">
            @if($business->logo)<img src="{{ $business->logo_url }}" alt="">@else {{ $business->category->icon ?? '🏢' }} @endif
          </div>
          <div>
            <div class="pp-biz-name">{{ $business->name }}</div>
            @if($business->city)<div class="pp-biz-loc">📍 {{ $business->location }}</div>@endif
          </div>
        </div>
        <a href="{{ route('directory.show', $business->slug) }}" class="pp-btn pp-btn-outline"><i class="fa-solid fa-store"></i> View Business</a>
        @auth
          @if(Auth::id() !== $post->user_id)
            <a href="{{ route('chat.business.post', [$business->slug, $post->slug]) }}" class="pp-btn pp-btn-primary" style="background:var(--green);display:flex;align-items:center;justify-content:center;gap:8px">
              <i class="fa-solid fa-comments"></i> Chat with Seller
            </a>
          @endif
        @else
          <a href="{{ route('login') }}" class="pp-btn pp-btn-primary" style="background:var(--green);display:flex;align-items:center;justify-content:center;gap:8px">
            <i class="fa-solid fa-comments"></i> Chat with Seller
          </a>
        @endauth
        @if($business->phone)
          <a href="tel:{{ $business->phone }}" class="pp-btn pp-btn-primary"><i class="fa-solid fa-phone"></i> {{ $business->phone }}</a>
        @endif
        @if($business->website)
          <a href="{{ $business->website }}" target="_blank" class="pp-btn pp-btn-outline"><i class="fa-solid fa-globe"></i> Visit Website</a>
        @endif
      </div>
    </div>

    @if($morePosts->isNotEmpty())
    <div class="pp-side-card">
      <div class="pp-side-head">More from this business</div>
      <div class="pp-side-body">
        <div class="more-grid">
          @foreach($morePosts as $mp)
            <a href="{{ route('directory.post', [$business->slug, $mp->slug]) }}" class="more-card">
              <div class="more-img">@if($mp->image_url)<img src="{{ $mp->image_url }}" alt="{{ $mp->title }}">@else 📦 @endif</div>
              <div class="more-body">
                <div class="more-title">{{ Str::limit($mp->title, 28) }}</div>
                @if($mp->price)<div class="more-price">{{ $mp->price }}</div>@endif
              </div>
            </a>
          @endforeach
        </div>
      </div>
    </div>
    @endif
  </div>

</div>
@endsection
