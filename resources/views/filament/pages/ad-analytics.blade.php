<x-filament-panels::page>

@php
$data = $this->getViewData();
extract($data);
$posLabels = ['home-banner' => 'Home Banner', 'sidebar' => 'Sidebar', 'inline' => 'Inline'];
$posColors = ['home-banner' => '#10b981', 'sidebar' => '#3b82f6', 'inline' => '#f59e0b'];
@endphp

<style>
  .aa-wrap { font-family: -apple-system,'Segoe UI',system-ui,sans-serif; color:#1a2e2d; }

  /* Toolbar */
  .aa-toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; }
  .aa-toolbar-title { font-size:13px; font-weight:600; color:#6b8f8d; letter-spacing:.06em; text-transform:uppercase; }
  .aa-period-group { display:flex; gap:6px; }
  .aa-period-btn {
    padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;
    border:1.5px solid #c8d8d7; background:transparent; color:#6b8f8d; transition:all .15s;
  }
  .aa-period-btn.active { background:#f97316; border-color:#f97316; color:#fff; }
  .aa-period-btn:hover:not(.active) { border-color:#f97316; color:#f97316; }

  /* KPI strip */
  .aa-kpi-strip { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
  .aa-kpi { background:#fff; border:1px solid #e8f0ef; border-radius:10px; padding:18px 20px 14px; border-top:3px solid var(--kc); }
  .aa-kpi-label { font-size:11px; font-weight:600; letter-spacing:.07em; text-transform:uppercase; color:#6b8f8d; margin-bottom:6px; }
  .aa-kpi-value { font-size:32px; font-weight:800; font-variant-numeric:tabular-nums; line-height:1; color:var(--kc); }
  .aa-kpi-sub { font-size:11px; color:#9bb5b3; margin-top:5px; }

  /* Overview chart card */
  .aa-card { background:#fff; border:1px solid #e8f0ef; border-radius:10px; padding:20px; }
  .aa-card-title { font-size:12px; font-weight:700; letter-spacing:.05em; text-transform:uppercase; color:#1a2e2d; margin-bottom:3px; }
  .aa-card-sub { font-size:11px; color:#9bb5b3; margin-bottom:14px; }

  /* Position badges */
  .aa-pos-badge {
    display:inline-block; font-size:9px; font-weight:700; letter-spacing:.05em;
    text-transform:uppercase; padding:2px 7px; border-radius:4px;
  }

  /* Ad cards grid */
  .aa-ads-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:14px; margin-top:16px; }

  /* Individual ad card */
  .aa-ad-card {
    background:#fff; border:1px solid #e8f0ef; border-radius:12px;
    border-left:4px solid var(--ac); overflow:hidden;
  }
  .aa-ad-header { padding:14px 16px 10px; display:flex; justify-content:space-between; align-items:flex-start; }
  .aa-ad-title { font-size:13px; font-weight:700; color:#1a2e2d; margin-bottom:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px; }
  .aa-ad-meta { font-size:10px; color:#9bb5b3; }
  .aa-ad-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:0; border-top:1px solid #f0f5f4; border-bottom:1px solid #f0f5f4; }
  .aa-ad-stat { padding:10px 14px; text-align:center; }
  .aa-ad-stat + .aa-ad-stat { border-left:1px solid #f0f5f4; }
  .aa-ad-stat-label { font-size:9px; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:#9bb5b3; margin-bottom:4px; }
  .aa-ad-stat-value { font-size:20px; font-weight:800; font-variant-numeric:tabular-nums; line-height:1; }
  .aa-ad-chart { padding:12px 14px 10px; }

  /* Legend */
  .aa-legend { display:flex; gap:14px; font-size:10px; color:#6b8f8d; margin-top:8px; }
  .aa-legend span { display:flex; align-items:center; gap:4px; }
  .aa-legend-dot { width:10px; height:3px; border-radius:2px; display:inline-block; }

  /* Empty state */
  .aa-empty { color:#9bb5b3; font-style:italic; font-size:13px; padding:60px 0; text-align:center; }

  /* Dark mode */
  @media (prefers-color-scheme: dark) {
    .aa-wrap { color:#d4e8e6; }
    .aa-kpi, .aa-card, .aa-ad-card { background:#1e2d2c; border-color:#2a3f3d; }
    .aa-ad-stats { border-color:#2a3f3d; }
    .aa-ad-stat + .aa-ad-stat { border-color:#2a3f3d; }
    .aa-ad-title, .aa-card-title { color:#d4e8e6; }
    .aa-period-btn { border-color:#2a3f3d; color:#6b8f8d; }
  }
  :root[data-theme="dark"] .aa-wrap { color:#d4e8e6; }
  :root[data-theme="dark"] .aa-kpi,
  :root[data-theme="dark"] .aa-card,
  :root[data-theme="dark"] .aa-ad-card { background:#1e2d2c; border-color:#2a3f3d; }
  :root[data-theme="dark"] .aa-ad-stats,
  :root[data-theme="dark"] .aa-ad-stat + .aa-ad-stat { border-color:#2a3f3d; }
  :root[data-theme="dark"] .aa-ad-title,
  :root[data-theme="dark"] .aa-card-title { color:#d4e8e6; }
  :root[data-theme="dark"] .aa-period-btn { border-color:#2a3f3d; color:#6b8f8d; }
  :root[data-theme="light"] .aa-kpi,
  :root[data-theme="light"] .aa-card,
  :root[data-theme="light"] .aa-ad-card { background:#fff; border-color:#e8f0ef; }
</style>

<div class="aa-wrap">

  {{-- Toolbar --}}
  <div class="aa-toolbar">
    <div style="display:flex;align-items:center;gap:12px">
      @if($mode === 'single' && $singleAd)
        <a href="{{ \App\Filament\Pages\AdAnalytics::getUrl() }}"
           style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:#6b8f8d;text-decoration:none;padding:5px 10px;border:1.5px solid #c8d8d7;border-radius:6px;transition:all .15s"
           onmouseover="this.style.borderColor='#f97316';this.style.color='#f97316'"
           onmouseout="this.style.borderColor='#c8d8d7';this.style.color='#6b8f8d'">
          ← All Ads
        </a>
        <span class="aa-toolbar-title" style="max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
              title="{{ $singleAd['title'] }}">
          {{ $singleAd['title'] }}
        </span>
      @else
        <span class="aa-toolbar-title">Last {{ $days }} days</span>
      @endif
    </div>
    <div class="aa-period-group">
      @foreach($this->getPeriodOptions() as $val => $label)
        <button wire:click="$set('period','{{ $val }}')"
          class="aa-period-btn {{ $period == $val ? 'active' : '' }}">{{ $label }}</button>
      @endforeach
    </div>
  </div>

  {{-- KPI Strip --}}
  <div class="aa-kpi-strip">
    <div class="aa-kpi" style="--kc:#f97316">
      <div class="aa-kpi-label">Impressions</div>
      <div class="aa-kpi-value">{{ number_format($totalImpressions) }}</div>
      <div class="aa-kpi-sub">{{ $mode === 'single' ? 'This ad' : 'All ads combined' }}</div>
    </div>
    <div class="aa-kpi" style="--kc:#06b6d4">
      <div class="aa-kpi-label">Clicks</div>
      <div class="aa-kpi-value">{{ number_format($totalClicks) }}</div>
      <div class="aa-kpi-sub">{{ $mode === 'single' ? 'This ad' : 'All ads combined' }}</div>
    </div>
    <div class="aa-kpi" style="--kc:#ec4899">
      <div class="aa-kpi-label">CTR</div>
      <div class="aa-kpi-value">{{ $overallCtr }}%</div>
      <div class="aa-kpi-sub">Click-through rate</div>
    </div>
    @if($mode === 'single' && $singleAd)
    <div class="aa-kpi" style="--kc:#8b5cf6">
      <div class="aa-kpi-label">Best Day</div>
      <div class="aa-kpi-value" style="font-size:20px;padding-top:4px">
        {{ $bestDay ? $bestDay->day : '—' }}
      </div>
      <div class="aa-kpi-sub">{{ $bestDay ? number_format($bestDay->impressions).' impressions' : 'No data yet' }}</div>
    </div>
    @else
    <div class="aa-kpi" style="--kc:#8b5cf6">
      <div class="aa-kpi-label">Active Ads</div>
      <div class="aa-kpi-value">{{ $activeAds }}</div>
      <div class="aa-kpi-sub">Currently running</div>
    </div>
    @endif
  </div>

  {{-- Overview Chart + Position Breakdown --}}
  <div style="display:{{ $mode === 'single' ? 'none' : 'grid' }};grid-template-columns:minmax(0,1fr) 240px;gap:14px;margin-bottom:20px">

    {{-- Daily Overview Chart --}}
    <div class="aa-card">
      <div class="aa-card-title">Daily Impressions & Clicks — All Ads</div>
      <div class="aa-card-sub">Combined performance across all running ads</div>
      @if($dailyTotals->count())
        <canvas id="overviewChart" style="width:100%;height:120px;display:block"></canvas>
        <div class="aa-legend">
          <span><span class="aa-legend-dot" style="background:#f97316"></span>Impressions</span>
          <span><span class="aa-legend-dot" style="background:#06b6d4"></span>Clicks</span>
        </div>
        <script>
        (function(){
          var canvas = document.getElementById('overviewChart');
          var raw    = @json($dailyTotals->map(fn($r) => ['day'=>$r->day,'impressions'=>(int)$r->impressions,'clicks'=>(int)$r->clicks])->values());
          var labels = raw.map(function(r){ return r.day; });
          var imps   = raw.map(function(r){ return r.impressions; });
          var clks   = raw.map(function(r){ return r.clicks; });
          var dpr=window.devicePixelRatio||1, W=canvas.offsetWidth||600, H=120;
          canvas.width=W*dpr; canvas.height=H*dpr;
          canvas.style.width=W+'px'; canvas.style.height=H+'px';
          var ctx=canvas.getContext('2d'); ctx.scale(dpr,dpr);
          var maxV=Math.max.apply(null,imps.concat(clks))||1;
          var pad={t:8,r:8,b:24,l:36}, cw=W-pad.l-pad.r, ch=H-pad.t-pad.b;
          var n=labels.length, step=n>1?cw/(n-1):cw;

          // grid
          [0,.5,1].forEach(function(f){
            ctx.strokeStyle='rgba(249,115,22,.07)'; ctx.lineWidth=1;
            var y=pad.t+ch*f; ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(pad.l+cw,y); ctx.stroke();
          });
          // y labels
          ctx.fillStyle='#9bb5b3'; ctx.font='10px system-ui,sans-serif'; ctx.textAlign='right';
          ctx.fillText(maxV,pad.l-4,pad.t+4); ctx.fillText(0,pad.l-4,pad.t+ch+4);

          function drawLine(vals,color){
            var pts=vals.map(function(v,i){return{x:pad.l+i*step,y:pad.t+ch-(v/maxV)*ch};});
            // area
            ctx.beginPath(); ctx.moveTo(pts[0].x,pad.t+ch);
            pts.forEach(function(p){ctx.lineTo(p.x,p.y);}); ctx.lineTo(pts[pts.length-1].x,pad.t+ch);
            ctx.closePath(); ctx.fillStyle=color+'22'; ctx.fill();
            // line
            ctx.beginPath(); pts.forEach(function(p,i){i===0?ctx.moveTo(p.x,p.y):ctx.lineTo(p.x,p.y);});
            ctx.strokeStyle=color; ctx.lineWidth=2; ctx.lineJoin='round'; ctx.stroke();
            // dot
            var l=pts[pts.length-1];
            ctx.beginPath(); ctx.arc(l.x,l.y,3.5,0,2*Math.PI);
            ctx.fillStyle=color; ctx.fill(); ctx.strokeStyle='#fff'; ctx.lineWidth=1.5; ctx.stroke();
          }
          drawLine(imps,'#f97316'); drawLine(clks,'#06b6d4');
          // x labels
          ctx.fillStyle='#9bb5b3'; ctx.font='10px system-ui,sans-serif';
          ctx.textAlign='left'; ctx.fillText(labels[0],pad.l,H-4);
          ctx.textAlign='right'; ctx.fillText(labels[labels.length-1],pad.l+cw,H-4);
        })();
        </script>
      @else
        <div class="aa-empty" style="padding:30px 0">No impression data yet</div>
      @endif
    </div>

    {{-- Position Breakdown --}}
    <div class="aa-card">
      <div class="aa-card-title">By Position</div>
      <div class="aa-card-sub">Impressions per slot type</div>
      @php $posMax = $byPosition->max('impressions') ?: 1; @endphp
      @forelse($byPosition as $row)
        @php $col = $posColors[$row->position] ?? '#6b7280'; @endphp
        <div style="margin-bottom:14px">
          <div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:5px">
            <span style="font-weight:600;color:#1a2e2d">{{ $posLabels[$row->position] ?? $row->position }}</span>
            <span style="font-variant-numeric:tabular-nums;font-weight:700;color:{{ $col }}">{{ number_format($row->impressions) }}</span>
          </div>
          <div style="background:#f0f5f4;border-radius:4px;height:7px">
            <div style="width:{{ round(($row->impressions/$posMax)*100) }}%;height:7px;border-radius:4px;background:{{ $col }}"></div>
          </div>
          <div style="font-size:10px;color:#9bb5b3;margin-top:3px">
            {{ number_format($row->clicks) }} clicks
            @if($row->impressions > 0)
              · {{ round(($row->clicks/$row->impressions)*100,1) }}% CTR
            @endif
          </div>
        </div>
      @empty
        <div style="color:#9bb5b3;font-size:12px;padding:20px 0;text-align:center">No data yet</div>
      @endforelse
    </div>

  </div>

  {{-- Single-Ad Detailed View --}}
  @if($mode === 'single' && $singleAd)
    @php
      $sa = $singleAd;
      $sac = $posColors[$sa['position']] ?? '#6b7280';
    @endphp
    <div class="aa-card" style="margin-bottom:16px;border-left:4px solid {{ $sac }}">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;flex-wrap:wrap;gap:10px">
        <div>
          <div style="font-size:15px;font-weight:700;color:#1a2e2d;margin-bottom:4px">{{ $sa['title'] }}</div>
          <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <span class="aa-pos-badge" style="background:{{ $sac }}22;color:{{ $sac }}">
              {{ $posLabels[$sa['position']] ?? $sa['position'] }}
            </span>
            @if(!$sa['is_active'])
              <span class="aa-pos-badge" style="background:#fee2e2;color:#dc2626">Inactive</span>
            @endif
            @if($sa['starts_at'])
              <span style="font-size:11px;color:#9bb5b3">{{ $sa['starts_at'] }} → {{ $sa['ends_at'] ?? 'Forever' }}</span>
            @endif
            <a href="{{ $sa['click_url'] }}" target="_blank"
               style="font-size:11px;color:#06b6d4;text-decoration:none">🔗 {{ Str::limit($sa['click_url'], 40) }}</a>
          </div>
        </div>
        <a href="{{ route('filament.admin.resources.advertisements.edit', $sa['id']) }}"
           style="font-size:12px;font-weight:600;color:#6b8f8d;text-decoration:none;padding:5px 12px;border:1.5px solid #c8d8d7;border-radius:6px">
          Edit Ad
        </a>
      </div>

      {{-- Full daily chart --}}
      <div class="aa-card-title">Daily Performance</div>
      <div class="aa-card-sub">Impressions and clicks per day for this ad</div>
      @if(count($sa['daily']) > 0)
        <canvas id="singleAdChart" style="width:100%;height:160px;display:block"></canvas>
        <div class="aa-legend">
          <span><span class="aa-legend-dot" style="background:#f97316"></span>Impressions</span>
          <span><span class="aa-legend-dot" style="background:#06b6d4"></span>Clicks</span>
        </div>
        <script>
        (function(){
          var canvas=document.getElementById('singleAdChart');
          var raw=@json($sa['daily']);
          var imps=raw.map(function(r){return r.impressions;});
          var clks=raw.map(function(r){return r.clicks;});
          var labels=raw.map(function(r){return r.day;});
          var dpr=window.devicePixelRatio||1, W=canvas.offsetWidth||700, H=160;
          canvas.width=W*dpr; canvas.height=H*dpr;
          canvas.style.width=W+'px'; canvas.style.height=H+'px';
          var ctx=canvas.getContext('2d'); ctx.scale(dpr,dpr);
          var maxV=Math.max.apply(null,imps.concat(clks))||1;
          var pad={t:10,r:10,b:26,l:40}, cw=W-pad.l-pad.r, ch=H-pad.t-pad.b;
          var n=labels.length, step=n>1?cw/(n-1):cw;

          // grid
          [0,.25,.5,.75,1].forEach(function(f){
            ctx.strokeStyle='rgba(249,115,22,.06)'; ctx.lineWidth=1;
            var y=pad.t+ch*f; ctx.beginPath(); ctx.moveTo(pad.l,y); ctx.lineTo(pad.l+cw,y); ctx.stroke();
            ctx.fillStyle='#9bb5b3'; ctx.font='9px system-ui,sans-serif'; ctx.textAlign='right';
            ctx.fillText(Math.round(maxV*(1-f)), pad.l-5, y+4);
          });

          function drawLine(vals,color){
            var pts=vals.map(function(v,i){return{x:pad.l+i*step,y:pad.t+ch-(v/maxV)*ch};});
            ctx.beginPath(); ctx.moveTo(pts[0].x,pad.t+ch);
            pts.forEach(function(p){ctx.lineTo(p.x,p.y);}); ctx.lineTo(pts[pts.length-1].x,pad.t+ch);
            ctx.closePath(); ctx.fillStyle=color+'22'; ctx.fill();
            ctx.beginPath(); pts.forEach(function(p,i){i===0?ctx.moveTo(p.x,p.y):ctx.lineTo(p.x,p.y);});
            ctx.strokeStyle=color; ctx.lineWidth=2; ctx.lineJoin='round'; ctx.stroke();
            var l=pts[pts.length-1];
            ctx.beginPath(); ctx.arc(l.x,l.y,4,0,2*Math.PI);
            ctx.fillStyle=color; ctx.fill(); ctx.strokeStyle='#fff'; ctx.lineWidth=2; ctx.stroke();
          }
          drawLine(imps,'#f97316'); drawLine(clks,'#06b6d4');

          // x labels — show every Nth label to avoid crowding
          ctx.fillStyle='#9bb5b3'; ctx.font='10px system-ui,sans-serif';
          var every=Math.ceil(n/8);
          labels.forEach(function(l,i){
            if(i===0||i===n-1||i%every===0){
              ctx.textAlign='center';
              ctx.fillText(l, pad.l+i*step, H-4);
            }
          });
        })();
        </script>
      @else
        <div style="text-align:center;padding:40px 0;color:#9bb5b3;font-size:12px">No data in this period</div>
      @endif
    </div>
  @endif

  {{-- Per-Ad Cards (all-mode only) --}}
  <div style="display:{{ $mode === 'single' ? 'none' : 'block' }};margin-bottom:6px">
    <div style="font-size:12px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#1a2e2d">
      Ad-wise Performance
    </div>
    <div style="font-size:11px;color:#9bb5b3;margin-top:2px">Each ad's impressions, clicks & daily trend</div>
  </div>

  @if($ads->count() && $mode === 'all')
    <div class="aa-ads-grid">
      @foreach($ads as $adIdx => $ad)
        @php
          $ac = $posColors[$ad['position']] ?? '#6b7280';
          $ctrColor = $ad['ctr'] >= 2 ? '#059669' : ($ad['ctr'] >= 0.5 ? '#f59e0b' : '#9bb5b3');
          $chartId  = 'adchart_'.$adIdx;
        @endphp
        <div class="aa-ad-card" style="--ac:{{ $ac }}">

          {{-- Header --}}
          <div class="aa-ad-header">
            <div style="min-width:0;flex:1;margin-right:10px">
              <div class="aa-ad-title" title="{{ $ad['title'] }}">{{ $ad['title'] }}</div>
              <div class="aa-ad-meta">
                <span class="aa-pos-badge" style="background:{{ $ac }}22;color:{{ $ac }}">
                  {{ $posLabels[$ad['position']] ?? $ad['position'] }}
                </span>
                @if(!$ad['is_active'])
                  <span class="aa-pos-badge" style="background:#fee2e2;color:#dc2626;margin-left:4px">Inactive</span>
                @endif
              </div>
            </div>
            <a href="{{ $ad['click_url'] }}" target="_blank"
               style="font-size:10px;color:#9bb5b3;text-decoration:none;flex-shrink:0"
               title="{{ $ad['click_url'] }}">
              🔗 Link
            </a>
          </div>

          {{-- Stats row --}}
          <div class="aa-ad-stats">
            <div class="aa-ad-stat">
              <div class="aa-ad-stat-label">Impressions</div>
              <div class="aa-ad-stat-value" style="color:#f97316">{{ number_format($ad['impressions']) }}</div>
            </div>
            <div class="aa-ad-stat">
              <div class="aa-ad-stat-label">Clicks</div>
              <div class="aa-ad-stat-value" style="color:#06b6d4">{{ number_format($ad['clicks']) }}</div>
            </div>
            <div class="aa-ad-stat">
              <div class="aa-ad-stat-label">CTR</div>
              <div class="aa-ad-stat-value" style="color:{{ $ctrColor }}">
                {{ $ad['ctr'] > 0 ? $ad['ctr'].'%' : '—' }}
              </div>
            </div>
          </div>

          {{-- Mini chart --}}
          <div class="aa-ad-chart">
            @if(count($ad['daily']) > 0)
              <canvas id="{{ $chartId }}" style="width:100%;height:60px;display:block"></canvas>
              <div class="aa-legend">
                <span><span class="aa-legend-dot" style="background:#f97316"></span>Impr.</span>
                <span><span class="aa-legend-dot" style="background:#06b6d4"></span>Clicks</span>
              </div>
              <script>
              (function(){
                var canvas=document.getElementById('{{ $chartId }}');
                var raw=@json($ad['daily']);
                if(!raw||!raw.length){return;}
                var imps=raw.map(function(r){return r.impressions;});
                var clks=raw.map(function(r){return r.clicks;});
                var labels=raw.map(function(r){return r.day;});
                var dpr=window.devicePixelRatio||1,W=canvas.offsetWidth||300,H=60;
                canvas.width=W*dpr; canvas.height=H*dpr;
                canvas.style.width=W+'px'; canvas.style.height=H+'px';
                var ctx=canvas.getContext('2d'); ctx.scale(dpr,dpr);
                var maxV=Math.max.apply(null,imps.concat(clks))||1;
                var pad={t:4,r:4,b:16,l:28},cw=W-pad.l-pad.r,ch=H-pad.t-pad.b;
                var n=raw.length,step=n>1?cw/(n-1):cw;

                // grid line
                ctx.strokeStyle='rgba(0,0,0,.05)'; ctx.lineWidth=1;
                ctx.beginPath(); ctx.moveTo(pad.l,pad.t+ch); ctx.lineTo(pad.l+cw,pad.t+ch); ctx.stroke();

                // y label
                ctx.fillStyle='#9bb5b3'; ctx.font='9px system-ui,sans-serif'; ctx.textAlign='right';
                ctx.fillText(maxV,pad.l-3,pad.t+5);

                function miniLine(vals,color){
                  if(!vals.some(function(v){return v>0;})){return;}
                  var pts=vals.map(function(v,i){return{x:pad.l+i*step,y:pad.t+ch-(v/maxV)*ch};});
                  ctx.beginPath(); ctx.moveTo(pts[0].x,pad.t+ch);
                  pts.forEach(function(p){ctx.lineTo(p.x,p.y);}); ctx.lineTo(pts[pts.length-1].x,pad.t+ch);
                  ctx.closePath(); ctx.fillStyle=color+'18'; ctx.fill();
                  ctx.beginPath(); pts.forEach(function(p,i){i===0?ctx.moveTo(p.x,p.y):ctx.lineTo(p.x,p.y);});
                  ctx.strokeStyle=color; ctx.lineWidth=1.5; ctx.lineJoin='round'; ctx.stroke();
                  var l=pts[pts.length-1];
                  ctx.beginPath(); ctx.arc(l.x,l.y,2.5,0,2*Math.PI);
                  ctx.fillStyle=color; ctx.fill();
                }
                miniLine(imps,'#f97316'); miniLine(clks,'#06b6d4');

                // x labels
                ctx.fillStyle='#9bb5b3'; ctx.font='9px system-ui,sans-serif';
                ctx.textAlign='left'; ctx.fillText(labels[0],pad.l,H-2);
                ctx.textAlign='right'; ctx.fillText(labels[labels.length-1],pad.l+cw,H-2);
              })();
              </script>
            @else
              <div style="font-size:11px;color:#9bb5b3;text-align:center;padding:10px 0">No data in this period</div>
            @endif
          </div>

        </div>
      @endforeach
    </div>
  @elseif($mode === 'all')
    <div class="aa-empty">No ads found — create ads in <strong>Advertising → Paid Ads</strong> to start tracking.</div>
  @endif

</div>

</x-filament-panels::page>
