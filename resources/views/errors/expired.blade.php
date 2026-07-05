@extends('layouts.app')
@section('title', 'Listing Expired — GoBazaar')

@section('content')
<div style="max-width:560px;margin:60px auto;padding:0 20px;text-align:center">
  <div style="font-size:64px;margin-bottom:16px">⏰</div>
  <h1 style="font-family:var(--fh);font-size:24px;font-weight:800;color:var(--text);margin-bottom:8px">
    This {{ $type }} has expired
  </h1>
  @if(!empty($title))
    <p style="font-size:15px;color:var(--muted);margin-bottom:6px">
      <strong style="color:var(--text)">{{ $title }}</strong>
    </p>
  @endif
  @if(!empty($expiredAt))
    <p style="font-size:13px;color:var(--hint);margin-bottom:24px">
      Expired {{ $expiredAt->diffForHumans() }} &middot; {{ $expiredAt->format('d M Y') }}
    </p>
  @else
    <p style="font-size:13px;color:var(--hint);margin-bottom:24px">
      This content is no longer available.
    </p>
  @endif

  <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
    <a href="{{ $browseUrl }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px">
      Browse {{ ucfirst($type) }}s
    </a>
    <a href="{{ route('home') }}" class="btn btn-ghost" style="display:inline-flex;align-items:center;gap:6px">
      ← Back to Home
    </a>
  </div>
</div>
@endsection
