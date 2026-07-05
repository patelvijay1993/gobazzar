@extends('layouts.app')
@section('title', 'Jobs')

@push('styles')
<style>
/* ── LAYOUT ── */
.jobs-wrap{max-width:1280px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:240px 1fr;gap:20px;align-items:start}

/* ── SIDEBAR ── */
.cl-sidebar{display:flex;flex-direction:column;gap:12px}
.sb-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.sb-box-head{background:var(--primary);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;display:flex;align-items:center;gap:7px}
.sb-box-head i{font-size:13px;opacity:.85}
.filter-list{padding:6px 0}
.filter-item{display:flex;align-items:center;padding:9px 14px;font-size:13px;transition:background .12s;gap:9px;color:var(--text);text-decoration:none;border-left:3px solid transparent}
.filter-item:hover{background:var(--primary-light);color:var(--primary);border-left-color:var(--primary)}
.filter-item.active{color:var(--primary);font-weight:600;background:var(--primary-light);border-left-color:var(--primary)}

/* ── MOBILE TOGGLE ── */
.mobile-filter-toggle{display:none;width:100%;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);padding:11px 16px;font-size:13px;font-weight:600;margin-bottom:12px;cursor:pointer;align-items:center;gap:8px}

/* ── SEARCH BAR ── */
.jobs-search{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);display:flex;overflow:hidden;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.jobs-search input{flex:1;border:none;padding:11px 14px;font-size:13.5px;background:none;color:#111;font-family:var(--fb)}
.jobs-search input:focus{outline:none}
.jobs-search select{border:none;border-left:1px solid var(--border);padding:0 12px;font-size:12.5px;color:#555;background:#f9fafb;font-family:var(--fb)}
.jobs-search button{background:var(--primary);color:#fff;border:none;padding:0 20px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;white-space:nowrap;transition:background .2s}
.jobs-search button:hover{background:var(--primary-dark)}

/* ── RESULTS HEAD ── */
.results-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px}
.results-count{font-size:13px;color:var(--muted)}
.results-count strong{color:var(--text);font-weight:700}

/* ── ACTIVE FILTERS ── */
.active-filters{display:flex;flex-wrap:wrap;gap:7px;margin-bottom:12px}
.filter-tag{display:inline-flex;align-items:center;gap:5px;background:var(--primary-light);color:var(--primary);font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;text-decoration:none;border:1px solid #c5d0ef}
.filter-tag:hover{background:#d0d9f0}

/* ── JOB CARDS ── */
.job-list{display:flex;flex-direction:column;gap:10px}
.job-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:16px 18px;display:flex;gap:14px;transition:all .18s;color:var(--text);text-decoration:none;align-items:flex-start}
.job-card:hover{border-color:var(--primary);box-shadow:0 4px 16px rgba(26,58,143,.1);transform:translateY(-1px)}
.job-logo{width:52px;height:52px;border-radius:10px;background:var(--primary-light);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;overflow:hidden}
.job-logo img{width:100%;height:100%;object-fit:cover}
.job-body{flex:1;min-width:0}
.job-title{font-family:var(--fh);font-size:15px;font-weight:700;margin-bottom:3px;color:var(--text)}
.job-company{font-size:12.5px;color:var(--muted);margin-bottom:8px;display:flex;align-items:center;gap:5px}
.job-badges{display:flex;gap:5px;flex-wrap:wrap;margin-bottom:8px}
.jbadge{font-size:10.5px;font-weight:600;padding:3px 10px;border-radius:20px}
.jb-type{background:#dcfce7;color:#15803d}
.jb-mode{background:#dbeafe;color:#1d4ed8}
.jb-feat{background:#fef9c3;color:#92400e}
.jb-cat{background:var(--primary-light);color:var(--primary)}
.job-meta{display:flex;gap:14px;flex-wrap:wrap;font-size:11.5px;color:var(--muted);align-items:center}
.job-meta i{font-size:11px;margin-right:3px;color:var(--primary);opacity:.7}
.job-right{text-align:right;flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:6px}
.job-salary{font-family:var(--fh);font-size:15px;font-weight:800;color:var(--green);white-space:nowrap}
.job-date{font-size:11px;color:var(--muted);white-space:nowrap}
.apply-btn{background:var(--primary);color:#fff;font-size:12px;font-weight:600;padding:6px 14px;border-radius:20px;white-space:nowrap;text-decoration:none;transition:background .2s}
.apply-btn:hover{background:var(--primary-dark)}
.job-tags{display:flex;flex-wrap:wrap;gap:5px;margin-top:7px}
.job-tag{background:#f0ede8;color:#555;font-size:10px;padding:2px 8px;border-radius:20px;border:1px solid var(--border)}

.empty-state{padding:60px 20px;text-align:center;background:#fff;border:1px solid var(--border);border-radius:var(--radius)}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px}
.empty-state h3{font-family:var(--fh);font-size:16px;margin-bottom:6px}
.empty-state p{font-size:13px;color:var(--muted);margin-bottom:16px}
.empty-state a{background:var(--primary);color:#fff;padding:9px 20px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px}

/* ── RESPONSIVE ── */
@media(max-width:900px){
  .jobs-wrap{grid-template-columns:1fr;padding:0 14px;margin:14px auto}
  .cl-sidebar{display:none}
  .cl-sidebar.open{display:flex}
  .mobile-filter-toggle{display:flex}
  .job-right .apply-btn{display:none}
}
@media(max-width:520px){
  .job-card{flex-wrap:wrap;gap:10px;padding:12px}
  .job-right{width:100%;flex-direction:row;align-items:center;justify-content:space-between}
  .job-salary{font-size:14px}
  .job-meta{gap:8px}
}
</style>
@endpush

@section('content')
<h1 style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0">Jobs — Indian Community in Canada</h1>
<div class="jobs-wrap">

  {{-- Mobile toggle --}}
  <button class="mobile-filter-toggle" onclick="document.querySelector('.cl-sidebar').classList.toggle('open');this.innerHTML=document.querySelector('.cl-sidebar').classList.contains('open')?'<i class=\'fa-solid fa-times\'></i> Hide Filters':'<i class=\'fa-solid fa-sliders\'></i> Filters & Categories'">
    <i class="fa-solid fa-sliders"></i> Filters & Categories
  </button>

  {{-- SIDEBAR --}}
  <aside class="cl-sidebar">
    {{-- Categories --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-grid-2"></i> Categories</div>
      <div class="filter-list">
        <a href="{{ route('jobs.index', request()->except('category','page')) }}"
           class="filter-item {{ !request('category') ? 'active' : '' }}">
          <i class="fa-solid fa-briefcase" style="width:16px;font-size:12px;color:var(--muted)"></i> All Jobs
        </a>
        @foreach($categories as $cat)
          <a href="{{ route('jobs.index', array_merge(request()->except('page'), ['category' => $cat->id])) }}"
             class="filter-item {{ request('category') == $cat->id ? 'active' : '' }}">
            <span style="width:16px;text-align:center">{{ $cat->icon }}</span> {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </div>

    {{-- Job Type --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-clock"></i> Job Type</div>
      <div class="filter-list">
        @foreach([''=>'All Types','full-time'=>'Full Time','part-time'=>'Part Time','contract'=>'Contract','freelance'=>'Freelance','internship'=>'Internship'] as $val=>$label)
          <a href="{{ route('jobs.index', array_merge(request()->except('page'), ['job_type' => $val ?: null])) }}"
             class="filter-item {{ request('job_type','') === $val ? 'active' : '' }}">
            {{ $label }}
          </a>
        @endforeach
      </div>
    </div>

    {{-- Work Mode --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-laptop-house"></i> Work Mode</div>
      <div class="filter-list">
        @foreach([''=>'All Modes','onsite'=>'On-site','remote'=>'Remote','hybrid'=>'Hybrid'] as $val=>$label)
          <a href="{{ route('jobs.index', array_merge(request()->except('page'), ['work_mode' => $val ?: null])) }}"
             class="filter-item {{ request('work_mode','') === $val ? 'active' : '' }}">
            {{ $label }}
          </a>
        @endforeach
      </div>
    </div>

    {{-- Location --}}
    <div class="sb-box">
      <div class="sb-box-head"><i class="fa-solid fa-location-dot"></i> Location</div>
      <form method="GET" action="{{ route('jobs.index') }}" style="padding:14px">
        @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
        @if(request('job_type'))<input type="hidden" name="job_type" value="{{ request('job_type') }}">@endif
        @if(request('work_mode'))<input type="hidden" name="work_mode" value="{{ request('work_mode') }}">@endif
        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
        <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Province</div>
        <select name="province" style="width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 10px;font-size:13px;background:#fafafa;margin-bottom:10px;font-family:var(--fb)" onchange="this.form.submit()">
          <option value="">All Provinces</option>
          @foreach($provinces as $prov)
            <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
          @endforeach
        </select>
        @if(request('province') || request('city'))
          <a href="{{ route('jobs.index', request()->except('province','city','page')) }}"
             style="display:block;text-align:center;font-size:12px;color:var(--muted);text-decoration:none;margin-top:4px">
            <i class="fa-solid fa-times"></i> Clear Location
          </a>
        @endif
      </form>
    </div>

    {{-- Post Job CTA --}}
    <div class="sb-box" style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);border-color:transparent;padding:16px;text-align:center">
      <div style="font-size:26px;margin-bottom:8px">💼</div>
      <div style="font-family:var(--fh);font-size:14px;font-weight:700;color:#fff;margin-bottom:4px">Hiring?</div>
      <div style="font-size:11px;color:rgba(255,255,255,.65);margin-bottom:12px;line-height:1.5">Post a job and reach Indian-Canadian professionals</div>
      @auth
        <a href="{{ route('post.create') }}" style="display:block;background:var(--accent);color:#fff;padding:9px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none"><i class="fa-solid fa-plus"></i> Post a Job</a>
      @else
        <a href="{{ route('register') }}" style="display:block;background:var(--accent);color:#fff;padding:9px;border-radius:6px;font-size:13px;font-weight:700;text-decoration:none"><i class="fa-solid fa-plus"></i> Post a Job</a>
      @endauth
    </div>

    {{-- Sidebar Ad --}}
    @if(isset($ads) && $ads->where('position','sidebar')->isNotEmpty())
    <div class="sb-box" style="padding:10px;overflow:hidden">
      <x-ad-slot position="sidebar" :ads="$ads" />
    </div>
    @endif
  </aside>

  {{-- MAIN CONTENT --}}
  <div>
    {{-- Search --}}
    <form method="GET" action="{{ route('jobs.index') }}">
      @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
      @if(request('job_type'))<input type="hidden" name="job_type" value="{{ request('job_type') }}">@endif
      @if(request('work_mode'))<input type="hidden" name="work_mode" value="{{ request('work_mode') }}">@endif
      @if(request('province'))<input type="hidden" name="province" value="{{ request('province') }}">@endif
      <div class="jobs-search">
        <i class="fa-solid fa-magnifying-glass" style="padding:0 10px 0 14px;color:#bbb;font-size:15px;align-self:center;flex-shrink:0"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search job title or company...">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      </div>
    </form>

    {{-- Active filters --}}
    @if(request('search') || request('province') || request('job_type') || request('work_mode') || request('category'))
    <div class="active-filters">
      @if(request('search'))
        <a href="{{ route('jobs.index', request()->except('search','page')) }}" class="filter-tag">"{{ request('search') }}" <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('job_type'))
        <a href="{{ route('jobs.index', request()->except('job_type','page')) }}" class="filter-tag"><i class="fa-solid fa-clock"></i> {{ request('job_type') }} <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('work_mode'))
        <a href="{{ route('jobs.index', request()->except('work_mode','page')) }}" class="filter-tag"><i class="fa-solid fa-laptop-house"></i> {{ request('work_mode') }} <i class="fa-solid fa-times"></i></a>
      @endif
      @if(request('province'))
        <span class="filter-tag" style="cursor:pointer" onclick="clearLocation()"><i class="fa-solid fa-map"></i> {{ request('province') }} <i class="fa-solid fa-times"></i></span>
      @endif
      <span style="font-size:12px;color:var(--muted);align-self:center;cursor:pointer;margin-left:4px" onclick="clearAllFilters()">Clear all</span>
    </div>
    @endif

    {{-- Results count --}}
    <div class="results-head">
      <div class="results-count"><strong>{{ number_format($jobs->total()) }}</strong> job{{ $jobs->total() != 1 ? 's' : '' }} found</div>
    </div>

    @if($jobs->isEmpty())
      <div class="empty-state">
        <div class="empty-icon">📭</div>
        <h3>No jobs found</h3>
        <p>Try adjusting your filters or search terms.</p>
        <a href="{{ route('post.create') }}"><i class="fa-solid fa-plus"></i> Post a Job</a>
      </div>
    @else
      <div class="job-list">
        @foreach($jobs as $job)
          <a href="{{ route('jobs.show', $job) }}" class="job-card">
            <div class="job-logo">
              @if($job->company_logo)
                <img src="{{ $job->logo_url }}" alt="{{ $job->company }}">
              @else
                {{ $job->category->icon ?? '💼' }}
              @endif
            </div>
            <div class="job-body">
              <div class="job-title">{{ $job->title }}</div>
              @if($job->company)
                <div class="job-company"><i class="fa-regular fa-building" style="font-size:11px"></i> {{ $job->company }}</div>
              @endif
              <div class="job-badges">
                @if($job->is_featured)<span class="jbadge jb-feat"><i class="fa-solid fa-star" style="font-size:9px"></i> Featured</span>@endif
                <span class="jbadge jb-type">{{ $job->job_type_label }}</span>
                <span class="jbadge jb-mode">{{ $job->work_mode_label }}</span>
                @if($job->category)<span class="jbadge jb-cat">{{ $job->category->name }}</span>@endif
              </div>
              <div class="job-meta">
                @if($job->city)<span><i class="fa-solid fa-location-dot"></i>{{ $job->city }}{{ $job->province ? ', '.$job->province : '' }}</span>@endif
                @if($job->experience)<span><i class="fa-solid fa-user-graduate"></i>{{ $job->experience }}</span>@endif
                <span><i class="fa-regular fa-eye"></i>{{ $job->views }} views</span>
                <span><i class="fa-regular fa-clock"></i>{{ $job->created_at->diffForHumans() }}</span>
              </div>
              @if($job->tags)
                <div class="job-tags">
                  @foreach(array_slice($job->tags, 0, 4) as $tag)
                    <span class="job-tag">{{ $tag }}</span>
                  @endforeach
                </div>
              @endif
            </div>
            <div class="job-right">
              @if($job->salary)
                <div class="job-salary">{{ $job->formatted_salary }}</div>
              @endif
              <div class="job-date">{{ $job->created_at->format('d M Y') }}</div>
              <span class="apply-btn">Apply Now →</span>
            </div>
          </a>
        @endforeach
      </div>
      {{-- Inline Ad --}}
      @if(isset($ads) && $ads->where('position','inline')->isNotEmpty())
        <x-ad-slot position="inline" :ads="$ads" style="margin:14px 0" />
      @endif
      <div style="margin-top:20px">{{ $jobs->withQueryString()->links() }}</div>
    @endif
  </div>

</div>

{{-- MOBILE SIDEBAR AD --}}
@if(isset($ads) && $ads->where('position','sidebar')->isNotEmpty())
<div class="mob-sidebar-ad">
  <x-ad-slot position="sidebar" :ads="$ads" />
</div>
@endif

@endsection
