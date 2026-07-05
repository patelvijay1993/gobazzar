@extends('layouts.app')
@section('title', 'Insights — ' . $listing->title)

@push('styles')
<style>
body{--red:#1a3a8f;--red2:#e74c3c;--red-dark:#122970;--red-pale:#e8edf7;--surface:#fff;--bg:#f9fafb;--hint:#9ca3af;--rl:14px;--r:8px;--green:#16a34a;--green-bg:#dcfce7;--gold:#e8a020;}
.insights-wrap{max-width:1100px;margin:28px auto;padding:0 20px}
.insights-header{display:flex;align-items:center;gap:16px;margin-bottom:28px;flex-wrap:wrap}
.insights-back{color:var(--primary);font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px}
.insights-title{font-family:var(--fh);font-size:22px;font-weight:800;color:var(--text);flex:1}
.insights-sub{font-size:12px;color:var(--muted);margin-top:2px}

/* KPI cards */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px}
@media(max-width:768px){.kpi-grid{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.kpi-grid{grid-template-columns:1fr}}
.kpi-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);padding:18px 20px}
.kpi-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;font-weight:600;margin-bottom:6px}
.kpi-val{font-family:var(--fh);font-size:32px;font-weight:800;color:var(--text);line-height:1}
.kpi-sub{font-size:11px;color:var(--hint);margin-top:5px}
.kpi-accent-blue .kpi-val{color:var(--primary)}
.kpi-accent-green .kpi-val{color:var(--green)}
.kpi-accent-gold .kpi-val{color:var(--gold)}

/* Chart card */
.chart-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);padding:20px 24px;margin-bottom:24px}
.chart-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px}
.chart-head h3{font-family:var(--fh);font-size:14px;font-weight:700;color:var(--text)}
.chart-legend{display:flex;gap:14px;font-size:11px;color:var(--muted)}
.legend-dot{width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:4px}

/* Two-col bottom */
.bottom-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:768px){.bottom-grid{grid-template-columns:1fr}}

/* Device pie card */
.device-item{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px}
.device-item:last-child{border-bottom:none}
.device-bar-wrap{flex:1;margin:0 12px;height:6px;background:var(--bg);border-radius:3px;overflow:hidden}
.device-bar{height:100%;border-radius:3px;background:var(--primary)}

/* Referrer table */
.ref-row{display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--border);font-size:12px;gap:8px}
.ref-row:last-child{border-bottom:none}
.ref-url{flex:1;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px}
.ref-cnt{font-weight:700;color:var(--text);flex-shrink:0}

/* Upgrade prompt */
.upgrade-banner{background:var(--red-pale);border:1.5px solid var(--primary);border-radius:var(--rl);padding:20px 24px;text-align:center;margin-bottom:28px}
</style>
@endpush

@section('content')
<div class="insights-wrap">

  {{-- Header --}}
  <div class="insights-header">
    <div style="flex:1;min-width:0">
      <a href="{{ route('account') }}" class="insights-back">← Back to Account</a>
      <h1 class="insights-title">📊 Insights</h1>
      <div class="insights-sub">
        {{ $listing->title }}
        @if($listing->image_url)
          &nbsp;·&nbsp; <a href="{{ route('classifieds.show', $listing->slug) }}" style="color:var(--primary)" target="_blank">View listing ↗</a>
        @endif
      </div>
    </div>
    <div style="text-align:right;flex-shrink:0">
      <div style="font-size:11px;color:var(--muted)">Status</div>
      <span class="status-badge status-{{ $listing->status }}">{{ ucfirst($listing->status) }}</span>
    </div>
  </div>

  {{-- KPI cards --}}
  <div class="kpi-grid">
    <div class="kpi-card kpi-accent-blue">
      <div class="kpi-label">Total Views</div>
      <div class="kpi-val">{{ number_format($totalViews) }}</div>
      <div class="kpi-sub">All time</div>
    </div>
    <div class="kpi-card kpi-accent-green">
      <div class="kpi-label">Unique Visitors</div>
      <div class="kpi-val">{{ number_format($uniqueViews) }}</div>
      <div class="kpi-sub">Distinct IPs</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Today</div>
      <div class="kpi-val">{{ number_format($todayViews) }}</div>
      <div class="kpi-sub">Views today</div>
    </div>
    <div class="kpi-card kpi-accent-gold">
      <div class="kpi-label">Last 7 Days</div>
      <div class="kpi-val">{{ number_format($last7Views) }}</div>
      <div class="kpi-sub">Rolling 7-day</div>
    </div>
  </div>

  {{-- Views chart (30 days) --}}
  <div class="chart-card">
    <div class="chart-head">
      <h3>Views — Last 30 Days</h3>
      <div class="chart-legend">
        <span><span class="legend-dot" style="background:#1a3a8f"></span>Total views</span>
        <span><span class="legend-dot" style="background:#16a34a"></span>Unique visitors</span>
      </div>
    </div>
    <canvas id="viewsChart" height="80"></canvas>
  </div>

  {{-- Device + Referrers --}}
  <div class="bottom-grid">
    {{-- Device breakdown --}}
    <div class="chart-card" style="margin-bottom:0">
      <div class="chart-head"><h3>Device Breakdown</h3></div>
      @php $devTotal = $devices->sum() ?: 1; @endphp
      @foreach([['desktop','🖥️'], ['mobile','📱'], ['tablet','📟']] as [$dev, $icon])
        @php $cnt = $devices->get($dev, 0); @endphp
        <div class="device-item">
          <span style="width:80px;color:var(--text);font-weight:500">{{ $icon }} {{ ucfirst($dev) }}</span>
          <div class="device-bar-wrap">
            <div class="device-bar" style="width:{{ round($cnt/$devTotal*100) }}%"></div>
          </div>
          <span style="color:var(--muted);width:52px;text-align:right">{{ $cnt }} <small>({{ round($cnt/$devTotal*100) }}%)</small></span>
        </div>
      @endforeach
    </div>

    {{-- Top referrers --}}
    <div class="chart-card" style="margin-bottom:0">
      <div class="chart-head"><h3>Top Referrers</h3></div>
      @if($referrers->isEmpty())
        <div style="color:var(--muted);font-size:13px;padding:20px 0;text-align:center">No referrer data yet</div>
      @else
        @foreach($referrers as $ref)
          <div class="ref-row">
            <span class="ref-url" title="{{ $ref->referrer }}">{{ $ref->referrer }}</span>
            <span class="ref-cnt">{{ $ref->cnt }}</span>
          </div>
        @endforeach
      @endif
    </div>
  </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('viewsChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: @json($labels),
    datasets: [
      {
        label: 'Total Views',
        data: @json($totals),
        borderColor: '#1a3a8f',
        backgroundColor: 'rgba(26,58,143,0.08)',
        borderWidth: 2,
        pointRadius: 3,
        tension: 0.3,
        fill: true,
      },
      {
        label: 'Unique Visitors',
        data: @json($uniques),
        borderColor: '#16a34a',
        backgroundColor: 'rgba(22,163,74,0.06)',
        borderWidth: 2,
        pointRadius: 3,
        tension: 0.3,
        fill: true,
      }
    ]
  },
  options: {
    responsive: true,
    interaction: { mode: 'index', intersect: false },
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 10 }, maxTicksLimit: 10 } },
      y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } }
    }
  }
});
</script>
@endpush
@endsection
