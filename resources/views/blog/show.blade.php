@extends('layouts.app')

@section('title', $post->title.' — GoBazaar Blog')
@section('description', $post->excerpt ?? Str::limit(strip_tags($post->body), 160))

@push('styles')
<style>
/* Legacy var bridge */
body{--red:#1a3a8f;--red2:#e74c3c;--red-dark:#122970;--red-pale:#e8edf7;--border2:#e2e0db;--surface:#fff;--bg:#f9fafb;--hint:#9ca3af;--rl:14px;--r:8px;--amber:#92400e;--amber-bg:#fef9c3;--amber-light:#fef9c3;--dark:#1a3a8f;--dark2:#122970;--gold:#e8a020;--blue:#1d4ed8;--blue-bg:#eff6ff;--green:#16a34a;--green-bg:#dcfce7;}
.blog-show-wrap{max-width:1200px;margin:32px auto;padding:0 20px;display:grid;grid-template-columns:1fr 300px;gap:32px}
@media(max-width:768px){.blog-show-wrap{grid-template-columns:1fr}}

.post-header{margin-bottom:28px}
.post-header .post-cat{font-size:11px;font-weight:700;color:var(--red);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px}
.post-header h1{font-family:var(--fh);font-size:28px;font-weight:800;line-height:1.3;margin-bottom:16px}
.post-meta-bar{display:flex;gap:18px;flex-wrap:wrap;font-size:12px;color:var(--muted);padding:14px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-bottom:24px}
.post-meta-bar span{display:flex;align-items:center;gap:5px}

.post-cover{width:100%;border-radius:var(--rl);overflow:hidden;margin-bottom:28px;max-height:420px}
.post-cover img{width:100%;height:420px;object-fit:cover}

.post-body{font-size:15px;line-height:1.8;color:var(--text)}
.post-body h2,.post-body h3{font-family:var(--fh);margin:28px 0 12px}
.post-body h2{font-size:20px}
.post-body h3{font-size:17px}
.post-body p{margin-bottom:16px}
.post-body ul,.post-body ol{margin:0 0 16px 24px}
.post-body li{margin-bottom:6px}
.post-body blockquote{border-left:4px solid var(--red);background:var(--red-pale);padding:14px 18px;margin:20px 0;border-radius:0 var(--r) var(--r) 0;font-style:italic;color:var(--dark)}
.post-body img{max-width:100%;border-radius:var(--r);margin:8px 0}
.post-body a{color:var(--red);text-decoration:underline}

.tags-wrap{margin-top:28px;padding-top:20px;border-top:1px solid var(--border)}
.tag-chip{display:inline-block;background:var(--bg);border:1px solid var(--border2);border-radius:20px;padding:4px 12px;font-size:11px;color:var(--muted);margin:3px}

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

/* Author card */
.author-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);padding:20px;display:flex;gap:14px;align-items:flex-start;margin-bottom:20px}
.author-avatar{width:52px;height:52px;border-radius:50%;background:var(--red);color:#fff;display:grid;place-items:center;font-size:20px;font-weight:700;flex-shrink:0;overflow:hidden}
.author-avatar img{width:100%;height:100%;object-fit:cover}
</style>
@endpush

@section('content')
<div class="blog-show-wrap">
  {{-- Main article --}}
  <article>
    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a>
      <span>›</span>
      <a href="{{ route('blog.index') }}">Blog</a>
      @if($post->category)
        <span>›</span>
        <a href="{{ route('blog.index', ['category' => $post->category]) }}">{{ $post->category }}</a>
      @endif
      <span>›</span>
      {{ Str::limit($post->title, 40) }}
    </div>

    <div class="post-header">
      @if($post->category)<div class="post-cat">{{ $post->category }}</div>@endif
      <h1>{{ $post->title }}</h1>
      <div class="post-meta-bar">
        @if($post->author)
          <span>✍️ {{ $post->author->name }}</span>
        @endif
        <span>🕐 {{ $post->read_time }}</span>
        @if($post->published_at)
          <span>📅 {{ $post->published_at->format('F j, Y') }}</span>
        @endif
        <span>👁 {{ number_format($post->views) }} views</span>
      </div>
    </div>

    @if($post->image_url)
    <div class="post-cover">
      <img src="{{ $post->image_url }}" alt="{{ $post->title }}">
    </div>
    @endif

    @if($post->excerpt)
    <p style="font-size:16px;color:var(--muted);font-style:italic;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--border)">{{ $post->excerpt }}</p>
    @endif

    <div class="post-body">
      {!! clean($post->body) !!}
    </div>

    @if($post->tags && count($post->tags))
    <div class="tags-wrap">
      <span style="font-size:12px;color:var(--muted);margin-right:6px">Tags:</span>
      @foreach($post->tags as $tag)
        <span class="tag-chip">{{ $tag }}</span>
      @endforeach
    </div>
    @endif

    {{-- Author bio --}}
    @if($post->author)
    <div class="author-card" style="margin-top:28px">
      <div class="author-avatar">
        @if($post->author->avatar)
          <img src="{{ str_starts_with($post->author->avatar,'http') ? $post->author->avatar : \Illuminate\Support\Facades\Storage::disk('s3')->url($post->author->avatar) }}" alt="{{ $post->author->name }}">
        @else
          {{ strtoupper(substr($post->author->name,0,1)) }}
        @endif
      </div>
      <div>
        <div style="font-weight:700;font-size:14px;margin-bottom:4px">{{ $post->author->name }}</div>
        @if($post->author->bio)
          <div style="font-size:12px;color:var(--muted);line-height:1.6">{{ $post->author->bio }}</div>
        @endif
        @if($post->author->city)
          <div style="font-size:11px;color:var(--hint);margin-top:4px">📍 {{ $post->author->city }}{{ $post->author->province ? ', '.$post->author->province : '' }}</div>
        @endif
      </div>
    </div>
    @endif

    {{-- Related posts --}}
    @if($related->isNotEmpty())
    <div style="margin-top:40px">
      <h3 style="font-family:var(--fh);font-size:17px;font-weight:700;margin-bottom:4px">Related Posts</h3>
      <div class="related-grid">
        @foreach($related as $rp)
        <div class="related-card">
          @if($rp->image)
            <img src="{{ str_starts_with($rp->image,'http') ? $rp->image : \Illuminate\Support\Facades\Storage::disk('s3')->url($rp->image) }}" alt="{{ $rp->title }}">
          @else
            <div style="height:120px;background:var(--red-pale);display:grid;place-items:center;font-size:30px">📰</div>
          @endif
          <div class="related-card-body">
            @if($rp->category)<div style="font-size:10px;color:var(--red);font-weight:700;text-transform:uppercase;margin-bottom:4px">{{ $rp->category }}</div>@endif
            <h4><a href="{{ route('blog.show', $rp->slug) }}">{{ $rp->title }}</a></h4>
            <div style="font-size:11px;color:var(--hint)">{{ $rp->read_time }} · {{ $rp->published_at?->format('M j, Y') }}</div>
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
      <h4>Latest Posts</h4>
      @php
        $latest = \App\Models\BlogPost::where('status','published')->where('id','!=',$post->id)->latest('published_at')->limit(6)->get();
      @endphp
      @foreach($latest as $lp)
      <div style="display:flex;gap:10px;align-items:flex-start;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid var(--border)">
        @if($lp->image)
          <img src="{{ str_starts_with($lp->image,'http') ? $lp->image : \Illuminate\Support\Facades\Storage::disk('s3')->url($lp->image) }}" style="width:44px;height:44px;border-radius:6px;object-fit:cover;flex-shrink:0">
        @else
          <div style="width:44px;height:44px;border-radius:6px;background:var(--red-pale);display:grid;place-items:center;font-size:18px;flex-shrink:0">📰</div>
        @endif
        <div>
          <a href="{{ route('blog.show', $lp->slug) }}" style="font-size:12px;font-weight:600;color:var(--text);line-height:1.4;display:block">{{ Str::limit($lp->title, 50) }}</a>
          <span style="font-size:11px;color:var(--hint)">{{ $lp->published_at?->format('M j, Y') }}</span>
        </div>
      </div>
      @endforeach
    </div>

    @php $cats = \App\Models\BlogPost::where('status','published')->whereNotNull('category')->distinct()->pluck('category'); @endphp
    @if($cats->isNotEmpty())
    <div class="sidebar-box">
      <h4>Categories</h4>
      @foreach($cats as $cat)
        <a href="{{ route('blog.index', ['category' => $cat]) }}"
           style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--border);font-size:13px;color:var(--muted)">
          <span>{{ $cat }}</span>
          <span style="color:var(--hint)">›</span>
        </a>
      @endforeach
    </div>
    @endif

    <div style="text-align:center;margin-top:4px">
      <a href="{{ route('blog.index') }}" class="btn btn-ghost" style="width:100%;justify-content:center">← Back to Blog</a>
    </div>
  </aside>
</div>
@endsection
