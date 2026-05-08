@extends('layouts.app')

@section('title', 'Subscription Plans — GoBazzar')

@push('styles')
<style>
.pricing-hero{background:linear-gradient(135deg,var(--dark) 0%,var(--dark2) 100%);color:#fff;padding:56px 20px;text-align:center}
.pricing-hero h1{font-family:var(--fh);font-size:32px;font-weight:800;margin-bottom:10px}
.pricing-hero p{color:rgba(255,255,255,.65);font-size:15px;max-width:500px;margin:0 auto}

.pricing-wrap{max-width:1100px;margin:48px auto;padding:0 20px}

.plans-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:20px;margin-bottom:48px}

.plan-card{background:var(--surface);border:2px solid var(--border);border-radius:var(--rl);padding:28px 24px;position:relative;transition:box-shadow .2s,border-color .2s}
.plan-card:hover{box-shadow:0 6px 28px rgba(26,10,9,.1)}
.plan-card.popular{border-color:var(--red);box-shadow:0 4px 20px rgba(192,57,43,.15)}

.popular-badge{position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:4px 14px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}

.plan-name{font-family:var(--fh);font-size:13px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:10px}
.plan-price{font-family:var(--fh);font-size:36px;font-weight:800;color:var(--text);line-height:1;margin-bottom:4px}
.plan-price sup{font-size:18px;vertical-align:top;margin-top:6px;display:inline-block}
.plan-price span{font-size:14px;font-weight:400;color:var(--muted)}
.plan-tagline{font-size:12px;color:var(--muted);margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border)}

.plan-features{list-style:none;margin-bottom:24px}
.plan-features li{font-size:13px;color:var(--text);padding:5px 0;display:flex;align-items:flex-start;gap:8px}
.plan-features li .check{color:var(--green);font-size:14px;flex-shrink:0;margin-top:1px}
.plan-features li .cross{color:var(--hint);font-size:14px;flex-shrink:0;margin-top:1px}
.plan-features li.faded{color:var(--hint)}

.plan-cta{display:block;text-align:center;padding:11px;border-radius:var(--r);font-size:13px;font-weight:600;transition:all .15s}
.plan-cta-red{background:var(--red);color:#fff}
.plan-cta-red:hover{background:var(--red-dark);color:#fff}
.plan-cta-outline{border:2px solid var(--border2);color:var(--muted)}
.plan-cta-outline:hover{border-color:var(--red);color:var(--red)}
.plan-cta-current{background:var(--green-bg);color:var(--green);border:2px solid var(--green);cursor:default}

.faq-section{max-width:720px;margin:0 auto 48px}
.faq-section h2{font-family:var(--fh);font-size:20px;font-weight:700;margin-bottom:24px;text-align:center}
.faq-item{border:1px solid var(--border);border-radius:var(--r);margin-bottom:10px;overflow:hidden}
.faq-q{padding:14px 16px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:space-between;user-select:none}
.faq-q:hover{background:var(--bg)}
.faq-a{padding:0 16px;font-size:13px;color:var(--muted);line-height:1.7;max-height:0;overflow:hidden;transition:max-height .3s,padding .3s}
.faq-item.open .faq-a{max-height:200px;padding:0 16px 14px}
.faq-item.open .faq-icon{transform:rotate(45deg)}
.faq-icon{transition:transform .25s;font-size:16px;color:var(--muted)}

.contact-cta{background:var(--dark);color:#fff;border-radius:var(--rl);padding:36px;text-align:center;margin-bottom:48px}
.contact-cta h3{font-family:var(--fh);font-size:20px;font-weight:700;margin-bottom:8px}
.contact-cta p{color:rgba(255,255,255,.6);font-size:13px;margin-bottom:20px}
.contact-cta a{background:var(--red);color:#fff;padding:11px 28px;border-radius:var(--r);font-size:13px;font-weight:600;display:inline-block;transition:background .15s}
.contact-cta a:hover{background:var(--red-dark)}

.current-plan-info{background:var(--blue-bg);border:1px solid #bfdbfe;border-radius:var(--r);padding:14px 18px;margin-bottom:28px;font-size:13px;display:flex;align-items:center;gap:10px}
.current-plan-info strong{color:var(--blue)}
</style>
@endpush

@section('content')

<div class="pricing-hero">
  <h1>Simple, Transparent Pricing</h1>
  <p>Post ads, list businesses, and reach the Indian community in Canada. Upgrade anytime.</p>
</div>

<div class="pricing-wrap">

  @auth
  <div class="current-plan-info">
    <span style="font-size:20px">📋</span>
    <div>
      You are currently on the <strong>{{ ucfirst(auth()->user()->activePlan()) }}</strong> plan.
      @if(auth()->user()->isSubscribed() && auth()->user()->plan_expires_at)
        Your subscription expires on <strong>{{ auth()->user()->plan_expires_at->format('M d, Y') }}</strong>.
      @elseif(!auth()->user()->isSubscribed())
        Your posts stay live for <strong>7 days</strong> then are automatically removed.
        <a href="{{ route('pricing') }}" style="color:var(--red);font-weight:600;margin-left:6px">Upgrade →</a>
      @endif
    </div>
  </div>
  @endauth

  <div class="plans-grid">

    {{-- FREE --}}
    <div class="plan-card">
      <div class="plan-name">Free</div>
      <div class="plan-price"><sup>$</sup>0 <span>/ forever</span></div>
      <div class="plan-tagline">Get started at no cost</div>
      <ul class="plan-features">
        <li><span class="check">✓</span> Post classifieds &amp; jobs</li>
        <li><span class="check">✓</span> List matrimonial profile</li>
        <li><span class="check">✓</span> Post community events</li>
        <li><span class="check">✓</span> <strong>7-day</strong> post visibility</li>
        <li class="faded"><span class="cross">✗</span> Featured placement</li>
        <li class="faded"><span class="cross">✗</span> Business directory listing</li>
        <li class="faded"><span class="cross">✗</span> Analytics &amp; insights</li>
      </ul>
      @auth
        @if(auth()->user()->activePlan() === 'free')
          <span class="plan-cta plan-cta-current">✓ Current Plan</span>
        @else
          <span class="plan-cta plan-cta-outline" style="cursor:default">Downgrade</span>
        @endif
      @else
        <a href="{{ route('register') }}" class="plan-cta plan-cta-outline">Get Started Free</a>
      @endauth
    </div>

    {{-- BASIC --}}
    <div class="plan-card">
      <div class="plan-name">Basic</div>
      <div class="plan-price"><sup>$</sup>9 <span>/ month</span></div>
      <div class="plan-tagline">For regular community members</div>
      <ul class="plan-features">
        <li><span class="check">✓</span> Post classifieds, jobs &amp; events</li>
        <li><span class="check">✓</span> List matrimonial profile</li>
        <li><span class="check">✓</span> <strong>30-day</strong> post visibility</li>
        <li><span class="check">✓</span> 1 business directory listing</li>
        <li class="faded"><span class="cross">✗</span> Featured placement</li>
        <li class="faded"><span class="cross">✗</span> Priority support</li>
        <li class="faded"><span class="cross">✗</span> Analytics &amp; insights</li>
      </ul>
      @auth
        @if(auth()->user()->activePlan() === 'basic')
          <span class="plan-cta plan-cta-current">✓ Current Plan</span>
        @else
          <a href="{{ route('pricing.upgrade', 'basic') }}" class="plan-cta plan-cta-outline">Upgrade to Basic</a>
        @endif
      @else
        <a href="{{ route('register') }}" class="plan-cta plan-cta-outline">Get Started</a>
      @endauth
    </div>

    {{-- PREMIUM --}}
    <div class="plan-card popular">
      <span class="popular-badge">Most Popular</span>
      <div class="plan-name">Premium</div>
      <div class="plan-price"><sup>$</sup>19 <span>/ month</span></div>
      <div class="plan-tagline">For active community contributors</div>
      <ul class="plan-features">
        <li><span class="check">✓</span> Unlimited posts across all sections</li>
        <li><span class="check">✓</span> <strong>90-day</strong> post visibility</li>
        <li><span class="check">✓</span> <strong>Featured</strong> placement in listings</li>
        <li><span class="check">✓</span> 3 business directory listings</li>
        <li><span class="check">✓</span> Priority support</li>
        <li class="faded"><span class="cross">✗</span> Analytics &amp; insights</li>
        <li class="faded"><span class="cross">✗</span> Dedicated account manager</li>
      </ul>
      @auth
        @if(auth()->user()->activePlan() === 'premium')
          <span class="plan-cta plan-cta-current">✓ Current Plan</span>
        @else
          <a href="{{ route('pricing.upgrade', 'premium') }}" class="plan-cta plan-cta-red">Upgrade to Premium</a>
        @endif
      @else
        <a href="{{ route('register') }}" class="plan-cta plan-cta-red">Get Started</a>
      @endauth
    </div>

    {{-- BUSINESS --}}
    <div class="plan-card">
      <div class="plan-name">Business</div>
      <div class="plan-price"><sup>$</sup>49 <span>/ month</span></div>
      <div class="plan-tagline">For businesses &amp; power users</div>
      <ul class="plan-features">
        <li><span class="check">✓</span> Everything in Premium</li>
        <li><span class="check">✓</span> <strong>Permanent</strong> post visibility</li>
        <li><span class="check">✓</span> Unlimited business listings</li>
        <li><span class="check">✓</span> Top featured placement</li>
        <li><span class="check">✓</span> Analytics &amp; insights dashboard</li>
        <li><span class="check">✓</span> Dedicated account manager</li>
        <li><span class="check">✓</span> Custom banner advertising</li>
      </ul>
      @auth
        @if(auth()->user()->activePlan() === 'business')
          <span class="plan-cta plan-cta-current">✓ Current Plan</span>
        @else
          <a href="{{ route('pricing.upgrade', 'business') }}" class="plan-cta plan-cta-outline">Upgrade to Business</a>
        @endif
      @else
        <a href="{{ route('register') }}" class="plan-cta plan-cta-outline">Get Started</a>
      @endauth
    </div>

  </div>

  {{-- FAQ --}}
  <div class="faq-section">
    <h2>Frequently Asked Questions</h2>

    <div class="faq-item">
      <div class="faq-q">What happens to my free posts after 7 days? <span class="faq-icon">+</span></div>
      <div class="faq-a">Free plan posts are automatically removed after 7 days to keep listings fresh. You can repost anytime at no cost. Upgrading to a paid plan gives you 30, 90, or permanent visibility.</div>
    </div>

    <div class="faq-item">
      <div class="faq-q">Can I upgrade or downgrade anytime? <span class="faq-icon">+</span></div>
      <div class="faq-a">Yes — you can upgrade anytime and your new plan activates immediately. Downgrading takes effect at the end of your current billing cycle.</div>
    </div>

    <div class="faq-item">
      <div class="faq-q">How does "Featured" placement work? <span class="faq-icon">+</span></div>
      <div class="faq-a">Featured posts appear at the top of listing pages with a highlighted badge. Premium and Business subscribers can mark their posts as featured for maximum visibility.</div>
    </div>

    <div class="faq-item">
      <div class="faq-q">Is there a free trial for paid plans? <span class="faq-icon">+</span></div>
      <div class="faq-a">We offer a 7-day free trial for the Basic and Premium plans. No credit card required to start — contact us to activate your trial.</div>
    </div>

    <div class="faq-item">
      <div class="faq-q">Do you accept payments online? <span class="faq-icon">+</span></div>
      <div class="faq-a">Currently we process upgrades manually. Contact us via email or WhatsApp to upgrade your account. Online payment (Stripe/PayPal) is coming soon.</div>
    </div>
  </div>

  {{-- Contact CTA --}}
  <div class="contact-cta">
    <h3>Ready to upgrade? Contact us</h3>
    <p>Send us a message and we'll upgrade your account within 24 hours.</p>
    <a href="mailto:admin@gobazzar.ca">📧 Email Us to Upgrade</a>
  </div>

</div>

<script>
document.querySelectorAll('.faq-q').forEach(q => {
  q.addEventListener('click', () => {
    q.closest('.faq-item').classList.toggle('open');
  });
});
</script>
@endsection
