<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'GoBazaar') — Indian Community in Canada</title>
<meta name="description" content="@yield('description', 'Best community website for Indians living in Canada.')">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&family=Noto+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
  --primary:#1a3a8f;--primary-dark:#122970;--primary-light:#e8edf7;
  --accent:#e8a020;--accent-light:#fef6e4;
  --dark:#1a1a1a;--text:#2d2d2d;--muted:#6b6b6b;
  --light:#f5f5f0;--border:#e2e0db;--white:#ffffff;
  --green:#2e7d32;--green-light:#e8f5e9;
  --nav-bg:#1a3a8f;--bg:#ffffff;
  --radius:10px;--radius-sm:6px;--radius-lg:14px;
  --fh:'Baloo 2',sans-serif;--fb:'Noto Sans',sans-serif;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--fb);background:var(--bg);color:var(--text);font-size:14px;line-height:1.5}
a{text-decoration:none;color:inherit}
button{cursor:pointer;font-family:var(--fb)}
input,select,textarea{font-family:var(--fb);outline:none}

/* ── TOPBAR ── */
.topbar{background:var(--nav-bg);padding:5px 0;font-size:11px;color:rgba(255,255,255,.75);border-bottom:1px solid #2a4fa8}
.topbar-inner{max-width:1280px;margin:0 auto;padding:0 20px;display:flex;justify-content:space-between;align-items:center}
.topbar a{color:rgba(255,255,255,.8);margin-left:14px;transition:color .15s}
.topbar a:hover{color:var(--accent)}

/* ── NAVBAR ── */
.navbar{background:var(--nav-bg);position:sticky;top:0;z-index:200;box-shadow:0 2px 8px rgba(0,0,0,.3)}
.navbar-inner{max-width:1280px;margin:0 auto;padding:10px 20px;display:flex;align-items:center;gap:14px}
.logo{display:flex;align-items:center;gap:10px;flex-shrink:0;text-decoration:none}
.logo-icon{width:40px;height:40px;background:rgba(255,255,255,.12);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px}
.logo-name{font-family:var(--fh);font-size:22px;font-weight:800;color:#fff;line-height:1}
.logo-name span{color:var(--accent)}
.logo-tag{font-size:10px;color:rgba(255,255,255,.55)}
.nav-location{display:flex;align-items:center;gap:5px;color:rgba(255,255,255,.85);font-size:12px;white-space:nowrap;cursor:pointer;background:#2a4fa8;padding:6px 10px;border-radius:var(--radius-sm);border:1px solid #3a5fc0;flex-shrink:0}
.nav-location:hover{border-color:rgba(255,255,255,.4)}
.nav-location i{color:var(--accent);font-size:13px}
.nav-actions{margin-left:auto;display:flex;align-items:center;gap:14px;flex-shrink:0}
.nav-icon-btn{position:relative;color:rgba(255,255,255,.75);font-size:18px;cursor:pointer;text-decoration:none}
.nav-icon-btn:hover{color:#fff}
.nav-badge{position:absolute;top:-5px;right:-6px;background:var(--accent);color:#fff;font-size:8px;width:15px;height:15px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700}
.nav-post-btn{background:var(--accent);color:#fff;border:none;padding:8px 16px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;white-space:nowrap;text-decoration:none;transition:opacity .2s}
.nav-post-btn:hover{opacity:.88}
.nav-user{display:flex;align-items:center;gap:7px;color:rgba(255,255,255,.9);font-size:12px;cursor:pointer}
.nav-avatar{width:32px;height:32px;border-radius:50%;background:#2a4fa8;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;border:2px solid rgba(255,255,255,.2)}
.nav-auth-link{color:rgba(255,255,255,.9);font-size:13px;font-weight:600;text-decoration:none;transition:color .15s}
.nav-auth-link:hover{color:var(--accent)}

/* ── HAMBURGER ── */
.nav-toggle{display:none;flex-direction:column;gap:5px;padding:6px 4px;cursor:pointer;background:none;border:none;flex-shrink:0}
.nav-toggle span{display:block;width:22px;height:2px;background:#fff;border-radius:2px;transition:all .2s}
.nav-toggle.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.nav-toggle.open span:nth-child(2){opacity:0}
.nav-toggle.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}

/* ── SUBNAV ── */
.subnav{background:#fff;border-bottom:2px solid var(--border)}
.subnav-inner{max-width:1280px;margin:0 auto;padding:0 20px;display:flex;gap:0;overflow-x:auto}
.subnav a{display:flex;align-items:center;gap:6px;padding:11px 14px;font-size:13px;color:#555;white-space:nowrap;border-bottom:3px solid transparent;font-weight:500;transition:color .15s,border-color .15s}
.subnav a:hover{color:var(--primary)}
.subnav a.active{color:var(--primary);border-bottom-color:var(--primary)}
.subnav a i{font-size:14px}

/* ── MOBILE NAV DRAWER ── */
.nav-drawer{display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:300}
.nav-drawer-bg{position:absolute;inset:0;background:rgba(0,0,0,.5)}
.nav-drawer-panel{position:absolute;top:0;left:0;width:280px;height:100%;background:#fff;overflow-y:auto;padding:20px 0;box-shadow:4px 0 20px rgba(0,0,0,.15)}
.nav-drawer.open{display:block}
.drawer-logo{display:flex;align-items:center;gap:10px;padding:0 20px 16px;border-bottom:1px solid var(--border);margin-bottom:6px}
.drawer-link{display:flex;align-items:center;gap:9px;padding:12px 20px;font-size:14px;font-weight:500;color:var(--text);border-left:3px solid transparent;transition:all .15s;text-decoration:none}
.drawer-link:hover,.drawer-link.active{color:var(--primary);border-left-color:var(--primary);background:var(--primary-light)}
.drawer-divider{height:1px;background:var(--border);margin:8px 0}
.drawer-actions{padding:14px 20px;display:flex;flex-direction:column;gap:8px}
.drawer-btn{padding:10px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;text-align:center;display:block;text-decoration:none}
.drawer-btn-primary{background:var(--accent);color:#fff}
.drawer-btn-outline{border:1.5px solid var(--border);color:var(--text)}

/* ── CONTAINER / FOOTER ── */
.container{max-width:1280px;margin:0 auto;padding:20px}

footer.site-footer{background:var(--nav-bg);border-top:2px solid #2a4fa8;margin-top:30px}
.footer-top{max-width:1280px;margin:0 auto;padding:30px 20px;display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:28px}
.footer-brand p{font-size:12px;color:rgba(255,255,255,.6);margin-top:8px;line-height:1.7;max-width:260px}
.footer-col h4{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.9px;color:rgba(255,255,255,.55);margin-bottom:11px}
.footer-col a{display:block;font-size:12px;color:rgba(255,255,255,.55);margin-bottom:7px;transition:color .15s}
.footer-col a:hover{color:var(--accent)}
.footer-bottom{border-top:1px solid #2a4fa8;padding:13px 20px;text-align:center;font-size:11px;color:rgba(255,255,255,.5);max-width:1280px;margin:0 auto}
.footer-bottom a{color:rgba(255,255,255,.6)}
.footer-bottom a:hover{color:var(--accent)}
.footer-socials{display:flex;gap:8px;margin-top:12px}
.footer-socials a{width:30px;height:30px;background:#2a4fa8;border-radius:50%;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.75);font-size:13px;transition:background .15s}
.footer-socials a:hover{background:var(--accent);color:#fff}

/* ── MOBILE INNER PAGE SIDEBAR ADS ── */
.mob-sidebar-ad{display:none}
@media(max-width:600px){
  .mob-sidebar-ad{display:block;padding:0 12px;margin:14px 0}
  .mob-sidebar-ad .ad-slot--sidebar img{height:180px;border-radius:10px}
}

/* ── AD SLOTS ── */
.ad-slot{display:block;position:relative;text-decoration:none;margin-bottom:12px}
.ad-slot:last-child{margin-bottom:0}
.ad-slot img{border-radius:8px;border:1px solid var(--border)}
.ad-label{position:absolute;top:6px;left:6px;background:rgba(0,0,0,.45);color:#fff;font-size:9px;font-weight:700;padding:1px 6px;border-radius:4px;letter-spacing:.5px;z-index:1}
.ad-slot--home-banner{display:block;width:100%;max-width:1200px;margin:0 auto 18px}
.ad-slot--home-banner img{height:120px;border-radius:10px}
.ad-slot--sidebar img{height:250px}
.ad-slot--inline{display:block;width:100%;margin:14px 0}
.ad-slot--inline img{height:120px;border-radius:8px}
@media(max-width:600px){
  .ad-slot--home-banner img{height:70px}
  .ad-slot--inline img{height:80px}
  .ad-slot--sidebar img{height:200px}
}

/* ── FLASH ── */
.flash{padding:12px 16px;border-radius:var(--radius);margin-bottom:16px;font-size:13px}
.flash-success{background:var(--green-light);color:var(--green);border:1px solid #bbf7d0}
.flash-error{background:#fee2e2;color:#b91c1c;border:1px solid #fecaca}

@media(max-width:900px){
  .nav-location{display:none}
  .nav-toggle{display:flex}
  .subnav{display:none}
  .topbar{display:none}
  .footer-top{grid-template-columns:1fr 1fr;gap:20px;padding:24px 16px}
  .navbar-inner{padding:8px 14px;gap:10px}
}
@media(max-width:600px){
  .nav-location{display:flex;padding:4px 8px;font-size:11px;max-width:130px;overflow:hidden}
  .nav-location span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:90px}
  .nav-actions .nav-auth-link,
  .nav-actions form,
  .nav-actions .nav-user span,
  .nav-actions > form { display:none }
}
@media(max-width:600px){
  .footer-top{grid-template-columns:1fr;gap:16px;padding:20px 16px}
  .footer-bottom{padding:12px 16px;font-size:10px}
  .nav-actions .nav-user span{display:none}
  .nav-actions{gap:8px}
  .nav-post-btn{padding:7px 12px;font-size:12px}
  .logo-name{font-size:19px}
  .logo-tag{display:none}
  .logo-icon{width:34px;height:34px;font-size:17px}
}

/* ══ MOBILE APP UI (≤600px) ══════════════════════════════════════════ */
@media(max-width:600px){
  /* Hide desktop footer & post btn (tab bar handles these) */
  .site-footer{display:none}
  .nav-post-btn{display:none}

  /* Body padding so content clears the bottom tab bar */
  body{padding-bottom:64px}

  /* Compact fixed navbar */
  .navbar{position:fixed;top:0;left:0;right:0;z-index:200}
  /* Single wrapper pushes ALL page content below the fixed navbar */
  .page-content-wrap{padding-top:50px}

  /* Drawer fills full height on mobile, slides from left */
  .nav-drawer-panel{width:85vw;max-width:300px}
  .drawer-link{padding:13px 18px;font-size:15px}

  /* Bottom Tab Bar */
  .mobile-tab-bar{
    display:flex;
    position:fixed;bottom:0;left:0;right:0;
    height:60px;
    background:#fff;
    border-top:1px solid #e5e7eb;
    z-index:500;
    box-shadow:0 -2px 12px rgba(0,0,0,.08);
    padding-bottom:env(safe-area-inset-bottom);
  }
  .mob-tab{
    flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:3px;text-decoration:none;color:#9ca3af;font-size:9.5px;font-weight:600;
    letter-spacing:.2px;padding:6px 0;cursor:pointer;border:none;background:none;
    position:relative;transition:color .15s;
  }
  .mob-tab i{font-size:20px;transition:color .15s}
  .mob-tab.active,.mob-tab:active{color:var(--primary)}
  .mob-tab.active i{color:var(--primary)}
  .mob-tab-post{
    flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;
    background:none;border:none;cursor:pointer;padding:0;
  }
  .mob-tab-post-circle{
    width:46px;height:46px;border-radius:50%;
    background:var(--accent);
    display:flex;align-items:center;justify-content:center;
    box-shadow:0 4px 14px rgba(232,160,32,.5);
    margin-top:-18px;
    border:3px solid #fff;
  }
  .mob-tab-post-circle i{font-size:20px;color:#fff}
  .mob-tab-post span{font-size:9.5px;font-weight:600;color:#9ca3af;letter-spacing:.2px}
}

/* Hide tab bar on larger screens */
@media(min-width:601px){
  .mobile-tab-bar{display:none}
}
</style>
@stack('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <div class="topbar-inner">
    <span><i class="fa-solid fa-location-dot" style="color:var(--accent);margin-right:4px"></i> Canada's #1 Indian Community Portal</span>
    <div>
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('blog.index') }}">Blog</a>
      <a href="{{ route('pricing') }}">Pricing</a>
      @auth
        <a href="{{ route('account') }}">My Account</a>
      @else
        <a href="{{ route('register') }}">Register Free</a>
      @endauth
    </div>
  </div>
</div>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="navbar-inner">
    <button class="nav-toggle" id="nav-toggle" onclick="toggleDrawer()" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>

    <a href="{{ route('home') }}" class="logo">
      <div class="logo-icon" style="font-size:22px">🛍️</div>
      <div>
        <div class="logo-name">Go<span>Bazaar</span></div>
        <div class="logo-tag">Buy. Sell. Connect.</div>
      </div>
    </a>

    <div class="nav-location" id="nav-location-btn" onclick="openLocationModal()" style="cursor:pointer">
      <i class="fa-solid fa-location-dot"></i>
      <span id="nav-location-text">All Canada</span>
      <i class="fa-solid fa-chevron-down" style="font-size:10px"></i>
    </div>


<div class="nav-actions">
      @auth
        <a href="{{ route('chat.inbox') }}" class="nav-icon-btn" id="nav-chat-btn" title="Inbox">
          <i class="fa-solid fa-comments"></i>
          <span class="nav-badge" id="nav-chat-badge" style="display:none">0</span>
        </a>
        <a href="{{ route('post.create') }}" class="nav-post-btn"><i class="fa-solid fa-plus"></i> Post Free Ad</a>
        <a href="{{ route('account') }}" class="nav-user">
          <div class="nav-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
          <span>Hi, {{ Str::limit(Auth::user()->name, 10) }}</span>
          <i class="fa-solid fa-chevron-down" style="font-size:10px;color:rgba(255,255,255,.6)"></i>
        </a>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
          @csrf
          <button type="submit" class="nav-auth-link" style="background:none;border:none;padding:0">Logout</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="nav-auth-link">Login</a>
        <a href="{{ route('post.create') }}" class="nav-post-btn"><i class="fa-solid fa-plus"></i> Post Free Ad</a>
      @endauth
    </div>
  </div>
</nav>

<!-- SUBNAV -->
<div class="subnav">
  <div class="subnav-inner">
    @php $navClassifiedCats = \App\Models\Category::where('type','classifieds')->where('is_active',true)->orderBy('sort_order')->get(); @endphp
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
      <i class="fa-solid fa-house"></i> Home
    </a>
    <a href="{{ route('classifieds.index') }}" class="{{ request()->routeIs('classifieds.*') ? 'active' : '' }}">
      <i class="fa-solid fa-tag"></i> Classifieds
    </a>
    <a href="{{ route('jobs.index') }}" class="{{ request()->routeIs('jobs.*') ? 'active' : '' }}">
      <i class="fa-solid fa-briefcase"></i> Jobs
    </a>
    <a href="{{ route('events.index') }}" class="{{ request()->routeIs('events.*') ? 'active' : '' }}">
      <i class="fa-solid fa-calendar-days"></i> Events
    </a>
    <a href="{{ route('directory.index') }}" class="{{ request()->routeIs('directory.*') ? 'active' : '' }}">
      <i class="fa-solid fa-building-columns"></i> Directory
    </a>
    <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">
      <i class="fa-solid fa-newspaper"></i> Blog
    </a>
    <a href="{{ route('pricing') }}" class="{{ request()->routeIs('pricing*') ? 'active' : '' }}">
      <i class="fa-solid fa-dollar-sign"></i> Pricing
    </a>
  </div>
</div>


<!-- MOBILE NAV DRAWER -->
<div class="nav-drawer" id="nav-drawer">
  <div class="nav-drawer-bg" onclick="toggleDrawer()"></div>
  <div class="nav-drawer-panel">
    <div class="drawer-logo">
      <div style="width:36px;height:36px;background:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff">🛍️</div>
      <div>
        <div style="font-family:var(--fh);font-size:18px;font-weight:800;color:var(--primary)">Go<span style="color:var(--accent)">Bazaar</span></div>
        <div style="font-size:10px;color:var(--muted)">Buy. Sell. Connect.</div>
      </div>
    </div>
    <a href="{{ route('home') }}" class="drawer-link {{ request()->routeIs('home') ? 'active' : '' }}"><i class="fa-solid fa-house" style="width:18px"></i> Home</a>
    <a href="{{ route('classifieds.index') }}" class="drawer-link {{ request()->routeIs('classifieds.*') ? 'active' : '' }}"><i class="fa-solid fa-tag" style="width:18px"></i> Classifieds</a>
    @foreach($navClassifiedCats as $navCat)
      <a href="{{ route('classifieds.index', ['category' => $navCat->id]) }}" class="drawer-link" style="padding-left:44px;font-size:13px;color:var(--muted);padding-top:8px;padding-bottom:8px">{{ $navCat->icon }} {{ $navCat->name }}</a>
    @endforeach
    <a href="{{ route('jobs.index') }}" class="drawer-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}"><i class="fa-solid fa-briefcase" style="width:18px"></i> Jobs</a>
    <a href="{{ route('events.index') }}" class="drawer-link {{ request()->routeIs('events.*') ? 'active' : '' }}"><i class="fa-solid fa-calendar-days" style="width:18px"></i> Events</a>
    <a href="{{ route('directory.index') }}" class="drawer-link {{ request()->routeIs('directory.*') ? 'active' : '' }}"><i class="fa-solid fa-building-columns" style="width:18px"></i> Directory</a>
    <a href="{{ route('blog.index') }}" class="drawer-link {{ request()->routeIs('blog.*') ? 'active' : '' }}"><i class="fa-solid fa-newspaper" style="width:18px"></i> Blog</a>
    <a href="{{ route('pricing') }}" class="drawer-link {{ request()->routeIs('pricing*') ? 'active' : '' }}"><i class="fa-solid fa-dollar-sign" style="width:18px"></i> Pricing</a>
    <div class="drawer-divider"></div>
    <div class="drawer-actions">
      @auth
        <a href="{{ route('post.create') }}" class="drawer-btn drawer-btn-primary"><i class="fa-solid fa-plus"></i> Post Free Ad</a>
        <a href="{{ route('account') }}" class="drawer-btn drawer-btn-outline">👤 My Account</a>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="drawer-btn drawer-btn-outline" style="width:100%;border:1.5px solid var(--border);border-radius:var(--radius-sm)">Logout</button>
        </form>
      @else
        <a href="{{ route('post.create') }}" class="drawer-btn drawer-btn-primary"><i class="fa-solid fa-plus"></i> Post Free Ad</a>
        <a href="{{ route('register') }}" class="drawer-btn drawer-btn-outline">Register Free</a>
        <a href="{{ route('login') }}" class="drawer-btn drawer-btn-outline">Login</a>
      @endauth
    </div>
  </div>
</div>

@if(session('success'))
<div class="container"><div class="flash flash-success">{{ session('success') }}</div></div>
@endif
@if(session('error'))
<div class="container"><div class="flash flash-error">{{ session('error') }}</div></div>
@endif

<div class="page-content-wrap">
@yield('content')
</div>

<!-- FOOTER -->
<footer class="site-footer">
  <div class="footer-top">
    <div class="footer-brand">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:38px;height:38px;background:rgba(255,255,255,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px">🛍️</div>
        <div>
          <div style="font-family:var(--fh);font-size:20px;font-weight:800;color:#fff;line-height:1">Go<span style="color:var(--accent)">Bazaar</span></div>
          <div style="font-size:10px;color:rgba(255,255,255,.5)">Buy. Sell. Connect.</div>
        </div>
      </div>
      <p>Canada's #1 Indian Community Portal. Classifieds · Yellow Pages · Events · Jobs · Blog · Matrimonial — everything the Indian-Canadian community needs, in one place.</p>
      <div class="footer-socials">
        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#"><i class="fa-brands fa-instagram"></i></a>
        <a href="#"><i class="fa-brands fa-twitter"></i></a>
        <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
      </div>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('post.create') }}">Post Free Ad</a>
      <a href="{{ route('classifieds.index') }}">Classifieds</a>
      <a href="{{ route('jobs.index') }}">Jobs</a>
      <a href="{{ route('events.index') }}">Events</a>
      <a href="{{ route('directory.index') }}">Business Directory</a>
    </div>
    <div class="footer-col">
      <h4>Community</h4>
      <a href="{{ route('blog.index') }}">Community Blog</a>
      <a href="{{ route('feed') }}">Community Feed</a>
      <a href="{{ route('classifieds.index') }}">Find Roommate</a>
      <a href="{{ route('directory.index') }}">Travel Agents</a>
    </div>
    <div class="footer-col">
      <h4>Company</h4>
      <a href="{{ route('about') }}">About GoBazaar</a>
      <a href="{{ route('advertise') }}">Advertise With Us</a>
      <a href="{{ route('pricing') }}">Pricing</a>
      <a href="{{ route('contact') }}">Contact Us</a>
      <a href="{{ route('privacy') }}">Privacy Policy</a>
      <a href="{{ route('terms') }}">Terms of Use</a>
      <a href="/gobazzar-app/public/admin">Admin</a>
    </div>
  </div>
  <div class="footer-bottom">
    &copy; {{ date('Y') }} GoBazaar &nbsp;·&nbsp; Canada's #1 Indian Community Portal &nbsp;·&nbsp;
    <a href="{{ route('home') }}">Home</a> &nbsp;·&nbsp;
    <a href="{{ route('classifieds.index') }}">Classifieds</a> &nbsp;·&nbsp;
    <a href="{{ route('jobs.index') }}">Jobs</a>
  </div>
</footer>

{{-- OLX-STYLE LOCATION MODAL --}}
<style>
.loc-modal{display:none;position:fixed;inset:0;z-index:1000;align-items:flex-start;justify-content:center;background:rgba(0,0,0,.55);padding-top:60px}
.loc-modal.open{display:flex}
.loc-box{background:#fff;border-radius:16px;width:100%;max-width:480px;max-height:80vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3);animation:locSlideIn .2s ease}
@keyframes locSlideIn{from{transform:translateY(-20px);opacity:0}to{transform:translateY(0);opacity:1}}
.loc-header{display:flex;align-items:center;gap:12px;padding:16px 18px;border-bottom:1px solid #f0f0f0;flex-shrink:0}
#loc-back-btn{width:32px;height:32px;border-radius:50%;background:#f5f5f5;border:none;cursor:pointer;display:none;align-items:center;justify-content:center;color:#333;flex-shrink:0}
#loc-back-btn:hover{background:#e8e8e8}
#loc-modal-title{font-family:var(--fh);font-size:17px;font-weight:800;color:#111;flex:1}
.loc-close-btn{width:32px;height:32px;border-radius:50%;background:#f5f5f5;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#555;flex-shrink:0;font-size:16px}
.loc-close-btn:hover{background:#e8e8e8}

.loc-detect-wrap{padding:14px 18px;border-bottom:1px solid #f0f0f0;flex-shrink:0}
#loc-detect-btn{width:100%;background:var(--primary-light);color:var(--primary);border:1.5px solid #c5d0ef;border-radius:10px;padding:12px 16px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:9px;transition:background .15s;font-family:var(--fb)}
#loc-detect-btn:hover{background:#d0d9f0}
#loc-detect-btn:disabled{opacity:.6;cursor:not-allowed}

.loc-search-wrap{padding:12px 18px;border-bottom:1px solid #f0f0f0;flex-shrink:0}
#loc-city-search{width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:10px 14px 10px 38px;font-size:14px;font-family:var(--fb);background:#f9fafb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E") no-repeat 12px center;color:#111}
#loc-city-search:focus{outline:none;border-color:var(--primary)}

/* Both steps fill remaining height so inner scroll works */
#loc-step-province,#loc-step-city{flex-direction:column;flex:1;min-height:0;overflow:hidden}
.loc-scroll{overflow-y:auto;flex:1;min-height:0;-webkit-overflow-scrolling:touch}
.loc-section-label{padding:10px 18px 4px;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px}
.loc-item{display:flex;align-items:center;gap:13px;padding:13px 18px;cursor:pointer;transition:background .1s;border-bottom:1px solid #f9f9f9}
.loc-item:hover{background:#f5f7fb}
.loc-item-icon{font-size:14px;color:#9ca3af;width:18px;flex-shrink:0}
.loc-item span{font-size:14px;color:#111;font-weight:500;flex:1}
.loc-item .loc-arrow{font-size:12px;color:#d1d5db}
.loc-item-active{background:var(--primary-light) !important}
.loc-item-active span{color:var(--primary);font-weight:700}
.loc-item-active .loc-item-icon{color:var(--primary)}
@media(max-width:520px){
  .loc-modal{padding-top:0;align-items:flex-end}
  .loc-box{border-radius:16px 16px 0 0;max-height:90vh}
}
</style>

<div class="loc-modal" id="loc-modal" onclick="if(event.target===this)closeLocationModal()">
  <div class="loc-box">
    <div class="loc-header">
      <button id="loc-back-btn" onclick="showProvinceStep()"><i class="fa-solid fa-arrow-left" style="font-size:13px"></i></button>
      <div id="loc-modal-title">Select Location</div>
      <button class="loc-close-btn" onclick="closeLocationModal()"><i class="fa-solid fa-times"></i></button>
    </div>

    {{-- PROVINCE STEP --}}
    <div id="loc-step-province" style="display:flex">
      <div class="loc-detect-wrap">
        <button id="loc-detect-btn" onclick="detectMyLocation()">
          <i class="fa-solid fa-crosshairs"></i> Detect My Location
        </button>
      </div>
      <div class="loc-scroll">
        <div class="loc-section-label">All of Canada</div>
        <div class="loc-item" onclick="applyLocation('','')">
          <i class="fa-solid fa-globe loc-item-icon"></i>
          <span>All Canada</span>
        </div>
        <div class="loc-section-label">Provinces</div>
        @foreach(['Alberta','British Columbia','Manitoba','New Brunswick','Nova Scotia','Ontario','Quebec','Saskatchewan'] as $prov)
        <div class="loc-item" onclick="showCityStep('{{ $prov }}')">
          <i class="fa-solid fa-map loc-item-icon"></i>
          <span>{{ $prov }}</span>
          <i class="fa-solid fa-chevron-right loc-arrow"></i>
        </div>
        @endforeach
      </div>
    </div>

    {{-- CITY STEP --}}
    <div id="loc-step-city" style="display:none;flex-direction:column;flex:1;overflow:hidden">
      <div class="loc-search-wrap">
        <input type="text" id="loc-city-search" placeholder="Search city…">
      </div>
      <div class="loc-scroll" id="loc-city-list">
        <div style="text-align:center;padding:30px;color:#9ca3af"><i class="fa-solid fa-spinner fa-spin"></i></div>
      </div>
    </div>

  </div>
</div>

@stack('modals')

{{-- GLOBAL REPORT MODAL --}}
@auth
<style>
.report-modal{display:none;position:fixed;inset:0;z-index:2000;align-items:center;justify-content:center;background:rgba(0,0,0,.55);padding:20px}
.report-modal.open{display:flex}
.report-box{background:#fff;border-radius:16px;width:100%;max-width:440px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.25);animation:locSlideIn .2s ease}
.report-head{display:flex;align-items:center;justify-content:space-between;padding:16px 18px;border-bottom:1px solid var(--border)}
.report-head h3{font-family:var(--fh);font-size:16px;font-weight:800;color:var(--dark)}
.report-body{padding:18px}
.report-reason{display:flex;flex-direction:column;gap:8px;margin-bottom:14px}
.report-reason label{display:flex;align-items:center;gap:10px;padding:10px 12px;border:1.5px solid var(--border);border-radius:9px;cursor:pointer;font-size:13.5px;transition:all .15s}
.report-reason label:hover{border-color:var(--primary);background:var(--primary-light)}
.report-reason input[type=radio]{accent-color:var(--primary)}
.report-reason input[type=radio]:checked + span{font-weight:700;color:var(--primary)}
.report-reason label:has(input:checked){border-color:var(--primary);background:var(--primary-light)}
.report-details{width:100%;border:1.5px solid var(--border);border-radius:9px;padding:10px 12px;font-size:13px;font-family:var(--fb);resize:none;height:72px;margin-bottom:14px}
.report-details:focus{outline:none;border-color:var(--primary)}
.report-submit{width:100%;background:var(--primary);color:#fff;border:none;border-radius:9px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;transition:opacity .15s}
.report-submit:hover{opacity:.88}
.report-submit:disabled{opacity:.5;cursor:not-allowed}
.report-success{text-align:center;padding:24px;color:var(--green)}
.report-success i{font-size:40px;display:block;margin-bottom:10px}
</style>

<div class="report-modal" id="report-modal" onclick="if(event.target===this)closeReportModal()">
  <div class="report-box">
    <div class="report-head">
      <h3><i class="fa-solid fa-flag" style="color:#e74c3c;margin-right:8px"></i>Report Content</h3>
      <button onclick="closeReportModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--muted)">&times;</button>
    </div>
    <div class="report-body" id="report-body">
      <div class="report-reason">
        @foreach(\App\Models\Report::reasons() as $value => $label)
          <label>
            <input type="radio" name="report_reason" value="{{ $value }}">
            <span>{{ $label }}</span>
          </label>
        @endforeach
      </div>
      <textarea class="report-details" id="report-details" placeholder="Additional details (optional)…"></textarea>
      <button class="report-submit" id="report-submit-btn" onclick="submitReport()">Submit Report</button>
    </div>
  </div>
</div>

<script>
let _reportType = '', _reportId = 0;

function openReportModal(type, id) {
  _reportType = type;
  _reportId   = id;
  document.querySelectorAll('input[name=report_reason]').forEach(r => r.checked = false);
  document.getElementById('report-details').value = '';
  document.getElementById('report-body').innerHTML = document.getElementById('report-body').innerHTML; // reset
  document.getElementById('report-modal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeReportModal() {
  document.getElementById('report-modal').classList.remove('open');
  document.body.style.overflow = '';
}

async function submitReport() {
  const reason = document.querySelector('input[name=report_reason]:checked')?.value;
  if (!reason) { alert('Please select a reason.'); return; }
  const details = document.getElementById('report-details').value;
  const btn = document.getElementById('report-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Submitting…';

  try {
    const res = await fetch('{{ route('report.store') }}', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
      body: JSON.stringify({reportable_type: _reportType, reportable_id: _reportId, reason, details})
    });
    const data = await res.json();
    document.getElementById('report-body').innerHTML =
      '<div class="report-success"><i class="fa-solid fa-circle-check"></i><strong>' + data.message + '</strong></div>';
    setTimeout(closeReportModal, 2500);
  } catch(e) {
    btn.disabled = false;
    btn.textContent = 'Submit Report';
    alert('Something went wrong. Please try again.');
  }
}
</script>
@endauth

<!-- MOBILE BOTTOM TAB BAR -->
<nav class="mobile-tab-bar" id="mobile-tab-bar">
  <a href="{{ route('home') }}" class="mob-tab {{ request()->routeIs('home') ? 'active' : '' }}">
    <i class="fa-solid fa-house"></i>
    <span>Home</span>
  </a>
  <a href="{{ route('classifieds.index') }}" class="mob-tab {{ request()->routeIs('classifieds.*') ? 'active' : '' }}">
    <i class="fa-solid fa-tag"></i>
    <span>Ads</span>
  </a>
  {{-- Centre post button --}}
  @auth
  <a href="{{ route('post.create') }}" class="mob-tab-post">
    <div class="mob-tab-post-circle"><i class="fa-solid fa-plus"></i></div>
    <span>Post</span>
  </a>
  @else
  <a href="{{ route('register') }}" class="mob-tab-post">
    <div class="mob-tab-post-circle"><i class="fa-solid fa-plus"></i></div>
    <span>Post</span>
  </a>
  @endauth
  <a href="{{ route('jobs.index') }}" class="mob-tab {{ request()->routeIs('jobs.*') ? 'active' : '' }}">
    <i class="fa-solid fa-briefcase"></i>
    <span>Jobs</span>
  </a>
  <a href="#" class="mob-tab" onclick="toggleDrawer();return false">
    <i class="fa-solid fa-grip"></i>
    <span>More</span>
  </a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
@stack('scripts')
<script>
// ── OLX-STYLE LOCATION MODAL ──────────────────────────────────────
var _coordToProvince = [
  {name:'Ontario',lat:[41.7,56.9],lng:[-95.2,-74.3]},
  {name:'British Columbia',lat:[48.3,60.0],lng:[-139.1,-114.0]},
  {name:'Alberta',lat:[49.0,60.0],lng:[-120.0,-110.0]},
  {name:'Quebec',lat:[44.9,62.6],lng:[-79.8,-57.1]},
  {name:'Manitoba',lat:[49.0,60.0],lng:[-102.0,-88.9]},
  {name:'Saskatchewan',lat:[49.0,60.0],lng:[-110.0,-101.4]},
  {name:'Nova Scotia',lat:[43.3,47.1],lng:[-66.4,-59.7]},
  {name:'New Brunswick',lat:[44.6,48.1],lng:[-69.1,-63.7]},
];

var _citiesCache = {};
var _currentProvince = '';
var _currentCity = '';

function guessProvinceFromCoords(lat, lng) {
  for (var i = 0; i < _coordToProvince.length; i++) {
    var p = _coordToProvince[i];
    if (lat >= p.lat[0] && lat <= p.lat[1] && lng >= p.lng[0] && lng <= p.lng[1]) return p.name;
  }
  return null;
}

function openLocationModal() {
  document.getElementById('loc-modal').classList.add('open');
  document.body.style.overflow = 'hidden';
  document.getElementById('loc-city-search').value = '';
  // Show province step by default
  showProvinceStep();
}
function closeLocationModal() {
  document.getElementById('loc-modal').classList.remove('open');
  document.body.style.overflow = '';
}

function showProvinceStep() {
  document.getElementById('loc-step-province').style.display = 'flex';
  document.getElementById('loc-step-city').style.display = 'none';
  document.getElementById('loc-modal-title').textContent = 'Select Location';
  document.getElementById('loc-back-btn').style.display = 'none';
}

function showCityStep(province) {
  _currentProvince = province;
  document.getElementById('loc-step-province').style.display = 'none';
  document.getElementById('loc-step-city').style.display = 'flex';
  document.getElementById('loc-modal-title').textContent = province;
  document.getElementById('loc-back-btn').style.display = 'flex';
  document.getElementById('loc-city-search').value = '';
  loadCitiesForModal(province);
}

function loadCitiesForModal(province) {
  var list = document.getElementById('loc-city-list');
  if (_citiesCache[province]) { renderCities(_citiesCache[province]); return; }
  list.innerHTML = '<div style="text-align:center;padding:30px;color:#9ca3af"><i class="fa-solid fa-spinner fa-spin"></i> Loading cities…</div>';
  fetch('{{ route("locations.cities") }}?province=' + encodeURIComponent(province))
    .then(r => r.json()).then(cities => {
      _citiesCache[province] = cities;
      renderCities(cities);
    }).catch(() => {
      list.innerHTML = '<div style="text-align:center;padding:30px;color:#9ca3af">Could not load cities.</div>';
    });
}

function renderCities(cities) {
  var search = document.getElementById('loc-city-search').value.toLowerCase();
  var filtered = search ? cities.filter(c => c.toLowerCase().includes(search)) : cities;
  var list = document.getElementById('loc-city-list');
  var html = '<div class="loc-item" onclick="applyLocation(\'' + _currentProvince + '\',\'\')"><i class="fa-solid fa-building-columns loc-item-icon"></i><span>All cities in ' + _currentProvince + '</span></div>';
  filtered.forEach(function(city) {
    html += '<div class="loc-item" onclick="applyLocation(\'' + _currentProvince + '\',\'' + city.replace(/'/g,"\\'") + '\')"><i class="fa-solid fa-location-dot loc-item-icon"></i><span>' + city + '</span></div>';
  });
  if (filtered.length === 0) html = '<div style="text-align:center;padding:30px;color:#9ca3af">No cities found.</div>';
  list.innerHTML = html;
}

function applyLocation(province, city) {
  _currentProvince = province;
  _currentCity = city;
  localStorage.setItem('gobazaar_province', province);
  localStorage.setItem('gobazaar_city', city);
  closeLocationModal();

  // Reload current page with new location params
  var url = new URL(window.location.href);
  if (province) {
    url.searchParams.set('province', province);
    if (city) url.searchParams.set('city', city);
    else url.searchParams.delete('city');
  } else {
    url.searchParams.delete('province');
    url.searchParams.delete('city');
  }
  url.searchParams.delete('page'); // reset pagination
  window.location.href = url.toString();
}

function clearLocation() {
  localStorage.removeItem('gobazaar_province');
  localStorage.removeItem('gobazaar_city');
  _currentProvince = ''; _currentCity = '';
  var el = document.getElementById('nav-location-text');
  if (el) el.textContent = 'All Canada';
  var url = new URL(window.location.href);
  url.searchParams.delete('province');
  url.searchParams.delete('city');
  url.searchParams.delete('page');
  var remaining = url.searchParams.toString();
  window.location.href = remaining ? (url.pathname + '?' + remaining) : url.pathname;
}

// Clear ALL filters (location + search + category etc.) → go to clean page
function clearAllFilters() {
  localStorage.removeItem('gobazaar_province');
  localStorage.removeItem('gobazaar_city');
  _currentProvince = ''; _currentCity = '';
  window.location.href = window.location.pathname;
}

function detectMyLocation() {
  var btn = document.getElementById('loc-detect-btn');
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Detecting…';
  btn.disabled = true;
  if (!navigator.geolocation) {
    btn.innerHTML = '<i class="fa-solid fa-crosshairs"></i> Not supported';
    btn.disabled = false; return;
  }
  navigator.geolocation.getCurrentPosition(function(pos) {
    var province = guessProvinceFromCoords(pos.coords.latitude, pos.coords.longitude);
    btn.innerHTML = '<i class="fa-solid fa-crosshairs"></i> Detect My Location';
    btn.disabled = false;
    if (province) { showCityStep(province); }
    else { btn.innerHTML = '<i class="fa-solid fa-exclamation-circle"></i> Location not found'; setTimeout(()=>{ btn.innerHTML='<i class="fa-solid fa-crosshairs"></i> Detect My Location'; },2000); }
  }, function() {
    btn.innerHTML = '<i class="fa-solid fa-crosshairs"></i> Detect My Location';
    btn.disabled = false;
  }, {timeout: 6000});
}

// City search filter
document.addEventListener('DOMContentLoaded', function() {
  var inp = document.getElementById('loc-city-search');
  if (inp) inp.addEventListener('input', function() { if (_currentProvince) renderCities(_citiesCache[_currentProvince] || []); });
});

// Restore on load + inject location into nav links
(function() {
  var prov = localStorage.getItem('gobazaar_province') || '';
  var city = localStorage.getItem('gobazaar_city') || '';
  _currentProvince = prov; _currentCity = city;
  var label = city ? city + ', ' + prov : (prov || 'All Canada');
  var el = document.getElementById('nav-location-text');
  if (el) el.textContent = label;

  // Inject province/city into subnav + sidebar links & auto-redirect
  if (prov) {
    injectLocationParams(prov, city);
  }

  // Update location banner on page (JS version for when banner is hidden)
  updateLocationBanner(prov, city);
})();

function injectLocationParams(prov, city) {
  // All pages that support province/city filtering (including home)
  var filterableRoutes = ['/', '/classifieds', '/jobs', '/events', '/directory'];

  // Inject into all subnav + drawer links
  document.querySelectorAll('.subnav-inner a, .nav-drawer-panel a, .drawer-link').forEach(function(link) {
    var href = link.getAttribute('href');
    if (!href || href === '#' || href.startsWith('mailto') || href.startsWith('javascript')) return;
    try {
      var url = new URL(href, window.location.origin);
      var isFilterable = filterableRoutes.some(function(r) {
        return r === '/' ? url.pathname === '/' : url.pathname.startsWith(r);
      });
      if (!isFilterable) return;
      if (!url.searchParams.get('province')) url.searchParams.set('province', prov);
      if (city && !url.searchParams.get('city')) url.searchParams.set('city', city);
      link.setAttribute('href', url.pathname + '?' + url.searchParams.toString());
    } catch(e) {}
  });

  // Auto-inject on current page if province not already in URL
  var curPath = window.location.pathname;
  var isFilterable = filterableRoutes.some(function(r) {
    return r === '/' ? curPath === '/' : curPath.startsWith(r);
  });
  if (isFilterable) {
    var cur = new URL(window.location.href);
    if (!cur.searchParams.get('province')) {
      cur.searchParams.set('province', prov);
      if (city) cur.searchParams.set('city', city);
      window.location.replace(cur.toString());
    }
  }
}

function updateLocationBanner(prov, city) {
  var banner = document.getElementById('location-banner');
  if (!banner) return;
  if (prov) {
    var label = city ? city + ', ' + prov : prov;
    document.getElementById('location-banner-text').textContent = 'Showing results for: ' + label;
    banner.style.display = 'flex';
  } else {
    banner.style.display = 'none';
  }
}

function toggleDrawer() {
  var drawer = document.getElementById('nav-drawer');
  var toggle = document.getElementById('nav-toggle');
  var isOpen = drawer.classList.contains('open');
  drawer.classList.toggle('open', !isOpen);
  toggle.classList.toggle('open', !isOpen);
  document.body.style.overflow = isOpen ? '' : 'hidden';
}

function navSearch() {
  var q = document.getElementById('nav-search-input')?.value.trim();
  var cat = document.getElementById('nav-search-cat')?.value;
  var params = new URLSearchParams();
  if (q) params.set('search', q);
  var base = cat === 'directory' ? '{{ route("directory.index") }}'
           : cat === 'jobs'      ? '{{ route("jobs.index") }}'
           : cat === 'events'    ? '{{ route("events.index") }}'
           : '{{ route("classifieds.index") }}';
  window.location.href = base + (q ? '?' + params.toString() : '');
}
document.getElementById('nav-search-input')?.addEventListener('keydown', function(e){
  if (e.key === 'Enter') navSearch();
});

// Chat unread badge
@auth
(function() {
  let _lastNavUnread = 0;
  function refreshChatBadge() {
    fetch('{{ route('chat.unread') }}', {headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}})
      .then(r => r.json())
      .then(data => {
        const badge = document.getElementById('nav-chat-badge');
        if (badge) {
          if (data.count > 0) {
            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.style.display = 'flex';
          } else {
            badge.style.display = 'none';
          }
        }
        // Show browser notification from any page
        if (data.count > _lastNavUnread && 'Notification' in window && Notification.permission === 'granted') {
          const notif = new Notification('New message — GoBazaar', {
            body: 'You have ' + data.count + ' unread message' + (data.count > 1 ? 's' : ''),
            icon: '/favicon.ico',
            tag: 'gobazaar-nav',
            renotify: true,
          });
          notif.onclick = function() {
            notif.close();
            window.open(data.conv_url || '{{ route('chat.inbox') }}', '_self');
            window.focus();
          };
        }
        _lastNavUnread = data.count;
      }).catch(() => {});
  }
  refreshChatBadge();
  setInterval(refreshChatBadge, 30000);
})();
@endauth

function loadCities(citySelectId, province) {
  var sel = document.getElementById(citySelectId);
  if (!sel) return;
  sel.innerHTML = '<option value="">Loading…</option>';
  fetch('{{ route('locations.cities') }}?province=' + encodeURIComponent(province))
    .then(r => r.json())
    .then(cities => {
      sel.innerHTML = '<option value="">All Cities</option>';
      cities.forEach(c => {
        var o = document.createElement('option');
        o.value = c; o.textContent = c;
        sel.appendChild(o);
      });
    });
}
</script>

<script>
// ── GLOBAL IMAGE FALLBACK ─────────────────────────────────────────
(function(){
  var PH = '/images/placeholder.svg';
  function applyFallback(img) {
    if (img._gbFallback) return;
    img._gbFallback = true;
    img.onerror = function() {
      if (this.src !== window.location.origin + PH && this.src !== PH) {
        this.onerror = null;
        this.src = PH;
        this.style.objectFit = 'contain';
        this.style.padding = '8px';
        this.style.background = '#f0ede8';
      }
    };
    // Trigger for already-broken images
    if (img.complete && img.naturalWidth === 0 && img.src && !img.src.endsWith(PH)) {
      img.onerror();
    }
  }
  // Apply to all existing images
  document.querySelectorAll('img').forEach(applyFallback);
  // Apply to future images added dynamically
  var obs = new MutationObserver(function(mutations) {
    mutations.forEach(function(m) {
      m.addedNodes.forEach(function(node) {
        if (node.nodeType !== 1) return;
        if (node.tagName === 'IMG') applyFallback(node);
        node.querySelectorAll && node.querySelectorAll('img').forEach(applyFallback);
      });
    });
  });
  obs.observe(document.body, { childList: true, subtree: true });
})();
</script>
</body>
</html>
