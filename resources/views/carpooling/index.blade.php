@extends('layouts.app')
@section('title', 'Carpooling — Find or Offer a Ride | GoBazaar')
@section('canonical', route('carpooling.index'))

@push('styles')
<style>
/* ── LAYOUT ── */
.cp-wrap{max-width:1280px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:260px 1fr;gap:20px;align-items:start}

/* ── SIDEBAR ── */
.cl-sidebar{display:flex;flex-direction:column;gap:12px}
.sb-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.sb-box-head{background:var(--primary);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;display:flex;align-items:center;gap:7px}
.sb-box-head i{font-size:13px;opacity:.85}
.filter-list{padding:6px 0}
.filter-item{display:flex;align-items:center;padding:9px 14px;font-size:13px;transition:background .12s;gap:9px;color:var(--text);text-decoration:none;border-left:3px solid transparent}
.filter-item:hover{background:var(--primary-light);color:var(--primary);border-left-color:var(--primary)}
.filter-item.active{color:var(--primary);font-weight:600;background:var(--primary-light);border-left-color:var(--primary)}

.route-form{padding:14px}
.route-form label{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px;display:block}
.route-form select,.route-form input{width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 10px;font-size:13px;background:#fafafa;margin-bottom:10px;font-family:var(--fb)}
.route-swap{text-align:center;margin:-4px 0 8px}
.route-swap button{background:var(--primary-light);color:var(--primary);border:none;border-radius:50%;width:26px;height:26px;cursor:pointer;font-size:12px}
.route-submit{width:100%;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);padding:10px;font-size:13px;font-weight:700;cursor:pointer}
.route-submit:hover{background:var(--primary-dark)}

/* ── MOBILE TOGGLE ── */
.mobile-filter-toggle{display:none;width:100%;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 16px;font-size:13px;font-weight:600;margin-bottom:12px;cursor:pointer;align-items:center;gap:8px}

/* ── SEARCH ── */
.cp-search{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);display:flex;overflow:hidden;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.cp-search input{flex:1;border:none;padding:11px 14px;font-size:13.5px;background:none;color:#111;font-family:var(--fb)}
.cp-search input:focus{outline:none}
.cp-search button{background:var(--primary);color:#fff;border:none;padding:0 20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;white-space:nowrap;transition:background .2s}
.cp-search button:hover{background:var(--primary-dark)}

/* ── RESULTS HEAD ── */
.results-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px}
.results-count{font-size:13px;color:var(--muted)}
.results-count strong{color:var(--text);font-weight:700}

/* ── ACTIVE FILTERS ── */
.active-filters{display:flex;flex-wrap:wrap;gap:7px;margin-bottom:12px}
.filter-tag{display:inline-flex;align-items:center;gap:5px;background:var(--primary-light);color:var(--primary);font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;text-decoration:none;border:1px solid #c5d0ef}
.filter-tag:hover{background:#d0d9f0}

/* ── CARPOOL CARDS ── */
.cp-list{display:flex;flex-direction:column;gap:12px}
.cp-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;display:flex;transition:all .18s;color:var(--text);text-decoration:none}
.cp-card:hover{border-color:var(--primary);box-shadow:0 4px 16px rgba(26,58,143,.1);transform:translateY(-1px)}

.cp-date-col{width:70px;min-height:110px;background:var(--primary);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;padding:12px 6px}
.cp-day{font-family:var(--fh);font-size:30px;font-weight:800;line-height:1;color:#fff}
.cp-mon{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.8);margin-top:2px}
.cp-time{font-size:10px;color:rgba(255,255,255,.7);margin-top:3px}

.cp-img{width:150px;height:auto;object-fit:contain;flex-shrink:0;border-left:1px solid var(--border)}

.cp-body{padding:14px 16px;flex:1;min-width:0;display:flex;flex-direction:column;justify-content:center;gap:5px}
.cp-type-badge{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;font-weight:700;padding:2px 9px;border-radius:20px;width:fit-content}
.type-offer{background:#dcfce7;color:#15803d}
.type-request{background:#e0f0ff;color:#1d4ed8}
.cp-route{font-family:var(--fh);font-size:15px;font-weight:700;line-height:1.3;color:var(--text);display:flex;align-items:center;gap:8px}
.cp-route i{color:var(--primary);font-size:12px}
.cp-title{font-size:12.5px;color:var(--muted);display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden}
.cp-meta{display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:var(--muted);align-items:center}
.cp-meta i{font-size:11px;color:var(--primary);opacity:.8;margin-right:3px}
.cp-footer{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:2px}
.cp-price-badge{display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:4px 11px;border-radius:20px}
.price-free{background:#dcfce7;color:#15803d}
.price-paid{background:#fef9c3;color:#92400e}
.cp-feat-badge{background:var(--primary);color:#fff;font-size:9.5px;font-weight:700;padding:3px 9px;border-radius:20px}
.cp-seats{background:#f0ede8;color:#555;font-size:10.5px;font-weight:600;padding:3px 9px;border-radius:20px;border:1px solid var(--border)}

.cp-right{padding:14px 16px;display:flex;flex-direction:column;align-items:flex-end;justify-content:center;gap:8px;flex-shrink:0}
.register-btn{background:var(--primary);color:#fff;font-size:12px;font-weight:600;padding:7px 14px;border-radius:20px;white-space:nowrap;text-decoration:none;transition:background .2s}
.register-btn:hover{background:var(--primary-dark)}

.empty-state{padding:60px 20px;text-align:center;background:#fff;border:1px solid var(--border);border-radius:var(--radius)}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px}
.empty-state h3{font-family:var(--fh);font-size:16px;margin-bottom:6px}
.empty-state p{font-size:13px;color:var(--muted);margin-bottom:16px}
.empty-state a{background:var(--primary);color:#fff;padding:9px 20px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .cp-wrap{grid-template-columns:1fr;padding:0 14px;margin:14px auto}
  .cl-sidebar{display:none}
  .cl-sidebar.open{display:flex}
  .mobile-filter-toggle{display:flex}
  .cp-img{width:120px}
  .cp-right{display:none}
}
@media(max-width:520px){
  .cp-card{flex-wrap:nowrap}
  .cp-img{display:none}
  .cp-date-col{width:60px;min-height:90px}
  .cp-day{font-size:24px}
  .cp-body{padding:10px 12px}
  .cp-route{font-size:13.5px}
  .cp-meta{gap:8px;font-size:11px}
}
</style>
@endpush

@section('content')
<h1 style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0">Carpooling — Find or Offer a Ride</h1>
<div class="cp-wrap">

  {{-- Mobile toggle --}}
  <button class="mobile-filter-toggle" onclick="document.querySelector('.cl-sidebar').classList.toggle('open');this.innerHTML=document.querySelector('.cl-sidebar').classList.contains('open')?'<i class=\'fa-solid fa-times\'></i> Hide Filters':'<i class=\'fa-solid fa-sliders\'></i> Search Rides'">
    <i class="fa-solid fa-sliders"></i> Search Rides
  </button>

  {{-- SIDEBAR --}}
  <aside class="cl-sidebar">
    {{-- Route search --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-route"></i> Find a Ride</div>
      <form method="GET" action="{{ route('carpooling.index') }}" class="route-form">
        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
        <label>From</label>
        <select name="from">
          <option value="">Any City</option>
          @foreach($cities as $c)
            <option value="{{ $c }}" {{ request('from') === $c ? 'selected' : '' }}>{{ $c }}</option>
          @endforeach
        </select>
        <label>To</label>
        <select name="to">
          <option value="">Any City</option>
          @foreach($cities as $c)
            <option value="{{ $c }}" {{ request('to') === $c ? 'selected' : '' }}>{{ $c }}</option>
          @endforeach
        </select>
        <label>Date</label>
        <input type="date" name="date" value="{{ request('date') }}">
        <label>Ride Type</label>
        <select name="ride_type">
          <option value="">All</option>
          <option value="offer" {{ request('ride_type') === 'offer' ? 'selected' : '' }}>Offering a Ride</option>
          <option value="request" {{ request('ride_type') === 'request' ? 'selected' : '' }}>Looking for a Ride</option>
        </select>
        <button type="submit" class="route-submit"><i class="fa-solid fa-magnifying-glass"></i> Search Rides</button>
      </form>
    </div>

    {{-- Filter --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-filter"></i> Filter</div>
      <div class="filter-list">
        <a href="{{ route('carpooling.index', request()->except('filter','page')) }}"
           class="filter-item {{ !request('filter') ? 'active' : '' }}">
          <i class="fa-regular fa-calendar" style="width:16px;font-size:12px"></i> All Rides
        </a>
        <a href="{{ route('carpooling.index', array_merge(request()->except('page'), ['filter' => 'upcoming'])) }}"
           class="filter-item {{ request('filter') === 'upcoming' ? 'active' : '' }}">
          <i class="fa-solid fa-clock" style="width:16px;font-size:12px"></i> Upcoming Only
        </a>
      </div>
    </div>

    {{-- Post Ride CTA --}}
    <div class="sb-box" style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);border-color:transparent;padding:16px;text-align:center">
      <div style="font-size:26px;margin-bottom:8px">🚗</div>
      <div style="font-family:var(--fh);font-size:14px;font-weight:700;color:#fff;margin-bottom:4px">Have a Ride to Share?</div>
      <div style="font-size:11px;color:rgba(255,255,255,.65);margin-bottom:12px;line-height:1.5">Post your ride free and connect with the community</div>
      @auth
        <a href="{{ route('post.create', ['type' => 'carpool']) }}" style="display:block;background:var(--accent);color:#fff;padding:9px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none"><i class="fa-solid fa-plus"></i> Post a Ride</a>
      @else
        <a href="{{ route('register') }}" style="display:block;background:var(--accent);color:#fff;padding:9px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none"><i class="fa-solid fa-plus"></i> Post a Ride</a>
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
    <form method="GET" action="{{ route('carpooling.index') }}">
      @if(request('from'))<input type="hidden" name="from" value="{{ request('from') }}">@endif
      @if(request('to'))<input type="hidden" name="to" value="{{ request('to') }}">@endif
      @if(request('date'))<input type="hidden" name="date" value="{{ request('date') }}">@endif
      @if(request('ride_type'))<input type="hidden" name="ride_type" value="{{ request('ride_type') }}">@endif
      @if(request('filter'))<input type="hidden" name="filter" value="{{ request('filter') }}">@endif
      <div class="cp-search">
        <i class="fa-solid fa-magnifying-glass" style="padding:0 10px 0 14px;color:#bbb;font-size:15px;align-self:center;flex-shrink:0"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search rides by title or city...">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      </div>
    </form>

    {{-- Active filters --}}
    @if(request('search') || request('from') || request('to') || request('date') || request('ride_type') || request('filter'))
    <div class="active-filters">
      @if(request('search'))
        <a href="{{ route('carpooling.index', request()->except('search','page')) }}" class="filter-tag">"{{ request('search') }}" <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('from'))
        <a href="{{ route('carpooling.index', request()->except('from','page')) }}" class="filter-tag"><i class="fa-solid fa-location-dot"></i> From {{ request('from') }} <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('to'))
        <a href="{{ route('carpooling.index', request()->except('to','page')) }}" class="filter-tag"><i class="fa-solid fa-flag-checkered"></i> To {{ request('to') }} <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('date'))
        <a href="{{ route('carpooling.index', request()->except('date','page')) }}" class="filter-tag"><i class="fa-regular fa-calendar"></i> {{ request('date') }} <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('ride_type'))
        <a href="{{ route('carpooling.index', request()->except('ride_type','page')) }}" class="filter-tag">{{ request('ride_type') === 'offer' ? 'Offering' : 'Looking for' }} <i class="fa-solid fa-times"></i></a>
      @endif
      <span style="font-size:12px;color:var(--muted);align-self:center;cursor:pointer;margin-left:4px" onclick="window.location.href='{{ route('carpooling.index') }}'">Clear all</span>
    </div>
    @endif

    {{-- Results count --}}
    <div class="results-head">
      <div class="results-count"><strong>{{ number_format($carpools->total()) }}</strong> ride{{ $carpools->total() != 1 ? 's' : '' }} found</div>
    </div>

    @if($carpools->isEmpty())
      <div class="empty-state">
        <div class="empty-icon">🚗</div>
        <h3>No rides found</h3>
        <p>Try adjusting your route or search terms.</p>
        <a href="{{ route('post.create', ['type' => 'carpool']) }}"><i class="fa-solid fa-plus"></i> Post a Ride</a>
      </div>
    @else
      <div class="cp-list">
        @foreach($carpools as $ride)
        @php
          $colors = ['#1a3a8f','#e8a020','#c0392b','#2e7d32','#7c3aed','#0891b2'];
          $color  = $colors[$loop->index % count($colors)];
          $isFree = strtolower($ride->price ?? '') === 'free' || $ride->price === '0' || !$ride->price;
        @endphp
          <a href="{{ route('carpooling.show', $ride) }}" class="cp-card">
            {{-- Date column --}}
            <div class="cp-date-col" style="background:{{ $color }}">
              <div class="cp-day">{{ $ride->travel_date->format('d') }}</div>
              <div class="cp-mon">{{ $ride->travel_date->format('M') }}</div>
              <div class="cp-time">{{ $ride->travel_date->format('h:i A') }}</div>
            </div>

            {{-- Image --}}
            @if($ride->image_url)
              <img src="{{ $ride->image_url }}" alt="{{ $ride->title }}" class="cp-img">
            @endif

            {{-- Body --}}
            <div class="cp-body">
              <span class="cp-type-badge {{ $ride->ride_type === 'offer' ? 'type-offer' : 'type-request' }}">
                {{ $ride->ride_type === 'offer' ? '🚗 Offering' : '🙋 Looking for' }}
              </span>
              <div class="cp-route"><i class="fa-solid fa-location-dot"></i> {{ $ride->from_city }} <i class="fa-solid fa-arrow-right" style="font-size:10px"></i> {{ $ride->to_city }}</div>
              <div class="cp-title">{{ $ride->title }}</div>
              <div class="cp-meta">
                @if($ride->is_recurring)<span><i class="fa-solid fa-repeat"></i>{{ $ride->recurring_days ?: 'Recurring' }}</span>@endif
                <span><i class="fa-solid fa-users"></i>{{ $ride->seats_available }} seat{{ $ride->seats_available != 1 ? 's' : '' }}</span>
              </div>
              <div class="cp-footer">
                @if($isFree)
                  <span class="cp-price-badge price-free"><i class="fa-solid fa-tag"></i> Free</span>
                @else
                  <span class="cp-price-badge price-paid"><i class="fa-solid fa-tag"></i> {{ $ride->formatted_price }}/seat</span>
                @endif
                @if($ride->is_featured)<span class="cp-feat-badge"><i class="fa-solid fa-star" style="font-size:8px"></i> Featured</span>@endif
              </div>
            </div>

            {{-- Right CTA --}}
            <div class="cp-right">
              @if($ride->vehicle)
                <div style="font-size:11px;color:var(--muted);text-align:right">{{ Str::limit($ride->vehicle, 20) }}</div>
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
      <div style="margin-top:20px">{{ $carpools->withQueryString()->links() }}</div>
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
