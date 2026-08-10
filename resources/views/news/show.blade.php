@extends('layouts.app')

@section('title', $article->title.' — GoBazaar News')
@section('description', Str::limit(strip_tags($article->description ?? $article->title), 160))
@section('canonical', route('news.show', $article->slug))
@section('og_type', 'article')
@section('og_title', $article->title)
@section('og_description', Str::limit(strip_tags($article->description ?? $article->title), 200))
@section('og_image', $article->image_url ?? asset('images/og-default.jpg'))
@push('schema')
<script type="application/ld+json">
{!! json_encode(array_filter([
  '@context'      => 'https://schema.org',
  '@type'         => 'NewsArticle',
  'headline'      => $article->title,
  'description'   => Str::limit(strip_tags($article->description ?? $article->title), 300),
  'url'           => route('news.show', $article->slug),
  'image'         => $article->image_url ?? null,
  'datePublished' => optional($article->pub_date)->toIso8601String(),
  'dateModified'  => $article->updated_at->toIso8601String(),
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
.news-show-wrap{max-width:1200px;margin:32px auto;padding:0 20px;display:grid;grid-template-columns:1fr 300px;gap:32px}
@media(max-width:768px){.news-show-wrap{grid-template-columns:1fr}}

.news-header{margin-bottom:28px}
.news-header .news-cat{font-size:11px;font-weight:700;color:var(--red);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px}
.news-header h1{font-family:var(--fh);font-size:28px;font-weight:800;line-height:1.3;margin-bottom:16px}
.news-meta-bar{display:flex;gap:18px;flex-wrap:wrap;font-size:12px;color:var(--muted);padding:14px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-bottom:24px}
.news-meta-bar span{display:flex;align-items:center;gap:5px}

.news-cover{width:100%;border-radius:var(--rl);overflow:hidden;margin-bottom:28px;max-height:420px}
.news-cover img{width:100%;height:420px;object-fit:cover}

.news-body{font-size:15px;line-height:1.8;color:var(--text)}
.news-body p{margin-bottom:16px}
.news-body a{color:var(--red);text-decoration:underline}

.source-link{display:inline-flex;align-items:center;gap:8px;background:var(--red-pale);color:var(--red);padding:10px 16px;border-radius:20px;font-size:13px;font-weight:600;text-decoration:none;margin-top:20px}
.source-link:hover{background:#d0d9f0}

/* Related */
.related-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;margin-top:16px}
.related-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);overflow:hidden}
.related-card img{width:100%;height:120px;object-fit:cover}
.related-card-body{padding:12px}
.related-card-body h4{font-size:13px;font-weight:600;line-height:1.4;margin-bottom:4px}
.related-card-body h4 a{color:var(--text)}
.related-card-body h4 a:hover{color:var(--red)}

/* Sidebar */
.sidebar-box{background:var(--surface);border-radius:var(--rl);border:1px solid var(--border);padding:20px;margin-bottom:20px}
.sidebar-box h4{font-family:var(--fh);font-size:13px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border)}
</style>
@endpush

@section('content')
<div class="news-show-wrap">
  {{-- Main article --}}
  <article>
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a>
      <span>›</span>
      <a href="{{ route('news.index') }}">News</a>
      @if(!empty($article->category[0]))
        <span>›</span>
        <a href="{{ route('news.index', ['category' => $article->category[0]]) }}">{{ $article->category[0] }}</a>
      @endif
      <span>›</span>
      {{ Str::limit($article->title, 40) }}
    </div>

    <div class="news-header">
      @if(!empty($article->category[0]))<div class="news-cat">{{ $article->category[0] }}</div>@endif
      <h1>{{ $article->title }}</h1>
      <div class="news-meta-bar">
        @if($article->source_name)
          <span>📰 {{ $article->source_name }}</span>
        @endif
        <span>🕐 {{ $article->read_time }}</span>
        @if($article->pub_date)
          <span>📅 {{ $article->pub_date->format('F j, Y') }}</span>
        @endif
        <span>👁 {{ number_format($article->views) }} views</span>
      </div>
    </div>

    @if($article->image_url)
    <div class="news-cover">
      <img src="{{ $article->image_url }}" alt="{{ $article->title }}">
    </div>
    @endif

    <div class="news-body">
      {!! clean($article->description ?? '') !!}
    </div>

    @if($article->link)
    <a href="{{ $article->link }}" target="_blank" rel="noopener noreferrer nofollow" class="source-link">
      <i class="fa-solid fa-arrow-up-right-from-square"></i> Read full article on {{ $article->source_name ?? 'source' }}
    </a>
    @endif

    {{-- Related news --}}
    @if($related->isNotEmpty())
    <div style="margin-top:40px">
      <h3 style="font-family:var(--fh);font-size:17px;font-weight:700;margin-bottom:4px">Related News</h3>
      <div class="related-grid">
        @foreach($related as $rn)
        <div class="related-card">
          @if($rn->image_url)
            <img src="{{ $rn->image_url }}" alt="{{ $rn->title }}">
          @else
            <div style="height:120px;background:var(--red-pale);display:grid;place-items:center;font-size:30px">📰</div>
          @endif
          <div class="related-card-body">
            @if(!empty($rn->category[0]))<div style="font-size:10px;color:var(--red);font-weight:700;text-transform:uppercase;margin-bottom:4px">{{ $rn->category[0] }}</div>@endif
            <h4><a href="{{ route('news.show', $rn->slug) }}">{{ $rn->title }}</a></h4>
            <div style="font-size:11px;color:var(--hint)">{{ $rn->read_time }} · {{ $rn->pub_date?->format('M j, Y') }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </article>

  {{-- Sidebar --}}
  <aside>
    <div class="sidebar-box">
      <h4>Latest News</h4>
      @php
        $latest = \App\Models\NewsArticle::where('status','published')->where('id','!=',$article->id)->latest('pub_date')->limit(6)->get();
      @endphp
      @foreach($latest as $ln)
      <div style="display:flex;gap:10px;align-items:flex-start;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid var(--border)">
        @if($ln->image_url)
          <img src="{{ $ln->image_url }}" style="width:44px;height:44px;border-radius:6px;object-fit:cover;flex-shrink:0">
        @else
          <div style="width:44px;height:44px;border-radius:6px;background:var(--red-pale);display:grid;place-items:center;font-size:18px;flex-shrink:0">📰</div>
        @endif
        <div>
          <a href="{{ route('news.show', $ln->slug) }}" style="font-size:12px;font-weight:600;color:var(--text);line-height:1.4;display:block">{{ Str::limit($ln->title, 50) }}</a>
          <span style="font-size:11px;color:var(--hint)">{{ $ln->pub_date?->format('M j, Y') }}</span>
        </div>
      </div>
      @endforeach
    </div>

    <div style="text-align:center;margin-top:4px">
      <a href="{{ route('news.index') }}" class="btn btn-ghost" style="width:100%;justify-content:center">← Back to News</a>
    </div>
  </aside>
</div>
@endsection
