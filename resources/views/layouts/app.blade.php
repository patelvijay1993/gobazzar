<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title', 'GoBazzar') — Indian Community in Canada</title>
<meta name="description" content="@yield('description', 'Best community website for Indians living in Canada.')">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --red:#C0392B;--red2:#E74C3C;--red-pale:#FEF2F1;--red-dark:#96281B;
  --dark:#1A0A09;--dark2:#2C1A19;
  --bg:#FFFAF9;--surface:#fff;
  --border:#F0E8E7;--border2:#E2D0CF;
  --text:#1C1212;--muted:#7A5555;--hint:#B89090;
  --green:#16A34A;--green-bg:#F0FDF4;
  --amber:#D97706;--amber-bg:#FFFBEB;
  --blue:#1D4ED8;--blue-bg:#EFF6FF;
  --gold:#F59E0B;
  --r:8px;--rl:14px;
  --sh:0 1px 3px rgba(26,10,9,.07),0 4px 14px rgba(26,10,9,.05);
  --fh:'Poppins',sans-serif;--fb:'DM Sans',sans-serif;
}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--fb);background:var(--bg);color:var(--text);font-size:14px;line-height:1.6}
a{text-decoration:none;color:inherit}
button{cursor:pointer;font-family:var(--fb);border:none;background:none}
input,select,textarea{font-family:var(--fb);outline:none}

/* NAVBAR */
nav{background:var(--surface);border-bottom:2px solid var(--border);position:sticky;top:0;z-index:200;overflow:visible}
.nav-inner{max-width:1200px;margin:0 auto;display:flex;align-items:stretch;padding:0 16px;gap:0}
.nav-logo{display:flex;align-items:center;gap:10px;padding:10px 16px 10px 0;text-decoration:none;border-right:1px solid var(--border);margin-right:8px;flex-shrink:0}
.nav-links{display:flex;align-items:stretch;flex:1;gap:0;overflow:visible}

.nav-link{display:flex;align-items:center;padding:0 12px;font-size:12.5px;font-weight:500;color:var(--text);border-bottom:2px solid transparent;transition:all .15s;white-space:nowrap}
.nav-link:hover,.nav-link.active{color:var(--red);border-bottom-color:var(--red)}

/* NAV DROPDOWN */
.nav-dropdown{position:relative;display:flex;align-items:stretch}
.nav-dropdown-toggle{display:flex;align-items:center;gap:4px;padding:0 12px;font-size:12.5px;font-weight:500;color:var(--text);border-bottom:2px solid transparent;transition:all .15s;white-space:nowrap;cursor:pointer;background:none;border-top:none;border-left:none;border-right:none;font-family:var(--fb)}
.nav-dropdown-toggle:hover,.nav-dropdown.open .nav-dropdown-toggle{color:var(--red);border-bottom-color:var(--red)}
.nav-dropdown-toggle svg{transition:transform .2s}
.nav-dropdown.open .nav-dropdown-toggle svg{transform:rotate(180deg)}
.nav-dropdown-menu{display:none;position:absolute;top:100%;left:0;min-width:200px;background:var(--surface);border:1.5px solid var(--border);border-radius:var(--r);box-shadow:0 8px 24px rgba(26,10,9,.12);z-index:500;padding:6px 0;margin-top:2px}
.nav-dropdown.open .nav-dropdown-menu{display:block}
.nav-dropdown-item{display:flex;align-items:center;justify-content:space-between;padding:9px 16px;font-size:12.5px;color:var(--text);transition:background .1s;white-space:nowrap}
.nav-dropdown-item:hover{background:var(--red-pale);color:var(--red)}
.nav-dropdown-item svg{opacity:.4;flex-shrink:0}
.nav-dropdown.open-active .nav-dropdown-toggle{color:var(--red);border-bottom-color:var(--red)}
.nav-right{display:flex;align-items:center;gap:8px;margin-left:auto;flex-shrink:0}
.btn{padding:7px 16px;border-radius:var(--r);font-size:12.5px;font-weight:500;display:inline-flex;align-items:center;gap:5px;transition:all .15s;cursor:pointer}
.btn-red{background:var(--red);color:#fff}
.btn-red:hover{background:var(--red-dark)}
.btn-ghost{border:1.5px solid var(--border2);color:var(--muted)}
.btn-ghost:hover{border-color:var(--red);color:var(--red)}

/* HAMBURGER */
.nav-toggle{display:none;flex-direction:column;gap:5px;padding:10px 4px;cursor:pointer;background:none;border:none;flex-shrink:0}
.nav-toggle span{display:block;width:22px;height:2px;background:var(--text);border-radius:2px;transition:all .2s}
.nav-toggle.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.nav-toggle.open span:nth-child(2){opacity:0}
.nav-toggle.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}

/* MOBILE NAV DRAWER */
.nav-drawer{display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:300}
.nav-drawer-bg{position:absolute;inset:0;background:rgba(0,0,0,.5)}
.nav-drawer-panel{position:absolute;top:0;left:0;width:280px;height:100%;background:var(--surface);overflow-y:auto;padding:20px 0;box-shadow:4px 0 20px rgba(0,0,0,.15)}
.nav-drawer.open{display:block}
.drawer-logo{display:flex;align-items:center;gap:10px;padding:0 20px 20px;border-bottom:1px solid var(--border);margin-bottom:8px}
.drawer-link{display:flex;align-items:center;padding:13px 20px;font-size:14px;font-weight:500;color:var(--text);border-left:3px solid transparent;transition:all .15s;text-decoration:none}
.drawer-link:hover,.drawer-link.active{color:var(--red);border-left-color:var(--red);background:var(--red-pale)}
.drawer-divider{height:1px;background:var(--border);margin:8px 0}
.drawer-actions{padding:16px 20px;display:flex;flex-direction:column;gap:8px}
.drawer-actions .btn{justify-content:center;text-align:center}

@media(max-width:900px){
  .nav-links{display:none}
  .nav-right .btn-ghost:not(.btn-post){display:none}
  .nav-toggle{display:flex}
}
@media(max-width:480px){
  .nav-right .btn{padding:7px 12px;font-size:12px}
  .nav-right .btn-ghost{display:none}
}

/* FOOTER */
footer{background:var(--dark);color:rgba(255,255,255,.5);padding:30px 20px;margin-top:40px;font-size:12px;text-align:center}
footer a{color:rgba(255,255,255,.6)}
footer a:hover{color:#fff}
@media(max-width:600px){footer{padding:20px 16px}}

/* CONTAINER */
.container{max-width:1200px;margin:0 auto;padding:20px}
@media(max-width:600px){.container{padding:14px}}

/* BREADCRUMB */
.breadcrumb{font-size:12px;color:var(--muted);margin-bottom:16px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.breadcrumb a{color:var(--red)}
.breadcrumb span{margin:0 6px;color:var(--hint)}

/* FLASH */
.flash{padding:12px 16px;border-radius:var(--r);margin-bottom:16px;font-size:13px}
.flash-success{background:var(--green-bg);color:var(--green);border:1px solid #bbf7d0}
.flash-error{background:var(--red-pale);color:var(--red);border:1px solid #fecaca}
</style>
@stack('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
</head>
<body>


<nav>
  <div class="nav-inner">
    <button class="nav-toggle" id="nav-toggle" onclick="toggleDrawer()" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
    <a href="{{ route('home') }}" class="nav-logo">
      <img src="{{ asset('images/logo.png') }}" alt="GoBazaar" style="height:44px;width:auto;display:block">
    </a>
    @php $navClassifiedCats = \App\Models\Category::where('type','classifieds')->where('is_active',true)->orderBy('sort_order')->get(); @endphp
    <div class="nav-links">
      <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
      {{-- Classifieds dropdown --}}
      <div class="nav-dropdown {{ request()->routeIs('classifieds.*') ? 'open-active' : '' }}" id="nav-cl-dropdown">
        <button class="nav-dropdown-toggle {{ request()->routeIs('classifieds.*') ? 'active' : '' }}" onclick="toggleNavDropdown('nav-cl-dropdown',event)">
          Classifieds
          <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 3.5L5 6.5L8 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <div class="nav-dropdown-menu">
          <a href="{{ route('classifieds.index') }}" class="nav-dropdown-item" style="font-weight:600;color:var(--red);border-bottom:1px solid var(--border);margin-bottom:4px">All Classifieds</a>
          @foreach($navClassifiedCats as $navCat)
            <a href="{{ route('classifieds.index', ['category' => $navCat->id]) }}" class="nav-dropdown-item">
              <span>{{ $navCat->icon }} {{ $navCat->name }}</span>
              <svg width="8" height="8" viewBox="0 0 8 8" fill="none"><path d="M2 1.5L5.5 4L2 6.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
          @endforeach
        </div>
      </div>
      <a href="{{ route('jobs.index') }}" class="nav-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">Jobs</a>
      <a href="{{ route('events.index') }}" class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}">Events</a>
      <a href="{{ route('directory.index') }}" class="nav-link {{ request()->routeIs('directory.*') ? 'active' : '' }}">Directory</a>
      <a href="{{ route('blog.index') }}" class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">Blog</a>
      <a href="{{ route('pricing') }}" class="nav-link {{ request()->routeIs('pricing*') ? 'active' : '' }}">Pricing</a>
    </div>
    <div class="nav-right">
      @auth
        <a href="{{ route('post.create') }}" class="btn btn-red">+ Post</a>
        <a href="{{ route('account') }}" class="btn btn-ghost">👤 {{ Str::limit(Auth::user()->name, 12) }}</a>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
          @csrf
          <button type="submit" class="btn btn-ghost">Logout</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="btn btn-ghost">Login</a>
        <a href="{{ route('register') }}" class="btn btn-red">Register Free</a>
      @endauth
    </div>
  </div>
</nav>

{{-- Mobile Nav Drawer --}}
<div class="nav-drawer" id="nav-drawer">
  <div class="nav-drawer-bg" onclick="toggleDrawer()"></div>
  <div class="nav-drawer-panel">
    <div class="drawer-logo">
      <img src="{{ asset('images/logo.png') }}" alt="GoBazaar" style="height:40px;width:auto;display:block">
    </div>
    <a href="{{ route('home') }}" class="drawer-link {{ request()->routeIs('home') ? 'active' : '' }}">🏠 Home</a>
    <a href="{{ route('classifieds.index') }}" class="drawer-link {{ request()->routeIs('classifieds.*') ? 'active' : '' }}">📋 Classifieds</a>
    @foreach($navClassifiedCats as $navCat)
      <a href="{{ route('classifieds.index', ['category' => $navCat->id]) }}" class="drawer-link" style="padding-left:36px;font-size:13px;color:var(--muted)">{{ $navCat->icon }} {{ $navCat->name }}</a>
    @endforeach
    <a href="{{ route('jobs.index') }}" class="drawer-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">💼 Jobs</a>
    <a href="{{ route('events.index') }}" class="drawer-link {{ request()->routeIs('events.*') ? 'active' : '' }}">📅 Events</a>
    <a href="{{ route('directory.index') }}" class="drawer-link {{ request()->routeIs('directory.*') ? 'active' : '' }}">🗂 Directory</a>
    <a href="{{ route('blog.index') }}" class="drawer-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">📰 Blog</a>
    <a href="{{ route('pricing') }}" class="drawer-link {{ request()->routeIs('pricing*') ? 'active' : '' }}">💳 Pricing</a>
    <div class="drawer-divider"></div>
    <div class="drawer-actions">
      @auth
        <a href="{{ route('post.create') }}" class="btn btn-red">+ Post Free Ad</a>
        <a href="{{ route('account') }}" class="btn btn-ghost">👤 My Account</a>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-ghost" style="width:100%;justify-content:center">Logout</button>
        </form>
      @else
        <a href="{{ route('register') }}" class="btn btn-red">Register Free</a>
        <a href="{{ route('login') }}" class="btn btn-ghost">Login</a>
      @endauth
    </div>
  </div>
</div>

@if(session('success'))
<div class="container"><div class="flash flash-success">{{ session('success') }}</div></div>
@endif

@yield('content')

<footer>
  <p>&copy; {{ date('Y') }} GoBazzar — Indian Community Portal in Canada</p>
  <p style="margin-top:8px">
    <a href="{{ route('home') }}">Home</a> &nbsp;·&nbsp;
    <a href="{{ route('classifieds.index') }}">Classifieds</a> &nbsp;·&nbsp;
    <a href="/gobazzar-app/public/admin">Admin</a>
  </p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
@stack('scripts')
<script>
function toggleNavDropdown(id, event) {
  event.stopPropagation();
  var el = document.getElementById(id);
  var isOpen = el.classList.contains('open');
  document.querySelectorAll('.nav-dropdown.open').forEach(function(d){ d.classList.remove('open'); });
  if (!isOpen) el.classList.add('open');
}
document.addEventListener('click', function(e) {
  if (!e.target.closest('.nav-dropdown')) {
    document.querySelectorAll('.nav-dropdown.open').forEach(function(d){ d.classList.remove('open'); });
  }
});

function toggleDrawer() {
  var drawer = document.getElementById('nav-drawer');
  var toggle = document.getElementById('nav-toggle');
  var isOpen = drawer.classList.contains('open');
  drawer.classList.toggle('open', !isOpen);
  toggle.classList.toggle('open', !isOpen);
  document.body.style.overflow = isOpen ? '' : 'hidden';
}
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
</body>
</html>
