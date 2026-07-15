@props(['position', 'ads', 'class' => ''])

@php
$slotAds = $ads->where('position', $position)->values();
$sizes   = \App\Models\Advertisement::SIZES;
$size    = $sizes[$position] ?? ['width' => 300, 'height' => 250];
$count        = $slotAds->count();
$sliderId     = 'adslider_' . $position . '_' . uniqid();
$durationMs   = ($slotAds->first()->slide_duration ?? 3) * 1000;
@endphp

@if($count === 0)
  {{-- nothing --}}
@elseif($count === 1)
  {{-- single ad, no slider needed --}}
  @php $ad = $slotAds->first(); @endphp
  <a href="{{ route('ads.click', $ad) }}"
     target="_blank" rel="noopener sponsored"
     class="ad-slot ad-slot--{{ $position }} {{ $class }}"
     title="{{ $ad->title }}">
    <span class="ad-label">Ad</span>
    <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}"
         width="{{ $size['width'] }}" height="{{ $size['height'] }}"
         loading="lazy"
         style="width:100%;height:{{ $size['height'] }}px;object-fit:cover;display:block;border-radius:8px">
  </a>
  @once
  <script>
  fetch('/ads/{{ $ad->id }}/impression', { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'} });
  </script>
  @endonce
@else
  {{-- slider --}}
  <div id="{{ $sliderId }}"
       class="ad-slider ad-slot--{{ $position }} {{ $class }}"
       style="position:relative;overflow:hidden;border-radius:8px;width:100%;max-width:100%">

    <span class="ad-label" style="z-index:10">Ad</span>

    @foreach($slotAds as $i => $ad)
    <a href="{{ route('ads.click', $ad) }}"
       target="_blank" rel="noopener sponsored"
       class="ad-slide"
       data-index="{{ $i }}"
       style="position:absolute;inset:0;opacity:{{ $i === 0 ? 1 : 0 }};transition:opacity .6s ease;pointer-events:{{ $i === 0 ? 'auto' : 'none' }}">
      <img src="{{ $ad->image_url }}" alt="{{ $ad->title }}"
           width="{{ $size['width'] }}" height="{{ $size['height'] }}"
           loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
           style="width:100%;height:{{ $size['height'] }}px;object-fit:cover;display:block">
    </a>
    @endforeach
    <script>
    @foreach($slotAds as $ad)
    fetch('/ads/{{ $ad->id }}/impression', { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'} });
    @endforeach
    </script>

    {{-- dots --}}
    @if($count > 1)
    <div style="position:absolute;bottom:6px;left:50%;transform:translateX(-50%);display:flex;gap:5px;z-index:10">
      @foreach($slotAds as $i => $ad)
      <span class="ad-dot"
            data-slider="{{ $sliderId }}" data-index="{{ $i }}"
            style="width:6px;height:6px;border-radius:50%;background:{{ $i===0?'#fff':'rgba(255,255,255,.5)' }};cursor:pointer;transition:background .3s"></span>
      @endforeach
    </div>
    @endif
  </div>

  <script>
  (function(){
    var el    = document.getElementById('{{ $sliderId }}');
    var slides= el.querySelectorAll('.ad-slide');
    var dots  = document.querySelectorAll('[data-slider="{{ $sliderId }}"]');
    var cur   = 0;
    var total = slides.length;

    function goTo(n){
      slides[cur].style.opacity = '0';
      slides[cur].style.pointerEvents = 'none';
      dots[cur] && (dots[cur].style.background = 'rgba(255,255,255,.5)');
      cur = (n + total) % total;
      slides[cur].style.opacity = '1';
      slides[cur].style.pointerEvents = 'auto';
      dots[cur] && (dots[cur].style.background = '#fff');
    }

    dots.forEach(function(d){
      d.addEventListener('click', function(){ goTo(parseInt(d.dataset.index)); });
    });

    setInterval(function(){ goTo(cur + 1); }, {{ $durationMs }});
  })();
  </script>
@endif
