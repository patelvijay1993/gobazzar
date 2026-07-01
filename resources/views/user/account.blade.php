@extends('layouts.app')
@section('title', 'My Account')

@push('styles')
<style>
/* Legacy var bridge → blue theme */
.acct-wrap{
  --red:var(--primary);--red-dark:var(--primary-dark);--red-pale:var(--primary-light);
  --border2:var(--border);--surface:#fff;--bg:#f5f7fb;--hint:#9ca3af;
  --rl:14px;--r:9px;--amber:#92400e;--amber-bg:#fef9c3;--dark:#1a3a8f;
  --blue:#1d4ed8;--green:#16a34a;--green-bg:#dcfce7;
}
.acct-wrap{max-width:1100px;margin:24px auto;padding:0 20px;display:grid;grid-template-columns:260px 1fr;gap:20px;align-items:start}
@media(max-width:768px){.acct-wrap{grid-template-columns:1fr;padding:0 14px}.form-row{grid-template-columns:1fr}.acct-sidebar{position:static}}
.acct-sidebar{background:#fff;border:1px solid var(--border);border-radius:var(--rl);overflow:hidden;position:sticky;top:72px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.acct-profile{padding:26px 20px;text-align:center;background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);color:#fff}
.acct-avatar{width:76px;height:76px;border-radius:50%;object-fit:cover;border:3px solid rgba(255,255,255,.4);margin:0 auto 12px;display:block}
.acct-avatar-placeholder{width:76px;height:76px;border-radius:50%;background:rgba(255,255,255,.18);color:#fff;display:flex;align-items:center;justify-content:center;font-size:30px;font-family:var(--fh);font-weight:800;margin:0 auto 12px;border:3px solid rgba(255,255,255,.3)}
.acct-name{font-family:var(--fh);font-size:16px;font-weight:800;color:#fff}
.acct-email{font-size:11.5px;color:rgba(255,255,255,.7);margin-top:2px}
.acct-loc{font-size:11px;color:rgba(255,255,255,.6);margin-top:5px;display:inline-flex;align-items:center;gap:4px}
.acct-menu{padding:10px 0}
.acct-mi{display:flex;align-items:center;gap:11px;padding:11px 20px;font-size:13.5px;cursor:pointer;transition:all .12s;color:var(--text);border-left:3px solid transparent;text-decoration:none}
.acct-mi i{width:18px;text-align:center;color:var(--muted);font-size:14px}
.acct-mi:hover{background:var(--primary-light);color:var(--primary)}
.acct-mi:hover i{color:var(--primary)}
.acct-mi.active{background:var(--primary-light);color:var(--primary);border-left-color:var(--primary);font-weight:600}
.acct-mi.active i{color:var(--primary)}

.post-btn{display:flex;align-items:center;justify-content:center;gap:6px;margin:14px 16px;background:var(--accent);color:#fff;border-radius:var(--r);padding:11px 14px;font-size:13px;font-weight:700;text-align:center;transition:opacity .15s;text-decoration:none}
.post-btn:hover{opacity:.88;color:#fff}

.acct-main{display:flex;flex-direction:column;gap:16px}
.panel{background:#fff;border:1px solid var(--border);border-radius:var(--rl);overflow:hidden;display:none;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.panel.active{display:block}
.panel-head{background:var(--primary);color:#fff;padding:14px 20px;font-family:var(--fh);font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;display:flex;align-items:center;justify-content:space-between}
.panel-head a{background:var(--accent) !important;color:#fff !important}
.panel-body{padding:22px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px}
.form-input{width:100%;border:1.5px solid var(--border2);border-radius:var(--r);padding:10px 14px;font-size:13.5px;transition:border .15s;background:var(--surface)}
.form-input:focus{border-color:var(--red);outline:none}
.btn-save{padding:10px 24px;background:var(--red);color:#fff;border-radius:var(--r);font-size:13px;font-weight:600;cursor:pointer;border:none;transition:background .15s}
.btn-save:hover{background:var(--red-dark)}
.flash{padding:10px 16px;border-radius:var(--r);margin-bottom:16px;font-size:13px}
.flash-success{background:var(--green-bg);color:var(--green);border:1px solid #bbf7d0}
.flash-error{background:var(--red-pale);color:var(--red);border:1px solid #fecaca}

.sub-row{display:flex;gap:13px;padding:13px 10px;border-bottom:1px solid var(--border);align-items:center;border-radius:8px;transition:background .12s}
.sub-row:last-child{border-bottom:none}
.sub-row:hover{background:#f5f7fb}
.sub-thumb{width:58px;height:50px;border-radius:8px;overflow:hidden;background:#f5f0ec;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;border:1px solid var(--border)}
.sub-thumb img{width:100%;height:100%;object-fit:cover}
.sub-title{font-size:13px;font-weight:500;line-height:1.3}
.sub-meta{font-size:11px;color:var(--muted);margin-top:2px}
.status-badge{font-size:9.5px;font-weight:700;padding:2px 8px;border-radius:20px}
.status-active{background:var(--green-bg);color:var(--green)}
.status-pending{background:var(--amber-bg);color:var(--amber)}
.status-rejected,.status-inactive{background:var(--red-pale);color:var(--red)}
.status-draft{background:#f1f5f9;color:#64748b}
.empty-state{text-align:center;padding:30px;color:var(--muted);font-size:13px}
.row-actions{display:flex;gap:6px;flex-shrink:0}
.btn-edit{font-size:11px;padding:4px 10px;border-radius:6px;border:1.5px solid var(--border2);color:var(--muted);cursor:pointer;text-decoration:none;transition:all .15s}
.btn-edit:hover{border-color:var(--blue);color:var(--blue)}
.btn-del{font-size:11px;padding:4px 10px;border-radius:6px;border:1.5px solid var(--border2);color:var(--muted);cursor:pointer;background:none;transition:all .15s}
.btn-del:hover{border-color:var(--red);color:var(--red)}

/* Sub-tabs inside panels */
.sub-tabs{display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:20px;overflow-x:auto}
.sub-tab{padding:8px 16px;font-size:12.5px;font-weight:600;color:var(--muted);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;white-space:nowrap}
.sub-tab.active{color:var(--red);border-bottom-color:var(--red)}
.sub-panel{display:none}.sub-panel.active{display:block}
</style>
@endpush

@section('content')
<div class="acct-wrap">
  <aside class="acct-sidebar">
    <div class="acct-profile">
      @if($user->avatar)
        <img src="{{ str_starts_with($user->avatar,'http') ? $user->avatar : \Illuminate\Support\Facades\Storage::disk('s3')->url($user->avatar) }}" alt="{{ $user->name }}" class="acct-avatar">
      @else
        <div class="acct-avatar-placeholder">{{ strtoupper(substr($user->name,0,1)) }}</div>
      @endif
      <div class="acct-name">
        {{ $user->name }}
        @if($user->hasVerifiedBadge())
          <span style="background:rgba(255,255,255,.25);color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:4px;vertical-align:middle"><i class="fa-solid fa-circle-check"></i> Verified</span>
        @endif
      </div>
      <div class="acct-email">{{ $user->email }}</div>
      @if($user->city)<div class="acct-loc"><i class="fa-solid fa-location-dot"></i> {{ $user->city }}{{ $user->province ? ', '.$user->province : '' }}</div>@endif
      <div style="margin-top:10px;padding:7px 12px;background:rgba(255,255,255,.12);border-radius:8px;font-size:11.5px;color:rgba(255,255,255,.9);text-align:left">
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span><i class="fa-solid fa-id-card" style="margin-right:4px"></i> {{ $user->planName() }} Plan</span>
          @if(!$user->isSubscribed())
            <a href="{{ route('pricing') }}" style="color:#fbbf24;font-size:10px;font-weight:700;text-decoration:none">Upgrade →</a>
          @endif
        </div>
        @if($user->activePlan() === 'power_seller')
          <div style="font-size:10px;color:rgba(255,255,255,.7);margin-top:3px">Listings: Unlimited · Auto-renew</div>
        @elseif($user->activePlan() === 'verified')
          <div style="font-size:10px;color:rgba(255,255,255,.7);margin-top:3px">Listings: {{ $user->activeListingCount() }}/10 · 30-day visibility</div>
        @else
          <div style="font-size:10px;color:rgba(255,255,255,.7);margin-top:3px">Listings: {{ $user->activeListingCount() }}/3 · 3-day visibility</div>
        @endif
      </div>
    </div>

    <a href="{{ route('post.create') }}" class="post-btn"><i class="fa-solid fa-plus"></i> Post Something</a>

    <nav class="acct-menu">
      <a href="#" class="acct-mi active" onclick="showPanel('submissions',this)"><i class="fa-solid fa-list"></i> My Submissions</a>
      <a href="#" class="acct-mi" onclick="showPanel('billing',this)"><i class="fa-solid fa-credit-card"></i> Billing & Payments</a>
      <a href="#" class="acct-mi" onclick="showPanel('profile',this)"><i class="fa-solid fa-user"></i> Edit Profile</a>
      <a href="#" class="acct-mi" onclick="showPanel('password',this)"><i class="fa-solid fa-lock"></i> Change Password</a>
      <a href="{{ route('home') }}" class="acct-mi"><i class="fa-solid fa-house"></i> Browse Site</a>
      <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit()" class="acct-mi" style="color:#dc2626"><i class="fa-solid fa-right-from-bracket" style="color:#dc2626"></i> Logout</a>
    </nav>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
  </aside>

  <div class="acct-main">
    @if(session('success'))
      <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    {{-- MY SUBMISSIONS --}}
    <div class="panel active" id="panel-submissions">
      <div class="panel-head">
        <span>My Submissions</span>
        <a href="{{ route('post.create') }}" style="background:var(--red);color:#fff;font-size:11px;padding:4px 12px;border-radius:6px;font-weight:600">+ New Post</a>
      </div>
      <div class="panel-body">
        @php
          $totalCount = $listings->count() + $jobs->count() + $events->count() + $businesses->count() + $businessPosts->count();
        @endphp

        @if($totalCount === 0)
          <div class="empty-state">
            <div style="font-size:40px;margin-bottom:10px">📭</div>
            <p>You haven't posted anything yet.</p>
            <a href="{{ route('post.create') }}" style="color:var(--red);font-weight:600;margin-top:8px;display:inline-block">Post something now →</a>
          </div>
        @else
          <div class="sub-tabs">
            @if($listings->isNotEmpty())<div class="sub-tab active" onclick="showSubTab('classifieds',this)">🏷️ Classifieds ({{ $listings->count() }})</div>@endif
            @if($jobs->isNotEmpty())<div class="sub-tab {{ $listings->isEmpty() ? 'active' : '' }}" onclick="showSubTab('jobs',this)">💼 Jobs ({{ $jobs->count() }})</div>@endif
            @if($events->isNotEmpty())<div class="sub-tab {{ $listings->isEmpty() && $jobs->isEmpty() ? 'active' : '' }}" onclick="showSubTab('events',this)">🎉 Events ({{ $events->count() }})</div>@endif
            @if($businesses->isNotEmpty())<div class="sub-tab {{ $listings->isEmpty() && $jobs->isEmpty() && $events->isEmpty() ? 'active' : '' }}" onclick="showSubTab('biz',this)">🏢 Directory ({{ $businesses->count() }})</div>@endif
            @if($businessPosts->isNotEmpty())<div class="sub-tab {{ $listings->isEmpty() && $jobs->isEmpty() && $events->isEmpty() && $businesses->isEmpty() ? 'active' : '' }}" onclick="showSubTab('bizposts',this)">📦 Business Posts ({{ $businessPosts->count() }})</div>@endif
          </div>

          {{-- Classifieds --}}
          @if($listings->isNotEmpty())
          <div class="sub-panel active" id="sub-classifieds">
            @foreach($listings as $item)
            <div class="sub-row">
              <div class="sub-thumb">
                @if($item->image_url)<img src="{{ $item->image_url }}" alt="">
                @else {{ $item->category->icon ?? '🏷️' }} @endif
              </div>
              <div style="flex:1;min-width:0">
                <div class="sub-title">{{ $item->title }}</div>
                <div class="sub-meta">{{ $item->category->name ?? '' }} · {{ $item->location }} · {{ $item->created_at->format('d M Y') }}</div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
                <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                <div class="row-actions">
                  <a href="{{ route('post.edit', ['type'=>'classified','id'=>$item->id]) }}" class="btn-edit">Edit</a>
                  <form method="POST" action="{{ route('post.destroy', ['type'=>'classified','id'=>$item->id]) }}" onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif

          {{-- Jobs --}}
          @if($jobs->isNotEmpty())
          <div class="sub-panel {{ $listings->isEmpty() ? 'active' : '' }}" id="sub-jobs">
            @foreach($jobs as $item)
            <div class="sub-row">
              <div class="sub-thumb">
                @if($item->company_logo)<img src="{{ $item->logo_url }}" alt="">
                @else 💼 @endif
              </div>
              <div style="flex:1;min-width:0">
                <div class="sub-title">{{ $item->title }}</div>
                <div class="sub-meta">{{ $item->company }} · {{ $item->city }} · {{ $item->created_at->format('d M Y') }}</div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
                <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                <div class="row-actions">
                  <a href="{{ route('post.edit', ['type'=>'job','id'=>$item->id]) }}" class="btn-edit">Edit</a>
                  <form method="POST" action="{{ route('post.destroy', ['type'=>'job','id'=>$item->id]) }}" onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif

          {{-- Events --}}
          @if($events->isNotEmpty())
          <div class="sub-panel {{ $listings->isEmpty() && $jobs->isEmpty() ? 'active' : '' }}" id="sub-events">
            @foreach($events as $item)
            <div class="sub-row">
              <div class="sub-thumb">
                @if($item->image_url)<img src="{{ $item->image_url }}" alt="">
                @else 🎉 @endif
              </div>
              <div style="flex:1;min-width:0">
                <div class="sub-title">{{ $item->title }}</div>
                <div class="sub-meta">{{ $item->city }} · {{ $item->start_date?->format('d M Y') }} · {{ $item->created_at->format('d M Y') }}</div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
                <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                <div class="row-actions">
                  <a href="{{ route('post.edit', ['type'=>'event','id'=>$item->id]) }}" class="btn-edit">Edit</a>
                  <form method="POST" action="{{ route('post.destroy', ['type'=>'event','id'=>$item->id]) }}" onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif

          {{-- Businesses --}}
          @if($businesses->isNotEmpty())
          <div class="sub-panel {{ $listings->isEmpty() && $jobs->isEmpty() && $events->isEmpty() ? 'active' : '' }}" id="sub-biz">
            @foreach($businesses as $item)
            <div class="sub-row">
              <div class="sub-thumb">
                @if($item->image_url)<img src="{{ $item->image_url }}" alt="">
                @else 🏢 @endif
              </div>
              <div style="flex:1;min-width:0">
                <div class="sub-title">{{ $item->name }}</div>
                <div class="sub-meta">{{ $item->category->name ?? '' }} · {{ $item->city }} · {{ $item->created_at->format('d M Y') }}</div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
                <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                <div class="row-actions">
                  <a href="{{ route('post.edit', ['type'=>'business','id'=>$item->id]) }}" class="btn-edit">Edit</a>
                  <form method="POST" action="{{ route('post.destroy', ['type'=>'business','id'=>$item->id]) }}" onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif

          {{-- Business Posts --}}
          @if($businessPosts->isNotEmpty())
          <div class="sub-panel {{ $listings->isEmpty() && $jobs->isEmpty() && $events->isEmpty() && $businesses->isEmpty() ? 'active' : '' }}" id="sub-bizposts">
            @foreach($businessPosts as $item)
            <div class="sub-row">
              <div class="sub-thumb">
                @if($item->image_url)<img src="{{ $item->image_url }}" alt="">
                @else 📦 @endif
              </div>
              <div style="flex:1;min-width:0">
                <div class="sub-title">{{ $item->title }}</div>
                <div class="sub-meta">
                  🏢 {{ $item->business->name ?? '—' }}
                  @if($item->price) · {{ $item->price }}{{ $item->price_unit }}@endif
                  · {{ $item->created_at->format('d M Y') }}
                </div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
                <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                <div class="row-actions">
                  @if($item->business)
                    <a href="{{ route('directory.post', [$item->business->slug, $item->slug]) }}" class="btn-edit" target="_blank">View</a>
                  @endif
                  <a href="{{ route('post.edit', ['type'=>'business-post','id'=>$item->id]) }}" class="btn-edit">Edit</a>
                  <form method="POST" action="{{ route('post.destroy', ['type'=>'business-post','id'=>$item->id]) }}" onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif

        @endif
      </div>
    </div>

    {{-- BILLING & PAYMENTS --}}
    <div class="panel" id="panel-billing">
      <div class="panel-head">
        <span><i class="fa-solid fa-credit-card" style="margin-right:7px"></i>Billing & Payments</span>
        <a href="{{ route('pricing') }}" style="font-size:11px"><i class="fa-solid fa-arrow-up" style="margin-right:4px"></i>Upgrade Plan</a>
      </div>
      <div class="panel-body">

        {{-- Current Plan Card --}}
        <div style="background:linear-gradient(135deg,var(--primary) 0%,#1e3a8a 100%);border-radius:12px;padding:20px 22px;margin-bottom:22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
          <div>
            <div style="font-size:11px;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px">Current Plan</div>
            <div style="font-family:var(--fh);font-size:22px;font-weight:800;color:#fff">{{ $user->planName() }}</div>
            @if($user->isSubscribed())
              <div style="font-size:11.5px;color:rgba(255,255,255,.7);margin-top:3px">
                @if($user->subscription_status === 'active')
                  <i class="fa-solid fa-circle-check" style="color:#4ade80;margin-right:4px"></i>Active
                  @if($user->plan_expires_at) · Renews {{ $user->plan_expires_at->format('M d, Y') }} @endif
                @elseif($user->subscription_status === 'canceling')
                  <i class="fa-solid fa-clock" style="color:#fbbf24;margin-right:4px"></i>Cancels {{ $user->plan_expires_at?->format('M d, Y') }} — Access active till then
                @elseif($user->subscription_status === 'past_due')
                  <i class="fa-solid fa-triangle-exclamation" style="color:#fbbf24;margin-right:4px"></i>Payment Past Due
                @elseif($user->subscription_status === 'canceled')
                  <i class="fa-solid fa-circle-xmark" style="color:#f87171;margin-right:4px"></i>Canceled
                @endif
              </div>
            @else
              <div style="font-size:11.5px;color:rgba(255,255,255,.6);margin-top:3px">Free forever</div>
            @endif
          </div>
          <div style="text-align:right">
            @if($user->stripe_subscription_id && $user->subscription_status === 'active')
              <a href="{{ route('stripe.cancel.confirm') }}" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;display:inline-block">
                <i class="fa-solid fa-xmark" style="margin-right:4px"></i>Cancel Subscription
              </a>
          @elseif($user->stripe_subscription_id && $user->subscription_status === 'canceling')
              <div style="text-align:right">
                <div style="background:rgba(251,191,36,.2);border:1px solid rgba(251,191,36,.4);color:#fbbf24;font-size:11px;font-weight:600;padding:5px 12px;border-radius:6px;margin-bottom:8px">
                  <i class="fa-solid fa-clock" style="margin-right:4px"></i>Cancels {{ $user->plan_expires_at?->format('M d, Y') }}
                </div>
                <form action="{{ route('stripe.resume') }}" method="POST">
                  @csrf
                  <button type="submit" style="background:#4ade80;color:#14532d;border:none;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer">
                    <i class="fa-solid fa-rotate-left" style="margin-right:4px"></i>Resume Subscription
                  </button>
                </form>
              </div>
            @else
              <a href="{{ route('pricing') }}" style="background:#fbbf24;color:#1a1a1a;padding:9px 18px;border-radius:8px;font-size:12.5px;font-weight:700;text-decoration:none;display:inline-block">
                <i class="fa-solid fa-arrow-up" style="margin-right:4px"></i>Upgrade Now
              </a>
            @endif
          </div>
        </div>

        {{-- Payment History Table --}}
        <div style="font-family:var(--fh);font-size:14px;font-weight:700;color:var(--text);margin-bottom:14px">
          <i class="fa-solid fa-receipt" style="color:var(--primary);margin-right:7px"></i>Payment History
        </div>

        @if($paymentHistory->isEmpty())
          <div style="text-align:center;padding:40px 20px;color:var(--muted)">
            <i class="fa-solid fa-receipt" style="font-size:36px;color:#ddd;display:block;margin-bottom:12px"></i>
            <div style="font-size:13.5px;font-weight:600;color:#555;margin-bottom:5px">No payments yet</div>
            <div style="font-size:12.5px">Your payment history will appear here after your first purchase.</div>
            <a href="{{ route('pricing') }}" style="display:inline-block;margin-top:14px;background:var(--primary);color:#fff;padding:9px 20px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none">
              View Plans
            </a>
          </div>
        @else
          <div style="border:1px solid var(--border);border-radius:10px;overflow:hidden">
            <table style="width:100%;border-collapse:collapse;font-size:13px">
              <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid var(--border)">
                  <th style="text-align:left;padding:11px 14px;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Date</th>
                  <th style="text-align:left;padding:11px 14px;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Description</th>
                  <th style="text-align:left;padding:11px 14px;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Plan</th>
                  <th style="text-align:right;padding:11px 14px;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Amount</th>
                  <th style="text-align:center;padding:11px 14px;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($paymentHistory as $payment)
                  @php $badge = $payment->status_badge; @endphp
                  <tr style="border-bottom:{{ $loop->last ? 'none' : '1px solid var(--border)' }}">
                    <td style="padding:12px 14px;color:var(--muted);white-space:nowrap">
                      {{ $payment->paid_at?->format('M d, Y') ?? $payment->created_at->format('M d, Y') }}
                    </td>
                    <td style="padding:12px 14px;color:var(--text)">
                      {{ $payment->description ?? $payment->plan_name.' Plan' }}
                    </td>
                    <td style="padding:12px 14px">
                      <span style="background:var(--primary-light);color:var(--primary);font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px">
                        {{ $payment->plan_name }}
                      </span>
                    </td>
                    <td style="padding:12px 14px;text-align:right;font-family:var(--fh);font-weight:700;color:var(--text)">
                      {{ $payment->amount_formatted }}
                    </td>
                    <td style="padding:12px 14px;text-align:center">
                      <span style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px">
                        {{ $badge['label'] }}
                      </span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

      </div>
    </div>

    {{-- EDIT PROFILE --}}
    <div class="panel" id="panel-profile">
      <div class="panel-head"><span>Edit Profile</span></div>
      <div class="panel-body">
        @if($errors->has('name') || $errors->has('phone'))
          <div class="flash flash-error">Please fix the errors below.</div>
        @endif
        <form method="POST" action="{{ route('account.profile') }}" enctype="multipart/form-data">
          @csrf @method('PATCH')
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
            </div>
            <div class="form-group">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="+1 647 xxx xxxx">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">City</label>
              <input type="text" name="city" value="{{ old('city', $user->city) }}" class="form-input" placeholder="Toronto">
            </div>
            <div class="form-group">
              <label class="form-label">Province</label>
              <input type="text" name="province" value="{{ old('province', $user->province) }}" class="form-input" placeholder="ON">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-input" rows="3" placeholder="Tell the community about yourself...">{{ old('bio', $user->bio) }}</textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Profile Photo</label>
            @if($user->avatar)
              <div style="margin-bottom:8px"><img src="{{ str_starts_with($user->avatar,'http') ? $user->avatar : \Illuminate\Support\Facades\Storage::disk('s3')->url($user->avatar) }}" style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid var(--border2)"></div>
            @endif
            <input type="file" name="avatar" class="form-input" accept="image/*">
          </div>
          <button type="submit" class="btn-save">Save Profile</button>
        </form>
      </div>
    </div>

    {{-- CHANGE PASSWORD --}}
    <div class="panel" id="panel-password">
      <div class="panel-head"><span>Change Password</span></div>
      <div class="panel-body">
        @error('current_password')<div class="flash flash-error">{{ $message }}</div>@enderror
        <form method="POST" action="{{ route('account.password') }}">
          @csrf @method('PATCH')
          <div class="form-group">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-input" required>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">New Password</label>
              <input type="password" name="password" class="form-input" placeholder="Min 8 characters" required>
            </div>
            <div class="form-group">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-input" required>
            </div>
          </div>
          <button type="submit" class="btn-save">Update Password</button>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function showPanel(name, el) {
  event.preventDefault();
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.acct-mi').forEach(m => m.classList.remove('active'));
  document.getElementById('panel-' + name).classList.add('active');
  el.classList.add('active');
}
function showSubTab(name, el) {
  document.querySelectorAll('.sub-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.sub-tab').forEach(t => t.classList.remove('active'));
  document.getElementById('sub-' + name).classList.add('active');
  el.classList.add('active');
}
</script>
@endpush
@endsection
