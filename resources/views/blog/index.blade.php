@extends('layouts.app')

@section('title', 'Blog — GoBazzar')

@push('styles')
<style>
.blog-hero{background:linear-gradient(135deg,var(--dark) 0%,var(--dark2) 100%);color:#fff;padding:48px 20px;text-align:center}
.blog-hero h1{font-family:var(--fh);font-size:28px;font-weight:800;margin-bottom:8px}
.blog-hero p{color:rgba(255,255,255,.6);font-size:14px}

.blog-layout{max-width:1200px;margin:32px auto;padding:0 20px;display:grid;grid-template-columns:1fr 280px;gap:28px}
@media(max-width:768px){.blog-layout{grid-template-columns:1fr}}

/* Featured post */
.featured-post{background:var(--surface);border-radius:var(--rl);overflow:hidden;border:1px solid var(--border);margin:0 20px 28px;max-width:1160px;margin-left:auto;margin-right:auto;display:grid;grid-template-columns:1fr 1fr}
@media(max-width:768px){.featured-post{grid-template-columns:1fr;margin:0 20px 24px}}
.featured-post-img{height:280px;background:#eee;overflow:hidden}
.featured-post-img img{width:100%;height:100%;object-fit:cover}
.featured-post-body{padding:32px}
.featured-badge{display:inline-block;background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:.5px;margin-bottom:12px;text-transform:uppercase}
.featured-post-body h2{font-family:var(--fh);font-size:22px;font-weight:700;margin-bottom:10px;line-height:1.35}
.featured-post-body p{color:var(--muted);font-size:13px;line-height:1.65;margin-bottom:16px}
.post-meta{font-size:11px;color:var(--hint);display:flex;gap:14px;flex-wrap:wrap}

/* Posts grid */
.posts-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px}
.post-card{background:var(--surface);border-radius:var(--rl);border:1px solid var(--border);overflow:hidden;transition:box-shadow .2s}
.post-card:hover{box-shadow:var(--sh)}
.post-card-img{height:168px;background:#eee;overflow:hidden}
.post-card-img img{width:100%;height:100%;object-fit:cover}
.post-card-body{padding:16px}
.post-cat{font-size:10px;font-weight:700;color:var(--red);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.post-card-body h3{font-family:var(--fh);font-size:15px;font-weight:700;line-height:1.4;margin-bottom:8px}
.post-card-body p{font-size:12px;color:var(--muted);line-height:1.6;margin-bottom:12px}
.post-card-body h3 a{color:var(--text)}
.post-card-body h3 a:hover{color:var(--red)}

/* Sidebar */
.sidebar-box{background:var(--surface);border-radius:var(--rl);border:1px solid var(--border);padding:20px;margin-bottom:20px}
.sidebar-box h4{font-family:var(--fh);font-size:13px;font-weight:700;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border)}
.cat-pill{display:inline-block;background:var(--bg);border:1px solid var(--border2);border-radius:20px;padding:5px 14px;font-size:12px;color:var(--muted);margin:4px 3px;cursor:pointer;transition:all .15s}
.cat-pill:hover,.cat-pill.active{background:var(--red);border-color:var(--red);color:#fff}

/* Search */
.search-form{display:flex;gap:8px;margin-bottom:20px;max-width:1160px;margin-left:auto;margin-right:auto;padding:0 20px}
.search-form input{flex:1;border:1.5px solid var(--border2);border-radius:var(--r);padding:9px 14px;font-size:13px}
.search-form input:focus{border-color:var(--red)}
.search-form button{background:var(--red);color:#fff;border:none;border-radius:var(--r);padding:9px 18px;font-size:13px;cursor:pointer}
</style>
@endpush

@section('content')

<div class="blog-hero">
  <h1>GoBazzar Blog</h1>
  <p>Stories, tips, and insights for the Indian community in Canada</p>
</div>

{{-- Search --}}
<div style="max-width:1200px;margin:24px auto 0;padding:0 20px">
  <form method="GET" action="{{ route('blog.index') }}" class="search-form" style="padding:0;margin:0">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles…">
    @if(request('category'))
      <input type="hidden" name="category" value="{{ request('category') }}">
    @endif
    <button type="submit">Search</button>
  </form>
</div>

{{-- Featured Post --}}
@if($featured && !request('search') && !request('category'))
<div style="max-width:1200px;margin:20px auto 0;padding:0 20px">
  <a href="{{ route('blog.show', $featured->slug) }}" style="display:block;text-decoration:none;color:inherit">
    <div class="featured-post" style="margin:0">
      <div class="featured-post-img">
        @if($featured->image)
          <img src="{{ asset('storage/'.$featured->image) }}" alt="{{ $featured->title }}">
        @else
          <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--red-pale),var(--border));display:grid;place-items:center;font-size:48px">📰</div>
        @endif
      </div>
      <div class="featured-post-body">
        <span class="featured-badge">Featured</span>
        @if($featured->category)<div style="font-size:11px;color:var(--red);font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px">{{ $featured->category }}</div>@endif
        <h2>{{ $featured->title }}</h2>
        @if($featured->excerpt)<p>{{ $featured->excerpt }}</p>@endif
        <div class="post-meta">
          @if($featured->author)<span>✍️ {{ $featured->author->name }}</span>@endif
          <span>🕐 {{ $featured->read_time }}</span>
          @if($featured->published_at)<span>📅 {{ $featured->published_at->format('M j, Y') }}</span>@endif
          <span>👁 {{ number_format($featured->views) }} views</span>
        </div>
      </div>
    </div>
  </a>
</div>
@endif

<div class="blog-layout">
  {{-- Main content --}}
  <div>
    @if(request('search') || request('category'))
    <div style="margin-bottom:16px;font-size:13px;color:var(--muted)">
      @if(request('search'))Showing results for "<strong>{{ request('search') }}</strong>"@endif
      @if(request('category'))Category: <strong>{{ request('category') }}</strong>@endif
      — <a href="{{ route('blog.index') }}" style="color:var(--red)">Clear</a>
    </div>
    @endif

    @if($posts->isEmpty())
      <div style="text-align:center;padding:60px 20px;color:var(--muted)">
        <div style="font-size:40px;margin-bottom:12px">📝</div>
        <p>No posts found.</p>
      </div>
    @else
      <div class="posts-grid">
        @foreach($posts as $post)
        <div class="post-card">
          <a href="{{ route('blog.show', $post->slug) }}">
            <div class="post-card-img">
              @if($post->image)
                <img src="{{ asset('storage/'.$post->image) }}" alt="{{ $post->title }}">
              @else
                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--red-pale),var(--border));display:grid;place-items:center;font-size:36px">📰</div>
              @endif
            </div>
          </a>
          <div class="post-card-body">
            @if($post->category)<div class="post-cat">{{ $post->category }}</div>@endif
            <h3><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
            @if($post->excerpt)<p>{{ Str::limit($post->excerpt, 100) }}</p>@endif
            <div class="post-meta">
              <span>🕐 {{ $post->read_time }}</span>
              @if($post->published_at)<span>{{ $post->published_at->format('M j, Y') }}</span>@endif
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div style="margin-top:28px">
        {{ $posts->withQueryString()->links() }}
      </div>
    @endif
  </div>

  {{-- Sidebar --}}
  <aside>
    <div class="sidebar-box">
      <h4>Categories</h4>
      <div>
        <a href="{{ route('blog.index', array_merge(request()->except('category','page'), [])) }}"
           class="cat-pill {{ !request('category') ? 'active' : '' }}">All</a>
        @foreach($categories as $cat)
        <a href="{{ route('blog.index', array_merge(request()->except('category','page'), ['category' => $cat])) }}"
           class="cat-pill {{ request('category') === $cat ? 'active' : '' }}">{{ $cat }}</a>
        @endforeach
      </div>
    </div>

    <div class="sidebar-box">
      <h4>Latest Posts</h4>
      @php
        $latest = \App\Models\BlogPost::where('status','published')->latest('published_at')->limit(5)->get();
      @endphp
      @foreach($latest as $lp)
      <div style="display:flex;gap:10px;align-items:flex-start;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid var(--border)">
        @if($lp->image)
          <img src="{{ asset('storage/'.$lp->image) }}" style="width:48px;height:48px;border-radius:6px;object-fit:cover;flex-shrink:0">
        @else
          <div style="width:48px;height:48px;border-radius:6px;background:var(--red-pale);display:grid;place-items:center;font-size:20px;flex-shrink:0">📰</div>
        @endif
        <div>
          <a href="{{ route('blog.show', $lp->slug) }}" style="font-size:12px;font-weight:600;color:var(--text);line-height:1.4;display:block">{{ Str::limit($lp->title, 50) }}</a>
          <span style="font-size:11px;color:var(--hint)">{{ $lp->published_at?->format('M j, Y') }}</span>
        </div>
      </div>
      @endforeach
    </div>
  </aside>
</div>
@endsection
