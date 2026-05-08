@extends('layouts.app')

@section('title', 'Upgrade to ' . ucfirst($plan) . ' — GoBazzar')

@push('styles')
<style>
.upgrade-wrap{max-width:560px;margin:48px auto;padding:0 20px 48px}
.upgrade-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);padding:36px;box-shadow:var(--sh)}
.upgrade-card h1{font-family:var(--fh);font-size:22px;font-weight:800;margin-bottom:6px}
.upgrade-card .sub{color:var(--muted);font-size:13px;margin-bottom:28px;padding-bottom:28px;border-bottom:1px solid var(--border)}
.plan-summary{background:var(--bg);border-radius:var(--r);padding:16px;margin-bottom:24px;font-size:13px}
.plan-summary .row{display:flex;justify-content:space-between;padding:4px 0}
.plan-summary .row strong{color:var(--text)}
.plan-summary .row span{color:var(--muted)}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.form-group input,.form-group textarea,.form-group select{width:100%;border:1.5px solid var(--border2);border-radius:var(--r);padding:10px 12px;font-size:13px;color:var(--text);transition:border .15s}
.form-group input:focus,.form-group textarea:focus{border-color:var(--red);outline:none}
.form-group textarea{resize:vertical;min-height:80px}
.btn-submit{width:100%;background:var(--red);color:#fff;border:none;border-radius:var(--r);padding:13px;font-size:14px;font-weight:600;cursor:pointer;transition:background .15s;margin-top:8px}
.btn-submit:hover{background:var(--red-dark)}
.info-note{background:var(--blue-bg);border:1px solid #bfdbfe;border-radius:var(--r);padding:12px 14px;font-size:12px;color:#1e40af;margin-bottom:20px;line-height:1.6}
.back-link{display:inline-flex;align-items:center;gap:5px;color:var(--muted);font-size:13px;margin-bottom:20px;transition:color .15s}
.back-link:hover{color:var(--red)}
</style>
@endpush

@section('content')
<div class="upgrade-wrap">
  <a href="{{ route('pricing') }}" class="back-link">← Back to Plans</a>

  <div class="upgrade-card">
    <h1>Upgrade to {{ ucfirst($plan) }}</h1>
    <p class="sub">Fill out the form below and our team will upgrade your account within 24 hours.</p>

    <div class="plan-summary">
      @php
        $details = [
          'basic'    => ['price' => '$9/month',  'duration' => '30 days per post', 'features' => '1 business listing'],
          'premium'  => ['price' => '$19/month', 'duration' => '90 days per post', 'features' => 'Featured placement + 3 business listings'],
          'business' => ['price' => '$49/month', 'duration' => 'Permanent posts',  'features' => 'Unlimited + analytics + account manager'],
        ];
        $d = $details[$plan];
      @endphp
      <div class="row"><span>Plan</span><strong>{{ ucfirst($plan) }}</strong></div>
      <div class="row"><span>Price</span><strong>{{ $d['price'] }}</strong></div>
      <div class="row"><span>Post duration</span><strong>{{ $d['duration'] }}</strong></div>
      <div class="row"><span>Includes</span><strong>{{ $d['features'] }}</strong></div>
    </div>

    <div class="info-note">
      💡 Online payments are coming soon. For now, we process upgrades manually via email or WhatsApp. Submit this form and we'll confirm your upgrade within 24 hours.
    </div>

    @if(session('success'))
      <div style="background:var(--green-bg);border:1px solid #bbf7d0;border-radius:var(--r);padding:12px 14px;font-size:13px;color:var(--green);margin-bottom:16px">
        ✓ {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('pricing.request') }}" method="POST">
      @csrf
      <input type="hidden" name="plan" value="{{ $plan }}">

      <div class="form-group">
        <label>Your Name</label>
        <input type="text" name="name" value="{{ auth()->user()->name }}" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ auth()->user()->email }}" required>
      </div>

      <div class="form-group">
        <label>Phone / WhatsApp</label>
        <input type="text" name="phone" value="{{ auth()->user()->phone }}" placeholder="+1 (416) 555-0123">
      </div>

      <div class="form-group">
        <label>Message (optional)</label>
        <textarea name="message" placeholder="Any questions or special requirements…"></textarea>
      </div>

      <button type="submit" class="btn-submit">📩 Send Upgrade Request</button>
    </form>
  </div>
</div>
@endsection
