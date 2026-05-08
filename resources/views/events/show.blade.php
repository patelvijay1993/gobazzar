@extends('layouts.app')
@section('title', $event->title)

@push('styles')
<style>
.show-wrap{max-width:1200px;margin:24px auto;padding:0 20px;display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start}
@media(max-width:768px){.show-wrap{grid-template-columns:1fr;padding:0 14px}.ev-title{font-size:20px}.ev-info-grid{grid-template-columns:1fr}}
@media(max-width:480px){.ev-title{font-size:18px}}
.ev-main{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden}
.ev-banner{width:100%;max-height:320px;object-fit:cover;display:block}
.ev-banner-placeholder{height:200px;background:var(--red);display:flex;align-items:center;justify-content:center;font-size:60px;color:#fff}
.ev-body{padding:24px}
.ev-cat{font-size:11px;color:var(--red);font-weight:600;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px}
.ev-title{font-family:var(--fh);font-size:24px;font-weight:800;line-height:1.3;margin-bottom:14px}
.ev-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;background:var(--bg);border-radius:var(--r);padding:14px;margin-bottom:18px}
.ev-info-item{display:flex;align-items:flex-start;gap:8px;font-size:12.5px}
.ev-info-icon{font-size:16px;flex-shrink:0}
.ev-info-label{font-size:10px;color:var(--hint);text-transform:uppercase;letter-spacing:.5px;font-weight:600}
.ev-info-val{font-size:13px;color:var(--text);font-weight:500}
.price-badge{display:inline-block;font-size:14px;font-weight:700;padding:6px 16px;border-radius:20px;margin-bottom:16px}
.price-free{background:var(--green-bg);color:var(--green)}
.price-paid{background:var(--red-pale);color:var(--red)}
.ev-desc{font-size:13.5px;line-height:1.7;border-top:1px solid var(--border);padding-top:16px}
.tag{background:var(--bg);border:1px solid var(--border2);color:var(--muted);font-size:10px;padding:3px 9px;border-radius:20px;margin:2px}
.sidebar-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;margin-bottom:16px}
.sidebar-head{background:var(--dark);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.sidebar-body{padding:16px}
.btn-block{display:block;width:100%;text-align:center;padding:11px;border-radius:var(--r);font-size:13px;font-weight:600;margin-bottom:8px;transition:all .15s}
.rel-ev{display:flex;gap:10px;padding:8px 0;border-bottom:1px solid var(--border);align-items:center}
.rel-ev:last-child{border-bottom:none}
.rel-date{width:38px;height:38px;background:var(--red);color:#fff;border-radius:8px;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0}
.rel-day{font-size:14px;font-weight:800;line-height:1;font-family:var(--fh)}
.rel-mon{font-size:8px;text-transform:uppercase;opacity:.85}
</style>
@endpush

@section('content')
<div class="container" style="padding-top:8px">
  <div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a><span>›</span>
    <a href="{{ route('events.index') }}">Events</a><span>›</span>
    @if($event->category)<a href="{{ route('events.index', ['category' => $event->category_id]) }}">{{ $event->category->name }}</a><span>›</span>@endif
    {{ Str::limit($event->title, 50) }}
  </div>
</div>

<div class="show-wrap">
  <div class="ev-main">
    @if($event->image)
      <img src="{{ asset('storage/'.$event->image) }}" alt="{{ $event->title }}" class="ev-banner">
    @else
      <div class="ev-banner-placeholder">{{ $event->category->icon ?? '🎆' }}</div>
    @endif

    <div class="ev-body">
      @if($event->category)<div class="ev-cat">{{ $event->category->icon }} {{ $event->category->name }}</div>@endif
      <h1 class="ev-title">{{ $event->title }}</h1>

      <span class="price-badge {{ $event->price === 'Free' ? 'price-free' : 'price-paid' }}">
        {{ $event->price === 'Free' ? '🆓 Free Entry' : '🎟 '.$event->price }}
      </span>

      <div class="ev-info-grid">
        <div class="ev-info-item">
          <span class="ev-info-icon">📅</span>
          <div>
            <div class="ev-info-label">Date</div>
            <div class="ev-info-val">{{ $event->start_date->format('l, d M Y') }}</div>
          </div>
        </div>
        <div class="ev-info-item">
          <span class="ev-info-icon">🕐</span>
          <div>
            <div class="ev-info-label">Time</div>
            <div class="ev-info-val">
              {{ $event->start_date->format('h:i A') }}
              @if($event->end_date) – {{ $event->end_date->format('h:i A') }}@endif
            </div>
          </div>
        </div>
        @if($event->venue)
        <div class="ev-info-item">
          <span class="ev-info-icon">🏛</span>
          <div>
            <div class="ev-info-label">Venue</div>
            <div class="ev-info-val">{{ $event->venue }}</div>
          </div>
        </div>
        @endif
        @if($event->city)
        <div class="ev-info-item">
          <span class="ev-info-icon">📍</span>
          <div>
            <div class="ev-info-label">City</div>
            <div class="ev-info-val">{{ $event->city }}{{ $event->province ? ', '.$event->province : '' }}</div>
          </div>
        </div>
        @endif
        @if($event->organizer)
        <div class="ev-info-item">
          <span class="ev-info-icon">👤</span>
          <div>
            <div class="ev-info-label">Organizer</div>
            <div class="ev-info-val">{{ $event->organizer }}</div>
          </div>
        </div>
        @endif
        <div class="ev-info-item">
          <span class="ev-info-icon">👁</span>
          <div>
            <div class="ev-info-label">Views</div>
            <div class="ev-info-val">{{ $event->views }}</div>
          </div>
        </div>
      </div>

      @if($event->description)
        <div class="ev-desc">{!! $event->description !!}</div>
      @endif

      @if($event->tags)
        <div style="margin-top:14px">@foreach($event->tags as $tag)<span class="tag">{{ $tag }}</span>@endforeach</div>
      @endif
    </div>
  </div>

  <div>
    <div class="sidebar-card">
      <div class="sidebar-head">Contact Organizer</div>
      <div class="sidebar-body">
        @if($event->organizer_phone)
          <a href="tel:{{ $event->organizer_phone }}" class="btn btn-red btn-block">📞 {{ $event->organizer_phone }}</a>
        @endif
        @if($event->organizer_email)
          <a href="mailto:{{ $event->organizer_email }}" class="btn btn-ghost btn-block">✉ Send Email</a>
        @endif
        @if($event->website)
          <a href="{{ $event->website }}" target="_blank" class="btn btn-ghost btn-block">🌐 Event Website</a>
        @endif
        @if(!$event->organizer_phone && !$event->organizer_email && !$event->website)
          <p style="color:var(--muted);font-size:12px;text-align:center">No contact info available</p>
        @endif
      </div>
    </div>

    @if($related->count())
    <div class="sidebar-card">
      <div class="sidebar-head">More Events</div>
      <div class="sidebar-body" style="padding:8px 14px">
        @foreach($related as $rel)
          <a href="{{ route('events.show', $rel) }}" class="rel-ev" style="display:flex;text-decoration:none;color:var(--text)">
            <div class="rel-date">
              <div class="rel-day">{{ $rel->start_date->format('d') }}</div>
              <div class="rel-mon">{{ $rel->start_date->format('M') }}</div>
            </div>
            <div>
              <div style="font-size:12.5px;font-weight:500;line-height:1.3">{{ Str::limit($rel->title, 40) }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ $rel->city }}</div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>
@endsection
