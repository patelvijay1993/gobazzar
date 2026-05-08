@extends('layouts.app')
@section('title', $job->title . ' — ' . $job->company)

@push('styles')
<style>
.show-wrap{max-width:1200px;margin:24px auto;padding:0 20px;display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start}
@media(max-width:768px){.show-wrap{grid-template-columns:1fr;padding:0 14px}.job-title{font-size:18px}.job-meta-grid{grid-template-columns:1fr 1fr}.job-header{flex-wrap:wrap}}
@media(max-width:480px){.job-meta-grid{grid-template-columns:1fr}}
.job-main{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden}
.job-header{padding:24px;border-bottom:1px solid var(--border);display:flex;gap:16px;align-items:flex-start}
.job-logo-lg{width:70px;height:70px;border-radius:14px;background:var(--bg);border:2px solid var(--border2);display:flex;align-items:center;justify-content:center;font-size:30px;flex-shrink:0;overflow:hidden}
.job-logo-lg img{width:100%;height:100%;object-fit:cover}
.job-title{font-family:var(--fh);font-size:22px;font-weight:800;margin-bottom:4px}
.job-company{font-size:14px;color:var(--muted);margin-bottom:8px}
.jbadge{font-size:10.5px;font-weight:600;padding:4px 10px;border-radius:20px;margin-right:5px}
.jb-type{background:var(--green-bg);color:var(--green)}
.jb-mode{background:var(--blue-bg);color:var(--blue)}
.jb-feat{background:var(--gold);color:#fff}
.job-meta-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;padding:16px 24px;background:var(--bg);border-bottom:1px solid var(--border)}
.meta-item{display:flex;flex-direction:column;gap:2px}
.meta-label{font-size:10px;color:var(--hint);text-transform:uppercase;letter-spacing:.5px;font-weight:600}
.meta-val{font-size:13px;font-weight:500;color:var(--text)}
.job-body{padding:24px}
.job-section{margin-bottom:24px}
.job-section h3{font-family:var(--fh);font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);margin-bottom:10px;padding-bottom:6px;border-bottom:1px solid var(--border)}
.job-section .content{font-size:13.5px;line-height:1.7}
.tag{background:var(--bg);border:1px solid var(--border2);color:var(--muted);font-size:10px;padding:3px 9px;border-radius:20px;margin:2px}
.sidebar-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;margin-bottom:16px}
.sidebar-head{background:var(--dark);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.sidebar-body{padding:16px}
.btn-block{display:block;width:100%;text-align:center;padding:12px;border-radius:var(--r);font-size:13px;font-weight:600;margin-bottom:8px;transition:all .15s}
.rel-job{display:flex;gap:10px;padding:8px 0;border-bottom:1px solid var(--border);align-items:center}
.rel-job:last-child{border-bottom:none}
.rel-logo{width:36px;height:36px;border-radius:8px;background:var(--bg);border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
</style>
@endpush

@section('content')
<div class="container" style="padding-top:8px">
  <div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a><span>›</span>
    <a href="{{ route('jobs.index') }}">Jobs</a><span>›</span>
    @if($job->category)<a href="{{ route('jobs.index', ['category' => $job->category_id]) }}">{{ $job->category->name }}</a><span>›</span>@endif
    {{ Str::limit($job->title, 50) }}
  </div>
</div>

<div class="show-wrap">
  <div class="job-main">
    <div class="job-header">
      <div class="job-logo-lg">
        @if($job->company_logo)
          <img src="{{ asset('storage/'.$job->company_logo) }}" alt="{{ $job->company }}">
        @else 💼 @endif
      </div>
      <div>
        <h1 class="job-title">{{ $job->title }}</h1>
        <div class="job-company">🏢 {{ $job->company }}</div>
        <div>
          @if($job->is_featured)<span class="jbadge jb-feat">★ Featured</span>@endif
          <span class="jbadge jb-type">{{ $job->job_type_label }}</span>
          <span class="jbadge jb-mode">{{ $job->work_mode_label }}</span>
          @if($job->category)<span class="jbadge" style="background:var(--red-pale);color:var(--red)">{{ $job->category->name }}</span>@endif
        </div>
      </div>
    </div>

    <div class="job-meta-grid">
      @if($job->salary)
      <div class="meta-item">
        <span class="meta-label">Salary</span>
        <span class="meta-val" style="color:var(--green);font-family:var(--fh);font-size:15px;font-weight:700">{{ $job->salary }}</span>
      </div>
      @endif
      @if($job->city)
      <div class="meta-item">
        <span class="meta-label">Location</span>
        <span class="meta-val">📍 {{ $job->city }}{{ $job->province ? ', '.$job->province : '' }}</span>
      </div>
      @endif
      @if($job->experience)
      <div class="meta-item">
        <span class="meta-label">Experience</span>
        <span class="meta-val">🎓 {{ $job->experience }}</span>
      </div>
      @endif
      <div class="meta-item">
        <span class="meta-label">Posted</span>
        <span class="meta-val">📅 {{ $job->created_at->format('d M Y') }}</span>
      </div>
      <div class="meta-item">
        <span class="meta-label">Views</span>
        <span class="meta-val">👁 {{ $job->views }}</span>
      </div>
      @if($job->expires_at)
      <div class="meta-item">
        <span class="meta-label">Expires</span>
        <span class="meta-val">⏰ {{ $job->expires_at->format('d M Y') }}</span>
      </div>
      @endif
    </div>

    <div class="job-body">
      @if($job->description)
      <div class="job-section">
        <h3>Job Description</h3>
        <div class="content">{!! $job->description !!}</div>
      </div>
      @endif

      @if($job->requirements)
      <div class="job-section">
        <h3>Requirements</h3>
        <div class="content">{!! $job->requirements !!}</div>
      </div>
      @endif

      @if($job->tags)
      <div class="job-section">
        <h3>Skills & Tags</h3>
        @foreach($job->tags as $tag)<span class="tag">{{ $tag }}</span>@endforeach
      </div>
      @endif
    </div>
  </div>

  <div>
    <div class="sidebar-card">
      <div class="sidebar-head">Apply Now</div>
      <div class="sidebar-body">
        @if($job->apply_url)
          <a href="{{ $job->apply_url }}" target="_blank" class="btn btn-red btn-block">🚀 Apply Online</a>
        @endif
        @if($job->apply_email)
          <a href="mailto:{{ $job->apply_email }}?subject=Application: {{ $job->title }}" class="btn btn-ghost btn-block">✉ Apply via Email</a>
        @endif
        @if(!$job->apply_url && !$job->apply_email)
          <p style="color:var(--muted);font-size:12px;text-align:center">Contact company directly</p>
        @endif
      </div>
    </div>

    @if($related->count())
    <div class="sidebar-card">
      <div class="sidebar-head">Similar Jobs</div>
      <div class="sidebar-body" style="padding:8px 14px">
        @foreach($related as $rel)
          <a href="{{ route('jobs.show', $rel) }}" class="rel-job" style="display:flex;text-decoration:none;color:var(--text)">
            <div class="rel-logo">💼</div>
            <div>
              <div style="font-size:12.5px;font-weight:500;line-height:1.3">{{ Str::limit($rel->title, 35) }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ $rel->company }}</div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</div>
@endsection
