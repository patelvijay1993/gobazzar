@extends('layouts.app')
@section('title', 'Register Free — GoBazaar')

@push('styles')
<style>
.auth-page{min-height:calc(100vh - 120px);background:#f5f7fb;display:flex;align-items:center;justify-content:center;padding:30px 16px;position:relative;overflow:hidden}
.auth-page::before{display:none}

.auth-box{width:100%;max-width:480px;position:relative;z-index:1}

.auth-brand{text-align:center;margin-bottom:22px}
.auth-brand-logo{display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:8px}
.auth-brand-icon{width:44px;height:44px;background:var(--primary-light);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px}
.auth-brand-name{font-family:var(--fh);font-size:26px;font-weight:800;color:var(--primary);line-height:1}
.auth-brand-name span{color:var(--accent)}
.auth-brand p{font-size:13px;color:var(--muted)}

.auth-card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.auth-card-head{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);padding:22px 28px;text-align:center}
.auth-card-head h2{font-family:var(--fh);font-size:20px;font-weight:800;color:#fff;margin-bottom:3px}
.auth-card-head p{font-size:12.5px;color:rgba(255,255,255,.65)}

.auth-body{padding:26px 28px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}

.form-group{margin-bottom:16px}
.form-label{display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px;letter-spacing:.3px}
.form-label i{color:var(--primary);font-size:13px}

.input-wrap{position:relative}
.input-wrap i.input-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:14px;pointer-events:none}
.form-input{width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:11px 14px 11px 38px;font-size:13.5px;transition:border .15s,box-shadow .15s;background:#f9fafb;color:#111;font-family:var(--fb)}
.form-input:focus{border-color:var(--primary);outline:none;background:#fff;box-shadow:0 0 0 3px rgba(26,58,143,.1)}
.form-input.is-error{border-color:#ef4444;background:#fef2f2}
.form-input.no-icon{padding-left:14px}
.error-msg{font-size:11.5px;color:#ef4444;margin-top:5px;display:flex;align-items:center;gap:4px}
.error-msg i{font-size:11px}

.btn-register{width:100%;padding:13px;background:var(--accent);color:#fff;border-radius:9px;font-size:15px;font-weight:800;font-family:var(--fh);cursor:pointer;transition:opacity .2s,transform .1s;border:none;display:flex;align-items:center;justify-content:center;gap:8px;letter-spacing:.2px;margin-top:4px}
.btn-register:hover{opacity:.9}
.btn-register:active{transform:scale(.98)}

.free-note{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;font-size:12px;color:#16a34a;display:flex;align-items:center;gap:8px;margin-bottom:18px;font-weight:500}
.free-note i{font-size:14px;flex-shrink:0}

.auth-footer{text-align:center;padding:16px 28px 20px;font-size:13.5px;color:#6b7280;border-top:1px solid #f3f4f6}
.auth-footer a{color:var(--primary);font-weight:700;text-decoration:none}
.auth-footer a:hover{text-decoration:underline}

.trust-badges{display:flex;justify-content:center;gap:20px;margin-top:20px;flex-wrap:wrap}
.trust-badge{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--muted)}
.trust-badge i{color:var(--primary);font-size:12px}

@media(max-width:480px){
  .auth-body{padding:20px}
  .auth-card-head{padding:18px 20px}
  .form-row{grid-template-columns:1fr}
  .auth-footer{padding:14px 20px 18px}
  .auth-page{padding:20px 12px;align-items:flex-start;padding-top:24px}
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
      <p>Join Canada's #1 Community Marketplace</p>
    </div>

    <div class="auth-card">
      <div class="auth-card-head">
        <h1 style="font-family:var(--fh);font-size:20px;font-weight:800;color:#fff;margin-bottom:3px">Create Free Account 🇮🇳</h1>
        <p>Post ads, find jobs, connect with your community</p>
      </div>

      <div class="auth-body">
        <div class="free-note">
          <i class="fa-solid fa-circle-check"></i>
          100% Free — No credit card required. Post ads instantly after signup.
        </div>

        <form method="POST" action="{{ route('register') }}">
          @csrf

          <div class="form-group">
            <label class="form-label"><i class="fa-solid fa-user"></i> Full Name</label>
            <div class="input-wrap">
              <i class="fa-regular fa-user input-icon"></i>
              <input type="text" name="name" value="{{ old('name') }}"
                class="form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                placeholder="Your full name" required autocomplete="name">
            </div>
            @error('name')
              <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label class="form-label"><i class="fa-solid fa-envelope"></i> Email Address</label>
            <div class="input-wrap">
              <i class="fa-regular fa-envelope input-icon"></i>
              <input type="email" name="email" value="{{ old('email') }}"
                class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                placeholder="you@example.com" required autocomplete="email">
            </div>
            @error('email')
              <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
            @enderror
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label"><i class="fa-solid fa-phone"></i> Phone <span style="font-weight:400;color:#9ca3af">(Optional)</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-phone input-icon"></i>
                <input type="text" name="phone" value="{{ old('phone') }}"
                  class="form-input" placeholder="+1 647 xxx xxxx">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label"><i class="fa-solid fa-location-dot"></i> City <span style="font-weight:400;color:#9ca3af">(Optional)</span></label>
              <div class="input-wrap">
                <i class="fa-solid fa-location-dot input-icon"></i>
                <input type="text" name="city" value="{{ old('city') }}"
                  class="form-input" placeholder="Toronto, Brampton...">
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label"><i class="fa-solid fa-lock"></i> Password</label>
              <div class="input-wrap">
                <i class="fa-solid fa-lock input-icon"></i>
                <input type="password" name="password"
                  class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                  placeholder="Min 8 characters" required>
              </div>
              @error('password')
                <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
              @enderror
            </div>
            <div class="form-group">
              <label class="form-label"><i class="fa-solid fa-lock"></i> Confirm Password</label>
              <div class="input-wrap">
                <i class="fa-solid fa-lock input-icon"></i>
                <input type="password" name="password_confirmation"
                  class="form-input" placeholder="Repeat password" required>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-register">
            <i class="fa-solid fa-user-plus"></i> Create Free Account →
          </button>
        </form>
      </div>

      <div class="auth-footer">
        Already have an account? <a href="{{ route('login') }}">Login here →</a>
      </div>
    </div>

    <div class="trust-badges">
      <div class="trust-badge"><i class="fa-solid fa-shield-halved"></i> Secure & private</div>
      <div class="trust-badge"><i class="fa-solid fa-bolt"></i> Instant publishing</div>
      <div class="trust-badge"><i class="fa-solid fa-circle-check"></i> No spam ever</div>
    </div>

  </div>
</div>
@endsection
