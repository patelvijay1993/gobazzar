@extends('layouts.app')
@section('title', 'Business Directory')

@push('styles')
<style>
.page-header{background:var(--dark);padding:20px;color:#fff}
.page-header h1{font-family:var(--fh);font-size:20px;max-width:1200px;margin:0 auto}
.dir-wrap{max-width:1200px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:220px 1fr;gap:20px}
.sidebar{display:flex;flex-direction:column;gap:14px}
.mobile-filter-toggle{display:none;width:100%;background:var(--surface);border:1.5px solid var(--border2);border-radius:var(--r);padding:10px 16px;font-size:13px;font-weight:600;color:var(--text);text-align:left;margin-bottom:12px;cursor:pointer}
@media(max-width:768px){
  .dir-wrap{grid-template-columns:1fr;padding:0 14px}
  .sidebar{display:none}
  .sidebar.open{display:flex}
  .mobile-filter-toggle{display:block}
  .biz-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:480px){
  .page-header h1{font-size:16px}
  .biz-grid{grid-template-columns:1fr}
}
.filter-box{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r);overflow:hidden}
.filter-box-head{background:var(--dark);color:#fff;padding:8px 12px;font-family:var(--fh);font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.filter-list{padding:8px 0}
.filter-item{display:flex;align-items:center;padding:7px 14px;font-size:12.5px;cursor:pointer;transition:background .12s;gap:8px;color:var(--text)}
.filter-item:hover{background:var(--red-pale);color:var(--red)}
.filter-item.active{color:var(--red);font-weight:600;background:var(--red-pale)}
.search-bar{background:var(--surface);border:1.5px solid var(--border2);border-radius:var(--r);display:flex;overflow:hidden;margin-bottom:14px}
.search-bar input,.search-bar select{flex:1;border:none;padding:10px 14px;font-size:13px;background:none}
.search-bar button{background:var(--red);color:#fff;padding:0 18px;font-size:13px;font-weight:500;white-space:nowrap}

.biz-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px}
.biz-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;transition:all .18s;display:block;color:var(--text)}
.biz-card:hover{border-color:var(--red2);box-shadow:var(--sh);transform:translateY(-2px)}
.biz-banner{height:100px;display:flex;align-items:center;justify-content:center;font-size:36px;position:relative;overflow:hidden}
.biz-banner img{width:100%;height:100%;object-fit:cover}
.biz-logo{width:52px;height:52px;border-radius:10px;background:var(--surface);border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:22px;position:absolute;bottom:-20px;left:14px;overflow:hidden}
.biz-logo img{width:100%;height:100%;object-fit:cover}
.biz-body{padding:28px 14px 14px}
.biz-name{font-family:var(--fh);font-size:14px;font-weight:700;margin-bottom:3px}
.biz-cat{font-size:11px;color:var(--muted);margin-bottom:6px}
.biz-loc{font-size:11.5px;color:var(--muted)}
.biz-rating{display:flex;align-items:center;gap:5px;margin-top:6px;font-size:12px}
.stars{color:var(--gold);letter-spacing:1px}
.biz-tags{display:flex;gap:4px;flex-wrap:wrap;margin-top:8px}
.tag{background:var(--bg);border:1px solid var(--border2);color:var(--muted);font-size:9.5px;padding:2px 7px;border-radius:20px}
.badge-ver{background:var(--green);color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:20px}
.badge-feat{background:var(--gold);color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:20px}
.empty{padding:40px;text-align:center;color:var(--muted);font-size:13px;background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r)}
</style>
@endpush

@section('content')
<div class="page-header">
  <h1>🏪 Business Directory — Indian Businesses in Canada</h1>
</div>

<div class="dir-wrap">
  <button class="mobile-filter-toggle" onclick="this.nextElementSibling.classList.toggle('open');this.textContent=this.nextElementSibling.classList.contains('open')?'▲ Hide Filters':'▼ Filters & Categories'">▼ Filters & Categories</button>
  <aside class="sidebar">
    <div class="filter-box">
      <div class="filter-box-head">Categories</div>
      <div class="filter-list">
        <a href="{{ route('directory.index', request()->except('category')) }}"
           class="filter-item {{ !request('category') ? 'active' : '' }}">🏢 All Businesses</a>
        @foreach($categories as $cat)
          <a href="{{ route('directory.index', array_merge(request()->query(), ['category' => $cat->id])) }}"
             class="filter-item {{ request('category') == $cat->id ? 'active' : '' }}">
            {{ $cat->icon }} {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </div>

    <x-location-filter route="directory.index" :provinces="$provinces" :cities="$cities" />
  </aside>

  <div>
    <form method="GET" action="{{ route('directory.index') }}">
      @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
      @if(request('city'))<input type="hidden" name="city" value="{{ request('city') }}">@endif
      <div class="search-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search businesses...">
        <button type="submit">Search</button>
      </div>
    </form>

    @if($businesses->isEmpty())
      <div class="empty">
        <div style="font-size:36px;margin-bottom:10px">🏪</div>
        No businesses found.
      </div>
    @else
      <div class="biz-grid">
        @foreach($businesses as $biz)
          <a href="{{ route('directory.show', $biz) }}" class="biz-card">
            <div class="biz-banner" style="background:{{ '#'.substr(md5($biz->name),0,6) }}22">
              @if($biz->image)
                <img src="{{ asset('storage/'.$biz->image) }}" alt="{{ $biz->name }}">
              @else
                {{ $biz->category->icon ?? '🏢' }}
              @endif
              <div class="biz-logo">
                @if($biz->logo)
                  <img src="{{ asset('storage/'.$biz->logo) }}" alt="">
                @else
                  {{ $biz->category->icon ?? '🏢' }}
                @endif
              </div>
            </div>
            <div class="biz-body">
              <div style="display:flex;align-items:center;gap:5px;margin-bottom:3px">
                <span class="biz-name">{{ $biz->name }}</span>
                @if($biz->is_verified)<span class="badge-ver">✓</span>@endif
                @if($biz->is_featured)<span class="badge-feat">★</span>@endif
              </div>
              <div class="biz-cat">{{ $biz->category->name ?? '' }}</div>
              @if($biz->city)<div class="biz-loc">📍 {{ $biz->city }}{{ $biz->province ? ', '.$biz->province : '' }}</div>@endif
              @if($biz->rating > 0)
                <div class="biz-rating">
                  <span class="stars">★★★★★</span>
                  <span style="font-weight:600">{{ number_format($biz->rating,1) }}</span>
                  <span style="color:var(--hint)">({{ $biz->review_count }})</span>
                </div>
              @endif
              @if($biz->tags)
                <div class="biz-tags">
                  @foreach(array_slice($biz->tags, 0, 3) as $tag)
                    <span class="tag">{{ $tag }}</span>
                  @endforeach
                </div>
              @endif
            </div>
          </a>
        @endforeach
      </div>
      <div style="margin-top:20px">{{ $businesses->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
