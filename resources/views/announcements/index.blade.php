@extends('layouts.app')
@section('title', 'Announcements — GoBazaar')

@push('styles')
<style>
/* ── LAYOUT ── */
.ann-wrap{max-width:1280px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:1fr 280px;gap:24px;align-items:start}

/* ── SEARCH ── */
.ann-search{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);display:flex;overflow:hidden;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.ann-search input{flex:1;border:none;padding:11px 14px;font-size:13.5px;background:none;color:#111;font-family:var(--fb)}
.ann-search input:focus{outline:none}
.ann-search button{background:var(--primary);color:#fff;border:none;padding:0 20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;white-space:nowrap;transition:background .2s}
.ann-search button:hover{background:var(--primary-dark)}

/* ── PINNED ── */
.pinned-ann{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:20px;text-decoration:none;color:var(--text);transition:box-shadow .2s;display:block;padding:22px}
.pinned-ann:hover{box-shadow:0 6px 24px rgba(26,58,143,.12)}
.pinned-eyebrow{display:flex;align-items:center;gap:8px;margin-bottom:10px}
.pin-badge{background:var(--accent);color:#fff;font-size:9.5px;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px;display:inline-flex;align-items:center;gap:4px}
.pinned-ann h2{font-family:var(--fh);font-size:20px;font-weight:800;line-height:1.35;color:var(--text);margin-bottom:8px}
.pinned-ann p{font-size:13.5px;color:var(--muted);line-height:1.7;margin-bottom:12px}
.pinned-meta{display:flex;align-items:center;gap:14px;flex-wrap:wrap;font-size:12px;color:var(--muted)}
.pinned-meta i{font-size:11px;color:var(--primary);opacity:.7;margin-right:3px}

/* ── TYPE BADGE ── */
.ann-type{font-size:9.5px;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.4px}
.ann-type-info{background:#e0f0ff;color:#1d4ed8}
.ann-type-success{background:#dcfce7;color:#15803d}
.ann-type-warning{background:#fef9c3;color:#92400e}
.ann-type-urgent{background:#fee2e2;color:#b91c1c}

/* ── LIST ── */
.ann-list{display:flex;flex-direction:column;gap:12px}
.ann-item{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:16px 18px;transition:all .18s;display:block;text-decoration:none;color:var(--text)}
.ann-item:hover{border-color:var(--primary);box-shadow:0 4px 16px rgba(26,58,143,.1)}
.ann-item-head{display:flex;align-items:center;gap:8px;margin-bottom:8px}
.ann-item h3{font-family:var(--fh);font-size:15px;font-weight:700;line-height:1.4;color:var(--text)}
.ann-item p{font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:8px}
.ann-foot{display:flex;align-items:center;gap:14px;font-size:11px;color:var(--muted)}
.ann-foot i{font-size:10px;margin-right:3px;color:var(--primary);opacity:.7}

/* ── SIDEBAR ── */
.sb-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:14px}
.sb-box-head{background:var(--primary);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;display:flex;align-items:center;gap:7px}
.sb-box-head i{font-size:13px;opacity:.85}
.sb-body{padding:14px}

.latest-item{padding:10px 0;border-bottom:1px solid var(--border)}
.latest-item:last-child{border-bottom:none;padding-bottom:0}
.latest-item a{font-size:12.5px;font-weight:600;color:var(--text);line-height:1.5;display:block;text-decoration:none;margin-bottom:3px}
.latest-item a:hover{color:var(--primary)}
.latest-item span{font-size:11px;color:var(--muted)}

.empty-state{padding:60px 20px;text-align:center;background:#fff;border:1px solid var(--border);border-radius:var(--radius)}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px}
.empty-state h3{font-family:var(--fh);font-size:16px;margin-bottom:6px}
.empty-state p{font-size:13px;color:var(--muted)}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .ann-wrap{grid-template-columns:1fr;padding:0 14px;margin:14px auto}
  .ann-wrap aside{display:none}
}
</style>
@endpush

@section('content')
<h1 style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0">Announcements — GoBazaar</h1>
<div class="ann-wrap">

  {{-- MAIN CONTENT --}}
  <div>
    {{-- Search --}}
    <form method="GET" action="{{ route('announcements.index') }}">
      <div class="ann-search">
        <i class="fa-solid fa-magnifying-glass" style="padding:0 10px 0 14px;color:#bbb;font-size:15px;align-self:center;flex-shrink:0"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search announcements...">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      </div>
    </form>

    {{-- Pinned --}}
    @if($pinned && !request('search'))
    <a href="{{ route('announcements.show', $pinned->slug) }}" class="pinned-ann">
      <div class="pinned-eyebrow">
        <span class="pin-badge"><i class="fa-solid fa-thumbtack" style="font-size:8px"></i> Pinned</span>
        <span class="ann-type ann-type-{{ $pinned->type }}">{{ ucfirst($pinned->type) }}</span>
      </div>
      <h2>{{ $pinned->title }}</h2>
      @if($pinned->excerpt)<p>{{ Str::limit($pinned->excerpt, 160) }}</p>@endif
      <div class="pinned-meta">
        <span><i class="fa-regular fa-clock"></i>{{ $pinned->read_time }}</span>
        <span><i class="fa-regular fa-calendar"></i>{{ $pinned->created_at->format('M j, Y') }}</span>
        <span><i class="fa-regular fa-eye"></i>{{ number_format($pinned->views) }} views</span>
      </div>
    </a>
    @endif

    {{-- List --}}
    @if($announcements->isEmpty())
      <div class="empty-state">
        <div class="empty-icon">📣</div>
        <h3>No announcements found</h3>
        <p>Try a different search, or check back later.</p>
      </div>
    @else
      <div class="ann-list">
        @foreach($announcements as $item)
        <a href="{{ route('announcements.show', $item->slug) }}" class="ann-item">
          <div class="ann-item-head">
            <span class="ann-type ann-type-{{ $item->type }}">{{ ucfirst($item->type) }}</span>
            @if($item->is_pinned)<i class="fa-solid fa-thumbtack" style="font-size:10px;color:var(--accent)"></i>@endif
          </div>
          <h3>{{ $item->title }}</h3>
          @if($item->excerpt)<p>{{ Str::limit($item->excerpt, 140) }}</p>@endif
          <div class="ann-foot">
            <span><i class="fa-regular fa-clock"></i>{{ $item->read_time }}</span>
            <span><i class="fa-regular fa-calendar"></i>{{ $item->created_at->format('M j, Y') }}</span>
            <span><i class="fa-regular fa-eye"></i>{{ number_format($item->views) }}</span>
          </div>
        </a>
        @endforeach
      </div>
      <div style="margin-top:20px">{{ $announcements->withQueryString()->links() }}</div>
    @endif
  </div>

  {{-- SIDEBAR --}}
  <aside>
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-clock-rotate-left"></i> Recent Announcements</div>
      <div class="sb-body" style="padding-top:6px;padding-bottom:6px">
        @php
          $recentAnn = \App\Models\Announcement::live()->latest('created_at')->limit(6)->get();
        @endphp
        @forelse($recentAnn as $ra)
        <div class="latest-item">
          <a href="{{ route('announcements.show', $ra->slug) }}">{{ Str::limit($ra->title, 60) }}</a>
          <span>{{ $ra->created_at->format('M j, Y') }}</span>
        </div>
        @empty
          <div class="latest-item" style="border-bottom:none;color:var(--muted);font-size:12.5px">Nothing here yet.</div>
        @endforelse
      </div>
    </div>
  </aside>

</div>
@endsection
