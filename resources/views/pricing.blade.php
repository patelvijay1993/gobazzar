@extends('layouts.app')
@section('title', 'Pricing — GoBazaar')

@push('styles')
<style>
/* ── BASE ── */
.pricing-page-root{max-width:100vw;overflow-x:hidden}

/* ── HERO ── */
.pricing-hero{background:var(--primary);padding:48px 20px;text-align:center;position:relative;overflow:hidden;width:100%;box-sizing:border-box}
.pricing-hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");pointer-events:none}
.pricing-hero h1{font-family:var(--fh);font-size:32px;font-weight:800;color:#fff;margin-bottom:10px;position:relative}
.pricing-hero p{color:rgba(255,255,255,.7);font-size:15px;max-width:500px;margin:0 auto;position:relative}
.pricing-hero-badges{display:flex;justify-content:center;gap:20px;margin-top:20px;flex-wrap:wrap;position:relative}
.hero-badge{display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.12);color:rgba(255,255,255,.85);font-size:12px;font-weight:500;padding:6px 14px;border-radius:20px;border:1px solid rgba(255,255,255,.15)}
.hero-badge i{color:var(--accent);font-size:13px}

/* ── WRAP ── */
.pricing-wrap{max-width:1200px;margin:40px auto;padding:0 20px;box-sizing:border-box;width:100%}

/* ── CURRENT PLAN ── */
.current-plan-bar{background:#eff6ff;border:1px solid #bfdbfe;border-radius:var(--radius);padding:14px 18px;margin-bottom:28px;font-size:13px;display:flex;align-items:center;gap:10px;color:#1d4ed8}
.current-plan-bar i{font-size:18px;color:#1d4ed8;flex-shrink:0}

/* ── PLANS GRID ── */
.plans-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:50px;max-width:900px;margin-left:auto;margin-right:auto;width:100%;box-sizing:border-box}
.plan-card{box-sizing:border-box;width:100%}

.plan-card{background:#fff;border:1.5px solid var(--border);border-radius:var(--radius-lg);padding:28px 22px;position:relative;transition:box-shadow .2s,transform .2s;display:flex;flex-direction:column;margin-top:14px}
.plan-card:hover{box-shadow:0 8px 32px rgba(26,58,143,.12);transform:translateY(-3px)}
.plan-card.popular{border-color:var(--primary);box-shadow:0 4px 24px rgba(26,58,143,.18)}

.popular-badge{position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:var(--primary);color:#fff;font-size:10px;font-weight:700;padding:4px 12px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;display:flex;align-items:center;gap:5px;max-width:90%}

.plan-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:14px}
.plan-name{font-family:var(--fh);font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.9px;margin-bottom:8px}
.plan-price{font-family:var(--fh);font-size:38px;font-weight:800;color:var(--text);line-height:1;margin-bottom:4px;display:flex;align-items:flex-start;gap:2px}
.plan-price sup{font-size:18px;margin-top:8px;font-weight:700}
.plan-price .period{font-size:14px;font-weight:400;color:var(--muted);align-self:flex-end;margin-bottom:4px;margin-left:2px}
.plan-tagline{font-size:12.5px;color:var(--muted);margin-bottom:20px;padding-bottom:18px;border-bottom:1px solid var(--border)}

.plan-features{list-style:none;margin-bottom:24px;flex:1;display:flex;flex-direction:column;gap:9px}
.plan-features li{font-size:13px;color:var(--text);display:flex;align-items:flex-start;gap:9px;line-height:1.4}
.plan-features li .check{color:#16a34a;font-size:13px;flex-shrink:0;margin-top:1px}
.plan-features li .cross{color:#d1d5db;font-size:13px;flex-shrink:0;margin-top:1px}
.plan-features li.faded{color:#9ca3af}
.plan-features li strong{color:var(--primary)}

.plan-cta{display:block;text-align:center;padding:12px;border-radius:var(--radius-sm);font-size:13.5px;font-weight:700;transition:all .2s;text-decoration:none;margin-top:auto}
.cta-primary{background:var(--primary);color:#fff}
.cta-primary:hover{background:var(--primary-dark);color:#fff}
.cta-accent{background:var(--accent);color:#fff}
.cta-accent:hover{opacity:.88;color:#fff}
.cta-outline{border:2px solid var(--border);color:var(--muted)}
.cta-outline:hover{border-color:var(--primary);color:var(--primary)}
.cta-current{background:#dcfce7;color:#15803d;border:2px solid #bbf7d0;cursor:default}

/* ── COMPARE TABLE ── */
.compare-section{margin-bottom:50px}
.compare-section h2{font-family:var(--fh);font-size:20px;font-weight:700;text-align:center;margin-bottom:20px;color:var(--text)}
.compare-table{width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.compare-table th{background:var(--primary);color:#fff;padding:12px 16px;font-size:12.5px;font-weight:700;text-align:center}
.compare-table th:first-child{text-align:left}
.compare-table td{padding:11px 16px;font-size:13px;border-bottom:1px solid var(--border);text-align:center;color:var(--text)}
.compare-table td:first-child{text-align:left;font-weight:500;color:var(--muted)}
.compare-table tr:last-child td{border-bottom:none}
.compare-table tr:hover td{background:#f8faff}
.compare-table .yes{color:#16a34a;font-size:15px}
.compare-table .no{color:#d1d5db;font-size:15px}

/* ── FAQ ── */
.faq-section{max-width:760px;margin:0 auto 50px}
.faq-section h2{font-family:var(--fh);font-size:22px;font-weight:700;text-align:center;margin-bottom:24px;color:var(--text)}
.faq-item{background:#fff;border:1px solid var(--border);border-radius:var(--radius);margin-bottom:10px;overflow:hidden}
.faq-q{padding:15px 18px;font-size:13.5px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:12px;user-select:none;color:var(--text)}
.faq-q:hover{background:var(--primary-light);color:var(--primary)}
.faq-icon{width:24px;height:24px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;transition:transform .25s,background .2s}
.faq-item.open .faq-icon{transform:rotate(45deg);background:var(--primary);color:#fff}
.faq-a{padding:0 18px;font-size:13px;color:var(--muted);line-height:1.7;max-height:0;overflow:hidden;transition:max-height .3s,padding .3s}
.faq-item.open .faq-a{max-height:200px;padding:0 18px 16px}

/* ── PROMO CODE ── */
.promo-box{max-width:520px;margin:0 auto 40px;background:#fff;border:2px dashed var(--border);border-radius:var(--radius-lg);padding:24px 28px;text-align:center}
.promo-box h3{font-family:var(--fh);font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px}
.promo-box p{font-size:12.5px;color:var(--muted);margin-bottom:16px}
.promo-row{display:flex;gap:10px}
.promo-row input{flex:1;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:10px 14px;font-size:13.5px;font-family:inherit;text-transform:uppercase;letter-spacing:.5px;outline:none;transition:border-color .2s}
.promo-row input:focus{border-color:var(--primary)}
.promo-row button{background:var(--primary);color:#fff;border:none;border-radius:var(--radius-sm);padding:10px 22px;font-size:13.5px;font-weight:700;cursor:pointer;white-space:nowrap;transition:background .2s}
.promo-row button:hover{background:var(--primary-dark)}
.promo-msg{margin-top:10px;font-size:12.5px;font-weight:600;display:none}
.promo-msg.ok{color:#16a34a}.promo-msg.err{color:#dc2626}

/* ── CONTACT CTA ── */
.contact-cta{background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);border-radius:var(--radius-lg);padding:44px;text-align:center;margin-bottom:48px;position:relative;overflow:hidden}
.contact-cta::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4z'/%3E%3C/g%3E%3C/svg%3E');pointer-events:none}
.contact-cta h3{font-family:var(--fh);font-size:24px;font-weight:800;color:#fff !important;margin-bottom:10px;position:relative}
.contact-cta p{color:rgba(255,255,255,.75) !important;font-size:14px;margin-bottom:22px;position:relative}
.contact-cta a{background:var(--accent);color:#fff !important;padding:13px 30px;border-radius:var(--radius-sm);font-size:14px;font-weight:700;display:inline-flex;align-items:center;gap:8px;transition:opacity .2s;position:relative}
.contact-cta a:hover{opacity:.88;color:#fff !important}

/* ── RESPONSIVE ── */
@media(max-width:1024px){
  .plans-grid{grid-template-columns:1fr !important;max-width:480px}
  .compare-section{display:none}
}
@media(max-width:600px){
  .pricing-wrap{padding:0 12px}
  .plans-grid{max-width:100%;gap:20px}
  .plan-card{padding:20px 16px}
  .pricing-hero{padding:28px 14px}
  .pricing-hero h1{font-size:22px;word-break:break-word}
  .pricing-hero p{font-size:13px}
  .pricing-hero-badges{gap:8px;justify-content:center}
  .hero-badge{font-size:11px;padding:5px 10px}
  .plan-price{font-size:30px}
  .promo-box{padding:16px 14px}
  .promo-row{flex-direction:column;gap:8px}
  .promo-row input,.promo-row button{width:100%;box-sizing:border-box}
  .contact-cta{padding:24px 16px}
  .contact-cta h3{font-size:18px}
  .faq-section{padding:0}
  .current-plan-bar{font-size:12px;flex-wrap:wrap}
}
</style>
@endpush

@section('content')
<div class="pricing-page-root">

{{-- HERO --}}
<div class="pricing-hero">
  <h1>Simple, Transparent Pricing</h1>
  <p>Post ads, list businesses, and reach the Indian-Canadian community. Upgrade anytime.</p>
  <div class="pricing-hero-badges">
    <div class="hero-badge"><i class="fa-solid fa-circle-check"></i> No hidden fees</div>
    <div class="hero-badge"><i class="fa-solid fa-bolt"></i> Instant activation</div>
    <div class="hero-badge"><i class="fa-solid fa-rotate-left"></i> Cancel anytime</div>
    <div class="hero-badge"><i class="fa-solid fa-users"></i> 1.6M+ community</div>
  </div>
</div>

<div class="pricing-wrap">

  @auth
  <div class="current-plan-bar">
    <i class="fa-solid fa-id-card"></i>
    <div>
      You are on the <strong>{{ ucfirst(auth()->user()->activePlan()) }}</strong> plan.
      @if(auth()->user()->activePlan() === 'power_seller')
        Your listings <strong>auto-renew</strong> and never expire.
      @elseif(auth()->user()->isSubscribed() && auth()->user()->plan_expires_at)
        Plan expires on <strong>{{ auth()->user()->plan_expires_at->format('M d, Y') }}</strong>.
        Your posts stay live for <strong>{{ auth()->user()->postDays() }} days</strong>.
      @else
        Your posts stay live for <strong>3 days</strong>.
        ({{ auth()->user()->activeListingCount() }}/{{ auth()->user()->maxListings() }} active listings used)
      @endif
    </div>
  </div>
  @endauth

  {{-- PLANS GRID (dynamic from DB) --}}
  <div class="plans-grid">
    @foreach($plans as $plan)
    @php
      $userPlan = auth()->check() ? auth()->user()->activePlan() : null;
      $isCurrent = $userPlan === $plan->slug;
      $isFree = $plan->slug === 'free';
      $ctaClass = $plan->slug === 'business' ? 'cta-accent' : ($plan->is_popular ? 'cta-primary' : 'cta-outline');
    @endphp
    <div class="plan-card {{ $plan->is_popular ? 'popular' : '' }}">
      @if($plan->is_popular)
        <span class="popular-badge"><i class="fa-solid fa-star" style="font-size:9px"></i> Most Popular</span>
      @endif

      <div class="plan-icon" style="background:{{ $plan->icon_bg }}">{{ $plan->icon }}</div>
      <div class="plan-name">{{ $plan->name }}</div>
      <div class="plan-price">
        <sup>$</sup>{{ number_format($plan->price, 2) }}
        <span class="period">/ {{ $plan->period }}</span>
      </div>
      <div class="plan-tagline">{{ $plan->tagline }}</div>

      <ul class="plan-features">
        @foreach($plan->features ?? [] as $feature)
        <li class="{{ $feature['included'] ? '' : 'faded' }}">
          @if($feature['included'])
            <i class="fa-solid fa-check check"></i>
          @else
            <i class="fa-solid fa-xmark cross"></i>
          @endif
          @if($feature['highlight'] ?? false)
            <strong>{{ $feature['text'] }}</strong>
          @else
            {{ $feature['text'] }}
          @endif
        </li>
        @endforeach
      </ul>

      @auth
        @if($isCurrent)
          <span class="plan-cta cta-current"><i class="fa-solid fa-circle-check"></i> Current Plan</span>
        @elseif($isFree)
          <span class="plan-cta cta-outline" style="cursor:default">Your Free Plan</span>
        @elseif($plan->stripe_price_id)
          <a href="{{ route('stripe.checkout', $plan->slug) }}" class="plan-cta {{ $ctaClass }}">
            <i class="fa-brands fa-stripe" style="margin-right:5px"></i>Upgrade — ${{ number_format($plan->price, 2) }}/mo
          </a>
        @else
          <a href="{{ route('pricing.upgrade', $plan->slug) }}" class="plan-cta {{ $ctaClass }}">Upgrade to {{ $plan->name }}</a>
        @endif

        {{-- Cancel subscription --}}
        @if($isCurrent && auth()->user()->stripe_subscription_id && auth()->user()->subscription_status === 'active')
          <form action="{{ route('stripe.cancel') }}" method="POST" style="margin-top:8px"
            onsubmit="return confirm('Cancel your subscription? You will keep access until the end of the billing period.')">
            @csrf
            <button type="submit" style="width:100%;background:none;border:none;color:#ef4444;font-size:12px;cursor:pointer;padding:6px;text-decoration:underline">
              Cancel Subscription
            </button>
          </form>
        @endif
      @else
        @if($isFree)
          <a href="{{ route('register') }}" class="plan-cta cta-outline">Get Started Free</a>
        @else
          <a href="{{ route('register') }}" class="plan-cta {{ $ctaClass }}">
            <i class="fa-brands fa-stripe" style="margin-right:5px"></i>Get Started
          </a>
        @endif
      @endauth
    </div>
    @endforeach
  </div>

  {{-- PROMO CODE --}}
  @auth
  <div class="promo-box">
    <h3>Have a Promo Code?</h3>
    <p>Enter your code below to activate a free plan upgrade.</p>
    @if(session('promo_success'))
      <div class="promo-msg ok" style="display:block">{{ session('promo_success') }}</div>
    @endif
    @if(session('promo_error'))
      <div class="promo-msg err" style="display:block">{{ session('promo_error') }}</div>
    @endif
    <form method="POST" action="{{ route('promo.apply') }}" id="promo-form">
      @csrf
      <div class="promo-row">
        <input type="text" name="code" id="promo-input" placeholder="ENTER CODE" maxlength="32" autocomplete="off" required>
        <button type="submit">Apply</button>
      </div>
    </form>
  </div>
  @endauth

  {{-- COMPARE TABLE — fully dynamic from DB plans --}}
  <div class="compare-section">
    <h2>Compare Plans</h2>
    <table class="compare-table">
      <thead>
        <tr>
          <th>Feature</th>
          @foreach($plans as $p)
            <th @if($p->is_popular) style="background:#122970" @endif>
              {{ $p->name }} — ${{ number_format($p->price, 0) }}
            </th>
          @endforeach
        </tr>
      </thead>
      <tbody>

        {{-- Active Listings --}}
        <tr>
          <td>Active Listings</td>
          @foreach($plans as $p)
            <td>
              @if($p->unlimited_posts || $p->max_listings >= 9999)
                Unlimited
              @else
                Up to {{ $p->max_listings }}
              @endif
            </td>
          @endforeach
        </tr>

        {{-- Listing Visibility --}}
        <tr>
          <td>Listing Visibility</td>
          @foreach($plans as $p)
            <td>
              @if($p->auto_renew || $p->post_days == 0)
                Auto-Renew (Never expires)
              @else
                {{ $p->post_days }} days
              @endif
            </td>
          @endforeach
        </tr>

        {{-- Photos per Listing --}}
        <tr>
          <td>Photos per Listing</td>
          @foreach($plans as $p)
            <td>{{ $p->max_images }}</td>
          @endforeach
        </tr>

        {{-- Business Directory Listings --}}
        <tr>
          <td>Business Directory Listings</td>
          @foreach($plans as $p)
            <td>
              @if($p->biz_listings == 0)
                <i class="fa-solid fa-xmark no"></i>
              @elseif($p->biz_listings >= 999)
                Unlimited
              @else
                Up to {{ $p->biz_listings }}
              @endif
            </td>
          @endforeach
        </tr>

        {{-- Chat Conversation (always available) --}}
        <tr>
          <td>Chat Conversation</td>
          @foreach($plans as $p)
            <td><i class="fa-solid fa-check yes"></i></td>
          @endforeach
        </tr>

        {{-- Verified Badge --}}
        <tr>
          <td>Verified Badge</td>
          @foreach($plans as $p)
            <td>
              @if($p->verified_badge)
                <i class="fa-solid fa-check yes"></i>
              @else
                <i class="fa-solid fa-xmark no"></i>
              @endif
            </td>
          @endforeach
        </tr>

        {{-- Priority Search Placement --}}
        <tr>
          <td>Priority Search Placement</td>
          @foreach($plans as $p)
            <td>
              @if($p->featured_placement)
                <i class="fa-solid fa-check yes"></i>
              @else
                <i class="fa-solid fa-xmark no"></i>
              @endif
            </td>
          @endforeach
        </tr>

        {{-- Listing Analytics --}}
        <tr>
          <td>Listing Analytics</td>
          @foreach($plans as $p)
            <td>
              @if(!$p->analytics)
                <i class="fa-solid fa-xmark no"></i>
              @elseif($p->priority_support)
                Advanced
              @else
                Basic Insights
              @endif
            </td>
          @endforeach
        </tr>

        {{-- Unlimited Favorites --}}
        <tr>
          <td>Unlimited Favorites</td>
          @foreach($plans as $p)
            <td>
              @if($p->favorites)
                <i class="fa-solid fa-check yes"></i>
              @else
                <i class="fa-solid fa-xmark no"></i>
              @endif
            </td>
          @endforeach
        </tr>

        {{-- Featured Listing Credits --}}
        <tr>
          <td>Featured Listing Credits</td>
          @foreach($plans as $p)
            <td>
              @if($p->featured_credits > 0)
                {{ $p->featured_credits }} / month
              @else
                <i class="fa-solid fa-xmark no"></i>
              @endif
            </td>
          @endforeach
        </tr>

        {{-- Bulk Listing Upload --}}
        <tr>
          <td>Bulk Listing Upload</td>
          @foreach($plans as $p)
            <td>
              @if($p->bulk_upload)
                <i class="fa-solid fa-check yes"></i>
              @else
                <i class="fa-solid fa-xmark no"></i>
              @endif
            </td>
          @endforeach
        </tr>

        {{-- Priority Support --}}
        <tr>
          <td>Priority Support</td>
          @foreach($plans as $p)
            <td>
              @if($p->priority_support)
                <i class="fa-solid fa-check yes"></i>
              @else
                <i class="fa-solid fa-xmark no"></i>
              @endif
            </td>
          @endforeach
        </tr>

      </tbody>
    </table>
  </div>

  {{-- FAQ --}}
  <div class="faq-section">
    <h2>Frequently Asked Questions</h2>
    @foreach([
      ['q'=>'What happens to my Free plan listings after 3 days?','a'=>'Free plan listings are automatically removed after 3 days to keep the marketplace fresh. You can repost anytime at no cost. Upgrade to Verified (30 days) or Power Seller (auto-renew, never expires) for longer visibility.'],
      ['q'=>'What does "Auto Renew" mean on the Power Seller plan?','a'=>'Power Seller listings never expire — they stay live indefinitely and are automatically kept active as long as your subscription is active. No need to repost.'],
      ['q'=>'What is the Verified Badge?','a'=>'The Verified Badge appears on your profile and listings, signaling to buyers that you are a trusted, verified seller. It helps build confidence and typically leads to more inquiries.'],
      ['q'=>'What are Featured Listing Credits?','a'=>'Power Seller members receive 5 Featured Listing Credits per month. Use them to pin your listings to the top of search results for maximum visibility.'],
      ['q'=>'Can I upgrade or downgrade anytime?','a'=>'Yes — you can upgrade anytime and your new plan activates immediately. Downgrading takes effect at the end of your current billing cycle.'],
      ['q'=>'Do you accept online payments?','a'=>'Currently we process upgrades manually. Contact us via email or WhatsApp to upgrade your account. Online payment (Stripe/PayPal) is coming soon.'],
    ] as $faq)
    <div class="faq-item">
      <div class="faq-q">
        {{ $faq['q'] }}
        <span class="faq-icon"><i class="fa-solid fa-plus" style="font-size:12px"></i></span>
      </div>
      <div class="faq-a">{{ $faq['a'] }}</div>
    </div>
    @endforeach
  </div>

  {{-- CONTACT CTA --}}
  <div class="contact-cta" style="color:#fff">
    <h3 style="color:#fff !important">Ready to upgrade?</h3>
    <p style="color:rgba(255,255,255,.8) !important">Contact us and we'll upgrade your account within 24 hours. No automated billing — personal service guaranteed.</p>
    <a href="mailto:admin@gobazaar.ca" style="color:#fff !important"><i class="fa-solid fa-envelope"></i> Email Us to Upgrade</a>
  </div>

</div>{{-- pricing-wrap --}}
</div>{{-- pricing-page-root --}}

<script>
document.querySelectorAll('.faq-q').forEach(q => {
  q.addEventListener('click', () => {
    const item = q.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  });
});
</script>
@endsection
