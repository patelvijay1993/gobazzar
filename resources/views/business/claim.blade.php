@extends('layouts.app')
@section('title', 'Claim ' . $business->name . ' — GoBazaar')

@push('styles')
<style>
.claim-wrap{max-width:560px;margin:48px auto;padding:0 20px}
.claim-card{background:#fff;border:1.5px solid var(--border);border-radius:16px;overflow:hidden;text-align:center}
.claim-head{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);padding:32px 28px;color:#fff}
.claim-biz-thumb{width:64px;height:64px;border-radius:12px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 14px;overflow:hidden}
.claim-biz-thumb img{width:100%;height:100%;object-fit:cover}
.claim-head h1{font-family:var(--fh);font-size:19px;font-weight:800;margin-bottom:4px}
.claim-head p{font-size:12.5px;color:rgba(255,255,255,.75)}
.claim-body{padding:28px}
.claim-body p{font-size:13.5px;color:var(--muted);line-height:1.7;margin-bottom:20px}
.claim-btn{display:inline-flex;align-items:center;gap:8px;background:var(--primary);color:#fff;font-size:14px;font-weight:700;padding:13px 28px;border-radius:10px;text-decoration:none;border:none;cursor:pointer;transition:background .2s}
.claim-btn:hover{background:var(--primary-dark)}
.claim-benefits{background:var(--bg);border-radius:10px;padding:16px 20px;margin-bottom:22px;text-align:left}
.claim-benefits li{font-size:12.5px;color:var(--muted);line-height:1.9}
.claim-status{padding:14px 18px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:20px}
.claim-status.warn{background:#fef9c3;color:#92400e}
.claim-status.ok{background:#dcfce7;color:#15803d}
</style>
@endpush

@section('content')
<div class="claim-wrap">
  <div class="claim-card">
    <div class="claim-head">
      <div class="claim-biz-thumb">
        @if($business->image_url)
          <img src="{{ $business->image_url }}" alt="{{ $business->name }}">
        @else
          🏢
        @endif
      </div>
      <h1>{{ $business->name }}</h1>
      <p>{{ $business->city }}{{ $business->province ? ', '.$business->province : '' }}</p>
    </div>

    <div class="claim-body">
      @if($alreadyClaimed)
        @if($ownedByMe)
          <div class="claim-status ok">✓ You already own this business.</div>
          <p>You can manage it from your account.</p>
          <a href="{{ route('account', ['panel' => 'business']) }}" class="claim-btn">Go to My Business →</a>
        @else
          <div class="claim-status warn">⚠️ This business has already been claimed by another user.</div>
          <p>If you believe this is a mistake, please <a href="{{ route('contact') }}" style="color:var(--primary);font-weight:600">contact our support team</a>.</p>
        @endif
      @else
        <p>Is this your business? Claim it to manage your profile, respond to customer messages, and get a verified badge.</p>

        <div class="claim-benefits">
          <ul style="margin:0;padding-left:18px">
            <li>Edit your business info, hours, and photos</li>
            <li>Get a ✓ Verified badge on your listing</li>
            <li>Reply to customer chat messages directly</li>
            <li>Add posts, offers, and updates to your profile</li>
          </ul>
        </div>

        <form method="POST" action="{{ route('business.claim.confirm', $business) }}">
          @csrf
          <button type="submit" class="claim-btn">✓ Claim & Verify This Business</button>
        </form>
      @endif
    </div>
  </div>
</div>
@endsection
