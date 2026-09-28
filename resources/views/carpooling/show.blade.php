@extends('layouts.app')
@section('title', $carpool->route . ' — ' . $carpool->travel_date->format('d M Y') . ' | GoBazaar Carpooling')
@section('description', Str::limit(strip_tags($carpool->description ?? ($carpool->title . ' ride from ' . $carpool->from_city . ' to ' . $carpool->to_city . '. Date: ' . $carpool->travel_date->format('d M Y'))), 160))
@section('canonical', route('carpooling.show', $carpool))
@section('og_type', 'website')
@section('og_title', $carpool->route . ' — ' . $carpool->travel_date->format('d M Y'))
@section('og_description', Str::limit(strip_tags($carpool->description ?? ''), 200))
@section('og_image', $carpool->image_url ?? asset('images/og-default.jpg'))

@push('styles')
<style>
/* Legacy var bridge */
body{--red:#1a3a8f;--red2:#e74c3c;--red-dark:#122970;--red-pale:#e8edf7;--border2:#e2e0db;--surface:#fff;--bg:#f9fafb;--hint:#9ca3af;--rl:14px;--r:8px;--amber:#92400e;--amber-bg:#fef9c3;--amber-light:#fef9c3;--dark:#1a3a8f;--dark2:#122970;--gold:#e8a020;--blue:#1d4ed8;--blue-bg:#eff6ff;--green:#16a34a;--green-bg:#dcfce7;}
.show-wrap{max-width:1200px;margin:24px auto;padding:0 20px;display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start}
@media(max-width:768px){.show-wrap{grid-template-columns:1fr;padding:0 14px}.ev-title{font-size:20px}.ev-info-grid{grid-template-columns:1fr}}
@media(max-width:480px){.ev-title{font-size:18px}}
.ev-main{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden}
.ev-banner{width:100%;overflow:hidden}
.ev-banner-placeholder{height:200px;background:var(--red);display:flex;align-items:center;justify-content:center;font-size:60px;color:#fff}
.ev-body{padding:24px}
.ev-cat{font-size:11px;color:var(--red);font-weight:600;text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px}
.ev-title{font-family:var(--fh);font-size:22px;font-weight:800;line-height:1.3;margin-bottom:14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.ev-title i{color:var(--red);font-size:16px}
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
.ev-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:11px;border-radius:9px;font-size:13.5px;font-weight:700;margin-bottom:9px;text-decoration:none;transition:all .18s}
.ev-btn-primary{background:var(--primary);color:#fff}
.ev-btn-primary:hover{background:var(--primary-dark)}
.ev-btn-outline{background:#fff;border:1.5px solid var(--primary);color:var(--primary)}
.ev-btn-outline:hover{background:var(--primary-light)}
.rel-ev{display:flex;gap:10px;padding:8px 0;border-bottom:1px solid var(--border);align-items:center}
.rel-ev:last-child{border-bottom:none}
.rel-date{width:38px;height:38px;background:var(--red);color:#fff;border-radius:8px;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0}
.rel-day{font-size:14px;font-weight:800;line-height:1;font-family:var(--fh)}
.rel-mon{font-size:8px;text-transform:uppercase;opacity:.85}
.ride-type-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px;margin-bottom:12px}
.type-offer{background:#dcfce7;color:#15803d}
.type-request{background:#e0f0ff;color:#1d4ed8}
</style>
@endpush

@section('content')
<div class="container" style="padding-top:8px">
  <div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a><span>›</span>
    <a href="{{ route('carpooling.index') }}">Carpooling</a><span>›</span>
    {{ Str::limit($carpool->title, 50) }}
  </div>
</div>

<div class="show-wrap">
  <div class="ev-main">
    @if($carpool->image)
      <div class="ev-banner">
        <x-image-slider :images="[$carpool->image]" :alt="$carpool->title" height="320px" id="cp-banner-{{ $carpool->id }}" />
      </div>
    @else
      <div class="ev-banner-placeholder">🚗</div>
    @endif

    <div class="ev-body">
      <span class="ride-type-badge {{ $carpool->ride_type === 'offer' ? 'type-offer' : 'type-request' }}">
        {{ $carpool->ride_type === 'offer' ? '🚗 Offering a Ride' : '🙋 Looking for a Ride' }}
      </span>

      <h1 class="ev-title"><i class="fa-solid fa-location-dot"></i> {{ $carpool->from_city }} <i class="fa-solid fa-arrow-right" style="font-size:14px"></i> {{ $carpool->to_city }}</h1>
      <div style="font-size:14px;color:var(--muted);margin-bottom:14px">{{ $carpool->title }}</div>

      <span class="price-badge {{ $carpool->price === 'Free' ? 'price-free' : 'price-paid' }}">
        {{ $carpool->price === 'Free' ? '🆓 Free Ride' : '🎟 '.$carpool->formatted_price.'/seat' }}
      </span>

      <div class="ev-info-grid">
        <div class="ev-info-item">
          <span class="ev-info-icon">📅</span>
          <div>
            <div class="ev-info-label">Travel Date</div>
            <div class="ev-info-val">{{ $carpool->travel_date->format('l, d M Y') }}</div>
          </div>
        </div>
        <div class="ev-info-item">
          <span class="ev-info-icon">🕐</span>
          <div>
            <div class="ev-info-label">Time</div>
            <div class="ev-info-val">{{ $carpool->travel_date->format('h:i A') }}</div>
          </div>
        </div>
        <div class="ev-info-item">
          <span class="ev-info-icon">🧭</span>
          <div>
            <div class="ev-info-label">From</div>
            <div class="ev-info-val">{{ $carpool->from_city }}, {{ $carpool->from_province }}</div>
          </div>
        </div>
        <div class="ev-info-item">
          <span class="ev-info-icon">🏁</span>
          <div>
            <div class="ev-info-label">To</div>
            <div class="ev-info-val">{{ $carpool->to_city }}, {{ $carpool->to_province }}</div>
          </div>
        </div>
        <div class="ev-info-item">
          <span class="ev-info-icon">👥</span>
          <div>
            <div class="ev-info-label">Seats Available</div>
            <div class="ev-info-val">{{ $carpool->seats_available }}</div>
          </div>
        </div>
        @if($carpool->is_recurring)
        <div class="ev-info-item">
          <span class="ev-info-icon">🔁</span>
          <div>
            <div class="ev-info-label">Recurring</div>
            <div class="ev-info-val">{{ $carpool->recurring_days ?: 'Yes' }}</div>
          </div>
        </div>
        @endif
        @if($carpool->vehicle)
        <div class="ev-info-item">
          <span class="ev-info-icon">🚘</span>
          <div>
            <div class="ev-info-label">Vehicle</div>
            <div class="ev-info-val">{{ $carpool->vehicle }}</div>
          </div>
        </div>
        @endif
        <div class="ev-info-item">
          <span class="ev-info-icon">👁</span>
          <div>
            <div class="ev-info-label">Views</div>
            <div class="ev-info-val">{{ $carpool->views }}</div>
          </div>
        </div>
      </div>

      @if($carpool->description)
        <div class="ev-desc">{!! clean($carpool->description) !!}</div>
      @endif

      @if($carpool->tags)
        <div style="margin-top:14px">@foreach($carpool->tags as $tag)<span class="tag">{{ $tag }}</span>@endforeach</div>
      @endif
    </div>
  </div>

  <div>
    <div class="sidebar-card">
      <div class="sidebar-head">Contact {{ $carpool->ride_type === 'offer' ? 'Driver' : 'Rider' }}</div>
      <div class="sidebar-body">
        {{-- Chat button: only show if chat_enabled --}}
        @if($carpool->chat_enabled)
          @auth
            @if(Auth::id() !== $carpool->user_id)
              <button onclick="gcOpen('{{ route('chat.open.carpool', $carpool) }}')" class="ev-btn ev-btn-primary" style="background:var(--green);margin-bottom:8px;display:flex;align-items:center;justify-content:center;gap:8px;width:100%;border:none;cursor:pointer">
                <i class="fa-solid fa-comments"></i> Chat about this Ride
              </button>
            @endif
          @else
            <a href="{{ route('login') }}" class="ev-btn ev-btn-primary" style="background:var(--green);margin-bottom:8px;display:flex;align-items:center;justify-content:center;gap:8px">
              <i class="fa-solid fa-comments"></i> Chat about this Ride
            </a>
          @endauth
        @endif

        {{-- Owner toggle for chat --}}
        @auth
          @if(Auth::id() === $carpool->user_id)
            <form method="POST" action="{{ route('carpooling.toggle-chat', $carpool) }}" style="margin-bottom:8px">
              @csrf @method('PATCH')
              <button type="submit" class="ev-btn {{ $carpool->chat_enabled ? 'ev-btn-outline' : 'ev-btn-primary' }}" style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;font-size:12.5px">
                <i class="fa-solid fa-comments"></i>
                {{ $carpool->chat_enabled ? '💬 Chat ON — Click to Disable' : '💬 Chat OFF — Click to Enable' }}
              </button>
            </form>
          @endif
        @endauth
        @if($carpool->contact_phone && !optional($carpool->user)->hide_phone)
          <a href="tel:{{ $carpool->contact_phone }}" class="ev-btn ev-btn-primary"><i class="fa-solid fa-phone"></i> {{ $carpool->contact_phone }}</a>
        @endif
        @if($carpool->contact_email && !optional($carpool->user)->hide_email)
          <a href="mailto:{{ $carpool->contact_email }}" class="ev-btn ev-btn-outline"><i class="fa-solid fa-envelope"></i> Send Email</a>
        @endif
        @if(!$carpool->contact_phone && !$carpool->contact_email)
          <p style="color:var(--muted);font-size:12px;text-align:center">No contact info available</p>
        @endif
      </div>
    </div>

    @if($related->count())
    <div class="sidebar-card">
      <div class="sidebar-head">Similar Rides</div>
      <div class="sidebar-body" style="padding:8px 14px">
        @foreach($related as $rel)
          <a href="{{ route('carpooling.show', $rel) }}" class="rel-ev" style="display:flex;text-decoration:none;color:var(--text)">
            <div class="rel-date">
              <div class="rel-day">{{ $rel->travel_date->format('d') }}</div>
              <div class="rel-mon">{{ $rel->travel_date->format('M') }}</div>
            </div>
            <div>
              <div style="font-size:12.5px;font-weight:500;line-height:1.3">{{ $rel->from_city }} → {{ $rel->to_city }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ Str::limit($rel->title, 30) }}</div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
    @endif
    <div class="sidebar-card">
      <div class="sidebar-body" style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px">
        <x-favorite-btn type="carpool" :model-id="$carpool->id" :model-class="\App\Models\Carpool::class" />
        @auth
        <button onclick="openReportModal('carpool', {{ $carpool->id }})" style="background:none;border:none;color:var(--muted);font-size:12px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;padding:6px 10px;border-radius:6px;transition:color .15s" onmouseover="this.style.color='#e74c3c'" onmouseout="this.style.color='var(--muted)'">
          <i class="fa-solid fa-flag"></i> Report this ride
        </button>
        @endauth
      </div>
    </div>
  </div>
</div>
@endsection
