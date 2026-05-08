@extends('layouts.app')
@section('title', $listing->title)

@push('styles')
<style>
.show-wrap{max-width:1200px;margin:24px auto;padding:0 20px;display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start}
@media(max-width:768px){.show-wrap{grid-template-columns:1fr;padding:0 14px}.listing-title{font-size:18px}.listing-price{font-size:22px}}
@media(max-width:480px){.related-grid{grid-template-columns:1fr}}
.listing-main{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden}
.listing-img-placeholder{width:100%;height:200px;background:#f3f3f3;font-size:60px;display:flex;align-items:center;justify-content:center}
.listing-body{padding:20px}
.listing-cat{font-size:11px;font-weight:600;color:var(--red);text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px}
.listing-title{font-family:var(--fh);font-size:22px;font-weight:700;line-height:1.3;margin-bottom:10px}
.listing-meta{display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:var(--muted);margin-bottom:16px}
.listing-price{font-family:var(--fh);font-size:28px;font-weight:800;color:var(--red);margin-bottom:16px}
.listing-desc{font-size:13.5px;line-height:1.7;color:var(--text);border-top:1px solid var(--border);padding-top:16px}
.badge{font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;margin-right:4px}
.badge-feat{background:var(--gold);color:#fff}
.badge-ver{background:var(--green);color:#fff}
.badge-new{background:var(--blue);color:#fff}
.badge-hot{background:var(--red);color:#fff}
.tag{background:var(--bg);border:1px solid var(--border2);color:var(--muted);font-size:10px;padding:3px 9px;border-radius:20px;margin-right:4px;margin-top:4px;display:inline-block}

.sidebar-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;margin-bottom:16px}
.sidebar-head{background:var(--dark);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.sidebar-body{padding:16px}
.contact-row{display:flex;align-items:center;gap:10px;margin-bottom:10px;font-size:13px}
.contact-row .icon{font-size:16px}
.btn-block{display:block;width:100%;text-align:center;padding:12px;border-radius:var(--r);font-size:13px;font-weight:600;margin-bottom:8px}

.related-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;padding:10px}
.rel-card{border:1px solid var(--border);border-radius:var(--r);overflow:hidden;display:block;color:var(--text);transition:all .15s}
.rel-card:hover{border-color:var(--red2);transform:translateY(-2px)}
.rel-thumb{height:70px;background:#f3f3f3;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:24px}
.rel-thumb img{width:100%;height:100%;object-fit:cover}
.rel-body{padding:6px 8px}
.rel-title{font-size:11px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.rel-price{font-size:12px;font-weight:700;color:var(--red);font-family:var(--fh)}
</style>
@endpush

@section('content')
<div class="container" style="padding-top:8px">
  <div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a>
    <span>›</span>
    <a href="{{ route('classifieds.index') }}">Classifieds</a>
    <span>›</span>
    <a href="{{ route('classifieds.index', ['category' => $listing->category_id]) }}">{{ $listing->category->name }}</a>
    <span>›</span>
    {{ Str::limit($listing->title, 50) }}
  </div>
</div>

<div class="show-wrap">
  <div class="listing-main">
    @php $allImages = $listing->images ?? ($listing->image ? [$listing->image] : []); @endphp
    @if(count($allImages))
      <x-image-slider :images="$allImages" :alt="$listing->title" height="380px" />
    @else
      <div class="listing-img-placeholder">{{ $listing->category->icon ?? '📦' }}</div>
    @endif
    <div class="listing-body">
      <div class="listing-cat">{{ $listing->category->icon }} {{ $listing->category->name }}</div>
      <div style="margin-bottom:8px">
        @foreach($listing->badges ?? [] as $badge)
          <span class="badge badge-{{ $badge }}">{{ strtoupper($badge) }}</span>
        @endforeach
      </div>
      <h1 class="listing-title">{{ $listing->title }}</h1>
      <div class="listing-meta">
        <span>📍 {{ $listing->location }}</span>
        <span>👁 {{ $listing->views }} views</span>
        <span>📅 {{ $listing->created_at->diffForHumans() }}</span>
      </div>
      @if($listing->price)
        <div class="listing-price">{{ $listing->price }}<span style="font-size:14px;font-weight:400;color:var(--muted);font-family:var(--fb)">{{ $listing->price_unit }}</span></div>
      @endif
      @if($listing->description)
        <div class="listing-desc">{!! $listing->description !!}</div>
      @endif
      @if($listing->tags)
        <div style="margin-top:14px">
          @foreach($listing->tags as $tag)
            <span class="tag">{{ $tag }}</span>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  <div>
    <div class="sidebar-card">
      <div class="sidebar-head">Contact Seller</div>
      <div class="sidebar-body">
        @if($listing->contact_name)
          <div class="contact-row"><span class="icon">👤</span> {{ $listing->contact_name }}</div>
        @endif
        @if($listing->contact_phone)
          <div class="contact-row"><span class="icon">📞</span> {{ $listing->contact_phone }}</div>
        @endif
        @if($listing->contact_email)
          <a href="mailto:{{ $listing->contact_email }}" class="btn btn-red btn-block">✉ Send Email</a>
        @endif
        @if($listing->contact_phone)
          <a href="tel:{{ $listing->contact_phone }}" class="btn btn-ghost btn-block">📞 Call Now</a>
        @endif
      </div>
    </div>

    @if($related->count())
      <div class="sidebar-card">
        <div class="sidebar-head">Similar Listings</div>
        <div class="related-grid">
          @foreach($related as $rel)
            <a href="{{ route('classifieds.show', $rel) }}" class="rel-card">
              <div class="rel-thumb">
                @if($rel->image)
                  <img src="{{ asset('storage/'.$rel->image) }}" alt="{{ $rel->title }}">
                @else
                  {{ $rel->category->icon ?? '📦' }}
                @endif
              </div>
              <div class="rel-body">
                <div class="rel-title">{{ $rel->title }}</div>
                @if($rel->price)<div class="rel-price">{{ $rel->price }}</div>@endif
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>

@endsection
