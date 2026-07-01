@extends('layouts.app')
@section('title', 'Upgrade to ' . ucfirst($plan) . ' — GoBazaar')

@push('styles')
<style>
.upgrade-wrap{max-width:960px;margin:40px auto;padding:0 20px 60px;display:grid;grid-template-columns:1fr 380px;gap:28px;align-items:start}
.upgrade-left{}
.back-link{display:inline-flex;align-items:center;gap:6px;color:var(--muted);font-size:13px;margin-bottom:20px;text-decoration:none;transition:color .15s}
.back-link:hover{color:var(--primary)}
.upgrade-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-lg);padding:32px;box-shadow:0 4px 20px rgba(0,0,0,.06)}
.upgrade-card h1{font-family:var(--fh);font-size:22px;font-weight:800;color:var(--text);margin-bottom:6px}
.upgrade-card .sub{color:var(--muted);font-size:13px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--border)}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:11.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px}
.form-group input,.form-group textarea{width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:10px 13px;font-size:13.5px;color:var(--text);font-family:var(--fb);transition:border .15s;background:#fff}
.form-group input:focus,.form-group textarea:focus{border-color:var(--primary);outline:none;box-shadow:0 0 0 3px rgba(26,58,143,.08)}
.form-group textarea{resize:vertical;min-height:90px}
.btn-submit{width:100%;background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);padding:13px;font-size:14px;font-weight:700;cursor:pointer;transition:background .2s;margin-top:6px;font-family:var(--fb);display:flex;align-items:center;justify-content:center;gap:8px}
.btn-submit:hover{background:var(--primary-dark)}
.info-note{background:#eff6ff;border:1px solid #bfdbfe;border-radius:var(--radius-sm);padding:13px 15px;font-size:12.5px;color:#1e40af;margin-bottom:20px;line-height:1.6;display:flex;gap:10px;align-items:flex-start}
.info-note i{flex-shrink:0;margin-top:1px;color:#3b82f6}

/* Plan summary card */
.plan-summary-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-lg);padding:28px;box-shadow:0 4px 20px rgba(0,0,0,.06);position:sticky;top:80px}
.plan-summary-head{display:flex;align-items:center;gap:12px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border)}
.plan-summary-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0}
.plan-summary-name{font-family:var(--fh);font-size:18px;font-weight:800;color:var(--text)}
.plan-summary-tagline{font-size:12px;color:var(--muted);margin-top:2px}
.plan-price-row{background:var(--primary-light);border-radius:var(--radius-sm);padding:14px 16px;margin-bottom:18px;display:flex;align-items:flex-end;gap:4px}
.plan-price-row .amount{font-family:var(--fh);font-size:36px;font-weight:800;color:var(--primary);line-height:1}
.plan-price-row sup{font-size:16px;font-weight:700;color:var(--primary);margin-top:6px}
.plan-price-row .per{font-size:13px;color:var(--primary);font-weight:500;margin-bottom:4px;margin-left:2px}
.plan-feature-list{display:flex;flex-direction:column;gap:8px;margin-bottom:20px}
.plan-feature-item{display:flex;align-items:flex-start;gap:9px;font-size:13px;color:var(--text)}
.plan-feature-item i{color:#16a34a;font-size:13px;flex-shrink:0;margin-top:1px}
.plan-feature-item strong{color:var(--primary)}
.guarantee{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-sm);padding:11px 13px;font-size:12px;color:#15803d;display:flex;align-items:center;gap:8px}
.guarantee i{flex-shrink:0}

@media(max-width:768px){
  .upgrade-wrap{grid-template-columns:1fr;gap:20px}
  .plan-summary-card{position:static;order:-1}
}
</style>
@endpush

@section('content')
<div class="upgrade-wrap">

  {{-- LEFT: FORM --}}
  <div class="upgrade-left">
    <a href="{{ route('pricing') }}" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Back to Plans
    </a>

    <div class="upgrade-card">
      <h1>Upgrade to {{ $planModel->name }}</h1>
      <p class="sub">Fill out the form below and our team will upgrade your account within 24 hours.</p>

      <div class="info-note">
        <i class="fa-solid fa-circle-info"></i>
        <span>Online payments are coming soon. For now, we process upgrades manually via email or WhatsApp. Submit this form and we'll confirm your upgrade within 24 hours.</span>
      </div>

      @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-sm);padding:13px 15px;font-size:13px;color:#15803d;margin-bottom:18px;display:flex;align-items:center;gap:8px">
          <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
      @endif

      <form action="{{ route('pricing.request') }}" method="POST">
        @csrf
        <input type="hidden" name="plan" value="{{ $plan }}">

        <div class="form-group">
          <label>Your Name</label>
          <input type="text" name="name" value="{{ auth()->user()->name ?? '' }}" required placeholder="Full name">
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}" required placeholder="your@email.com">
        </div>

        <div class="form-group">
          <label>Phone / WhatsApp</label>
          <input type="text" name="phone" value="{{ auth()->user()->phone ?? '' }}" placeholder="+1 (416) 555-0123">
        </div>

        <div class="form-group">
          <label>Message (optional)</label>
          <textarea name="message" placeholder="Any questions or special requirements…"></textarea>
        </div>

        <button type="submit" class="btn-submit">
          <i class="fa-solid fa-paper-plane"></i> Send Upgrade Request
        </button>
      </form>
    </div>
  </div>

  {{-- RIGHT: PLAN SUMMARY --}}
  <div>
    <div class="plan-summary-card">
      <div class="plan-summary-head">
        <div class="plan-summary-icon" style="background:{{ $planModel->icon_bg }}">{{ $planModel->icon }}</div>
        <div>
          <div class="plan-summary-name">{{ $planModel->name }}</div>
          <div class="plan-summary-tagline">{{ $planModel->tagline }}</div>
        </div>
      </div>

      <div class="plan-price-row">
        <sup>$</sup>
        <div class="amount">{{ number_format($planModel->price, 2) }}</div>
        <div class="per">/ {{ $planModel->period }}</div>
      </div>

      <div class="plan-feature-list">
        @foreach($planModel->features ?? [] as $feature)
          @if($feature['included'])
          <div class="plan-feature-item">
            <i class="fa-solid fa-check"></i>
            @if($feature['highlight'] ?? false)
              <span><strong>{{ $feature['text'] }}</strong></span>
            @else
              <span>{{ $feature['text'] }}</span>
            @endif
          </div>
          @endif
        @endforeach
      </div>

      <div class="guarantee">
        <i class="fa-solid fa-shield-halved"></i>
        Activated within 24 hours · Cancel anytime
      </div>
    </div>
  </div>

</div>
@endsection
