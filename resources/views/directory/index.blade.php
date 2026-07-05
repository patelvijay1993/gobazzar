@extends('layouts.app')
@section('title', 'Business Directory — Indian Businesses in Canada')

@push('styles')
<style>
/* ── LAYOUT ── */
.dir-wrap{max-width:1280px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:240px 1fr;gap:20px;align-items:start}

/* ── SIDEBAR ── */
.cl-sidebar{display:flex;flex-direction:column;gap:12px}
.sb-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.sb-box-head{background:var(--primary);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;display:flex;align-items:center;gap:7px}
.sb-box-head i{font-size:13px;opacity:.85}
.filter-list{padding:6px 0}
.filter-item{display:flex;align-items:center;padding:9px 14px;font-size:13px;transition:background .12s;gap:9px;color:var(--text);text-decoration:none;border-left:3px solid transparent}
.filter-item:hover{background:var(--primary-light);color:var(--primary);border-left-color:var(--primary)}
.filter-item.active{color:var(--primary);font-weight:600;background:var(--primary-light);border-left-color:var(--primary)}

/* ── MOBILE TOGGLE ── */
.mobile-filter-toggle{display:none;width:100%;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 16px;font-size:13px;font-weight:600;margin-bottom:12px;cursor:pointer;align-items:center;gap:8px}

/* ── SEARCH ── */
.dir-search{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);display:flex;overflow:hidden;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.dir-search input{flex:1;border:none;padding:11px 14px;font-size:13.5px;background:none;color:#111;font-family:var(--fb)}
.dir-search input:focus{outline:none}
.dir-search button{background:var(--primary);color:#fff;border:none;padding:0 20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;white-space:nowrap;transition:background .2s}
.dir-search button:hover{background:var(--primary-dark)}

/* ── RESULTS HEAD ── */
.results-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px}
.results-count{font-size:13px;color:var(--muted)}
.results-count strong{color:var(--text);font-weight:700}

/* ── ACTIVE FILTERS ── */
.active-filters{display:flex;flex-wrap:wrap;gap:7px;margin-bottom:12px}
.filter-tag{display:inline-flex;align-items:center;gap:5px;background:var(--primary-light);color:var(--primary);font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;text-decoration:none;border:1px solid #c5d0ef}
.filter-tag:hover{background:#d0d9f0}

/* ── BIZ CARDS ── */
.biz-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.biz-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:all .18s;display:block;color:var(--text);text-decoration:none}
.biz-card:hover{border-color:var(--primary);box-shadow:0 6px 20px rgba(26,58,143,.12);transform:translateY(-2px)}

.biz-banner{height:120px;display:flex;align-items:center;justify-content:center;font-size:36px;position:relative;overflow:hidden;background:#f5f0ec}
.biz-banner img{width:100%;height:100%;object-fit:cover;display:block}
.biz-logo-wrap{position:absolute;bottom:-18px;left:14px}
.biz-logo{width:46px;height:46px;border-radius:10px;background:#fff;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:20px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1)}
.biz-logo img{width:100%;height:100%;object-fit:cover}

.biz-body{padding:26px 14px 14px}
.biz-name-row{display:flex;align-items:center;gap:6px;margin-bottom:3px;flex-wrap:wrap}
.biz-name{font-family:var(--fh);font-size:14px;font-weight:700;color:var(--text)}
.badge-ver{background:#dcfce7;color:#15803d;font-size:9.5px;font-weight:700;padding:2px 7px;border-radius:20px;display:inline-flex;align-items:center;gap:3px}
.badge-feat{background:#fef9c3;color:#92400e;font-size:9.5px;font-weight:700;padding:2px 7px;border-radius:20px}
.biz-cat{font-size:11.5px;color:var(--muted);margin-bottom:5px}
.biz-loc{font-size:12px;color:var(--muted);display:flex;align-items:center;gap:4px;margin-bottom:7px}
.biz-loc i{font-size:11px;color:var(--primary);opacity:.7}
.biz-rating{display:flex;align-items:center;gap:5px;font-size:12px;margin-bottom:8px}
.stars{color:#f59e0b}
.biz-footer{display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);padding-top:9px;margin-top:4px}
.biz-tags{display:flex;gap:4px;flex-wrap:wrap}
.biz-tag{background:#f0ede8;color:#555;font-size:10px;padding:2px 8px;border-radius:20px;border:1px solid var(--border)}
.view-btn{font-size:11.5px;color:var(--primary);font-weight:600;white-space:nowrap}

.empty-state{padding:60px 20px;text-align:center;background:#fff;border:1px solid var(--border);border-radius:var(--radius)}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px}
.empty-state h3{font-family:var(--fh);font-size:16px;margin-bottom:6px}
.empty-state p{font-size:13px;color:var(--muted);margin-bottom:16px}
.empty-state a{background:var(--primary);color:#fff;padding:9px 20px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .dir-wrap{grid-template-columns:1fr;padding:0 14px;margin:14px auto}
  .cl-sidebar{display:none}
  .cl-sidebar.open{display:flex}
  .mobile-filter-toggle{display:flex}
  .biz-grid{grid-template-columns:repeat(2,1fr);gap:12px}
}
@media(max-width:520px){
  .biz-grid{grid-template-columns:1fr 1fr;gap:9px}
  .biz-banner{height:100px}
  .biz-body{padding:22px 10px 10px}
  .biz-name{font-size:13px}
}
</style>
@endpush

@section('content')
<h1 style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0">Business Directory — Indian Businesses in Canada</h1>
<div class="dir-wrap">

  {{-- Mobile toggle --}}
  <button class="mobile-filter-toggle" onclick="document.querySelector('.cl-sidebar').classList.toggle('open');this.innerHTML=document.querySelector('.cl-sidebar').classList.contains('open')?'<i class=\'fa-solid fa-times\'></i> Hide Filters':'<i class=\'fa-solid fa-sliders\'></i> Filters & Categories'">
    <i class="fa-solid fa-sliders"></i> Filters & Categories
  </button>

  {{-- SIDEBAR --}}
  <aside class="cl-sidebar">
    {{-- Categories --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-grid-2"></i> Categories</div>
      <div class="filter-list">
        <a href="{{ route('directory.index', request()->except('category','page')) }}"
           class="filter-item {{ !request('category') ? 'active' : '' }}">
          <i class="fa-solid fa-building-columns" style="width:16px;font-size:12px;color:var(--muted)"></i> All Businesses
        </a>
        @foreach($categories as $cat)
          <a href="{{ route('directory.category', $cat->slug) }}"
             class="filter-item {{ request('category') == $cat->id ? 'active' : '' }}">
            <span style="width:16px;text-align:center">{{ $cat->icon }}</span> {{ $cat->name }}
            @if($cat->children->count())<i class="fa-solid fa-chevron-right" style="margin-left:auto;font-size:10px;color:var(--muted)"></i>@endif
          </a>
          @foreach($cat->children as $sub)
            <a href="{{ route('directory.index', ['category' => $sub->id]) }}"
               class="filter-item {{ request('category') == $sub->id ? 'active' : '' }}"
               style="padding-left:38px;font-size:12.5px">
              <span style="width:14px;text-align:center">{{ $sub->icon ?: '•' }}</span> {{ $sub->name }}
            </a>
          @endforeach
        @endforeach
      </div>
    </div>

    {{-- Location --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-location-dot"></i> Location</div>
      <form method="GET" action="{{ route('directory.index') }}" style="padding:14px">
        @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
        <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Province</div>
        <select name="province" style="width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 10px;font-size:13px;background:#fafafa;margin-bottom:10px;font-family:var(--fb)" onchange="this.form.submit()">
          <option value="">All Provinces</option>
          @foreach($provinces as $prov)
            <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
          @endforeach
        </select>
        @if(request('province'))
          <a href="javascript:void(0)" onclick="clearLocation()"
             style="display:block;text-align:center;font-size:12px;color:var(--muted);text-decoration:none">
            <i class="fa-solid fa-times"></i> Clear Location
          </a>
        @endif
      </form>
    </div>

    {{-- List Business CTA --}}
    <div class="sb-box" style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);border-color:transparent;padding:16px;text-align:center">
      <div style="font-size:26px;margin-bottom:8px">🏢</div>
      <div style="font-family:var(--fh);font-size:14px;font-weight:700;color:#fff;margin-bottom:4px">List Your Business</div>
      <div style="font-size:11px;color:rgba(255,255,255,.65);margin-bottom:12px;line-height:1.5">Reach thousands of Indian-Canadians looking for your services</div>
      @auth
        <a href="{{ route('post.create') }}" style="display:block;background:var(--accent);color:#fff;padding:9px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none"><i class="fa-solid fa-plus"></i> Add Your Business</a>
      @else
        <a href="{{ route('register') }}" style="display:block;background:var(--accent);color:#fff;padding:9px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none"><i class="fa-solid fa-plus"></i> Add Your Business</a>
      @endauth
    </div>

    {{-- Sidebar Ad --}}
    @if(isset($ads) && $ads->where('position','sidebar')->isNotEmpty())
    <div class="sb-box" style="padding:10px;overflow:hidden">
      <x-ad-slot position="sidebar" :ads="$ads" />
    </div>
    @endif
  </aside>

  {{-- MAIN CONTENT --}}
  <div>
    {{-- Search --}}
    <form method="GET" action="{{ route('directory.index') }}">
      @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
      @if(request('province'))<input type="hidden" name="province" value="{{ request('province') }}">@endif
      <div class="dir-search">
        <i class="fa-solid fa-magnifying-glass" style="padding:0 10px 0 14px;color:#bbb;font-size:15px;align-self:center;flex-shrink:0"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search businesses...">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      </div>
    </form>

    {{-- Active filters --}}
    @if(request('search') || request('province') || request('category'))
    <div class="active-filters">
      @if(request('search'))
        <a href="{{ route('directory.index', request()->except('search','page')) }}" class="filter-tag">"{{ request('search') }}" <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('province'))
        <span class="filter-tag" style="cursor:pointer" onclick="clearLocation()"><i class="fa-solid fa-map"></i> {{ request('province') }} <i class="fa-solid fa-times"></i></span>
      @endif
      <span style="font-size:12px;color:var(--muted);align-self:center;cursor:pointer;margin-left:4px" onclick="clearAllFilters()">Clear all</span>
    </div>
    @endif

    {{-- Results count --}}
    <div class="results-head">
      <div class="results-count"><strong>{{ number_format($businesses->total()) }}</strong> business{{ $businesses->total() != 1 ? 'es' : '' }} found</div>
    </div>

    @if($businesses->isEmpty())
      <div class="empty-state">
        <div class="empty-icon">🏢</div>
        <h3>No businesses found</h3>
        <p>Try adjusting your filters or search terms.</p>
        <a href="{{ route('post.create') }}"><i class="fa-solid fa-plus"></i> Add a Business</a>
      </div>
    @else
      <div class="biz-grid">
        @foreach($businesses as $biz)
          <a href="{{ route('directory.show', $biz) }}" class="biz-card">
            <div class="biz-banner">
              @if($biz->image_url)
                <img src="{{ $biz->image_url }}" alt="{{ $biz->name }}">
              @else
                <span style="font-size:40px">{{ $biz->category->icon ?? '🏢' }}</span>
              @endif
              <div class="biz-logo-wrap">
                <div class="biz-logo">
                  @if($biz->logo)
                    <img src="{{ $biz->logo_url }}" alt="">
                  @else
                    {{ $biz->category->icon ?? '🏢' }}
                  @endif
                </div>
              </div>
            </div>
            <div class="biz-body">
              <div class="biz-name-row">
                <span class="biz-name">{{ Str::limit($biz->name, 24) }}</span>
                @if($biz->is_verified)<span class="badge-ver"><i class="fa-solid fa-circle-check" style="font-size:9px"></i> Verified</span>@endif
                @if($biz->is_featured)<span class="badge-feat">★ Featured</span>@endif
              </div>
              <div class="biz-cat">{{ $biz->category->icon ?? '' }} {{ $biz->category->name ?? '' }}</div>
              @if($biz->city)
                <div class="biz-loc"><i class="fa-solid fa-location-dot"></i> {{ $biz->city }}{{ $biz->province ? ', '.$biz->province : '' }}</div>
              @endif
              @if($biz->rating > 0)
                <div class="biz-rating">
                  <span class="stars">
                    @for($s=1;$s<=5;$s++)
                      @if($s <= floor($biz->rating))
                        <i class="fa-solid fa-star" style="font-size:11px"></i>
                      @elseif($s - $biz->rating < 1)
                        <i class="fa-solid fa-star-half-stroke" style="font-size:11px"></i>
                      @else
                        <i class="fa-regular fa-star" style="font-size:11px"></i>
                      @endif
                    @endfor
                  </span>
                  <span style="font-weight:700;font-size:12px">{{ number_format($biz->rating,1) }}</span>
                  @if($biz->review_count)<span style="color:var(--muted);font-size:11px">({{ $biz->review_count }})</span>@endif
                </div>
              @endif
              <div class="biz-footer">
                <div class="biz-tags">
                  @if($biz->tags)
                    @foreach(array_slice($biz->tags, 0, 2) as $tag)
                      <span class="biz-tag">{{ $tag }}</span>
                    @endforeach
                  @endif
                </div>
                <span class="view-btn">View →</span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
      {{-- Inline Ad --}}
      @if(isset($ads) && $ads->where('position','inline')->isNotEmpty())
        <x-ad-slot position="inline" :ads="$ads" style="margin:14px 0" />
      @endif
      <div style="margin-top:20px">{{ $businesses->withQueryString()->links() }}</div>
    @endif
  </div>

</div>

{{-- MOBILE SIDEBAR AD --}}
@if(isset($ads) && $ads->where('position','sidebar')->isNotEmpty())
<div class="mob-sidebar-ad">
  <x-ad-slot position="sidebar" :ads="$ads" />
</div>
@endif

@endsection
