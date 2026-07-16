@extends('layouts.app')
@section('title', 'Classifieds — Find Anything in Canada')

@push('styles')
<style>
/* ── PAGE HEADER ── */
.page-hero{background:var(--primary);padding:22px 20px;position:relative;overflow:hidden}
.page-hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");pointer-events:none}
.page-hero-inner{max-width:1280px;margin:0 auto;position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.page-hero h1{font-family:var(--fh);font-size:22px;font-weight:800;color:#fff}
.page-hero h1 span{color:var(--accent)}
.page-hero p{font-size:13px;color:rgba(255,255,255,.65);margin-top:2px}
.post-btn-hero{background:var(--accent);color:#fff;padding:9px 18px;border-radius:var(--radius-sm);font-size:13px;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:6px;white-space:nowrap;flex-shrink:0;transition:opacity .2s}
.post-btn-hero:hover{opacity:.88}

/* ── LAYOUT ── */
.cl-wrap{max-width:1280px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:240px 1fr;gap:20px;align-items:start}

/* ── SIDEBAR ── */
.cl-sidebar{display:flex;flex-direction:column;gap:12px}
.sb-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.sb-box-head{background:var(--primary);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;display:flex;align-items:center;gap:7px}
.sb-box-head i{font-size:13px;opacity:.85}
.filter-list{padding:6px 0}
.filter-item{display:flex;align-items:center;padding:9px 14px;font-size:13px;cursor:pointer;transition:background .12s;gap:9px;color:var(--text);text-decoration:none;border-left:3px solid transparent}
.filter-item:hover{background:var(--primary-light);color:var(--primary);border-left-color:var(--primary)}
.filter-item.active{color:var(--primary);font-weight:600;background:var(--primary-light);border-left-color:var(--primary)}
.filter-item .cat-count{margin-left:auto;font-size:11px;color:var(--muted);background:#f0ede8;padding:1px 7px;border-radius:20px}

.loc-box{padding:14px}
.loc-label{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.loc-select{width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 10px;font-size:13px;color:var(--text);background:#fafafa;margin-bottom:10px;font-family:var(--fb)}
.loc-select:focus{border-color:var(--primary);outline:none}
.loc-btn{width:100%;background:var(--primary);color:#fff;border:none;padding:9px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;cursor:pointer;transition:background .2s;display:flex;align-items:center;justify-content:center;gap:6px}
.loc-btn:hover{background:var(--primary-dark)}

/* ── MOBILE TOGGLE ── */
.mobile-filter-toggle{display:none;width:100%;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 16px;font-size:13px;font-weight:600;margin-bottom:12px;cursor:pointer;align-items:center;gap:8px}

/* ── SEARCH BAR ── */
.cl-search{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);display:flex;overflow:hidden;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.cl-search input{flex:1;border:none;padding:11px 14px;font-size:13.5px;background:none;color:#111;font-family:var(--fb)}
.cl-search input:focus{outline:none}
.cl-search select{border:none;border-left:1px solid var(--border);padding:0 12px;font-size:12.5px;color:#555;background:#f9fafb;font-family:var(--fb)}
.cl-search button{background:var(--primary);color:#fff;border:none;padding:0 20px;font-size:13.5px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;transition:background .2s;white-space:nowrap}
.cl-search button:hover{background:var(--primary-dark)}

/* ── RESULTS HEADER ── */
.results-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px}
.results-count{font-size:13px;color:var(--muted)}
.results-count strong{color:var(--text);font-weight:700}
.sort-select{border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:6px 10px;font-size:12.5px;color:#555;background:#fff;font-family:var(--fb)}

/* ── AD CARDS ── */
.ads-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.ad-card{border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:all .18s;display:block;color:var(--text);background:#fff;text-decoration:none}
.ad-card:hover{border-color:var(--primary);box-shadow:0 6px 20px rgba(26,58,143,.12);transform:translateY(-2px)}
.ad-thumb{height:170px;overflow:hidden;background:#f5f0ec;display:flex;align-items:center;justify-content:center;font-size:44px;position:relative}
.ad-thumb img{width:100%;height:100%;object-fit:cover;display:block}
.ad-feat-badge{position:absolute;top:8px;left:8px;background:var(--primary);color:#fff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:4px;text-transform:uppercase;letter-spacing:.4px}
.ad-fav{position:absolute;top:8px;right:8px;background:rgba(255,255,255,.92);width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center}
.ad-fav i{font-size:12px;color:#bbb}
.ad-body{padding:12px}
.ad-price{font-family:var(--fh);font-size:17px;font-weight:800;color:var(--primary);margin-bottom:3px}
.ad-price small{font-size:11px;font-weight:400;color:var(--muted);font-family:var(--fb)}
.ad-title{font-size:12.5px;font-weight:500;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:5px;color:var(--text)}
.ad-cat{font-size:10.5px;color:var(--muted);margin-bottom:8px}
.ad-foot{display:flex;justify-content:space-between;align-items:center;font-size:10.5px;color:var(--muted);border-top:1px solid var(--border);padding-top:7px}
.ad-foot i{font-size:11px;margin-right:2px}

.badge{font-size:9px;font-weight:700;padding:2px 7px;border-radius:20px;margin-right:3px}
.badge-feat{background:#fef9c3;color:#92400e}
.badge-ver{background:#dcfce7;color:#15803d}
.badge-new{background:#dbeafe;color:#1d4ed8}
.badge-hot{background:#fee2e2;color:#b91c1c}

.empty-state{padding:60px 20px;text-align:center;background:#fff;border:1px solid var(--border);border-radius:var(--radius)}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px}
.empty-state h3{font-family:var(--fh);font-size:16px;color:var(--text);margin-bottom:6px}
.empty-state p{font-size:13px;color:var(--muted);margin-bottom:16px}
.empty-state a{background:var(--primary);color:#fff;padding:9px 20px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px}

.pagination-wrap{margin-top:20px}

/* ── CATEGORY COLLAPSE ── */
.cat-parent-row{display:flex;align-items:center;padding:9px 14px;font-size:13px;cursor:pointer;transition:background .12s;gap:9px;color:var(--text);border-left:3px solid transparent;user-select:none}
.cat-parent-row:hover{background:var(--primary-light);color:var(--primary);border-left-color:var(--primary)}
.cat-parent-row.active{color:var(--primary);font-weight:600;background:var(--primary-light);border-left-color:var(--primary)}
.cat-arrow{font-size:10px;color:var(--muted);transition:transform .22s ease;flex-shrink:0}
.cat-arrow.open{transform:rotate(180deg)}
.cat-children{max-height:0;overflow:hidden;transition:max-height .4s ease;background:rgba(26,58,143,.025);border-left:3px solid rgba(26,58,143,.12)}
.cat-children.open{max-height:2000px}
.sub-item{padding:7px 14px 7px 28px;font-size:12.5px}
.sub-item:hover{background:var(--primary-light)}

/* ── ACTIVE FILTERS ── */
.active-filters{display:flex;flex-wrap:wrap;gap:7px;margin-bottom:12px}
.filter-tag{display:inline-flex;align-items:center;gap:5px;background:var(--primary-light);color:var(--primary);font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;text-decoration:none;border:1px solid #c5d0ef}
.filter-tag i{font-size:10px;opacity:.7}
.filter-tag:hover{background:#d0d9f0}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .cl-wrap{grid-template-columns:1fr;padding:0 14px;margin:14px auto}
  .cl-sidebar{display:none}
  .cl-sidebar.open{display:flex}
  .mobile-filter-toggle{display:flex}
  .ads-grid{grid-template-columns:repeat(2,1fr);gap:12px}
  .ad-thumb{height:150px}
}
@media(max-width:520px){
  .ads-grid{grid-template-columns:1fr 1fr;gap:9px}
  .ad-thumb{height:120px}
  .ad-body{padding:9px}
  .ad-price{font-size:15px}
  .ad-title{font-size:12px}
  .cl-search select{display:none}
  .page-hero h1{font-size:18px}
  .post-btn-hero{display:none}
}
</style>
@endpush

@section('content')
<h1 style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0">Classifieds — Find Anything in Canada</h1>
<div class="cl-wrap">

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
        <a href="{{ route('classifieds.index', request()->except('category','categories','page')) }}"
           class="filter-item {{ !request('category') && !request('categories') ? 'active' : '' }}">
          <i class="fa-solid fa-border-all" style="width:16px;color:var(--muted);font-size:13px"></i> All Categories
        </a>
        @foreach($categories as $cat)
          @php
            $hasChildren   = $cat->children->isNotEmpty();
            $isParentActive = request('category') == $cat->id;
            $childActive   = $hasChildren && $cat->children->contains('id', (int) request('category'));
            $isOpen        = $isParentActive || $childActive;
          @endphp

          {{-- Parent row --}}
          <div class="cat-parent-row {{ $isParentActive ? 'active' : '' }}"
               @if($hasChildren) onclick="toggleCat({{ $cat->id }})" @endif
               @if(!$hasChildren) onclick="window.location='{{ route('classifieds.index', array_merge(request()->except('categories','page'), ['category' => $cat->id])) }}'" @endif>
            <span style="width:18px;text-align:center;flex-shrink:0">{{ $cat->icon }}</span>
            <span style="flex:1">{{ $cat->name }}</span>
            @if($hasChildren)
              <i class="fa-solid fa-chevron-down cat-arrow {{ $isOpen ? 'open' : '' }}" id="arrow-{{ $cat->id }}"></i>
            @endif
          </div>

          {{-- Children (subcategories) --}}
          @if($hasChildren)
          <div class="cat-children {{ $isOpen ? 'open' : '' }}" id="children-{{ $cat->id }}">
            {{-- "All [Parent]" option --}}
            <a href="{{ route('classifieds.index', array_merge(request()->except('categories','page'), ['category' => $cat->id])) }}"
               class="filter-item sub-item {{ $isParentActive && !$childActive ? 'active' : '' }}">
              <i class="fa-solid fa-layer-group" style="width:14px;font-size:11px;color:var(--muted)"></i> All {{ $cat->name }}
            </a>
            @foreach($cat->children as $child)
              <a href="{{ route('classifieds.index', array_merge(request()->except('categories','page'), ['category' => $child->id])) }}"
                 class="filter-item sub-item {{ request('category') == $child->id ? 'active' : '' }}">
                <span style="width:14px;text-align:center;font-size:12px">{{ $child->icon ?: '›' }}</span> {{ $child->name }}
              </a>
            @endforeach
          </div>
          @endif
        @endforeach
      </div>
    </div>

    {{-- Location --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-location-dot"></i> Location</div>
      <form method="GET" action="{{ route('classifieds.index') }}" class="loc-box">
        @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
        @if(request('categories'))<input type="hidden" name="categories" value="{{ request('categories') }}">@endif
        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
        <div class="loc-label">Province</div>
        <select name="province" class="loc-select" id="sb-province" onchange="sbLoadCities(this.value)">
          <option value="">All Provinces</option>
          @foreach($provinces as $prov)
            <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
          @endforeach
        </select>
        <div class="loc-label">City</div>
        <select name="city" class="loc-select" id="sb-city">
          <option value="">All Cities</option>
          @foreach($cities as $city)
            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
          @endforeach
        </select>
        <button type="submit" class="loc-btn"><i class="fa-solid fa-magnifying-glass"></i> Apply Filter</button>
        @if(request('province') || request('city'))
          <a href="javascript:void(0)" onclick="clearLocation()"
             style="display:block;text-align:center;margin-top:8px;font-size:12px;color:var(--muted);text-decoration:none">
            <i class="fa-solid fa-times"></i> Clear Location
          </a>
        @endif
      </form>
    </div>

    {{-- Post Ad CTA --}}
    <div class="sb-box" style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);border-color:transparent;padding:16px;text-align:center">
      <div style="font-size:24px;margin-bottom:8px">📢</div>
      <div style="font-family:var(--fh);font-size:14px;font-weight:700;color:#fff;margin-bottom:4px">Sell Something?</div>
      <div style="font-size:11px;color:rgba(255,255,255,.65);margin-bottom:12px;line-height:1.5">Post your free ad and reach thousands of buyers</div>
      @auth
        <a href="{{ route('post.create') }}" style="display:block;background:var(--accent);color:#fff;padding:9px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none"><i class="fa-solid fa-plus"></i> Post Your Ad</a>
      @else
        <a href="{{ route('register') }}" style="display:block;background:var(--accent);color:#fff;padding:9px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none"><i class="fa-solid fa-plus"></i> Post Your Ad</a>
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
    <form method="GET" action="{{ route('classifieds.index') }}">
      @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
      @if(request('categories'))<input type="hidden" name="categories" value="{{ request('categories') }}">@endif
      @if(request('province'))<input type="hidden" name="province" value="{{ request('province') }}">@endif
      @if(request('city'))<input type="hidden" name="city" value="{{ request('city') }}">@endif
      <div class="cl-search">
        <i class="fa-solid fa-magnifying-glass" style="padding:0 10px 0 14px;color:#bbb;font-size:15px;align-self:center;flex-shrink:0"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search classifieds...">
        <select name="sort">
          <option value="latest" {{ request('sort','latest') === 'latest' ? 'selected' : '' }}>Latest</option>
          <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low–High</option>
          <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High–Low</option>
        </select>
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      </div>
    </form>

    {{-- Active Filters --}}
    @if(request('search') || request('province') || request('city') || request('category'))
    <div class="active-filters">
      @if(request('search'))
        <a href="{{ route('classifieds.index', request()->except('search','page')) }}" class="filter-tag">
          "{{ request('search') }}" <i class="fa-solid fa-times"></i>
        </a>
      @endif
      @if(request('province'))
        <span class="filter-tag" style="cursor:pointer" onclick="clearLocation()">
          <i class="fa-solid fa-map"></i> {{ request('province') }} <i class="fa-solid fa-times"></i>
        </span>
      @endif
      @if(request('city'))
        <span class="filter-tag" style="cursor:pointer" onclick="clearLocation()">
          <i class="fa-solid fa-location-dot"></i> {{ request('city') }} <i class="fa-solid fa-times"></i>
        </span>
      @endif
      <span style="font-size:12px;color:var(--muted);align-self:center;cursor:pointer;margin-left:4px" onclick="clearAllFilters()">Clear all</span>
    </div>
    @endif

    {{-- Results header --}}
    <div class="results-head">
      <div class="results-count">
        <strong>{{ number_format($listings->total()) }}</strong> listings found
        @if(request('category'))
          in <strong>{{ $categories->where('id', request('category'))->first()?->name }}</strong>
        @endif
      </div>
    </div>

    {{-- Grid --}}
    @if($listings->isEmpty())
      <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>No listings found</h3>
        <p>Try adjusting your filters or search terms.</p>
        <a href="{{ route('post.create') }}"><i class="fa-solid fa-plus"></i> Post the First Ad</a>
      </div>
    @else
      <div class="ads-grid">
        @foreach($listings as $listing)
          <a href="{{ route('classifieds.show', $listing) }}" class="ad-card">
            <div class="ad-thumb">
              @if($listing->is_featured)<div class="ad-feat-badge">Featured</div>@endif
              <div class="ad-fav"><i class="fa-regular fa-heart"></i></div>
              @if($listing->image_url)
                <img src="{{ $listing->image_url }}" alt="{{ $listing->title }}">
              @else
                {{ $listing->category->icon ?? '📦' }}
              @endif
            </div>
            <div class="ad-body">
              @if($listing->price)
                <div class="ad-price">{{ $listing->formatted_price }}<small>{{ $listing->price_unit }}</small></div>
              @endif
              <div class="ad-title">
                {{ $listing->title }}
                @if($listing->is_verified)<span class="badge-ver" style="font-size:9px;padding:2px 6px;border-radius:10px;vertical-align:middle;margin-left:4px"><i class="fa-solid fa-circle-check" style="font-size:8px"></i> Verified</span>@endif
              </div>
              <div class="ad-cat">{{ $listing->category->icon ?? '' }} {{ $listing->category->name ?? 'Classifieds' }}</div>
              <div class="ad-foot">
                <span><i class="fa-solid fa-location-dot"></i> {{ Str::limit($listing->location, 20) }}</span>
                <span><i class="fa-regular fa-clock"></i> {{ $listing->created_at->diffForHumans(null, true) }}</span>
              </div>
            </div>
          </a>
        @endforeach
      </div>
      {{-- Inline Ad --}}
      @if(isset($ads) && $ads->where('position','inline')->isNotEmpty())
        <x-ad-slot position="inline" :ads="$ads" style="margin:14px 0" />
      @endif
      <div class="pagination-wrap">
        {{ $listings->withQueryString()->links() }}
      </div>
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

@push('scripts')
<script>
function toggleCat(id) {
  var children = document.getElementById('children-' + id);
  var arrow    = document.getElementById('arrow-' + id);
  if (!children) return;
  var isOpen = children.classList.contains('open');
  children.classList.toggle('open', !isOpen);
  if (arrow) arrow.classList.toggle('open', !isOpen);
}

function sbLoadCities(province) {
  var sel = document.getElementById('sb-city');
  if (!sel) return;
  sel.innerHTML = '<option value="">Loading…</option>';
  fetch('{{ route("locations.cities") }}?province=' + encodeURIComponent(province))
    .then(r => r.json())
    .then(cities => {
      sel.innerHTML = '<option value="">All Cities</option>';
      cities.forEach(c => {
        var o = document.createElement('option');
        o.value = c; o.textContent = c;
        sel.appendChild(o);
      });
    });
}
</script>
@endpush
