@extends('layouts.app')
@section('title', 'Community Feed — GoBazaar')

@push('styles')
<style>
/* Legacy var bridge */
body{--red:#1a3a8f;--red2:#e74c3c;--red-dark:#122970;--red-pale:#e8edf7;--border2:#e2e0db;--surface:#fff;--bg:#f9fafb;--hint:#9ca3af;--rl:14px;--r:8px;--amber:#92400e;--amber-bg:#fef9c3;--amber-light:#fef9c3;--dark:#1a3a8f;--dark2:#122970;--gold:#e8a020;--blue:#1d4ed8;--blue-bg:#eff6ff;--green:#16a34a;--green-bg:#dcfce7;}
.feed-hero{background:linear-gradient(135deg,var(--dark) 0%,var(--dark2) 100%);color:#fff;padding:40px 20px;text-align:center}
.feed-hero h1{font-family:var(--fh);font-size:26px;font-weight:800;margin-bottom:6px}
.feed-hero p{color:rgba(255,255,255,.6);font-size:13px}

.feed-wrap{max-width:1200px;margin:28px auto;padding:0 20px}

/* Filter bar */
.filter-bar{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:28px;padding-bottom:16px;border-bottom:2px solid var(--border)}
.f-tab{padding:7px 16px;border-radius:20px;font-size:12.5px;font-weight:600;color:var(--muted);border:1.5px solid var(--border2);text-decoration:none;transition:all .15s;white-space:nowrap}
.f-tab:hover{border-color:var(--red);color:var(--red)}
.f-tab.active{background:var(--red);border-color:var(--red);color:#fff}

/* Section heading */
.section-h{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.section-h h2{font-family:var(--fh);font-size:16px;font-weight:800;display:flex;align-items:center;gap:8px}
.section-h a{font-size:12px;color:var(--red);font-weight:600}
.feed-section{margin-bottom:36px}

/* Cards */
.feed-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:16px}
.feed-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);overflow:hidden;transition:box-shadow .2s;position:relative}
.feed-card:hover{box-shadow:var(--sh)}
.feed-card-img{height:140px;overflow:hidden;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:40px}
.feed-card-img img{width:100%;height:100%;object-fit:cover}
.feed-card-body{padding:12px}
.feed-card-type{font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;padding:2px 8px;border-radius:20px;display:inline-block;margin-bottom:6px}
.type-classified{background:#fef3c7;color:#d97706}
.type-job{background:#dbeafe;color:#1d4ed8}
.type-event{background:#fce7f3;color:#be185d}
.type-business{background:#d1fae5;color:#065f46}
.type-matrimonial{background:#ede9fe;color:#7c3aed}
.feed-card-body h3{font-family:var(--fh);font-size:13px;font-weight:700;line-height:1.4;margin-bottom:4px}
.feed-card-body h3 a{color:var(--text)}
.feed-card-body h3 a:hover{color:var(--red)}
.feed-meta{font-size:11px;color:var(--hint);display:flex;gap:8px;flex-wrap:wrap;margin-top:6px}
.feed-price{font-family:var(--fh);font-size:13px;font-weight:700;color:var(--red);margin-top:4px}
.user-chip{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted);margin-top:6px}
.user-chip-av{width:18px;height:18px;border-radius:50%;background:var(--red);color:#fff;display:grid;place-items:center;font-size:9px;font-weight:700;flex-shrink:0}

.empty-section{text-align:center;padding:32px;color:var(--muted);font-size:13px;background:var(--bg);border-radius:var(--rl);border:1px dashed var(--border2)}
</style>
@endpush

@section('content')
<div class="feed-hero">
  <h1>Community Feed</h1>
  <p>Latest posts from the GoBazaar Indian community in Canada</p>
</div>

<div class="feed-wrap">
  {{-- Filter tabs --}}
  <div class="filter-bar">
    @foreach(['all'=>'🌐 All','classifieds'=>'🏷️ Classifieds','jobs'=>'💼 Jobs','events'=>'🎉 Events','businesses'=>'🏢 Directory'] as $key=>$label)
      <a href="{{ route('feed', ['filter'=>$key]) }}" class="f-tab {{ $filter===$key ? 'active' : '' }}">{{ $label }}</a>
    @endforeach
    @auth
      <a href="{{ route('post.create') }}" class="f-tab" style="margin-left:auto;background:var(--red);color:#fff;border-color:var(--red)">+ Post Something</a>
    @endauth
  </div>

  {{-- Classifieds --}}
  @if($filter === 'all' || $filter === 'classifieds')
  <div class="feed-section">
    <div class="section-h">
      <h2>🏷️ Classifieds</h2>
      <a href="{{ route('classifieds.index') }}">View all →</a>
    </div>
    @if($classifieds->isEmpty())
      <div class="empty-section">No classified ads yet.</div>
    @else
    <div class="feed-grid">
      @foreach($classifieds as $item)
      <div class="feed-card">
        <a href="{{ route('classifieds.show', $item->slug) }}">
          <div class="feed-card-img">
            @if($item->image)<img src="{{ $item->image_url }}" alt="{{ $item->title }}">
            @else {{ $item->category->icon ?? '🏷️' }} @endif
          </div>
        </a>
        <div class="feed-card-body">
          <span class="feed-card-type type-classified">Classified</span>
          <h3><a href="{{ route('classifieds.show', $item->slug) }}">{{ Str::limit($item->title, 50) }}</a></h3>
          <div class="feed-meta">
            @if($item->location)<span>📍 {{ $item->location }}</span>@endif
            <span>{{ $item->created_at->diffForHumans() }}</span>
          </div>
          @if($item->price)<div class="feed-price">{{ $item->price }}{{ $item->price_unit }}</div>@endif
          @if($item->user)
          <div class="user-chip">
            <div class="user-chip-av">{{ strtoupper(substr($item->user->name,0,1)) }}</div>
            {{ Str::limit($item->user->name, 18) }}
          </div>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>
  @endif

  {{-- Jobs --}}
  @if($filter === 'all' || $filter === 'jobs')
  <div class="feed-section">
    <div class="section-h">
      <h2>💼 Jobs</h2>
      <a href="{{ route('jobs.index') }}">View all →</a>
    </div>
    @if($jobs->isEmpty())
      <div class="empty-section">No jobs posted yet.</div>
    @else
    <div class="feed-grid">
      @foreach($jobs as $item)
      <div class="feed-card">
        <a href="{{ route('jobs.show', $item->slug) }}">
          <div class="feed-card-img" style="background:#eff6ff">
            @if($item->company_logo)<img src="{{ $item->logo_url }}" alt="{{ $item->company }}">
            @else 💼 @endif
          </div>
        </a>
        <div class="feed-card-body">
          <span class="feed-card-type type-job">Job</span>
          <h3><a href="{{ route('jobs.show', $item->slug) }}">{{ Str::limit($item->title, 50) }}</a></h3>
          <div class="feed-meta">
            <span>🏢 {{ $item->company }}</span>
            @if($item->city)<span>📍 {{ $item->city }}</span>@endif
          </div>
          <div class="feed-meta" style="margin-top:4px">
            @if($item->salary)<span style="color:var(--green);font-weight:600">{{ $item->salary }}</span>@endif
            <span style="background:#dbeafe;color:#1d4ed8;padding:1px 6px;border-radius:10px;font-size:10px">{{ $item->job_type_label }}</span>
          </div>
          @if($item->user)
          <div class="user-chip">
            <div class="user-chip-av">{{ strtoupper(substr($item->user->name,0,1)) }}</div>
            {{ Str::limit($item->user->name, 18) }}
          </div>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>
  @endif

  {{-- Events --}}
  @if($filter === 'all' || $filter === 'events')
  <div class="feed-section">
    <div class="section-h">
      <h2>🎉 Events</h2>
      <a href="{{ route('events.index') }}">View all →</a>
    </div>
    @if($events->isEmpty())
      <div class="empty-section">No events posted yet.</div>
    @else
    <div class="feed-grid">
      @foreach($events as $item)
      <div class="feed-card">
        <a href="{{ route('events.show', $item->slug) }}">
          <div class="feed-card-img" style="background:#fdf2f8">
            @if($item->image)<img src="{{ $item->image_url }}" alt="{{ $item->title }}">
            @else 🎉 @endif
          </div>
        </a>
        <div class="feed-card-body">
          <span class="feed-card-type type-event">Event</span>
          <h3><a href="{{ route('events.show', $item->slug) }}">{{ Str::limit($item->title, 50) }}</a></h3>
          <div class="feed-meta">
            @if($item->city)<span>📍 {{ $item->city }}</span>@endif
            @if($item->start_date)<span>📅 {{ $item->start_date->format('M j') }}</span>@endif
          </div>
          @if($item->price)<div class="feed-price" style="font-size:12px">{{ $item->price === '0' || strtolower($item->price) === 'free' ? '🎟 Free' : '🎟 '.$item->price }}</div>@endif
          @if($item->user)
          <div class="user-chip">
            <div class="user-chip-av">{{ strtoupper(substr($item->user->name,0,1)) }}</div>
            {{ Str::limit($item->user->name, 18) }}
          </div>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>
  @endif

  {{-- Businesses --}}
  @if($filter === 'all' || $filter === 'businesses')
  <div class="feed-section">
    <div class="section-h">
      <h2>🏢 Business Directory</h2>
      <a href="{{ route('directory.index') }}">View all →</a>
    </div>
    @if($businesses->isEmpty())
      <div class="empty-section">No businesses listed yet.</div>
    @else
    <div class="feed-grid">
      @foreach($businesses as $item)
      <div class="feed-card">
        <a href="{{ route('directory.show', $item->slug) }}">
          <div class="feed-card-img" style="background:#ecfdf5">
            @if($item->image)<img src="{{ $item->image_url }}" alt="{{ $item->name }}">
            @elseif($item->logo)<img src="{{ $item->logo_url }}" alt="{{ $item->name }}">
            @else 🏢 @endif
          </div>
        </a>
        <div class="feed-card-body">
          <span class="feed-card-type type-business">Directory</span>
          <h3><a href="{{ route('directory.show', $item->slug) }}">{{ Str::limit($item->name, 40) }}</a></h3>
          <div class="feed-meta">
            @if($item->category)<span>{{ $item->category->icon ?? '' }} {{ $item->category->name }}</span>@endif
            @if($item->city)<span>📍 {{ $item->city }}</span>@endif
          </div>
          @if($item->rating > 0)
          <div style="font-size:11px;color:var(--amber);margin-top:4px">
            ⭐ {{ number_format($item->rating, 1) }} ({{ $item->review_count }} reviews)
          </div>
          @endif
          @if($item->user)
          <div class="user-chip">
            <div class="user-chip-av">{{ strtoupper(substr($item->user->name,0,1)) }}</div>
            {{ Str::limit($item->user->name, 18) }}
          </div>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>
  @endif


</div>
@endsection
