@extends('layouts.app')
@section('title', 'Events')

@push('styles')
<style>
.page-header{background:var(--dark);padding:20px;color:#fff}
.page-header h1{font-family:var(--fh);font-size:20px;max-width:1200px;margin:0 auto}
.ev-wrap{max-width:1200px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:220px 1fr;gap:20px}
.sidebar{display:flex;flex-direction:column;gap:14px}
.mobile-filter-toggle{display:none;width:100%;background:var(--surface);border:1.5px solid var(--border2);border-radius:var(--r);padding:10px 16px;font-size:13px;font-weight:600;color:var(--text);text-align:left;margin-bottom:12px;cursor:pointer}
@media(max-width:768px){
  .ev-wrap{grid-template-columns:1fr;padding:0 14px}
  .sidebar{display:none}
  .sidebar.open{display:flex}
  .mobile-filter-toggle{display:block}
  .ev-card{flex-direction:column}
  .ev-img{width:100%;height:140px}
  .ev-date-box{width:100%;flex-direction:row;gap:8px;min-height:auto;padding:8px 16px;justify-content:flex-start}
  .ev-day{font-size:20px}
}
@media(max-width:480px){.page-header h1{font-size:16px}}
.filter-box{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r);overflow:hidden}
.filter-box-head{background:var(--dark);color:#fff;padding:8px 12px;font-family:var(--fh);font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.filter-list{padding:8px 0}
.filter-item{display:flex;align-items:center;padding:7px 14px;font-size:12.5px;cursor:pointer;transition:background .12s;gap:8px;color:var(--text)}
.filter-item:hover{background:var(--red-pale);color:var(--red)}
.filter-item.active{color:var(--red);font-weight:600;background:var(--red-pale)}
.search-bar{background:var(--surface);border:1.5px solid var(--border2);border-radius:var(--r);display:flex;overflow:hidden;margin-bottom:14px}
.search-bar input{flex:1;border:none;padding:10px 14px;font-size:13px;background:none}
.search-bar button{background:var(--red);color:#fff;padding:0 18px;font-size:13px;font-weight:500}

.ev-list{display:flex;flex-direction:column;gap:12px}
.ev-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;display:flex;transition:all .18s;color:var(--text)}
.ev-card:hover{border-color:var(--red2);box-shadow:var(--sh);transform:translateY(-1px)}
.ev-date-box{width:68px;min-height:80px;background:var(--red);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;padding:10px 5px}
.ev-day{font-family:var(--fh);font-size:28px;font-weight:800;line-height:1}
.ev-mon{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;opacity:.85}
.ev-img{width:140px;height:100%;object-fit:cover;flex-shrink:0}
.ev-body{padding:12px 16px;flex:1;min-width:0}
.ev-title{font-family:var(--fh);font-size:14px;font-weight:700;line-height:1.3;margin-bottom:5px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.ev-meta{display:flex;gap:12px;flex-wrap:wrap;font-size:11.5px;color:var(--muted);margin-bottom:6px}
.ev-price{display:inline-block;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}
.price-free{background:var(--green-bg);color:var(--green)}
.price-paid{background:var(--red-pale);color:var(--red)}
.badge-feat{background:var(--gold);color:#fff;font-size:9.5px;font-weight:700;padding:2px 8px;border-radius:20px;margin-right:4px}
.tag{background:var(--bg);border:1px solid var(--border2);color:var(--muted);font-size:9.5px;padding:2px 7px;border-radius:20px;margin-right:3px}
.empty{padding:40px;text-align:center;color:var(--muted);font-size:13px;background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r)}
</style>
@endpush

@section('content')
<div class="page-header">
  <h1>🎆 Upcoming Events — Indian Community in Canada</h1>
</div>

<div class="ev-wrap">
  <button class="mobile-filter-toggle" onclick="this.nextElementSibling.classList.toggle('open');this.textContent=this.nextElementSibling.classList.contains('open')?'▲ Hide Filters':'▼ Filters & Categories'">▼ Filters & Categories</button>
  <aside class="sidebar">
    <div class="filter-box">
      <div class="filter-box-head">Categories</div>
      <div class="filter-list">
        <a href="{{ route('events.index', request()->except('category')) }}"
           class="filter-item {{ !request('category') ? 'active' : '' }}">🎉 All Events</a>
        @foreach($categories as $cat)
          <a href="{{ route('events.index', array_merge(request()->query(), ['category' => $cat->id])) }}"
             class="filter-item {{ request('category') == $cat->id ? 'active' : '' }}">
            {{ $cat->icon }} {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </div>

    <div class="filter-box">
      <div class="filter-box-head">Filter</div>
      <div class="filter-list">
        <a href="{{ route('events.index', request()->except('filter')) }}"
           class="filter-item {{ !request('filter') ? 'active' : '' }}">📅 All Events</a>
        <a href="{{ route('events.index', array_merge(request()->query(), ['filter' => 'upcoming'])) }}"
           class="filter-item {{ request('filter') === 'upcoming' ? 'active' : '' }}">⏰ Upcoming Only</a>
        <a href="{{ route('events.index', array_merge(request()->query(), ['filter' => 'free'])) }}"
           class="filter-item {{ request('filter') === 'free' ? 'active' : '' }}">🆓 Free Events</a>
      </div>
    </div>

    <x-location-filter route="events.index" :provinces="$provinces" :cities="$cities" />
  </aside>

  <div>
    <form method="GET" action="{{ route('events.index') }}">
      @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
      @if(request('filter'))<input type="hidden" name="filter" value="{{ request('filter') }}">@endif
      <div class="search-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events...">
        <button type="submit">Search</button>
      </div>
    </form>

    @if($events->isEmpty())
      <div class="empty">
        <div style="font-size:36px;margin-bottom:10px">📭</div>
        No events found.
      </div>
    @else
      <div class="ev-list">
        @foreach($events as $event)
          <a href="{{ route('events.show', $event) }}" class="ev-card">
            <div class="ev-date-box">
              <div class="ev-day">{{ $event->start_date->format('d') }}</div>
              <div class="ev-mon">{{ $event->start_date->format('M') }}</div>
            </div>
            @if($event->image)
              <img src="{{ asset('storage/'.$event->image) }}" alt="{{ $event->title }}" class="ev-img">
            @endif
            <div class="ev-body">
              <div style="margin-bottom:4px">
                @if($event->is_featured)<span class="badge-feat">★ Featured</span>@endif
                @if($event->category)<span style="font-size:10.5px;color:var(--muted)">{{ $event->category->icon }} {{ $event->category->name }}</span>@endif
              </div>
              <div class="ev-title">{{ $event->title }}</div>
              <div class="ev-meta">
                <span>🕐 {{ $event->start_date->format('h:i A') }}</span>
                @if($event->venue)<span>📍 {{ $event->venue }}</span>@endif
                @if($event->city)<span>{{ $event->city }}</span>@endif
              </div>
              <span class="ev-price {{ $event->price === 'Free' ? 'price-free' : 'price-paid' }}">
                {{ $event->price === 'Free' ? '🆓 Free' : '🎟 '.$event->price }}
              </span>
              @if($event->tags)
                @foreach(array_slice($event->tags, 0, 3) as $tag)
                  <span class="tag">{{ $tag }}</span>
                @endforeach
              @endif
            </div>
          </a>
        @endforeach
      </div>
      <div style="margin-top:20px">{{ $events->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
