@extends('layouts.app')
@section('title', 'Login')

@push('styles')
<style>
.auth-wrap{max-width:440px;margin:40px auto;padding:0 20px}
.auth-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden}
.auth-head{background:var(--dark);padding:24px;text-align:center}
.auth-head h1{font-family:var(--fh);font-size:20px;font-weight:800;color:#fff;margin-bottom:4px}
.auth-head p{font-size:12px;color:rgba(255,255,255,.5)}
.auth-body{padding:28px}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px}
.form-input{width:100%;border:1.5px solid var(--border2);border-radius:var(--r);padding:10px 14px;font-size:13.5px;transition:border .15s;background:var(--surface)}
.form-input:focus{border-color:var(--red);outline:none}
.form-input.error{border-color:var(--red2)}
.error-msg{font-size:11.5px;color:var(--red);margin-top:4px}
.btn-submit{width:100%;padding:13px;background:var(--red);color:#fff;border-radius:var(--r);font-size:14px;font-weight:700;font-family:var(--fh);cursor:pointer;transition:background .15s;border:none}
.btn-submit:hover{background:var(--red-dark)}
.auth-footer{text-align:center;padding:16px 28px;border-top:1px solid var(--border);font-size:13px;color:var(--muted)}
.auth-footer a{color:var(--red);font-weight:600}
.remember-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;font-size:12.5px}
</style>
@endpush

@section('content')
<div class="auth-wrap">
  @if(session('success'))
    <div style="background:var(--green-bg);color:var(--green);border:1px solid #bbf7d0;border-radius:var(--r);padding:12px 16px;margin-bottom:16px;font-size:13px">
      {{ session('success') }}
    </div>
  @endif

  <div class="auth-card">
    <div class="auth-head">
      <h1>Welcome Back 🙏</h1>
      <p>Login to your GoBazzar account</p>
    </div>
    <div class="auth-body">
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" value="{{ old('email') }}"
            class="form-input {{ $errors->has('email') ? 'error' : '' }}" placeholder="you@example.com" required>
          @error('email')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password"
            class="form-input {{ $errors->has('password') ? 'error' : '' }}" placeholder="••••••••" required>
          @error('password')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="remember-row">
          <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
            <input type="checkbox" name="remember"> Remember me
          </label>
        </div>

        <button type="submit" class="btn-submit">Login →</button>
      </form>
    </div>
    <div class="auth-footer">
      Don't have an account? <a href="{{ route('register') }}">Register Free</a>
    </div>
  </div>
</div>
@endsection
