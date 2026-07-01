@extends('layouts.app')
@section('title', $category->name . ' — Directory — GoBazaar')

@push('styles')
<style>
.dc-wrap{max-width:1200px;margin:20px auto;padding:0 20px}
.dc-breadcrumb{font-size:12.5px;color:var(--muted);margin-bottom:18px;display:flex;align-items:center;gap:7px;flex-wrap:wrap}
.dc-breadcrumb a{color:var(--primary);text-decoration:none}
.dc-breadcrumb a:hover{text-decoration:underline}
.dc-breadcrumb i{font-size:9px;color:#bbb}
.dc-head{display:flex;align-items:center;gap:14px;margin-bottom:22px}
.dc-head-icon{width:56px;height:56px;border-radius:14px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;font-size:28px;flex-shrink:0}
.dc-head h1{font-family:var(--fh);font-size:24px;font-weight:800;color:var(--text)}
.dc-head p{font-size:13px;color:var(--muted);margin-top:2px}

.subcat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:30px}
.subcat-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius);padding:18px 14px;text-align:center;text-decoration:none;color:var(--text);transition:all .15s}
.subcat-card:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:0 4px 14px rgba(26,58,143,.1)}
.subcat-card .sc-icon{font-size:30px;margin-bottom:8px}
.subcat-card .sc-name{font-size:13px;font-weight:700}
.subcat-card .sc-count{font-size:11px;color:var(--muted);margin-top:2px}

.sec-label{font-family:var(--fh);font-size:16px;font-weight:800;color:var(--text);margin-bottom:14px;display:flex;align-items:center;gap:8px}
.sec-label i{color:var(--primary);font-size:15px}

.biz-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px}
@media(max-width:900px){.biz-grid{grid-template-columns:repeat(2,1fr)}.subcat-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:520px){.biz-grid{grid-template-columns:1fr 1fr}}
.biz-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;text-decoration:none;color:var(--text);transition:all .15s;display:block}
.biz-card:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:0 4px 14px rgba(26,58,143,.1)}
.biz-img{height:120px;background:#f5f0ec;display:flex;align-items:center;justify-content:center;font-size:34px;overflow:hidden}
.biz-img img{width:100%;height:100%;object-fit:cover}
.biz-body{padding:11px 13px}
.biz-name{font-size:13.5px;font-weight:700;margin-bottom:3px}
.biz-cat{font-size:11px;color:var(--muted);margin-bottom:6px}
.biz-stars{font-size:12px;color:var(--accent)}
.empty-box{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:40px;text-align:center;color:var(--muted);font-size:13px}
</style>
@endpush

@section('content')
<div class="dc-wrap">

  {{-- Breadcrumb --}}
  <div class="dc-breadcrumb">
    <a href="{{ route('home') }}">Home</a>
    <i class="fa-solid fa-chevron-right"></i>
    <a href="{{ route('directory.index') }}">Directory</a>
    <i class="fa-solid fa-chevron-right"></i>
    <span>{{ $category->name }}</span>
  </div>

  <div class="dc-head">
    <div class="dc-head-icon">{{ $category->icon ?: '🏢' }}</div>
    <div>
      <h1>{{ $category->name }}</h1>
      <p>{{ $businesses->total() }} {{ Str::plural('business', $businesses->total()) }} in this category</p>
    </div>
  </div>

  {{-- Sub-categories --}}
  @if($subCategories->isNotEmpty())
  <div class="sec-label"><i class="fa-solid fa-layer-group"></i> Browse by sub-category</div>
  <div class="subcat-grid">
    @foreach($subCategories as $sub)
      <a href="{{ route('directory.index', ['category' => $sub->id]) }}" class="subcat-card">
        <div class="sc-icon">{{ $sub->icon ?: '📂' }}</div>
        <div class="sc-name">{{ $sub->name }}</div>
        <div class="sc-count">{{ $sub->businesses()->where('status','active')->count() }} listings</div>
      </a>
    @endforeach
  </div>
  @endif

  {{-- Businesses --}}
  <div class="sec-label"><i class="fa-solid fa-building-columns"></i> Businesses</div>
  @if($businesses->isEmpty())
    <div class="empty-box">
      <div style="font-size:34px;margin-bottom:8px">🏢</div>
      No businesses in this category yet.
    </div>
  @else
  <div class="biz-grid">
    @foreach($businesses as $biz)
      <a href="{{ route('directory.show', $biz->slug) }}" class="biz-card">
        <div class="biz-img">
          @if($biz->image_url)<img src="{{ $biz->image_url }}" alt="{{ $biz->name }}">@else 🏢 @endif
        </div>
        <div class="biz-body">
          <div class="biz-name">{{ Str::limit($biz->name, 24) }}</div>
          <div class="biz-cat">{{ $biz->category->name ?? 'Business' }}@if($biz->city) · {{ $biz->city }}@endif</div>
          @if($biz->rating > 0)<div class="biz-stars"><i class="fa-solid fa-star"></i> {{ number_format($biz->rating, 1) }}</div>@endif
        </div>
      </a>
    @endforeach
  </div>
  <div style="margin-top:20px">{{ $businesses->links() }}</div>
  @endif

</div>
@endsection
