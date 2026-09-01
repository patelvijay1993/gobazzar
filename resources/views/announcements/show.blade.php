@extends('layouts.app')

@section('title', $announcement->title.' — GoBazaar Announcements')
@section('description', Str::limit(strip_tags($announcement->excerpt ?? $announcement->title), 160))
@section('canonical', route('announcements.show', $announcement->slug))
@section('og_type', 'article')
@section('og_title', $announcement->title)
@section('og_description', Str::limit(strip_tags($announcement->excerpt ?? $announcement->title), 200))
@push('schema')
<script type="application/ld+json">
{!! json_encode(array_filter([
  '@context'      => 'https://schema.org',
  '@type'         => 'Article',
  'headline'      => $announcement->title,
  'description'   => Str::limit(strip_tags($announcement->excerpt ?? $announcement->title), 300),
  'url'           => route('announcements.show', $announcement->slug),
  'datePublished' => $announcement->created_at->toIso8601String(),
  'dateModified'  => $announcement->updated_at->toIso8601String(),
  'publisher' => [
    '@type' => 'Organization',
    'name'  => 'GoBazaar',
    'logo'  => ['@type' => 'ImageObject', 'url' => asset('favicon.png')],
  ],
]), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
</script>
@endpush

@push('styles')
<style>
body{--red:#1a3a8f;--red2:#e74c3c;--red-dark:#122970;--red-pale:#e8edf7;--border2:#e2e0db;--surface:#fff;--bg:#f9fafb;--hint:#9ca3af;--rl:14px;--r:8px;}
.ann-show-wrap{max-width:1200px;margin:32px auto;padding:0 20px;display:grid;grid-template-columns:1fr 300px;gap:32px}
@media(max-width:768px){.ann-show-wrap{grid-template-columns:1fr}}

.ann-header{margin-bottom:28px}
.ann-header h1{font-family:var(--fh);font-size:27px;font-weight:800;line-height:1.35;margin:10px 0 16px}
.ann-meta-bar{display:flex;gap:18px;flex-wrap:wrap;font-size:12px;color:var(--muted);padding:14px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-bottom:24px}
.ann-meta-bar span{display:flex;align-items:center;gap:5px}

.ann-body{font-size:15px;line-height:1.9;color:var(--text)}
.ann-body p{margin-bottom:16px}
.ann-body a{color:var(--red);text-decoration:underline}

.ann-type{font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.4px}
.ann-type-info{background:#e0f0ff;color:#1d4ed8}
.ann-type-success{background:#dcfce7;color:#15803d}
.ann-type-warning{background:#fef9c3;color:#92400e}
.ann-type-urgent{background:#fee2e2;color:#b91c1c}

.ann-cta{display:inline-flex;align-items:center;gap:8px;background:var(--red);color:#fff;padding:12px 22px;border-radius:20px;font-size:13.5px;font-weight:700;text-decoration:none;margin-top:24px;transition:background .2s}
.ann-cta:hover{background:var(--red-dark)}

/* Sidebar */
.sidebar-box{background:var(--surface);border-radius:var(--rl);border:1px solid var(--border);padding:20px;margin-bottom:20px}
.sidebar-box h4{font-family:var(--fh);font-size:13px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border)}
</style>
@endpush

@section('content')
<div class="ann-show-wrap">
  {{-- Main content --}}
  <article>
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a>
      <span>›</span>
      <a href="{{ route('announcements.index') }}">Announcements</a>
      <span>›</span>
      {{ Str::limit($announcement->title, 40) }}
    </div>

    <div class="ann-header">
      <span class="ann-type ann-type-{{ $announcement->type }}">{{ ucfirst($announcement->type) }}</span>
      <h1>{{ $announcement->title }}</h1>
      <div class="ann-meta-bar">
        <span>🕐 {{ $announcement->read_time }}</span>
        <span>📅 {{ $announcement->created_at->format('F j, Y') }}</span>
        <span>👁 {{ number_format($announcement->views) }} views</span>
      </div>
    </div>

    <div class="ann-body">
      {!! clean($announcement->body ?? '') !!}
    </div>

    @if($announcement->link_url)
    <a href="{{ $announcement->link_url }}" target="_blank" rel="noopener noreferrer" class="ann-cta">
      <i class="fa-solid fa-arrow-right"></i> {{ $announcement->link_text ?: 'Learn More' }}
    </a>
    @endif
  </article>

  {{-- Sidebar --}}
  <aside>
    <div class="sidebar-box">
      <h4>Recent Announcements</h4>
      @forelse($latest as $la)
      <div style="margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid var(--border)">
        <span class="ann-type ann-type-{{ $la->type }}" style="margin-bottom:5px;display:inline-block">{{ ucfirst($la->type) }}</span>
        <a href="{{ route('announcements.show', $la->slug) }}" style="font-size:12.5px;font-weight:600;color:var(--text);line-height:1.5;display:block">{{ Str::limit($la->title, 55) }}</a>
        <span style="font-size:11px;color:var(--hint)">{{ $la->created_at->format('M j, Y') }}</span>
      </div>
      @empty
        <div style="color:var(--muted);font-size:12.5px">Nothing here yet.</div>
      @endforelse
    </div>

    <div style="text-align:center;margin-top:4px">
      <a href="{{ route('announcements.index') }}" class="btn btn-ghost" style="width:100%;justify-content:center">← Back to Announcements</a>
    </div>
  </aside>
</div>
@endsection
