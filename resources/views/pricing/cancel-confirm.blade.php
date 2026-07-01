@extends('layouts.app')
@section('title', 'Cancel Subscription — GoBazaar')

@push('styles')
<style>
.cancel-page{min-height:calc(100vh - 120px);background:#f5f7fb;display:flex;align-items:center;justify-content:center;padding:30px 16px}
.cancel-box{width:100%;max-width:520px}
.cancel-card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.1)}
.cancel-head{background:linear-gradient(135deg,#dc2626 0%,#991b1b 100%);padding:28px;text-align:center}
.cancel-head-icon{width:60px;height:60px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;border:2px solid rgba(255,255,255,.25)}
.cancel-head-icon i{font-size:26px;color:#fff}
.cancel-head h2{font-family:var(--fh);font-size:20px;font-weight:800;color:#fff;margin-bottom:5px}
.cancel-head p{font-size:13px;color:rgba(255,255,255,.75)}
.cancel-body{padding:28px}
.info-row{display:flex;align-items:center;gap:12px;padding:13px 0;border-bottom:1px solid var(--border)}
.info-row:last-of-type{border-bottom:none}
.info-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.info-label{font-size:11.5px;color:var(--muted);margin-bottom:2px}
.info-value{font-size:13.5px;font-weight:600;color:var(--text)}
.what-happens{background:#fef9c3;border:1px solid #fde68a;border-radius:10px;padding:16px;margin:18px 0}
.what-happens-title{font-size:12.5px;font-weight:700;color:#92400e;margin-bottom:10px;display:flex;align-items:center;gap:6px}
.what-happens ul{list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:7px}
.what-happens ul li{font-size:12.5px;color:#78350f;display:flex;align-items:flex-start;gap:7px}
.what-happens ul li i{color:#d97706;margin-top:2px;flex-shrink:0}
.btn-cancel-confirm{display:block;width:100%;text-align:center;background:#dc2626;color:#fff;border:none;padding:13px;border-radius:9px;font-size:14px;font-weight:700;cursor:pointer;transition:background .2s;font-family:var(--fh)}
.btn-cancel-confirm:hover{background:#b91c1c}
.btn-keep{display:block;width:100%;text-align:center;background:var(--primary);color:#fff;padding:13px;border-radius:9px;font-size:14px;font-weight:700;text-decoration:none;transition:background .2s;font-family:var(--fh);margin-bottom:10px}
.btn-keep:hover{background:var(--primary-dark);color:#fff}
</style>
@endpush

@section('content')
<div class="cancel-page">
  <div class="cancel-box">

    <div class="cancel-card">
      <div class="cancel-head">
        <div class="cancel-head-icon"><i class="fa-solid fa-circle-xmark"></i></div>
        <h2>Cancel Subscription?</h2>
        <p>We're sorry to see you go. Here's what will happen.</p>
      </div>

      <div class="cancel-body">

        {{-- Plan Info --}}
        <div style="margin-bottom:4px">
          <div class="info-row">
            <div class="info-icon" style="background:var(--primary-light)">
              <i class="fa-solid fa-id-card" style="color:var(--primary)"></i>
            </div>
            <div>
              <div class="info-label">Current Plan</div>
              <div class="info-value">{{ $plan->name ?? ucfirst($user->plan) }}</div>
            </div>
          </div>
          <div class="info-row">
            <div class="info-icon" style="background:#dcfce7">
              <i class="fa-solid fa-calendar-check" style="color:#15803d"></i>
            </div>
            <div>
              <div class="info-label">Access Until</div>
              <div class="info-value" style="color:#15803d">{{ $endDate->format('F d, Y') }}</div>
            </div>
          </div>
          <div class="info-row">
            <div class="info-icon" style="background:#fee2e2">
              <i class="fa-solid fa-ban" style="color:#dc2626"></i>
            </div>
            <div>
              <div class="info-label">After That</div>
              <div class="info-value" style="color:#dc2626">Downgraded to Free Plan</div>
            </div>
          </div>
        </div>

        {{-- What Happens --}}
        <div class="what-happens">
          <div class="what-happens-title"><i class="fa-solid fa-triangle-exclamation"></i> What you will lose:</div>
          <ul>
            @if($plan)
              @if($plan->max_listings > 3)
                <li><i class="fa-solid fa-xmark"></i> Max listings reduced to 3 (currently {{ $plan->max_listings }})</li>
              @endif
              @if($plan->verified_badge)
                <li><i class="fa-solid fa-xmark"></i> Verified badge removed from your profile</li>
              @endif
              @if($plan->featured_placement)
                <li><i class="fa-solid fa-xmark"></i> Featured placement in search results</li>
              @endif
              @if($plan->analytics)
                <li><i class="fa-solid fa-xmark"></i> Analytics dashboard access</li>
              @endif
              @if($plan->priority_support)
                <li><i class="fa-solid fa-xmark"></i> Priority customer support</li>
              @endif
              @if($plan->auto_renew)
                <li><i class="fa-solid fa-xmark"></i> Auto-renew for your listings</li>
              @endif
            @else
              <li><i class="fa-solid fa-xmark"></i> All paid plan features and benefits</li>
            @endif
            <li><i class="fa-solid fa-xmark"></i> Post visibility reduced to 3 days per listing</li>
          </ul>
        </div>

        {{-- Actions --}}
        <a href="{{ route('account') }}" class="btn-keep">
          <i class="fa-solid fa-heart" style="margin-right:6px"></i>Keep My {{ $plan->name ?? 'Plan' }} — Stay Subscribed
        </a>

        <form action="{{ route('stripe.cancel') }}" method="POST">
          @csrf
          <button type="submit" class="btn-cancel-confirm">
            <i class="fa-solid fa-circle-xmark" style="margin-right:6px"></i>Yes, Cancel My Subscription
          </button>
        </form>

        <div style="text-align:center;margin-top:14px;font-size:12px;color:var(--muted)">
          You will keep full access until <strong>{{ $endDate->format('M d, Y') }}</strong>. No further charges.
        </div>

      </div>
    </div>

  </div>
</div>
@endsection
