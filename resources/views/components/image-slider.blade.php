{{--
  Image slider component.
  Props:
    images  – PHP array of storage-relative paths  (e.g. ['listings/abc.jpg', ...])
    alt     – alt text for the main image
    height  – CSS height of the main slide (default '360px')
    id      – unique id prefix (auto-generated if omitted)
--}}
@props([
    'images' => [],
    'alt'    => '',
    'height' => '360px',
    'id'     => 'sl' . Str::random(6),
])

@php
  $total = count($images);
  $resolveImg = fn($p) => str_starts_with($p, 'http') ? $p : \Illuminate\Support\Facades\Storage::disk('s3')->url($p);
@endphp

@if($total === 0)
  {{-- No images — caller renders placeholder --}}
@elseif($total === 1)
  {{-- Single image — no slider chrome needed --}}
  <img src="{{ $resolveImg($images[0]) }}"
       alt="{{ $alt }}"
       style="width:100%;height:{{ $height }};object-fit:cover;display:block">
@else
  {{-- ── SLIDER ── --}}
  <div class="sl-root" id="{{ $id }}" style="--sl-h:{{ $height }}">

    {{-- Slides --}}
    <div class="sl-track" id="{{ $id }}_track">
      @foreach($images as $i => $img)
        <div class="sl-slide">
          <img src="{{ $resolveImg($img) }}" alt="{{ $alt }} {{ $i+1 }}">
        </div>
      @endforeach
    </div>

    {{-- Prev / Next arrows --}}
    <button class="sl-arrow sl-prev" onclick="slMove('{{ $id }}',-1)" aria-label="Previous">&#8249;</button>
    <button class="sl-arrow sl-next" onclick="slMove('{{ $id }}', 1)" aria-label="Next">&#8250;</button>

    {{-- Counter badge --}}
    <div class="sl-counter" id="{{ $id }}_counter">1 / {{ $total }}</div>

    {{-- Dot indicators --}}
    <div class="sl-dots" id="{{ $id }}_dots">
      @for($i = 0; $i < $total; $i++)
        <button class="sl-dot {{ $i===0 ? 'active':'' }}"
                onclick="slGoTo('{{ $id }}',{{ $i }})"
                aria-label="Photo {{ $i+1 }}"></button>
      @endfor
    </div>
  </div>

  {{-- Thumbnail strip --}}
  <div class="sl-thumbs" id="{{ $id }}_thumbs">
    @foreach($images as $i => $img)
      <div class="sl-thumb {{ $i===0 ? 'active':'' }}"
           onclick="slGoTo('{{ $id }}',{{ $i }})">
        <img src="{{ $resolveImg($img) }}" alt="Thumb {{ $i+1 }}">
      </div>
    @endforeach
  </div>
@endif

@once
<style>
/* ── Slider root ──────────────────────────────────────── */
.sl-root{position:relative;overflow:hidden;background:#111;line-height:0}
.sl-track{display:flex;transition:transform .35s cubic-bezier(.4,0,.2,1);will-change:transform}
.sl-slide{flex:0 0 100%;min-width:0}
.sl-slide img{width:100%;height:var(--sl-h,360px);object-fit:cover;display:block}

/* ── Arrows ───────────────────────────────────────────── */
.sl-arrow{position:absolute;top:50%;transform:translateY(-50%);background:rgba(0,0,0,.45);color:#fff;border:none;width:38px;height:38px;border-radius:50%;font-size:22px;line-height:1;cursor:pointer;z-index:10;display:flex;align-items:center;justify-content:center;transition:background .15s;padding:0}
.sl-arrow:hover{background:rgba(0,0,0,.75)}
.sl-prev{left:10px}
.sl-next{right:10px}
@media(max-width:480px){.sl-arrow{width:30px;height:30px;font-size:18px}}

/* ── Counter ──────────────────────────────────────────── */
.sl-counter{position:absolute;top:10px;right:12px;background:rgba(0,0,0,.5);color:#fff;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;z-index:10;pointer-events:none}

/* ── Dots ─────────────────────────────────────────────── */
.sl-dots{position:absolute;bottom:10px;left:50%;transform:translateX(-50%);display:flex;gap:6px;z-index:10}
.sl-dot{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.45);border:none;cursor:pointer;padding:0;transition:background .2s,transform .2s}
.sl-dot.active{background:#fff;transform:scale(1.25)}

/* ── Thumbnail strip ──────────────────────────────────── */
.sl-thumbs{display:flex;gap:6px;padding:8px;background:var(--bg,#f9f9f9);border-top:1px solid var(--border,#eee);overflow-x:auto;scrollbar-width:none}
.sl-thumbs::-webkit-scrollbar{display:none}
.sl-thumb{flex:0 0 72px;height:56px;overflow:hidden;border-radius:6px;cursor:pointer;border:2px solid transparent;transition:border-color .15s;background:#ddd}
.sl-thumb.active{border-color:var(--red,#C0392B)}
.sl-thumb img{width:100%;height:100%;object-fit:cover;display:block}
@media(max-width:480px){.sl-thumb{flex:0 0 58px;height:46px}}
</style>
@endonce

@once
<script>
window._slState = {};   // id → { cur, total }

function slGoTo(id, idx) {
  var s = window._slState[id];
  if (!s) return;
  s.cur = ((idx % s.total) + s.total) % s.total;
  document.getElementById(id + '_track').style.transform = 'translateX(-' + (s.cur * 100) + '%)';
  // counter
  var c = document.getElementById(id + '_counter');
  if (c) c.textContent = (s.cur + 1) + ' / ' + s.total;
  // dots
  document.querySelectorAll('#' + id + '_dots .sl-dot').forEach(function(d, i) {
    d.classList.toggle('active', i === s.cur);
  });
  // thumbs
  var thumbs = document.querySelectorAll('#' + id + '_thumbs .sl-thumb');
  thumbs.forEach(function(t, i){ t.classList.toggle('active', i === s.cur); });
  // scroll active thumb into view
  if (thumbs[s.cur]) {
    thumbs[s.cur].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
  }
}

function slMove(id, dir) {
  var s = window._slState[id];
  if (s) slGoTo(id, s.cur + dir);
}

// Touch/swipe support
function slInitTouch(id) {
  var track = document.getElementById(id + '_track');
  if (!track) return;
  var startX = null;
  track.addEventListener('touchstart', function(e) {
    startX = e.touches[0].clientX;
  }, { passive: true });
  track.addEventListener('touchend', function(e) {
    if (startX === null) return;
    var dx = e.changedTouches[0].clientX - startX;
    startX = null;
    if (Math.abs(dx) > 40) slMove(id, dx < 0 ? 1 : -1);
  });
}

// Keyboard support (when focused)
function slInitKeys(id) {
  var root = document.getElementById(id);
  if (!root) return;
  root.setAttribute('tabindex', '0');
  root.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft')  slMove(id, -1);
    if (e.key === 'ArrowRight') slMove(id,  1);
  });
}
</script>
@endonce

@if($total > 1)
<script>
(function(){
  window._slState['{{ $id }}'] = { cur: 0, total: {{ $total }} };
  slInitTouch('{{ $id }}');
  slInitKeys('{{ $id }}');
})();
</script>
@endif
