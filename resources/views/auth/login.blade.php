@extends('layouts.app')
@section('title', 'Login — GoBazaar')

@push('styles')
<style>
.auth-page{min-height:calc(100vh - 120px);background:#f5f7fb;display:flex;align-items:center;justify-content:center;padding:30px 16px;position:relative;overflow:hidden}
.auth-page::before{display:none}

.auth-box{width:100%;max-width:420px;position:relative;z-index:1}

.auth-brand{text-align:center;margin-bottom:24px}
.auth-brand-logo{display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:8px}
.auth-brand-icon{width:44px;height:44px;background:var(--primary-light);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px}
.auth-brand-name{font-family:var(--fh);font-size:26px;font-weight:800;color:var(--primary);line-height:1}
.auth-brand-name span{color:var(--accent)}
.auth-brand p{font-size:13px;color:var(--muted)}

.auth-card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.auth-card-head{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);padding:22px 28px;text-align:center;border-bottom:1px solid rgba(255,255,255,.1)}
.auth-card-head h2{font-family:var(--fh);font-size:20px;font-weight:800;color:#fff;margin-bottom:3px}
.auth-card-head p{font-size:12.5px;color:rgba(255,255,255,.65)}

.auth-body{padding:28px}

.form-group{margin-bottom:18px}
.form-label{display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px;letter-spacing:.3px}
.form-label i{color:var(--primary);font-size:13px}

.input-wrap{position:relative}
.input-wrap i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:14px;pointer-events:none}
.form-input{width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:11px 14px 11px 38px;font-size:14px;transition:border .15s,box-shadow .15s;background:#f9fafb;color:#111;font-family:var(--fb)}
.form-input:focus{border-color:var(--primary);outline:none;background:#fff;box-shadow:0 0 0 3px rgba(26,58,143,.1)}
.form-input.is-error{border-color:#ef4444;background:#fef2f2}
.error-msg{font-size:11.5px;color:#ef4444;margin-top:5px;display:flex;align-items:center;gap:4px}
.error-msg i{font-size:11px}

.remember-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;font-size:13px;color:#6b7280}
.remember-row label{display:flex;align-items:center;gap:7px;cursor:pointer}
.remember-row input[type=checkbox]{width:15px;height:15px;accent-color:var(--primary);cursor:pointer}

.btn-login{width:100%;padding:13px;background:var(--primary);color:#fff;border-radius:9px;font-size:15px;font-weight:700;font-family:var(--fh);cursor:pointer;transition:background .2s,transform .1s;border:none;display:flex;align-items:center;justify-content:center;gap:8px;letter-spacing:.2px}
.btn-login:hover{background:var(--primary-dark)}
.btn-login:active{transform:scale(.98)}

.auth-divider{text-align:center;margin:18px 0;position:relative}
.auth-divider::before{content:'';position:absolute;top:50%;left:0;right:0;height:1px;background:#e5e7eb}
.auth-divider span{background:#fff;padding:0 12px;font-size:12px;color:#9ca3af;position:relative}

.auth-footer{text-align:center;padding:16px 28px 20px;font-size:13.5px;color:#6b7280;border-top:1px solid #f3f4f6}
.auth-footer a{color:var(--primary);font-weight:700;text-decoration:none}
.auth-footer a:hover{text-decoration:underline}

.trust-badges{display:flex;justify-content:center;gap:20px;margin-top:20px;flex-wrap:wrap}
.trust-badge{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted)}
.trust-badge i{color:var(--primary);font-size:12px}

@media(max-width:480px){
  .auth-body{padding:20px}
  .auth-card-head{padding:18px 20px}
  .auth-footer{padding:14px 20px 18px}
  .auth-page{padding:20px 12px;align-items:flex-start;padding-top:30px}
}
</style>
@endpush

@section('content')
<div class="auth-page">
  <div class="auth-box">

    {{-- Brand --}}
    <div class="auth-brand">
      <a href="{{ route('home') }}" class="auth-brand-logo">
        <div class="auth-brand-icon">🛍️</div>
        <div class="auth-brand-name">Go<span>Bazaar</span></div>
      </a>
      <p>Canada's #1 Community Marketplace</p>
    </div>

    <div class="auth-card">
      <div class="auth-card-head">
        <h1 style="font-family:var(--fh);font-size:20px;font-weight:800;color:#fff;margin-bottom:3px">Welcome Back 🙏</h1>
        <p>Login to your GoBazaar account</p>
      </div>

      <div class="auth-body">
        @if(session('success'))
          <div style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;border-radius:8px;padding:11px 14px;margin-bottom:18px;font-size:13px;display:flex;align-items:center;gap:8px">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
          </div>
        @endif

        @if(session('unverified_email'))
          <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:13px 14px;margin-bottom:18px;font-size:13px">
            <div style="display:flex;align-items:center;gap:7px;color:#c2410c;font-weight:700;margin-bottom:8px">
              <i class="fa-solid fa-envelope-circle-check"></i> Email not verified
            </div>
            <div style="color:#78350f;margin-bottom:10px">Your email address has not been verified yet. Please check your inbox for the verification link.</div>
            <form action="{{ route('verification.send') }}" method="POST" style="display:flex;gap:7px">
              @csrf
              <input type="hidden" name="email" value="{{ session('unverified_email') }}">
              <button type="submit" style="background:var(--primary);color:#fff;border:none;padding:8px 16px;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer;flex-shrink:0">
                <i class="fa-solid fa-rotate" style="margin-right:4px"></i>Resend Link
              </button>
              <span style="font-size:11.5px;color:#78350f;align-self:center">We'll resend the verification email to <strong>{{ session('unverified_email') }}</strong></span>
            </form>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
          @csrf

          <div class="form-group">
            <label class="form-label"><i class="fa-solid fa-envelope"></i> Email Address</label>
            <div class="input-wrap">
              <i class="fa-regular fa-envelope"></i>
              <input type="email" name="email" value="{{ old('email') }}"
                class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                placeholder="you@example.com" required autocomplete="email">
            </div>
            @error('email')
              <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label class="form-label"><i class="fa-solid fa-lock"></i> Password</label>
            <div class="input-wrap">
              <i class="fa-solid fa-lock"></i>
              <input type="password" name="password"
                class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                placeholder="Enter your password" required autocomplete="current-password">
            </div>
            @error('password')
              <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror
          </div>

          <div class="remember-row">
            <label>
              <input type="checkbox" name="remember"> Remember me
            </label>
            <a href="{{ route('password.request') }}" style="font-size:12px;color:var(--primary)">Forgot Password?</a>
          </div>

          <button type="submit" class="btn-login">
            <i class="fa-solid fa-right-to-bracket"></i> Login to GoBazaar
          </button>
        </form>
      </div>

      <div class="auth-footer">
        Don't have an account? <a href="{{ route('register') }}">Register Free →</a>
      </div>
    </div>

    <div class="trust-badges">
      <div class="trust-badge"><i class="fa-solid fa-shield-halved"></i> Secure login</div>
      <div class="trust-badge"><i class="fa-solid fa-users"></i> 1.6M+ community</div>
      <div class="trust-badge"><i class="fa-solid fa-circle-check"></i> Free to join</div>
    </div>

  </div>
</div>
@endsection
