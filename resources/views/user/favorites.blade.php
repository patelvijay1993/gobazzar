@extends('layouts.app')
@section('title', 'My Saved Items — GoBazaar')

@push('styles')
<style>
body{--red:#1a3a8f;--red2:#e74c3c;--red-dark:#122970;--red-pale:#e8edf7;--surface:#fff;--bg:#f9fafb;--hint:#9ca3af;--rl:14px;--r:8px;--green:#16a34a;--green-bg:#dcfce7;}
.fav-wrap{max-width:900px;margin:28px auto;padding:0 20px}
.fav-header{margin-bottom:24px}
.fav-header h1{font-family:var(--fh);font-size:22px;font-weight:800;color:var(--text);margin-bottom:4px}
.fav-header p{font-size:13px;color:var(--muted)}

.fav-tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;border-bottom:1.5px solid var(--border);padding-bottom:0}
.fav-tab{padding:8px 16px;font-size:12px;font-weight:600;border-radius:8px 8px 0 0;cursor:pointer;border:1.5px solid transparent;color:var(--muted);background:none;transition:all .15s;margin-bottom:-1.5px}
.fav-tab.active{background:var(--surface);border-color:var(--border);border-bottom-color:var(--surface);color:var(--primary)}

.fav-section{display:none}
.fav-section.active{display:block}

.fav-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);display:flex;gap:14px;align-items:flex-start;padding:14px 16px;margin-bottom:12px;transition:border-color .15s}
.fav-card:hover{border-color:var(--primary)}
.fav-thumb{width:64px;height:64px;border-radius:10px;overflow:hidden;flex-shrink:0;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:28px}
.fav-thumb img{width:100%;height:100%;object-fit:cover}
.fav-body{flex:1;min-width:0}
.fav-title{font-size:14px;font-weight:700;color:var(--text);margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.fav-title a{color:var(--text);text-decoration:none}
.fav-title a:hover{color:var(--primary)}
.fav-meta{font-size:11px;color:var(--muted);display:flex;gap:10px;flex-wrap:wrap;margin-bottom:6px}
.fav-price{font-family:var(--fh);font-size:15px;font-weight:800;color:var(--primary)}
.fav-actions{display:flex;gap:8px;align-items:center;flex-shrink:0}
.btn-view{font-size:11px;font-weight:600;padding:5px 12px;border-radius:6px;background:var(--primary);color:#fff;text-decoration:none;transition:background .15s}
.btn-view:hover{background:var(--primary-dark)}
.btn-unfav{font-size:11px;font-weight:600;padding:5px 12px;border-radius:6px;background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;cursor:pointer;transition:all .15s}
.btn-unfav:hover{background:#fecaca}

.empty-fav{text-align:center;padding:48px 20px;color:var(--muted)}
.empty-fav .icon{font-size:48px;margin-bottom:12px}
</style>
@endpush

@section('content')
<div class="fav-wrap">
  <div style="margin-bottom:16px">
    <a href="{{ route('account') }}" style="color:var(--primary);font-size:13px;font-weight:600;text-decoration:none">← Back to Account</a>
  </div>

  <div class="fav-header">
    <h1>❤️ My Saved Items</h1>
    <p>{{ $favorites->count() }} saved {{ Str::plural('item', $favorites->count()) }}</p>
  </div>

  @if($favorites->isEmpty())
    <div class="empty-fav">
      <div class="icon">🤍</div>
      <p style="font-size:15px;font-weight:600;margin-bottom:8px">Nothing saved yet</p>
      <p style="font-size:13px">Tap the ❤️ heart button on any listing, job, event or business to save it here.</p>
      <a href="{{ route('home') }}" class="btn btn-primary" style="display:inline-block;margin-top:16px">Browse Listings</a>
    </div>
  @else
    {{-- Tabs --}}
    @php
      $listings   = $grouped->get('Listing', collect());
      $jobs       = $grouped->get('Job', collect());
      $events     = $grouped->get('Event', collect());
      $businesses = $grouped->get('Business', collect());
      $firstTab   = $listings->isNotEmpty() ? 'listings' : ($jobs->isNotEmpty() ? 'jobs' : ($events->isNotEmpty() ? 'events' : 'businesses'));
    @endphp
    <div class="fav-tabs">
      @if($listings->isNotEmpty())   <button class="fav-tab {{ $firstTab==='listings'   ? 'active' : '' }}" onclick="showFavTab('listings',this)">🏷️ Classifieds ({{ $listings->count() }})</button>@endif
      @if($jobs->isNotEmpty())       <button class="fav-tab {{ $firstTab==='jobs'       ? 'active' : '' }}" onclick="showFavTab('jobs',this)">💼 Jobs ({{ $jobs->count() }})</button>@endif
      @if($events->isNotEmpty())     <button class="fav-tab {{ $firstTab==='events'     ? 'active' : '' }}" onclick="showFavTab('events',this)">🎉 Events ({{ $events->count() }})</button>@endif
      @if($businesses->isNotEmpty()) <button class="fav-tab {{ $firstTab==='businesses' ? 'active' : '' }}" onclick="showFavTab('businesses',this)">🏢 Businesses ({{ $businesses->count() }})</button>@endif
    </div>

    {{-- Classifieds --}}
    @if($listings->isNotEmpty())
    <div class="fav-section {{ $firstTab==='listings' ? 'active' : '' }}" id="fav-listings">
      @foreach($listings as $fav)
        @php $item = $fav->favoriteable; @endphp
        <div class="fav-card" id="fav-row-{{ $fav->id }}">
          <div class="fav-thumb">
            @if($item->image_url)<img src="{{ $item->image_url }}" alt="">
            @else {{ $item->category->icon ?? '🏷️' }} @endif
          </div>
          <div class="fav-body">
            <div class="fav-title"><a href="{{ route('classifieds.show', $item->slug) }}">{{ $item->title }}</a></div>
            <div class="fav-meta">
              <span>📍 {{ $item->location }}</span>
              <span>{{ $item->category->name ?? '' }}</span>
              <span>Saved {{ $fav->created_at->diffForHumans() }}</span>
            </div>
            @if($item->price)<div class="fav-price">{{ $item->price }}</div>@endif
          </div>
          <div class="fav-actions">
            <a href="{{ route('classifieds.show', $item->slug) }}" class="btn-view">View</a>
            <button class="btn-unfav fav-btn" data-type="listing" data-id="{{ $item->id }}" data-row="{{ $fav->id }}">Remove</button>
          </div>
        </div>
      @endforeach
    </div>
    @endif

    {{-- Jobs --}}
    @if($jobs->isNotEmpty())
    <div class="fav-section {{ $firstTab==='jobs' ? 'active' : '' }}" id="fav-jobs">
      @foreach($jobs as $fav)
        @php $item = $fav->favoriteable; @endphp
        <div class="fav-card" id="fav-row-{{ $fav->id }}">
          <div class="fav-thumb">
            @if($item->company_logo)<img src="{{ $item->logo_url }}" alt="">@else 💼 @endif
          </div>
          <div class="fav-body">
            <div class="fav-title"><a href="{{ route('jobs.show', $item->slug) }}">{{ $item->title }}</a></div>
            <div class="fav-meta">
              <span>🏢 {{ $item->company }}</span>
              <span>📍 {{ $item->city }}</span>
              <span>Saved {{ $fav->created_at->diffForHumans() }}</span>
            </div>
            @if($item->salary)<div class="fav-price">{{ $item->salary }}</div>@endif
          </div>
          <div class="fav-actions">
            <a href="{{ route('jobs.show', $item->slug) }}" class="btn-view">View</a>
            <button class="btn-unfav fav-btn" data-type="job" data-id="{{ $item->id }}" data-row="{{ $fav->id }}">Remove</button>
          </div>
        </div>
      @endforeach
    </div>
    @endif

    {{-- Events --}}
    @if($events->isNotEmpty())
    <div class="fav-section {{ $firstTab==='events' ? 'active' : '' }}" id="fav-events">
      @foreach($events as $fav)
        @php $item = $fav->favoriteable; @endphp
        <div class="fav-card" id="fav-row-{{ $fav->id }}">
          <div class="fav-thumb">
            @if($item->image_url)<img src="{{ $item->image_url }}" alt="">@else {{ $item->category->icon ?? '🎉' }} @endif
          </div>
          <div class="fav-body">
            <div class="fav-title"><a href="{{ route('events.show', $item->slug) }}">{{ $item->title }}</a></div>
            <div class="fav-meta">
              <span>📅 {{ $item->start_date?->format('d M Y') }}</span>
              <span>📍 {{ $item->city }}</span>
              <span>Saved {{ $fav->created_at->diffForHumans() }}</span>
            </div>
            <div class="fav-price">{{ $item->price === 'Free' ? '🆓 Free' : $item->price }}</div>
          </div>
          <div class="fav-actions">
            <a href="{{ route('events.show', $item->slug) }}" class="btn-view">View</a>
            <button class="btn-unfav fav-btn" data-type="event" data-id="{{ $item->id }}" data-row="{{ $fav->id }}">Remove</button>
          </div>
        </div>
      @endforeach
    </div>
    @endif

    {{-- Businesses --}}
    @if($businesses->isNotEmpty())
    <div class="fav-section {{ $firstTab==='businesses' ? 'active' : '' }}" id="fav-businesses">
      @foreach($businesses as $fav)
        @php $item = $fav->favoriteable; @endphp
        <div class="fav-card" id="fav-row-{{ $fav->id }}">
          <div class="fav-thumb">
            @if($item->logo_url)<img src="{{ $item->logo_url }}" alt="">@else {{ $item->category->icon ?? '🏢' }} @endif
          </div>
          <div class="fav-body">
            <div class="fav-title"><a href="{{ route('directory.show', $item->slug) }}">{{ $item->name }}</a></div>
            <div class="fav-meta">
              <span>{{ $item->category->name ?? '' }}</span>
              <span>📍 {{ $item->city }}</span>
              <span>Saved {{ $fav->created_at->diffForHumans() }}</span>
            </div>
          </div>
          <div class="fav-actions">
            <a href="{{ route('directory.show', $item->slug) }}" class="btn-view">View</a>
            <button class="btn-unfav fav-btn" data-type="business" data-id="{{ $item->id }}" data-row="{{ $fav->id }}">Remove</button>
          </div>
        </div>
      @endforeach
    </div>
    @endif
  @endif
</div>

@push('scripts')
<script>
function showFavTab(name, el) {
  document.querySelectorAll('.fav-tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.fav-section').forEach(s => s.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('fav-' + name).classList.add('active');
}

// After unfav on this page, remove the card row instead of toggling heart
document.addEventListener('click', function(e) {
  var btn = e.target.closest('.btn-unfav.fav-btn');
  if (!btn) return;
  var rowId = btn.dataset.row;
  if (rowId) {
    // Listen for the global fav handler to finish, then remove card
    var original = btn.textContent;
    btn.textContent = '...';
    setTimeout(function() {
      var row = document.getElementById('fav-row-' + rowId);
      if (row) row.remove();
    }, 400);
  }
});
</script>
@endpush
@endsection
