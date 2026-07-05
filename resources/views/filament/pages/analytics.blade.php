<x-filament-panels::page>

@php
$data = $this->getViewData();
extract($data);
$typeColors  = ['Listing'=>'#3b82f6','Job'=>'#10b981','Event'=>'#f59e0b','Business'=>'#8b5cf6'];
$typeLabels  = ['Listing'=>'Classifieds','Job'=>'Jobs','Event'=>'Events','Business'=>'Businesses'];
$secColors   = ['classifieds'=>'#3b82f6','jobs'=>'#10b981','events'=>'#f59e0b','directory'=>'#8b5cf6'];
$secLabels   = ['classifieds'=>'Classifieds','jobs'=>'Jobs','events'=>'Events','directory'=>'Directory'];
@endphp

<style>
  .an-wrap { font-family: -apple-system,'Segoe UI',system-ui,sans-serif; color: #1a2e2d; }
  .an-toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
  .an-toolbar-title { font-size:13px; font-weight:600; color:#6b8f8d; letter-spacing:.06em; text-transform:uppercase; }
  .an-period-group { display:flex; gap:6px; }
  .an-period-btn {
    padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;
    border:1.5px solid #c8d8d7; background:transparent; color:#6b8f8d;
    transition:all .15s;
  }
  .an-period-btn.active { background:#00897b; border-color:#00897b; color:#fff; }
  .an-period-btn:hover:not(.active) { border-color:#00897b; color:#00897b; }

  /* KPI strip */
  .an-kpi-strip { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:24px; }
  .an-kpi {
    background:#fff; border:1px solid #e0eded; border-radius:10px; padding:20px 20px 16px;
    border-top:3px solid var(--kpi-color);
  }
  .an-kpi-label { font-size:11px; font-weight:600; letter-spacing:.07em; text-transform:uppercase; color:#6b8f8d; margin-bottom:8px; }
  .an-kpi-value { font-size:36px; font-weight:800; font-variant-numeric:tabular-nums; line-height:1; color:var(--kpi-color); }
  .an-kpi-sub { font-size:11px; color:#9bb5b3; margin-top:6px; }

  /* Main grid */
  .an-grid-main { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,320px); gap:14px; margin-bottom:14px; }
  .an-grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:14px; }
  .an-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px; }

  /* Card */
  .an-card { background:#fff; border:1px solid #e0eded; border-radius:10px; padding:20px; }
  .an-card-title { font-size:12px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:#1a2e2d; margin-bottom:4px; }
  .an-card-sub { font-size:11px; color:#9bb5b3; margin-bottom:16px; }

  /* Bar rows */
  .an-bar-row { display:flex; align-items:center; gap:10px; padding:6px 0; border-bottom:1px solid #f0f4f4; }
  .an-bar-row:last-child { border-bottom:none; }
  .an-bar-label { font-size:12px; color:#1a2e2d; flex:0 0 90px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .an-bar-track { flex:1; background:#f0f4f4; border-radius:3px; height:6px; overflow:hidden; }
  .an-bar-fill { height:6px; border-radius:3px; }
  .an-bar-count { font-size:12px; font-weight:700; font-variant-numeric:tabular-nums; color:#1a2e2d; flex:0 0 36px; text-align:right; }

  /* Top posts */
  .an-post-row { display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #f0f4f4; }
  .an-post-row:last-child { border-bottom:none; }
  .an-post-rank { font-size:13px; font-weight:800; color:#c8d8d7; width:18px; flex-shrink:0; font-variant-numeric:tabular-nums; }
  .an-post-info { flex:1; min-width:0; }
  .an-post-title { font-size:13px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#1a2e2d; text-decoration:none; }
  .an-post-title:hover { color:#00897b; }
  .an-post-type { font-size:10px; color:#9bb5b3; letter-spacing:.04em; text-transform:uppercase; margin-top:1px; }
  .an-post-views { font-size:12px; font-weight:700; color:#00897b; font-variant-numeric:tabular-nums; white-space:nowrap; }

  /* Device boxes */
  .an-device-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
  .an-device-box { background:#f0f4f4; border-radius:8px; padding:14px 10px; text-align:center; }
  .an-device-icon { font-size:18px; margin-bottom:4px; }
  .an-device-pct { font-size:22px; font-weight:800; font-variant-numeric:tabular-nums; line-height:1; }
  .an-device-name { font-size:10px; color:#6b8f8d; text-transform:uppercase; letter-spacing:.05em; margin-top:3px; }
  .an-device-abs { font-size:10px; color:#9bb5b3; }

  /* Keyword rows */
  .an-kw-row { display:flex; justify-content:space-between; align-items:center; padding:6px 0; border-bottom:1px solid #f0f4f4; }
  .an-kw-row:last-child { border-bottom:none; }
  .an-kw-term { font-size:12px; font-weight:500; color:#1a2e2d; max-width:130px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .an-kw-meta { display:flex; align-items:center; gap:6px; }
  .an-badge-zero { font-size:9px; font-weight:700; letter-spacing:.04em; padding:2px 6px; border-radius:4px; background:#fef2f2; color:#dc2626; text-transform:uppercase; }
  .an-kw-count { font-size:12px; font-weight:700; color:#00897b; font-variant-numeric:tabular-nums; }

  /* Zero results */
  .an-zero-row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #f0f4f4; font-size:12px; }
  .an-zero-row:last-child { border-bottom:none; }
  .an-zero-term { color:#dc2626; font-weight:600; }
  .an-zero-count { font-weight:700; color:#6b8f8d; font-variant-numeric:tabular-nums; }

  /* Empty state */
  .an-empty { color:#9bb5b3; font-style:italic; font-size:12px; padding:12px 0; }

  /* Dark mode */
  @media (prefers-color-scheme: dark) {
    .an-wrap { color:#d4e8e6; }
    .an-kpi, .an-card { background:#1e2d2c; border-color:#2a3f3d; }
    .an-kpi-value { color:var(--kpi-color); }
    .an-bar-label, .an-post-title, .an-kw-term, .an-card-title { color:#d4e8e6; }
    .an-bar-track { background:#2a3f3d; }
    .an-post-row, .an-bar-row, .an-kw-row, .an-zero-row { border-color:#2a3f3d; }
    .an-card-title { color:#d4e8e6; }
    .an-device-box { background:#2a3f3d; }
    .an-period-btn { border-color:#2a3f3d; color:#6b8f8d; }
    .an-toolbar-title { color:#6b8f8d; }
  }
  :root[data-theme="dark"] .an-wrap { color:#d4e8e6; }
  :root[data-theme="dark"] .an-kpi,
  :root[data-theme="dark"] .an-card { background:#1e2d2c; border-color:#2a3f3d; }
  :root[data-theme="dark"] .an-bar-label,
  :root[data-theme="dark"] .an-post-title,
  :root[data-theme="dark"] .an-kw-term,
  :root[data-theme="dark"] .an-card-title { color:#d4e8e6; }
  :root[data-theme="dark"] .an-bar-track { background:#2a3f3d; }
  :root[data-theme="dark"] .an-post-row,
  :root[data-theme="dark"] .an-bar-row,
  :root[data-theme="dark"] .an-kw-row,
  :root[data-theme="dark"] .an-zero-row { border-color:#2a3f3d; }
  :root[data-theme="dark"] .an-device-box { background:#2a3f3d; }
  :root[data-theme="dark"] .an-period-btn { border-color:#2a3f3d; color:#6b8f8d; }
  :root[data-theme="light"] .an-wrap { color:#1a2e2d; }
  :root[data-theme="light"] .an-kpi,
  :root[data-theme="light"] .an-card { background:#fff; border-color:#e0eded; }
</style>

<div class="an-wrap">

  {{-- Toolbar --}}
  <div class="an-toolbar">
    <span class="an-toolbar-title">Last {{ $days }} days</span>
    <div class="an-period-group">
      @foreach($this->getPeriodOptions() as $val => $label)
        <button wire:click="$set('period','{{ $val }}')"
          class="an-period-btn {{ $period == $val ? 'active' : '' }}">
          {{ $label }}
        </button>
      @endforeach
    </div>
  </div>

  {{-- KPI Strip --}}
  <div class="an-kpi-strip">
    <div class="an-kpi" style="--kpi-color:#00897b">
      <div class="an-kpi-label">Page Views</div>
      <div class="an-kpi-value">{{ number_format($totalPageViews) }}</div>
      <div class="an-kpi-sub">All sections + posts</div>
    </div>
    <div class="an-kpi" style="--kpi-color:#3b82f6">
      <div class="an-kpi-label">Unique Visitors</div>
      <div class="an-kpi-value">{{ number_format($uniqueVisitors) }}</div>
      <div class="an-kpi-sub">Distinct IPs</div>
    </div>
    <div class="an-kpi" style="--kpi-color:#f59e0b">
      <div class="an-kpi-label">Searches</div>
      <div class="an-kpi-value">{{ number_format($totalSearches) }}</div>
      <div class="an-kpi-sub">With keyword</div>
    </div>
    <div class="an-kpi" style="--kpi-color:#8b5cf6">
      <div class="an-kpi-label">New Users</div>
      <div class="an-kpi-value">{{ number_format($newUsers) }}</div>
      <div class="an-kpi-sub">Registered</div>
    </div>
  </div>

  {{-- Row 1: Daily chart + right stack --}}
  <div class="an-grid-main">

    {{-- Daily Page Views Chart (Canvas) --}}
    <div class="an-card">
      <div class="an-card-title">Daily Page Views</div>
      <div class="an-card-sub">All visitor hits per day</div>
      @if($dailyViews->count())
        <canvas id="dailyChart" style="width:100%;height:130px;display:block"></canvas>
        <script>
        (function(){
          var canvas = document.getElementById('dailyChart');
          var raw = @json($dailyViews);
          var labels = Object.keys(raw);
          var vals   = Object.values(raw);
          var dpr = window.devicePixelRatio || 1;
          var W = canvas.offsetWidth || 600;
          var H = 130;
          canvas.width  = W * dpr;
          canvas.height = H * dpr;
          canvas.style.width  = W + 'px';
          canvas.style.height = H + 'px';
          var ctx = canvas.getContext('2d');
          ctx.scale(dpr, dpr);

          var max = Math.max.apply(null, vals) || 1;
          var pad = { t:10, r:10, b:28, l:32 };
          var cw = W - pad.l - pad.r;
          var ch = H - pad.t - pad.b;
          var n  = vals.length;
          var step = n > 1 ? cw / (n-1) : cw;

          // Points
          var pts = vals.map(function(v,i){ return { x: pad.l + i*step, y: pad.t + ch - (v/max)*ch }; });

          // Grid lines (3 horizontal)
          ctx.strokeStyle = 'rgba(0,137,123,0.08)';
          ctx.lineWidth = 1;
          [0,0.5,1].forEach(function(f){
            var y = pad.t + ch*f;
            ctx.beginPath(); ctx.moveTo(pad.l, y); ctx.lineTo(pad.l+cw, y); ctx.stroke();
          });

          // Y labels
          ctx.fillStyle = '#9bb5b3';
          ctx.font = '10px -apple-system,system-ui,sans-serif';
          ctx.textAlign = 'right';
          ctx.fillText(max, pad.l-4, pad.t+4);
          ctx.fillText(0,   pad.l-4, pad.t+ch+4);

          // Area fill
          var grad = ctx.createLinearGradient(0, pad.t, 0, pad.t+ch);
          grad.addColorStop(0, 'rgba(0,137,123,0.18)');
          grad.addColorStop(1, 'rgba(0,137,123,0)');
          ctx.beginPath();
          ctx.moveTo(pts[0].x, pad.t+ch);
          pts.forEach(function(p){ ctx.lineTo(p.x, p.y); });
          ctx.lineTo(pts[pts.length-1].x, pad.t+ch);
          ctx.closePath();
          ctx.fillStyle = grad;
          ctx.fill();

          // Line
          ctx.beginPath();
          pts.forEach(function(p,i){ i===0 ? ctx.moveTo(p.x,p.y) : ctx.lineTo(p.x,p.y); });
          ctx.strokeStyle = '#00897b';
          ctx.lineWidth = 2;
          ctx.lineJoin = 'round';
          ctx.stroke();

          // Endpoint dot
          var last = pts[pts.length-1];
          ctx.beginPath();
          ctx.arc(last.x, last.y, 4, 0, 2*Math.PI);
          ctx.fillStyle = '#00897b';
          ctx.fill();
          ctx.strokeStyle = '#fff';
          ctx.lineWidth = 2;
          ctx.stroke();

          // X labels (first + last)
          ctx.fillStyle = '#9bb5b3';
          ctx.font = '10px -apple-system,system-ui,sans-serif';
          ctx.textAlign = 'left';
          ctx.fillText(labels[0], pad.l, H-6);
          ctx.textAlign = 'right';
          ctx.fillText(labels[labels.length-1], pad.l+cw, H-6);
        })();
        </script>
      @else
        <div class="an-empty" style="text-align:center;padding:40px 0">No data yet — visits will appear here</div>
      @endif
    </div>

    {{-- Right stack: Section views + Device --}}
    <div style="display:flex;flex-direction:column;gap:14px">

      {{-- Page Views by Section --}}
      <div class="an-card">
        <div class="an-card-title">Section Traffic</div>
        <div class="an-card-sub">Which pages get visited most</div>
        @php $secMax = $viewsBySection->max() ?: 1; @endphp
        @forelse($viewsBySection as $sec => $cnt)
          <div class="an-bar-row">
            <div class="an-bar-label">{{ $secLabels[$sec] ?? ucfirst($sec) }}</div>
            <div class="an-bar-track">
              <div class="an-bar-fill" style="width:{{ round(($cnt/$secMax)*100) }}%;background:{{ $secColors[$sec] ?? '#00897b' }}"></div>
            </div>
            <div class="an-bar-count">{{ $cnt }}</div>
          </div>
        @empty
          <div class="an-empty">No section visits yet</div>
        @endforelse
      </div>

      {{-- Device Breakdown --}}
      <div class="an-card">
        <div class="an-card-title">Devices</div>
        <div class="an-card-sub">How visitors browse</div>
        @php
          $devTotal = $devices->sum() ?: 1;
          $devIcons = ['desktop'=>'🖥', 'mobile'=>'📱', 'tablet'=>'📋'];
          $devColors= ['desktop'=>'#3b82f6','mobile'=>'#00897b','tablet'=>'#f59e0b'];
        @endphp
        <div class="an-device-grid">
          @foreach(['desktop','mobile','tablet'] as $dev)
            @php $cnt=$devices->get($dev,0); $pct=round(($cnt/$devTotal)*100); @endphp
            <div class="an-device-box">
              <div class="an-device-icon">{{ $devIcons[$dev] }}</div>
              <div class="an-device-pct" style="color:{{ $devColors[$dev] }}">{{ $pct }}%</div>
              <div class="an-device-name">{{ ucfirst($dev) }}</div>
              <div class="an-device-abs">{{ $cnt }}</div>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>

  {{-- Row 2: Top Posts + New Users chart --}}
  <div class="an-grid-2">

    {{-- Top Viewed Posts --}}
    <div class="an-card">
      <div class="an-card-title">Top Viewed Posts</div>
      <div class="an-card-sub">Individual listings, jobs, events & businesses</div>
      @forelse($topPosts as $i => $post)
        <div class="an-post-row">
          <div class="an-post-rank">{{ $i+1 }}</div>
          <div class="an-post-info">
            @if($post['url'])
              <a href="{{ $post['url'] }}" target="_blank" class="an-post-title">{{ $post['title'] }}</a>
            @else
              <span class="an-post-title" style="color:#9bb5b3">{{ $post['title'] }}</span>
            @endif
            <div class="an-post-type">{{ $typeLabels[$post['type']] ?? $post['type'] }}</div>
          </div>
          <div class="an-post-views">{{ number_format($post['views']) }} views</div>
        </div>
      @empty
        <div class="an-empty">No post views yet — visit a listing to start tracking</div>
      @endforelse
    </div>

    {{-- New Users Chart --}}
    <div class="an-card">
      <div class="an-card-title">New Registrations</div>
      <div class="an-card-sub">Users who signed up each day</div>
      @if($newUsersDaily->count())
        <canvas id="usersChart" style="width:100%;height:130px;display:block"></canvas>
        <script>
        (function(){
          var canvas = document.getElementById('usersChart');
          var raw = @json($newUsersDaily);
          var labels = Object.keys(raw);
          var vals   = Object.values(raw);
          var dpr = window.devicePixelRatio || 1;
          var W = canvas.offsetWidth || 400;
          var H = 130;
          canvas.width  = W * dpr;
          canvas.height = H * dpr;
          canvas.style.width  = W + 'px';
          canvas.style.height = H + 'px';
          var ctx = canvas.getContext('2d');
          ctx.scale(dpr, dpr);
          var max  = Math.max.apply(null, vals) || 1;
          var pad  = { t:10, r:10, b:28, l:32 };
          var cw   = W - pad.l - pad.r;
          var ch   = H - pad.t - pad.b;
          var n    = vals.length;
          var barW = Math.max(3, cw/n - 3);
          var slotW = cw/n;

          // Grid
          ctx.strokeStyle = 'rgba(139,92,246,0.08)';
          ctx.lineWidth = 1;
          [0,0.5,1].forEach(function(f){
            var y = pad.t + ch*f;
            ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(pad.l+cw,y); ctx.stroke();
          });

          // Y labels
          ctx.fillStyle = '#9bb5b3';
          ctx.font = '10px -apple-system,system-ui,sans-serif';
          ctx.textAlign = 'right';
          ctx.fillText(max, pad.l-4, pad.t+4);
          ctx.fillText(0,   pad.l-4, pad.t+ch+4);

          // Bars
          vals.forEach(function(v,i){
            var bh = Math.max(3, (v/max)*ch);
            var x  = pad.l + i*slotW + (slotW-barW)/2;
            var y  = pad.t + ch - bh;
            var grad = ctx.createLinearGradient(0, y, 0, y+bh);
            grad.addColorStop(0, '#8b5cf6');
            grad.addColorStop(1, 'rgba(139,92,246,0.4)');
            ctx.fillStyle = grad;
            ctx.beginPath();
            ctx.roundRect(x, y, barW, bh, [3,3,0,0]);
            ctx.fill();
          });

          // X labels
          ctx.fillStyle = '#9bb5b3';
          ctx.font = '10px -apple-system,system-ui,sans-serif';
          ctx.textAlign = 'left';
          ctx.fillText(labels[0], pad.l, H-6);
          ctx.textAlign = 'right';
          ctx.fillText(labels[labels.length-1], pad.l+cw, H-6);
        })();
        </script>
      @else
        <div class="an-empty" style="text-align:center;padding:40px 0">No registrations in this period</div>
      @endif
    </div>

  </div>

  {{-- Row 3: Post type breakdown --}}
  @if($viewsByType->count())
  <div class="an-card" style="margin-bottom:14px">
    <div class="an-card-title">Post Type Views</div>
    <div class="an-card-sub">Which content type users open most</div>
    @php $typeMax = $viewsByType->max() ?: 1; @endphp
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:4px">
      @foreach($viewsByType as $type => $cnt)
        @php $pct = round(($cnt/$typeMax)*100); @endphp
        <div>
          <div style="display:flex;justify-content:space-between;font-size:11px;color:#6b8f8d;margin-bottom:6px">
            <span style="font-weight:600;text-transform:uppercase;letter-spacing:.05em">{{ $typeLabels[$type] ?? $type }}</span>
            <span style="font-variant-numeric:tabular-nums;font-weight:700;color:#1a2e2d">{{ number_format($cnt) }}</span>
          </div>
          <div style="background:#f0f4f4;border-radius:3px;height:8px">
            <div style="width:{{ $pct }}%;height:8px;border-radius:3px;background:{{ $typeColors[$type] ?? '#00897b' }}"></div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Row 4: Search intelligence --}}
  <div class="an-grid-3">

    {{-- Top Keywords --}}
    <div class="an-card">
      <div class="an-card-title">Top Keywords</div>
      <div class="an-card-sub">What people search for</div>
      @forelse($topKeywords as $kw)
        <div class="an-kw-row">
          <div class="an-kw-term">{{ $kw->keyword }}</div>
          <div class="an-kw-meta">
            @if($kw->avg_results < 1)
              <span class="an-badge-zero">0 results</span>
            @endif
            <span class="an-kw-count">{{ $kw->total }}×</span>
          </div>
        </div>
      @empty
        <div class="an-empty">No searches yet</div>
      @endforelse
    </div>

    {{-- Zero Results --}}
    <div class="an-card">
      <div class="an-card-title">Missed Searches</div>
      <div class="an-card-sub">Users searched, found nothing — add this content</div>
      @forelse($zeroResults as $kw)
        <div class="an-zero-row">
          <span class="an-zero-term">{{ $kw->keyword }}</span>
          <span class="an-zero-count">{{ $kw->total }}×</span>
        </div>
      @empty
        <div style="display:flex;align-items:center;gap:8px;padding:12px 0">
          <span style="font-size:18px">✓</span>
          <span style="font-size:12px;color:#059669;font-weight:600">All searches returned results</span>
        </div>
      @endforelse
    </div>

    {{-- Top Provinces + Search by Section --}}
    <div style="display:flex;flex-direction:column;gap:14px">
      <div class="an-card">
        <div class="an-card-title">Top Provinces</div>
        <div class="an-card-sub">Where searches come from</div>
        @php $provMax = $topProvinces->max('total') ?: 1; @endphp
        @forelse($topProvinces as $p)
          <div class="an-bar-row">
            <div class="an-bar-label">{{ $p->province }}</div>
            <div class="an-bar-track">
              <div class="an-bar-fill" style="width:{{ round(($p->total/$provMax)*100) }}%;background:#00897b"></div>
            </div>
            <div class="an-bar-count">{{ $p->total }}</div>
          </div>
        @empty
          <div class="an-empty">No province data yet</div>
        @endforelse
      </div>

      <div class="an-card">
        <div class="an-card-title">Searches by Section</div>
        <div class="an-card-sub">Where users search</div>
        @php $secSrchMax = $searchBySection->max() ?: 1; @endphp
        @forelse($searchBySection as $sec => $cnt)
          <div class="an-bar-row">
            <div class="an-bar-label">{{ ucfirst($sec) }}</div>
            <div class="an-bar-track">
              <div class="an-bar-fill" style="width:{{ round(($cnt/$secSrchMax)*100) }}%;background:{{ $secColors[$sec] ?? '#6b7280' }}"></div>
            </div>
            <div class="an-bar-count">{{ $cnt }}</div>
          </div>
        @empty
          <div class="an-empty">No search data yet</div>
        @endforelse
      </div>
    </div>

  </div>

  {{-- Ad Performance moved to /admin/ad-analytics --}}
  <div style="display:none">

    {{-- Ad KPI strip --}}
    <div class="an-kpi-strip" style="grid-template-columns:repeat(3,1fr);margin-bottom:14px">
      <div class="an-kpi" style="--kpi-color:#f97316">
        <div class="an-kpi-label">Ad Impressions</div>
        <div class="an-kpi-value">{{ number_format($adTotalImpressions) }}</div>
        <div class="an-kpi-sub">Total ad views</div>
      </div>
      <div class="an-kpi" style="--kpi-color:#06b6d4">
        <div class="an-kpi-label">Ad Clicks</div>
        <div class="an-kpi-value">{{ number_format($adTotalClicks) }}</div>
        <div class="an-kpi-sub">Times clicked</div>
      </div>
      <div class="an-kpi" style="--kpi-color:#ec4899">
        <div class="an-kpi-label">Overall CTR</div>
        <div class="an-kpi-value">{{ $adOverallCtr }}%</div>
        <div class="an-kpi-sub">Click-through rate</div>
      </div>
    </div>

    <div class="an-grid-2">

      {{-- Daily Impressions + Clicks Chart --}}
      <div class="an-card">
        <div class="an-card-title">Daily Ad Impressions & Clicks</div>
        <div class="an-card-sub">How ads perform day by day</div>
        @if($adDailyStats->count())
          <canvas id="adChart" style="width:100%;height:140px;display:block"></canvas>
          <div style="display:flex;gap:16px;margin-top:10px;font-size:11px;color:#6b8f8d">
            <span style="display:flex;align-items:center;gap:5px"><span style="width:14px;height:3px;background:#f97316;display:inline-block;border-radius:2px"></span>Impressions</span>
            <span style="display:flex;align-items:center;gap:5px"><span style="width:14px;height:3px;background:#06b6d4;display:inline-block;border-radius:2px"></span>Clicks</span>
          </div>
          <script>
          (function(){
            var canvas = document.getElementById('adChart');
            var raw = @json($adDailyStats->map(fn($r) => ['date'=>$r->day,'impressions'=>(int)$r->impressions,'clicks'=>(int)$r->clicks])->values());
            var labels = raw.map(function(r){ return r.date; });
            var imps   = raw.map(function(r){ return r.impressions; });
            var clicks = raw.map(function(r){ return r.clicks; });
            var dpr = window.devicePixelRatio || 1;
            var W = canvas.offsetWidth || 500;
            var H = 140;
            canvas.width  = W * dpr;
            canvas.height = H * dpr;
            canvas.style.width  = W + 'px';
            canvas.style.height = H + 'px';
            var ctx = canvas.getContext('2d');
            ctx.scale(dpr, dpr);

            var maxVal = Math.max.apply(null, imps.concat(clicks)) || 1;
            var pad = { t:10, r:10, b:28, l:38 };
            var cw  = W - pad.l - pad.r;
            var ch  = H - pad.t - pad.b;
            var n   = labels.length;
            var step = n > 1 ? cw / (n-1) : cw;

            // Grid lines
            ctx.strokeStyle = 'rgba(249,115,22,0.07)';
            ctx.lineWidth = 1;
            [0,0.5,1].forEach(function(f){
              var y = pad.t + ch*f;
              ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(pad.l+cw,y); ctx.stroke();
            });

            // Y labels
            ctx.fillStyle = '#9bb5b3';
            ctx.font = '10px -apple-system,system-ui,sans-serif';
            ctx.textAlign = 'right';
            ctx.fillText(maxVal, pad.l-4, pad.t+4);
            ctx.fillText(0,      pad.l-4, pad.t+ch+4);

            function drawLine(vals, color) {
              var pts = vals.map(function(v,i){ return { x: pad.l + i*step, y: pad.t + ch - (v/maxVal)*ch }; });
              // Area
              var grad = ctx.createLinearGradient(0, pad.t, 0, pad.t+ch);
              grad.addColorStop(0, color.replace(')',',0.15)').replace('rgb','rgba'));
              grad.addColorStop(1, color.replace(')',',0)').replace('rgb','rgba'));
              ctx.beginPath();
              ctx.moveTo(pts[0].x, pad.t+ch);
              pts.forEach(function(p){ ctx.lineTo(p.x, p.y); });
              ctx.lineTo(pts[pts.length-1].x, pad.t+ch);
              ctx.closePath();
              ctx.fillStyle = color + '22';
              ctx.fill();
              // Line
              ctx.beginPath();
              pts.forEach(function(p,i){ i===0 ? ctx.moveTo(p.x,p.y) : ctx.lineTo(p.x,p.y); });
              ctx.strokeStyle = color;
              ctx.lineWidth = 2;
              ctx.lineJoin = 'round';
              ctx.stroke();
              // Endpoint dot
              var last = pts[pts.length-1];
              ctx.beginPath(); ctx.arc(last.x, last.y, 3.5, 0, 2*Math.PI);
              ctx.fillStyle = color; ctx.fill();
              ctx.strokeStyle = '#fff'; ctx.lineWidth = 1.5; ctx.stroke();
            }

            drawLine(imps,   '#f97316');
            drawLine(clicks, '#06b6d4');

            // X labels
            ctx.fillStyle = '#9bb5b3';
            ctx.font = '10px -apple-system,system-ui,sans-serif';
            ctx.textAlign = 'left';
            ctx.fillText(labels[0], pad.l, H-6);
            ctx.textAlign = 'right';
            ctx.fillText(labels[labels.length-1], pad.l+cw, H-6);
          })();
          </script>
        @else
          <div class="an-empty" style="text-align:center;padding:50px 0">No ad data yet — ads will track once displayed on pages</div>
        @endif
      </div>

      {{-- Per-Ad Table --}}
      <div class="an-card">
        <div class="an-card-title">Top Ads Performance</div>
        <div class="an-card-sub">Impressions, clicks & CTR per ad</div>
        @if($adPerformance->count())
          <div style="overflow-x:auto">
            <table style="width:100%;font-size:12px;border-collapse:collapse">
              <thead>
                <tr style="color:#9bb5b3;font-size:10px;letter-spacing:.05em;text-transform:uppercase">
                  <th style="text-align:left;padding:4px 8px 8px 0;font-weight:600">Ad</th>
                  <th style="text-align:center;padding:4px 6px 8px;font-weight:600">Pos</th>
                  <th style="text-align:right;padding:4px 0 8px 6px;font-weight:600">Impr.</th>
                  <th style="text-align:right;padding:4px 0 8px 6px;font-weight:600">Clicks</th>
                  <th style="text-align:right;padding:4px 0 8px 6px;font-weight:600">CTR</th>
                </tr>
              </thead>
              <tbody>
                @foreach($adPerformance as $ad)
                  <tr style="border-top:1px solid #f0f4f4">
                    <td style="padding:7px 8px 7px 0;max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-weight:500;color:#1a2e2d">
                      {{ $ad['title'] }}
                    </td>
                    <td style="text-align:center;padding:7px 6px">
                      <span style="font-size:9px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;padding:2px 6px;border-radius:4px;background:#f0f4f4;color:#6b8f8d">
                        {{ match($ad['position']) { 'home-banner'=>'Home', 'sidebar'=>'Side', 'inline'=>'Inline', default=>$ad['position'] } }}
                      </span>
                    </td>
                    <td style="text-align:right;padding:7px 0 7px 6px;font-variant-numeric:tabular-nums;font-weight:600;color:#f97316">
                      {{ number_format($ad['impressions']) }}
                    </td>
                    <td style="text-align:right;padding:7px 0 7px 6px;font-variant-numeric:tabular-nums;font-weight:600;color:#06b6d4">
                      {{ number_format($ad['clicks']) }}
                    </td>
                    <td style="text-align:right;padding:7px 0 7px 6px;font-variant-numeric:tabular-nums;font-weight:700;color:{{ $ad['ctr'] >= 2 ? '#059669' : ($ad['ctr'] >= 0.5 ? '#f59e0b' : '#9bb5b3') }}">
                      {{ $ad['ctr'] > 0 ? $ad['ctr'].'%' : '—' }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="an-empty" style="text-align:center;padding:50px 0">No ads running yet</div>
        @endif
      </div>

    </div>
  </div>

</div>

</x-filament-panels::page>
