@extends('layouts.app')
@section('title', 'Jobs')

@push('styles')
<style>
.page-header{background:var(--dark);padding:20px;color:#fff}
.page-header h1{font-family:var(--fh);font-size:20px;max-width:1200px;margin:0 auto}
.jobs-wrap{max-width:1200px;margin:20px auto;padding:0 20px;display:grid;grid-template-columns:220px 1fr;gap:20px}
.sidebar{display:flex;flex-direction:column;gap:14px}
.mobile-filter-toggle{display:none;width:100%;background:var(--surface);border:1.5px solid var(--border2);border-radius:var(--r);padding:10px 16px;font-size:13px;font-weight:600;color:var(--text);text-align:left;margin-bottom:12px;cursor:pointer}
@media(max-width:768px){
  .jobs-wrap{grid-template-columns:1fr;padding:0 14px}
  .sidebar{display:none}
  .sidebar.open{display:flex}
  .mobile-filter-toggle{display:block}
  .job-card{flex-wrap:wrap}
  .job-salary{text-align:left;margin-top:8px}
}
@media(max-width:480px){
  .page-header h1{font-size:16px}
  .search-row{flex-wrap:wrap}
  .search-row select.filter-select{flex:1 1 auto}
}
.filter-box{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r);overflow:hidden}
.filter-box-head{background:var(--dark);color:#fff;padding:8px 12px;font-family:var(--fh);font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.filter-list{padding:8px 0}
.filter-item{display:flex;align-items:center;padding:7px 14px;font-size:12.5px;cursor:pointer;transition:background .12s;gap:8px;color:var(--text)}
.filter-item:hover{background:var(--red-pale);color:var(--red)}
.filter-item.active{color:var(--red);font-weight:600;background:var(--red-pale)}
.search-row{display:flex;gap:8px;margin-bottom:14px}
.search-bar{background:var(--surface);border:1.5px solid var(--border2);border-radius:var(--r);display:flex;overflow:hidden;flex:1}
.search-bar input{flex:1;border:none;padding:10px 14px;font-size:13px;background:none}
.search-bar button{background:var(--red);color:#fff;padding:0 18px;font-size:13px;font-weight:500;white-space:nowrap}
select.filter-select{border:1.5px solid var(--border2);border-radius:var(--r);padding:8px 12px;font-size:12.5px;background:var(--surface);color:var(--text);cursor:pointer}

.job-list{display:flex;flex-direction:column;gap:10px}
.job-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);padding:16px;display:flex;gap:14px;transition:all .18s;color:var(--text)}
.job-card:hover{border-color:var(--red2);box-shadow:var(--sh);transform:translateY(-1px)}
.job-logo{width:52px;height:52px;border-radius:10px;background:var(--bg);border:1.5px solid var(--border2);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;overflow:hidden}
.job-logo img{width:100%;height:100%;object-fit:cover}
.job-body{flex:1;min-width:0}
.job-title{font-family:var(--fh);font-size:14px;font-weight:700;margin-bottom:3px}
.job-company{font-size:12.5px;color:var(--muted);margin-bottom:7px}
.job-badges{display:flex;gap:5px;flex-wrap:wrap;margin-bottom:7px}
.jbadge{font-size:10px;font-weight:600;padding:3px 9px;border-radius:20px}
.jb-type{background:var(--green-bg);color:var(--green)}
.jb-mode{background:var(--blue-bg);color:var(--blue)}
.jb-feat{background:var(--gold);color:#fff}
.jb-cat{background:var(--red-pale);color:var(--red)}
.job-meta{display:flex;gap:12px;flex-wrap:wrap;font-size:11.5px;color:var(--muted)}
.job-salary{font-family:var(--fh);font-size:14px;font-weight:700;color:var(--green);text-align:right;white-space:nowrap}
.job-date{font-size:10.5px;color:var(--hint);text-align:right;margin-top:4px}
.tag{background:var(--bg);border:1px solid var(--border2);color:var(--muted);font-size:9.5px;padding:2px 7px;border-radius:20px;margin-right:3px}
.empty{padding:40px;text-align:center;color:var(--muted);font-size:13px;background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r)}
</style>
@endpush

@section('content')
<div class="page-header">
  <h1>💼 Jobs Board — Indian Community in Canada</h1>
</div>

<div class="jobs-wrap">
  <button class="mobile-filter-toggle" onclick="this.nextElementSibling.classList.toggle('open');this.textContent=this.nextElementSibling.classList.contains('open')?'▲ Hide Filters':'▼ Filters & Categories'">▼ Filters & Categories</button>
  <aside class="sidebar">
    <div class="filter-box">
      <div class="filter-box-head">Categories</div>
      <div class="filter-list">
        <a href="{{ route('jobs.index', request()->except('category')) }}"
           class="filter-item {{ !request('category') ? 'active' : '' }}">💼 All Jobs</a>
        @foreach($categories as $cat)
          <a href="{{ route('jobs.index', array_merge(request()->query(), ['category' => $cat->id])) }}"
             class="filter-item {{ request('category') == $cat->id ? 'active' : '' }}">
            {{ $cat->icon }} {{ $cat->name }}
          </a>
        @endforeach
      </div>
    </div>

    <div class="filter-box">
      <div class="filter-box-head">Job Type</div>
      <div class="filter-list">
        @foreach([''=>'All Types','full-time'=>'Full Time','part-time'=>'Part Time','contract'=>'Contract','freelance'=>'Freelance','internship'=>'Internship'] as $val=>$label)
          <a href="{{ route('jobs.index', array_merge(request()->query(), ['job_type' => $val ?: null])) }}"
             class="filter-item {{ request('job_type', '') === $val ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
      </div>
    </div>

    <div class="filter-box">
      <div class="filter-box-head">Work Mode</div>
      <div class="filter-list">
        @foreach([''=>'All Modes','onsite'=>'On-site','remote'=>'Remote','hybrid'=>'Hybrid'] as $val=>$label)
          <a href="{{ route('jobs.index', array_merge(request()->query(), ['work_mode' => $val ?: null])) }}"
             class="filter-item {{ request('work_mode', '') === $val ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
      </div>
    </div>

    <x-location-filter route="jobs.index" :provinces="$provinces" :cities="$cities" />
  </aside>

  <div>
    <form method="GET" action="{{ route('jobs.index') }}">
      @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
      @if(request('job_type'))<input type="hidden" name="job_type" value="{{ request('job_type') }}">@endif
      @if(request('work_mode'))<input type="hidden" name="work_mode" value="{{ request('work_mode') }}">@endif
      @if(request('city'))<input type="hidden" name="city" value="{{ request('city') }}">@endif
      <div class="search-bar" style="margin-bottom:14px">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search job title or company...">
        <button type="submit">Search</button>
      </div>
    </form>

    <div style="font-size:12px;color:var(--muted);margin-bottom:12px">
      {{ $jobs->total() }} job{{ $jobs->total() != 1 ? 's' : '' }} found
    </div>

    @if($jobs->isEmpty())
      <div class="empty">
        <div style="font-size:36px;margin-bottom:10px">📭</div>
        No jobs found.
      </div>
    @else
      <div class="job-list">
        @foreach($jobs as $job)
          <a href="{{ route('jobs.show', $job) }}" class="job-card">
            <div class="job-logo">
              @if($job->company_logo)
                <img src="{{ asset('storage/'.$job->company_logo) }}" alt="{{ $job->company }}">
              @else
                💼
              @endif
            </div>
            <div class="job-body">
              <div class="job-title">{{ $job->title }}</div>
              <div class="job-company">{{ $job->company }}</div>
              <div class="job-badges">
                @if($job->is_featured)<span class="jbadge jb-feat">★ Featured</span>@endif
                <span class="jbadge jb-type">{{ $job->job_type_label }}</span>
                <span class="jbadge jb-mode">{{ $job->work_mode_label }}</span>
                @if($job->category)<span class="jbadge jb-cat">{{ $job->category->name }}</span>@endif
              </div>
              <div class="job-meta">
                @if($job->city)<span>📍 {{ $job->city }}{{ $job->province ? ', '.$job->province : '' }}</span>@endif
                @if($job->experience)<span>🎓 {{ $job->experience }}</span>@endif
                <span>👁 {{ $job->views }}</span>
                <span>📅 {{ $job->created_at->diffForHumans() }}</span>
              </div>
              @if($job->tags)
                <div style="margin-top:6px">
                  @foreach(array_slice($job->tags, 0, 4) as $tag)
                    <span class="tag">{{ $tag }}</span>
                  @endforeach
                </div>
              @endif
            </div>
            @if($job->salary)
              <div>
                <div class="job-salary">{{ $job->salary }}</div>
                <div class="job-date">{{ $job->created_at->format('d M Y') }}</div>
              </div>
            @endif
          </a>
        @endforeach
      </div>
      <div style="margin-top:20px">{{ $jobs->withQueryString()->links() }}</div>
    @endif
  </div>
</div>
@endsection
