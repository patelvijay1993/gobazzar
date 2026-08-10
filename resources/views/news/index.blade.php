@extends('layouts.app')
@section('title', 'News — GoBazaar')

@push('styles')
<style>
/* ── LAYOUT ── */
.news-wrap{max-width:1280px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:1fr 280px;gap:24px;align-items:start}

/* ── SEARCH ── */
.news-search{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);display:flex;overflow:hidden;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.news-search input{flex:1;border:none;padding:11px 14px;font-size:13.5px;background:none;color:#111;font-family:var(--fb)}
.news-search input:focus{outline:none}
.news-search button{background:var(--primary);color:#fff;border:none;padding:0 20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;white-space:nowrap;transition:background .2s}
.news-search button:hover{background:var(--primary-dark)}

/* ── FEATURED ── */
.featured-news{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;display:grid;grid-template-columns:1.1fr 1fr;margin-bottom:20px;text-decoration:none;color:var(--text);transition:box-shadow .2s}
.featured-news:hover{box-shadow:0 6px 24px rgba(26,58,143,.12)}
.featured-img{height:260px;overflow:hidden;background:#f5f0ec}
.featured-img img{width:100%;height:100%;object-fit:cover;display:block}
.featured-body{padding:28px;display:flex;flex-direction:column;justify-content:center;gap:10px}
.featured-eyebrow{display:flex;align-items:center;gap:8px}
.feat-badge{background:var(--primary);color:#fff;font-size:9.5px;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px}
.feat-cat{font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.5px}
.featured-body h2{font-family:var(--fg);font-size:20px;font-weight:700;line-height:1.5;color:var(--text)}
.featured-body p{font-family:var(--fg);font-size:13.5px;color:var(--muted);line-height:1.8}
.featured-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap;font-size:12px;color:var(--muted)}
.featured-meta i{font-size:11px;color:var(--primary);opacity:.7;margin-right:3px}
.read-more-btn{display:inline-flex;align-items:center;gap:6px;background:var(--primary);color:#fff;font-size:12.5px;font-weight:600;padding:8px 16px;border-radius:20px;margin-top:2px;width:fit-content;transition:background .2s}
.read-more-btn:hover{background:var(--primary-dark)}

/* ── GRID ── */
.news-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
.news-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:all .18s;display:block;text-decoration:none;color:var(--text)}
.news-card:hover{border-color:var(--primary);box-shadow:0 4px 16px rgba(26,58,143,.1);transform:translateY(-2px)}
.news-card-img{height:160px;overflow:hidden;background:#f5f0ec;position:relative}
.news-card-img img{width:100%;height:100%;object-fit:cover;display:block}
.news-cat-tag{position:absolute;top:10px;left:10px;background:var(--primary);color:#fff;font-size:9.5px;font-weight:700;padding:3px 9px;border-radius:20px;text-transform:uppercase;letter-spacing:.4px}
.news-card-body{padding:14px}
.news-cat-label{font-size:10.5px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
.news-card-body h3{font-family:var(--fg);font-size:14.5px;font-weight:700;line-height:1.6;margin-bottom:6px;color:var(--text);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.news-card-body p{font-family:var(--fg);font-size:12.5px;color:var(--muted);line-height:1.8;margin-bottom:10px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.news-foot{display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);padding-top:9px;font-size:11px;color:var(--muted)}
.news-foot i{font-size:10px;margin-right:3px;color:var(--primary);opacity:.7}

/* ── ACTIVE FILTERS ── */
.active-filters{display:flex;flex-wrap:wrap;gap:7px;margin-bottom:14px}
.filter-tag{display:inline-flex;align-items:center;gap:5px;background:var(--primary-light);color:var(--primary);font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;text-decoration:none;border:1px solid #c5d0ef}
.filter-tag:hover{background:#d0d9f0}

/* ── SIDEBAR ── */
.sb-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:14px}
.sb-box-head{background:var(--primary);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;display:flex;align-items:center;gap:7px}
.sb-box-head i{font-size:13px;opacity:.85}
.sb-body{padding:14px}
.cat-pill{display:inline-block;background:#f0ede8;border:1px solid var(--border);border-radius:20px;padding:5px 14px;font-size:12px;color:var(--muted);margin:3px;cursor:pointer;transition:all .15s;text-decoration:none;text-transform:capitalize}
.cat-pill:hover,.cat-pill.active{background:var(--primary);border-color:var(--primary);color:#fff}

.latest-item{display:flex;gap:10px;align-items:flex-start;padding:10px 0;border-bottom:1px solid var(--border)}
.latest-item:last-child{border-bottom:none;padding-bottom:0}
.latest-thumb{width:52px;height:48px;border-radius:7px;overflow:hidden;background:#f5f0ec;flex-shrink:0}
.latest-thumb img{width:100%;height:100%;object-fit:cover}
.latest-info a{font-family:var(--fg);font-size:12.5px;font-weight:600;color:var(--text);line-height:1.6;display:block;text-decoration:none}
.latest-info a:hover{color:var(--primary)}
.latest-info span{font-size:11px;color:var(--muted)}

.empty-state{padding:60px 20px;text-align:center;background:#fff;border:1px solid var(--border);border-radius:var(--radius)}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px}
.empty-state h3{font-family:var(--fh);font-size:16px;margin-bottom:6px}
.empty-state p{font-size:13px;color:var(--muted)}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .news-wrap{grid-template-columns:1fr;padding:0 14px;margin:14px auto}
  .news-wrap aside{display:none}
  .news-grid{grid-template-columns:repeat(2,1fr)}
  .featured-news{grid-template-columns:1fr}
  .featured-img{height:200px}
}
@media(max-width:520px){
  .news-grid{grid-template-columns:1fr}
  .featured-body{padding:16px}
  .featured-body h2{font-size:17px}
  .news-card-img{height:140px}
}
</style>
@endpush

@section('content')
<h1 style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0">News — GoBazaar</h1>
<div class="news-wrap">

  {{-- MAIN CONTENT --}}
  <div>
    {{-- Search --}}
    <form method="GET" action="{{ route('news.index') }}">
      @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
      <div class="news-search">
        <i class="fa-solid fa-magnifying-glass" style="padding:0 10px 0 14px;color:#bbb;font-size:15px;align-self:center;flex-shrink:0"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search news...">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      </div>
    </form>

    {{-- Active filters --}}
    @if(request('search') || request('category'))
    <div class="active-filters">
      @if(request('search'))
        <a href="{{ route('news.index', request()->except('search','page')) }}" class="filter-tag">"{{ request('search') }}" <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('category'))
        <a href="{{ route('news.index', request()->except('category','page')) }}" class="filter-tag"><i class="fa-solid fa-tag"></i> {{ request('category') }} <i class="fa-solid fa-times"></i></a>
      @endif
      <a href="{{ route('news.index') }}" style="font-size:12px;color:var(--muted);align-self:center;text-decoration:none;margin-left:4px">Clear all</a>
    </div>
    @endif

    {{-- Featured --}}
    @if($featured && !request('search') && !request('category'))
    <a href="{{ route('news.show', $featured->slug) }}" class="featured-news">
      <div class="featured-img">
        @if($featured->image_url)
          <img src="{{ $featured->image_url }}" alt="{{ $featured->title }}">
        @else
          <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary-light),#c5d0ef);display:grid;place-items:center;font-size:56px">📰</div>
        @endif
      </div>
      <div class="featured-body">
        <div class="featured-eyebrow">
          <span class="feat-badge"><i class="fa-solid fa-star" style="font-size:8px"></i> Featured</span>
          @if(!empty($featured->category[0]))<span class="feat-cat">{{ $featured->category[0] }}</span>@endif
        </div>
        <h2>{{ $featured->title }}</h2>
        @if($featured->description)<p>{{ Str::limit(strip_tags($featured->description), 120) }}</p>@endif
        <div class="featured-meta">
          @if($featured->source_name)<span><i class="fa-regular fa-newspaper"></i>{{ $featured->source_name }}</span>@endif
          <span><i class="fa-regular fa-clock"></i>{{ $featured->read_time }}</span>
          @if($featured->pub_date)<span><i class="fa-regular fa-calendar"></i>{{ $featured->pub_date->format('M j, Y') }}</span>@endif
          <span><i class="fa-regular fa-eye"></i>{{ number_format($featured->views) }} views</span>
        </div>
        <span class="read-more-btn"><i class="fa-solid fa-arrow-right"></i> Read More</span>
      </div>
    </a>
    @endif

    {{-- Grid --}}
    @if($articles->isEmpty())
      <div class="empty-state">
        <div class="empty-icon">📰</div>
        <h3>No news found</h3>
        <p>Try a different search or category.</p>
      </div>
    @else
      <div class="news-grid">
        @foreach($articles as $article)
        <a href="{{ route('news.show', $article->slug) }}" class="news-card">
          <div class="news-card-img">
            @if($article->image_url)
              <img src="{{ $article->image_url }}" alt="{{ $article->title }}">
            @else
              <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--primary-light),#c5d0ef);display:grid;place-items:center;font-size:36px">📰</div>
            @endif
            @if(!empty($article->category[0]))<span class="news-cat-tag">{{ $article->category[0] }}</span>@endif
          </div>
          <div class="news-card-body">
            <h3>{{ $article->title }}</h3>
            @if($article->description)<p>{{ Str::limit(strip_tags($article->description), 90) }}</p>@endif
            <div class="news-foot">
              @if($article->source_name)<span><i class="fa-regular fa-newspaper"></i>{{ Str::limit($article->source_name, 16) }}</span>@endif
              @if($article->pub_date)<span><i class="fa-regular fa-calendar"></i>{{ $article->pub_date->format('M j, Y') }}</span>@endif
              <span><i class="fa-regular fa-eye"></i>{{ number_format($article->views) }}</span>
            </div>
          </div>
        </a>
        @endforeach
      </div>
      <div style="margin-top:20px">{{ $articles->withQueryString()->links() }}</div>
    @endif
  </div>

  {{-- SIDEBAR --}}
  <aside>
    {{-- Categories --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-tags"></i> Categories</div>
      <div class="sb-body">
        <a href="{{ route('news.index', request()->except('category','page')) }}"
           class="cat-pill {{ !request('category') ? 'active' : '' }}">All</a>
        @foreach($categories as $cat)
          <a href="{{ route('news.index', array_merge(request()->except('category','page'), ['category' => $cat])) }}"
             class="cat-pill {{ request('category') === $cat ? 'active' : '' }}">{{ $cat }}</a>
        @endforeach
      </div>
    </div>

    {{-- Latest --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-clock-rotate-left"></i> Latest News</div>
      <div class="sb-body" style="padding-top:6px;padding-bottom:6px">
        @php
          $latestNews = \App\Models\NewsArticle::where('status','published')->latest('pub_date')->limit(5)->get();
        @endphp
        @foreach($latestNews as $ln)
        <div class="latest-item">
          <div class="latest-thumb">
            @if($ln->image_url)
              <img src="{{ $ln->image_url }}" alt="{{ $ln->title }}">
            @else
              <div style="width:100%;height:100%;background:var(--primary-light);display:grid;place-items:center;font-size:20px">📰</div>
            @endif
          </div>
          <div class="latest-info">
            <a href="{{ route('news.show', $ln->slug) }}">{{ Str::limit($ln->title, 52) }}</a>
            <span>{{ $ln->pub_date?->format('M j, Y') }}</span>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </aside>

</div>
@endsection
