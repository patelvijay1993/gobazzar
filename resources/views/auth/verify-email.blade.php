@extends('layouts.app')
@section('title', 'Verify Your Email — GoBazaar')

@push('styles')
<style>
.auth-page{min-height:calc(100vh - 120px);background:#f5f7fb;display:flex;align-items:center;justify-content:center;padding:30px 16px}
.auth-box{width:100%;max-width:460px}
.auth-brand{text-align:center;margin-bottom:24px}
.auth-brand-logo{display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:8px}
.auth-brand-icon{width:44px;height:44px;background:var(--primary-light);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px}
.auth-brand-name{font-family:var(--fh);font-size:26px;font-weight:800;color:var(--primary);line-height:1}
.auth-brand-name span{color:var(--accent)}
.auth-card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.12)}
.auth-card-head{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);padding:28px 28px 24px;text-align:center}
.verify-icon{width:64px;height:64px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;border:2px solid rgba(255,255,255,.25)}
.verify-icon i{font-size:28px;color:#fff}
.auth-card-head h2{font-family:var(--fh);font-size:21px;font-weight:800;color:#fff;margin-bottom:5px}
.auth-card-head p{font-size:13px;color:rgba(255,255,255,.7);line-height:1.5}
.auth-body{padding:28px}
.step-row{display:flex;gap:12px;align-items:flex-start;margin-bottom:16px}
.step-num{width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px}
.step-text{font-size:13px;color:var(--text);line-height:1.5}
.step-text strong{color:var(--primary)}
.resend-form{border-top:1px solid var(--border);margin-top:22px;padding-top:22px}
.resend-label{font-size:12.5px;color:var(--muted);margin-bottom:10px}
.resend-input-row{display:flex;gap:8px}
.resend-input{flex:1;border:1.5px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;font-family:var(--fb)}
.resend-input:focus{outline:none;border-color:var(--primary)}
.resend-btn{background:var(--primary);color:#fff;border:none;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;transition:background .2s}
.resend-btn:hover{background:var(--primary-dark)}
.flash-success{background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px}
.flash-info{background:#e0f2fe;border:1px solid #bae6fd;color:#0369a1;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px}
.flash-error{background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px}
</style>
@endpush

@section('content')
<div class="auth-page">
  <div class="auth-box">
    <div class="auth-brand">
      <a href="{{ route('home') }}" class="auth-brand-logo">
        <div class="auth-brand-icon">🛍️</div>
        <div class="auth-brand-name">Go<span>Bazaar</span></div>
      </a>
    </div>

    <div class="auth-card">
      <div class="auth-card-head">
        <div class="verify-icon"><i class="fa-solid fa-envelope-circle-check"></i></div>
        <h2>Check Your Email</h2>
        <p>We sent a verification link to your email address.<br>Click the link to activate your account.</p>
      </div>

      <div class="auth-body">

        @if(session('success'))
          <div class="flash-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
        @endif
        @if(session('info'))
          <div class="flash-info"><i class="fa-solid fa-circle-info"></i> {{ session('info') }}</div>
        @endif
        @if($errors->any())
          <div class="flash-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
        @endif

        <div class="step-row">
          <div class="step-num">1</div>
          <div class="step-text">Open your <strong>email inbox</strong> (check spam/junk folder too)</div>
        </div>
        <div class="step-row">
          <div class="step-num">2</div>
          <div class="step-text">Find the email from <strong>GoBazaar</strong> with subject <strong>"Verify Email Address"</strong></div>
        </div>
        <div class="step-row">
          <div class="step-num">3</div>
          <div class="step-text">Click the <strong>Verify Email Address</strong> button in the email</div>
        </div>
        <div class="step-row">
          <div class="step-num">4</div>
          <div class="step-text">You'll be automatically logged in and redirected to your account</div>
        </div>

        <div class="resend-form">
          <div class="resend-label"><i class="fa-solid fa-rotate" style="margin-right:5px;color:var(--primary)"></i> Didn't receive the email? Enter your email and resend:</div>
          <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <div class="resend-input-row">
              <input type="email" name="email" class="resend-input" required
                placeholder="your@email.com"
                value="{{ session('unverified_email') ?? old('email') }}">
              <button type="submit" class="resend-btn">
                <i class="fa-solid fa-paper-plane" style="margin-right:5px"></i>Resend
              </button>
            </div>
          </form>
        </div>

        <div style="text-align:center;margin-top:20px;font-size:12.5px;color:var(--muted)">
          Already verified? <a href="{{ route('login') }}" style="color:var(--primary);font-weight:600">Login here</a>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
