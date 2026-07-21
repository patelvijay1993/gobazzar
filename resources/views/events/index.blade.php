@extends('layouts.app')
@section('title', 'Events — Indian-Canadian Community Events | GoBazaar')
@section('canonical', route('events.index'))

@push('styles')
<style>
/* ── LAYOUT ── */
.ev-wrap{max-width:1280px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:240px 1fr;gap:20px;align-items:start}

/* ── SIDEBAR ── */
.cl-sidebar{display:flex;flex-direction:column;gap:12px}
.sb-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.sb-box-head{background:var(--primary);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;display:flex;align-items:center;gap:7px}
.sb-box-head i{font-size:13px;opacity:.85}
.filter-list{padding:6px 0}
.filter-item{display:flex;align-items:center;padding:9px 14px;font-size:13px;transition:background .12s;gap:9px;color:var(--text);text-decoration:none;border-left:3px solid transparent}
.filter-item:hover{background:var(--primary-light);color:var(--primary);border-left-color:var(--primary)}
.filter-item.active{color:var(--primary);font-weight:600;background:var(--primary-light);border-left-color:var(--primary)}
.cat-group{border-bottom:1px solid var(--border)}
.cat-group:last-child{border-bottom:none}
.cat-parent-row{display:flex;align-items:center;justify-content:space-between;padding:9px 14px;font-size:13px;cursor:pointer;gap:9px;color:var(--text);border-left:3px solid transparent;transition:background .12s}
.cat-parent-row:hover{background:var(--primary-light);color:var(--primary);border-left-color:var(--primary)}
.cat-parent-row.active{color:var(--primary);font-weight:600;background:var(--primary-light);border-left-color:var(--primary)}
.cat-toggle-btn{background:none;border:none;padding:0;color:var(--muted);font-size:10px;cursor:pointer;transition:transform .2s;flex-shrink:0}
.cat-toggle-btn.open{transform:rotate(90deg)}
.cat-subs{max-height:0;overflow:hidden;transition:max-height .3s ease;background:var(--bg)}
.cat-subs.open{max-height:2000px}
.cat-sub-item{display:flex;align-items:center;padding:7px 14px 7px 36px;font-size:12px;color:var(--muted);text-decoration:none;border-left:3px solid transparent;transition:background .12s}
.cat-sub-item:hover{background:var(--primary-light);color:var(--primary);border-left-color:var(--primary)}
.cat-sub-item.active{color:var(--primary);font-weight:600;background:var(--primary-light);border-left-color:var(--primary)}

/* ── MOBILE TOGGLE ── */
.mobile-filter-toggle{display:none;width:100%;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 16px;font-size:13px;font-weight:600;margin-bottom:12px;cursor:pointer;align-items:center;gap:8px}

/* ── SEARCH ── */
.ev-search{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);display:flex;overflow:hidden;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.ev-search input{flex:1;border:none;padding:11px 14px;font-size:13.5px;background:none;color:#111;font-family:var(--fb)}
.ev-search input:focus{outline:none}
.ev-search button{background:var(--primary);color:#fff;border:none;padding:0 20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;white-space:nowrap;transition:background .2s}
.ev-search button:hover{background:var(--primary-dark)}

/* ── RESULTS HEAD ── */
.results-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px}
.results-count{font-size:13px;color:var(--muted)}
.results-count strong{color:var(--text);font-weight:700}

/* ── ACTIVE FILTERS ── */
.active-filters{display:flex;flex-wrap:wrap;gap:7px;margin-bottom:12px}
.filter-tag{display:inline-flex;align-items:center;gap:5px;background:var(--primary-light);color:var(--primary);font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;text-decoration:none;border:1px solid #c5d0ef}
.filter-tag:hover{background:#d0d9f0}

/* ── EVENT CARDS ── */
.ev-list{display:flex;flex-direction:column;gap:12px}
.ev-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;display:flex;transition:all .18s;color:var(--text);text-decoration:none}
.ev-card:hover{border-color:var(--primary);box-shadow:0 4px 16px rgba(26,58,143,.1);transform:translateY(-1px)}

.ev-date-col{width:70px;min-height:110px;background:var(--primary);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;padding:12px 6px}
.ev-day{font-family:var(--fh);font-size:30px;font-weight:800;line-height:1;color:#fff}
.ev-mon{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.8);margin-top:2px}
.ev-year{font-size:10px;color:rgba(255,255,255,.55);margin-top:1px}

.ev-img{width:150px;height:auto;object-fit:cover;flex-shrink:0;border-left:1px solid var(--border)}

.ev-body{padding:14px 16px;flex:1;min-width:0;display:flex;flex-direction:column;justify-content:center;gap:5px}
.ev-cat-badge{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;color:var(--muted);font-weight:500}
.ev-title{font-family:var(--fh);font-size:15px;font-weight:700;line-height:1.3;color:var(--text);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.ev-meta{display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:var(--muted);align-items:center}
.ev-meta i{font-size:11px;color:var(--primary);opacity:.8;margin-right:3px}
.ev-footer{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:2px}
.ev-price-badge{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:4px 11px;border-radius:20px}
.price-free{background:#dcfce7;color:#15803d}
.price-paid{background:#fef9c3;color:#92400e}
.ev-feat-badge{background:var(--primary);color:#fff;font-size:9.5px;font-weight:700;padding:3px 9px;border-radius:20px}
.ev-tag{background:#f0ede8;color:#555;font-size:10px;padding:2px 8px;border-radius:20px;border:1px solid var(--border)}

.ev-right{padding:14px 16px;display:flex;flex-direction:column;align-items:flex-end;justify-content:center;gap:8px;flex-shrink:0}
.register-btn{background:var(--primary);color:#fff;font-size:12px;font-weight:600;padding:7px 14px;border-radius:20px;white-space:nowrap;text-decoration:none;transition:background .2s}
.register-btn:hover{background:var(--primary-dark)}

.empty-state{padding:60px 20px;text-align:center;background:#fff;border:1px solid var(--border);border-radius:var(--radius)}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px}
.empty-state h3{font-family:var(--fh);font-size:16px;margin-bottom:6px}
.empty-state p{font-size:13px;color:var(--muted);margin-bottom:16px}
.empty-state a{background:var(--primary);color:#fff;padding:9px 20px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .ev-wrap{grid-template-columns:1fr;padding:0 14px;margin:14px auto}
  .cl-sidebar{display:none}
  .cl-sidebar.open{display:flex}
  .mobile-filter-toggle{display:flex}
  .ev-img{width:120px}
  .ev-right{display:none}
}
@media(max-width:520px){
  .ev-card{flex-wrap:nowrap}
  .ev-img{display:none}
  .ev-date-col{width:60px;min-height:90px}
  .ev-day{font-size:24px}
  .ev-body{padding:10px 12px}
  .ev-title{font-size:13.5px}
  .ev-meta{gap:8px;font-size:11px}
}
</style>
@endpush

@section('content')
<h1 style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0">Events — Indian Community in Canada</h1>
<div class="ev-wrap">

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
        <a href="{{ route('events.index', request()->except('category','page')) }}"
           class="filter-item {{ !request('category') ? 'active' : '' }}">
          <i class="fa-solid fa-calendar-days" style="width:16px;font-size:12px;color:var(--muted)"></i> All Events
        </a>
        @foreach($categories as $cat)
          @php
            $catActive = request('category') == $cat->id || $cat->children->contains('id', (int)request('category'));
          @endphp
          @if($cat->children->isNotEmpty())
            <div class="cat-group">
              <div class="cat-parent-row {{ $catActive ? 'active' : '' }}" onclick="toggleCat({{ $cat->id }})">
                <a href="{{ route('events.index', array_merge(request()->except('page'), ['category' => $cat->id])) }}"
                   style="display:flex;align-items:center;gap:9px;flex:1;color:inherit;text-decoration:none"
                   onclick="event.stopPropagation()">
                  <span style="width:16px;text-align:center">{{ $cat->icon }}</span> {{ $cat->name }}
                </a>
                <button class="cat-toggle-btn {{ $catActive ? 'open' : '' }}" id="cat-btn-{{ $cat->id }}">
                  <i class="fa-solid fa-chevron-right"></i>
                </button>
              </div>
              <div class="cat-subs {{ $catActive ? 'open' : '' }}" id="cat-subs-{{ $cat->id }}">
                @foreach($cat->children as $sub)
                  <a href="{{ route('events.index', array_merge(request()->except('page'), ['category' => $sub->id])) }}"
                     class="cat-sub-item {{ request('category') == $sub->id ? 'active' : '' }}">
                    › {{ $sub->name }}
                  </a>
                @endforeach
              </div>
            </div>
          @else
            <a href="{{ route('events.index', array_merge(request()->except('page'), ['category' => $cat->id])) }}"
               class="filter-item {{ $catActive ? 'active' : '' }}">
              <span style="width:16px;text-align:center">{{ $cat->icon }}</span> {{ $cat->name }}
            </a>
          @endif
        @endforeach
      </div>
    </div>

    {{-- Filter --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-filter"></i> Filter</div>
      <div class="filter-list">
        <a href="{{ route('events.index', request()->except('filter','page')) }}"
           class="filter-item {{ !request('filter') ? 'active' : '' }}">
          <i class="fa-regular fa-calendar" style="width:16px;font-size:12px"></i> All Events
        </a>
        <a href="{{ route('events.index', array_merge(request()->except('page'), ['filter' => 'upcoming'])) }}"
           class="filter-item {{ request('filter') === 'upcoming' ? 'active' : '' }}">
          <i class="fa-solid fa-clock" style="width:16px;font-size:12px"></i> Upcoming Only
        </a>
        <a href="{{ route('events.index', array_merge(request()->except('page'), ['filter' => 'free'])) }}"
           class="filter-item {{ request('filter') === 'free' ? 'active' : '' }}">
          <i class="fa-solid fa-ticket" style="width:16px;font-size:12px"></i> Free Events
        </a>
      </div>
    </div>

    {{-- Location --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-location-dot"></i> Location</div>
      <form method="GET" action="{{ route('events.index') }}" style="padding:14px">
        @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
        @if(request('filter'))<input type="hidden" name="filter" value="{{ request('filter') }}">@endif
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

    {{-- Post Event CTA --}}
    <div class="sb-box" style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);border-color:transparent;padding:16px;text-align:center">
      <div style="font-size:26px;margin-bottom:8px">🎉</div>
      <div style="font-family:var(--fh);font-size:14px;font-weight:700;color:#fff;margin-bottom:4px">Hosting an Event?</div>
      <div style="font-size:11px;color:rgba(255,255,255,.65);margin-bottom:12px;line-height:1.5">List your event free and reach the Indian-Canadian community</div>
      @auth
        <a href="{{ route('post.create', ['type' => 'event']) }}" style="display:block;background:var(--accent);color:#fff;padding:9px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none"><i class="fa-solid fa-plus"></i> Post Free Event</a>
      @else
        <a href="{{ route('register') }}" style="display:block;background:var(--accent);color:#fff;padding:9px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none"><i class="fa-solid fa-plus"></i> Post Free Event</a>
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
    <form method="GET" action="{{ route('events.index') }}">
      @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
      @if(request('filter'))<input type="hidden" name="filter" value="{{ request('filter') }}">@endif
      @if(request('province'))<input type="hidden" name="province" value="{{ request('province') }}">@endif
      <div class="ev-search">
        <i class="fa-solid fa-magnifying-glass" style="padding:0 10px 0 14px;color:#bbb;font-size:15px;align-self:center;flex-shrink:0"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events...">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      </div>
    </form>

    {{-- Active filters --}}
    @if(request('search') || request('province') || request('filter') || request('category'))
    <div class="active-filters">
      @if(request('search'))
        <a href="{{ route('events.index', request()->except('search','page')) }}" class="filter-tag">"{{ request('search') }}" <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('filter'))
        <a href="{{ route('events.index', request()->except('filter','page')) }}" class="filter-tag"><i class="fa-solid fa-filter"></i> {{ request('filter') }} <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('province'))
        <span class="filter-tag" style="cursor:pointer" onclick="clearLocation()"><i class="fa-solid fa-map"></i> {{ request('province') }} <i class="fa-solid fa-times"></i></span>
      @endif
      <span style="font-size:12px;color:var(--muted);align-self:center;cursor:pointer;margin-left:4px" onclick="clearAllFilters()">Clear all</span>
    </div>
    @endif

    {{-- Results count --}}
    <div class="results-head">
      <div class="results-count"><strong>{{ number_format($events->total()) }}</strong> event{{ $events->total() != 1 ? 's' : '' }} found</div>
    </div>

    @if($events->isEmpty())
      <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>No events found</h3>
        <p>Try adjusting your filters or search terms.</p>
        <a href="{{ route('post.create', ['type' => 'event']) }}"><i class="fa-solid fa-plus"></i> Post an Event</a>
      </div>
    @else
      <div class="ev-list">
        @foreach($events as $event)
        @php
          $colors = ['#1a3a8f','#e8a020','#c0392b','#2e7d32','#7c3aed','#0891b2'];
          $color  = $colors[$loop->index % count($colors)];
          $isFree = strtolower($event->price ?? '') === 'free' || $event->price === '0' || !$event->price;
        @endphp
          <a href="{{ route('events.show', $event) }}" class="ev-card">
            {{-- Date column --}}
            <div class="ev-date-col" style="background:{{ $color }}">
              <div class="ev-day">{{ $event->start_date->format('d') }}</div>
              <div class="ev-mon">{{ $event->start_date->format('M') }}</div>
              <div class="ev-year">{{ $event->start_date->format('Y') }}</div>
            </div>

            {{-- Image --}}
            @if($event->image_url)
              <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="ev-img">
            @endif

            {{-- Body --}}
            <div class="ev-body">
              <div class="ev-cat-badge">
                @if($event->is_featured)<span class="ev-feat-badge"><i class="fa-solid fa-star" style="font-size:9px"></i> Featured</span>&nbsp;@endif
                @if($event->category)<span>{{ $event->category->icon }} {{ $event->category->name }}</span>@endif
              </div>
              <div class="ev-title">{{ $event->title }}</div>
              <div class="ev-meta">
                <span><i class="fa-regular fa-clock"></i>{{ $event->start_date->format('h:i A') }}</span>
                @if($event->venue)<span><i class="fa-solid fa-location-dot"></i>{{ $event->venue }}</span>@endif
                @if($event->city)<span><i class="fa-solid fa-city"></i>{{ $event->city }}</span>@endif
              </div>
              <div class="ev-footer">
                @if($isFree)
                  <span class="ev-price-badge price-free"><i class="fa-solid fa-ticket"></i> Free</span>
                @else
                  <span class="ev-price-badge price-paid"><i class="fa-solid fa-ticket"></i> {{ $event->formatted_price }}</span>
                @endif
                @if($event->tags)
                  @foreach(array_slice($event->tags, 0, 2) as $tag)
                    <span class="ev-tag">{{ $tag }}</span>
                  @endforeach
                @endif
              </div>
            </div>

            {{-- Right CTA --}}
            <div class="ev-right">
              @if($event->organizer)
                <div style="font-size:11px;color:var(--muted);text-align:right">by {{ Str::limit($event->organizer, 20) }}</div>
              @endif
              <span class="register-btn">View Details →</span>
            </div>
          </a>
        @endforeach
      </div>
      {{-- Inline Ad --}}
      @if(isset($ads) && $ads->where('position','inline')->isNotEmpty())
        <x-ad-slot position="inline" :ads="$ads" style="margin:14px 0" />
      @endif
      <div style="margin-top:20px">{{ $events->withQueryString()->links() }}</div>
    @endif
  </div>

</div>

{{-- MOBILE SIDEBAR AD --}}
@if(isset($ads) && $ads->where('position','sidebar')->isNotEmpty())
<div class="mob-sidebar-ad">
  <x-ad-slot position="sidebar" :ads="$ads" />
</div>
@endif

@push('scripts')
<script>
function toggleCat(id) {
  var subs = document.getElementById('cat-subs-' + id);
  var btn  = document.getElementById('cat-btn-' + id);
  if (!subs) return;
  var open = subs.classList.contains('open');
  subs.classList.toggle('open', !open);
  if (btn) btn.classList.toggle('open', !open);
}
function clearLocation() {
  var url = new URL(window.location.href);
  url.searchParams.delete('province');
  url.searchParams.delete('page');
  window.location.href = url.toString();
}
function clearAllFilters() { window.location.href = '{{ route("events.index") }}'; }
</script>
@endpush

@endsection
