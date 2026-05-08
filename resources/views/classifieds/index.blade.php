@extends('layouts.app')
@section('title', 'Classifieds')

@push('styles')
<style>
.page-header{background:var(--dark);padding:20px;color:#fff}
.page-header h1{font-family:var(--fh);font-size:20px;max-width:1200px;margin:0 auto}
.cl-wrap{max-width:1200px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:220px 1fr;gap:20px}
.sidebar{display:flex;flex-direction:column;gap:14px}
.mobile-filter-toggle{display:none;width:100%;background:var(--surface);border:1.5px solid var(--border2);border-radius:var(--r);padding:10px 16px;font-size:13px;font-weight:600;color:var(--text);text-align:left;margin-bottom:12px;cursor:pointer}
@media(max-width:768px){
  .cl-wrap{grid-template-columns:1fr;padding:0 14px}
  .sidebar{display:none}
  .sidebar.open{display:flex}
  .mobile-filter-toggle{display:block}
  .ads-grid{grid-template-columns:repeat(2,1fr);gap:12px}
  .ad-thumb{height:150px}
}
@media(max-width:480px){
  .ads-grid{grid-template-columns:1fr}
  .page-header h1{font-size:16px}
}
.filter-box{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r);overflow:hidden}
.filter-box-head{background:var(--dark);color:#fff;padding:8px 12px;font-family:var(--fh);font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.filter-list{padding:8px 0}
.filter-item{display:flex;align-items:center;padding:7px 14px;font-size:12.5px;cursor:pointer;transition:background .12s;gap:8px;color:var(--text)}
.filter-item:hover{background:var(--red-pale);color:var(--red)}
.filter-item.active{color:var(--red);font-weight:600;background:var(--red-pale)}

.search-bar{background:var(--surface);border:1.5px solid var(--border2);border-radius:var(--r);display:flex;overflow:hidden;margin-bottom:14px}
.search-bar input{flex:1;border:none;padding:10px 14px;font-size:13px;background:none}
.search-bar button{background:var(--red);color:#fff;padding:0 18px;font-size:13px;font-weight:500}

.ads-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px}
.ad-card{border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;transition:all .18s;display:block;color:var(--text);background:var(--surface)}
.ad-card:hover{border-color:var(--red2);box-shadow:var(--sh);transform:translateY(-2px)}
.ad-thumb{height:190px;overflow:hidden;background:#f3f3f3;display:flex;align-items:center;justify-content:center;font-size:48px}
.ad-thumb img{width:100%;height:100%;object-fit:cover}
.ad-body{padding:14px}
.ad-title{font-size:14px;font-weight:500;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:6px}
.ad-loc{font-size:12px;color:var(--muted);margin-bottom:8px}
.ad-price{font-family:var(--fh);font-size:18px;font-weight:700;color:var(--red)}
.badge{font-size:9.5px;font-weight:700;padding:2px 7px;border-radius:20px;margin-right:3px}
.badge-feat{background:var(--gold);color:#fff}
.badge-ver{background:var(--green);color:#fff}
.badge-new{background:var(--blue);color:#fff}
.badge-hot{background:var(--red);color:#fff}
.empty{padding:40px;text-align:center;color:var(--muted);font-size:13px;background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r)}
.pagination-wrap{margin-top:20px}
</style>
@endpush

@section('content')
<div class="page-header">
  <h1>Classifieds — Find Anything in Canada</h1>
</div>

<div class="cl-wrap">
  <button class="mobile-filter-toggle" onclick="this.nextElementSibling.classList.toggle('open');this.textContent=this.nextElementSibling.classList.contains('open')?'▲ Hide Filters':'▼ Filters & Categories'">▼ Filters & Categories</button>
  <aside class="sidebar">
    <div class="filter-box">
      <div class="filter-box-head">Categories</div>
      <div class="filter-list">
        <a href="{{ route('classifieds.index', request()->except('category')) }}"
           class="filter-item {{ !request('category') ? 'active' : '' }}">
          📋 All Categories
        </a>
        @foreach($categories as $cat)
          <a href="{{ route('classifieds.index', array_merge(request()->query(), ['category' => $cat->id])) }}"
             class="filter-item {{ request('category') == $cat->id ? 'active' : '' }}">
            {{ $cat->icon }} {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </div>

    <x-location-filter route="classifieds.index" :provinces="$provinces" :cities="$cities" />
  </aside>

  <div>
    <form method="GET" action="{{ route('classifieds.index') }}">
      @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
      <div class="search-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search classifieds...">
        <button type="submit">Search</button>
      </div>
    </form>

    @if($listings->isEmpty())
      <div class="empty">
        <div style="font-size:36px;margin-bottom:10px">📭</div>
        No listings found.
      </div>
    @else
      <div class="ads-grid">
        @foreach($listings as $listing)
          <a href="{{ route('classifieds.show', $listing) }}" class="ad-card">
            <div class="ad-thumb">
              @if($listing->image)
                <img src="{{ asset('storage/'.$listing->image) }}" alt="{{ $listing->title }}">
              @else
                {{ $listing->category->icon ?? '📦' }}
              @endif
            </div>
            <div class="ad-body">
              <div style="margin-bottom:4px">
                @foreach($listing->badges ?? [] as $badge)
                  <span class="badge badge-{{ $badge }}">{{ strtoupper($badge) }}</span>
                @endforeach
              </div>
              <div class="ad-title">{{ $listing->title }}</div>
              <div class="ad-loc">📍 {{ $listing->location }}</div>
              @if($listing->price)
                <div class="ad-price">{{ $listing->price }}<small style="font-size:10px;font-weight:400;font-family:var(--fb);color:var(--muted)">{{ $listing->price_unit }}</small></div>
              @endif
            </div>
          </a>
        @endforeach
      </div>
      <div class="pagination-wrap">
        {{ $listings->withQueryString()->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
