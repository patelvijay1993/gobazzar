<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Coming Soon — GoBazaar</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{--primary:#1a3a8f;--accent:#f0a500;--bg:#0a0f2e}
body{min-height:100vh;background:var(--bg);font-family:'Segoe UI',system-ui,sans-serif;overflow-x:hidden;overflow-y:auto;display:flex;align-items:center;justify-content:center}

/* Animated background */
.bg-wrap{position:fixed;inset:0;z-index:0;pointer-events:none}
.bg-gradient{position:absolute;inset:0;background:radial-gradient(ellipse at 20% 50%,rgba(26,58,143,.4) 0%,transparent 60%),radial-gradient(ellipse at 80% 20%,rgba(240,165,0,.15) 0%,transparent 50%)}

/* Floating orbs */
.orb{position:absolute;border-radius:50%;filter:blur(60px);animation:float 8s ease-in-out infinite}
.orb1{width:min(400px,80vw);height:min(400px,80vw);background:rgba(26,58,143,.3);top:-100px;left:-100px;animation-delay:0s}
.orb2{width:min(300px,60vw);height:min(300px,60vw);background:rgba(240,165,0,.15);bottom:-50px;right:-50px;animation-delay:-3s}
.orb3{width:min(200px,40vw);height:min(200px,40vw);background:rgba(99,102,241,.2);top:50%;left:50%;animation-delay:-6s}
@keyframes float{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(30px,-20px) scale(1.05)}66%{transform:translate(-20px,10px) scale(.95)}}

/* Main container */
.container{position:relative;z-index:1;text-align:center;padding:40px 20px;max-width:700px;width:100%}

/* Logo */
.logo{display:inline-flex;align-items:center;gap:12px;margin-bottom:32px}
.logo-icon{width:48px;height:48px;background:linear-gradient(135deg,var(--primary),#2952c8);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 8px 32px rgba(26,58,143,.4)}
.logo-text{font-size:24px;font-weight:800;color:#fff;letter-spacing:-.5px}
.logo-text span{color:var(--accent)}

/* Heading */
.badge{display:inline-block;background:rgba(240,165,0,.15);border:1px solid rgba(240,165,0,.3);color:var(--accent);font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:6px 16px;border-radius:100px;margin-bottom:20px}
h1{font-size:clamp(28px,7vw,72px);font-weight:900;color:#fff;line-height:1.1;margin-bottom:14px;text-shadow:0 4px 40px rgba(26,58,143,.5)}
h1 span{background:linear-gradient(135deg,var(--accent),#ff8c00);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.subtitle{font-size:clamp(14px,3.5vw,17px);color:rgba(255,255,255,.6);margin-bottom:36px;line-height:1.7}

/* Countdown */
.countdown-wrap{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:36px;align-items:center}
.cd-box{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);backdrop-filter:blur(20px);border-radius:16px;padding:16px 8px;position:relative;overflow:hidden}
.cd-box::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(26,58,143,.2),transparent)}
.cd-num{font-size:clamp(28px,6vw,52px);font-weight:900;color:#fff;line-height:1;display:block;font-variant-numeric:tabular-nums}
.cd-lbl{font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-top:6px;display:block}

/* No date state */
.no-date{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:16px 24px;display:inline-block;color:rgba(255,255,255,.5);font-size:14px;margin-bottom:36px}

/* Progress bar */
.progress-wrap{margin-bottom:36px}
.progress-label{display:flex;justify-content:space-between;margin-bottom:10px;font-size:12px;color:rgba(255,255,255,.4);font-weight:600;letter-spacing:.5px}
.progress-bar{height:6px;background:rgba(255,255,255,.08);border-radius:100px;overflow:hidden}
.progress-fill{height:100%;background:linear-gradient(90deg,var(--primary),var(--accent));border-radius:100px;transition:width .3s}
@keyframes shimmer{0%,100%{opacity:1}50%{opacity:.7}}

/* Notify */
.notify-wrap{margin-bottom:28px}
.notify-form{display:flex;flex-direction:column;gap:10px;max-width:440px;margin:0 auto}
.notify-input{width:100%;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:12px;padding:14px 20px;color:#fff;font-size:15px;outline:none;transition:.2s}
.notify-input::placeholder{color:rgba(255,255,255,.35)}
.notify-input:focus{border-color:var(--accent);background:rgba(255,255,255,.12)}
.notify-btn{width:100%;background:linear-gradient(135deg,var(--accent),#e09400);color:#fff;border:none;border-radius:12px;padding:14px 28px;font-size:15px;font-weight:700;cursor:pointer;transition:.2s}
.notify-btn:active{transform:scale(.98)}

/* Social */
.social{display:flex;justify-content:center;gap:12px}
.soc-btn{width:48px;height:48px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.5);text-decoration:none;font-size:18px}

/* Stars */
.stars{position:fixed;inset:0;pointer-events:none;z-index:0}
.star{position:absolute;width:2px;height:2px;background:#fff;border-radius:50%;animation:twinkle var(--d) ease-in-out infinite var(--delay)}
@keyframes twinkle{0%,100%{opacity:0;transform:scale(.5)}50%{opacity:var(--op);transform:scale(1)}}

@media(min-width:480px){
  .notify-form{flex-direction:row}
  .notify-input{flex:1}
  .notify-btn{width:auto}
}
</style>
</head>
<body>

<div class="stars" id="stars"></div>
<div class="bg-wrap">
  <div class="bg-gradient"></div>
  <div class="orb orb1"></div>
  <div class="orb orb2"></div>
  <div class="orb orb3"></div>
</div>

<div class="container">
  <div class="logo">
    <img src="/images/logo.png" alt="GoBazaar" style="height:56px;width:auto;object-fit:contain;filter:drop-shadow(0 4px 16px rgba(26,58,143,.4))">
  </div>

  <div class="badge">⚡ Something Amazing is Coming</div>
  <h1>We're Launching <span>Soon!</span></h1>
  <p class="subtitle">Canada's #1 South Asian community marketplace is getting a major upgrade. Get ready for an even better experience.</p>

  @php
    $launchDate = \App\Models\Setting::get('coming_soon_date');
    $hasDate    = $launchDate && strtotime($launchDate) > time();
  @endphp

  @if($hasDate)
  <div class="countdown-wrap" id="countdown">
    <div class="cd-box"><span class="cd-num" id="cd-days">00</span><span class="cd-lbl">Days</span></div>
    <div class="cd-box"><span class="cd-num" id="cd-hours">00</span><span class="cd-lbl">Hours</span></div>
    <div class="cd-box"><span class="cd-num" id="cd-mins">00</span><span class="cd-lbl">Mins</span></div>
    <div class="cd-box"><span class="cd-num" id="cd-secs">00</span><span class="cd-lbl">Secs</span></div>
  </div>

  <div class="progress-wrap">
    <div class="progress-label"><span>Progress</span><span id="progress-pct">0%</span></div>
    <div class="progress-bar"><div class="progress-fill" id="progress-fill" style="width:0%"></div></div>
  </div>
  @else
  <div class="no-date">🚀 &nbsp; Launching very soon — stay tuned!</div>
  @endif

  <div class="notify-wrap">
    <div class="notify-form">
      <input type="email" class="notify-input" placeholder="Enter your email for early access..." id="notify-email">
      <button class="notify-btn" onclick="notifyMe()">Notify Me →</button>
    </div>
  </div>

  <div class="social">
    <a href="#" class="soc-btn" title="Facebook">f</a>
    <a href="#" class="soc-btn" title="Instagram">📷</a>
    <a href="#" class="soc-btn" title="Twitter/X">𝕏</a>
  </div>
</div>

<script>
// Stars
const starsEl = document.getElementById('stars');
for(let i=0;i<120;i++){
  const s=document.createElement('div');
  s.className='star';
  s.style.cssText=`left:${Math.random()*100}%;top:${Math.random()*100}%;--d:${2+Math.random()*4}s;--delay:-${Math.random()*4}s;--op:${.3+Math.random()*.7}`;
  starsEl.appendChild(s);
}

@if($hasDate)
// Countdown
const launch = new Date('{{ $launchDate }}').getTime();
const start  = {{ \App\Models\Setting::get('coming_soon_start_date') ? 'new Date("'.\App\Models\Setting::get('coming_soon_start_date').'").getTime()' : 'Date.now() - 86400000' }};

function pad(n){return String(n).padStart(2,'0')}
function tick(){
  const now  = Date.now();
  const diff = launch - now;
  if(diff <= 0){
    document.getElementById('countdown').innerHTML='<div style="color:#f0a500;font-size:24px;font-weight:900">🎉 We\'re Live!</div>';
    return;
  }
  const d=Math.floor(diff/86400000);
  const h=Math.floor((diff%86400000)/3600000);
  const m=Math.floor((diff%3600000)/60000);
  const s=Math.floor((diff%60000)/1000);
  document.getElementById('cd-days').textContent=pad(d);
  document.getElementById('cd-hours').textContent=pad(h);
  document.getElementById('cd-mins').textContent=pad(m);
  document.getElementById('cd-secs').textContent=pad(s);

  // Progress
  const total=launch-start;
  const elapsed=now-start;
  const pct=Math.min(100,Math.max(0,Math.round(elapsed/total*100)));
  document.getElementById('progress-fill').style.width=pct+'%';
  document.getElementById('progress-pct').textContent=pct+'%';
}
tick();
setInterval(tick,1000);
@endif

function notifyMe(){
  const el=document.getElementById('notify-email');
  const email=el.value.trim();
  if(!email||!email.includes('@')){el.style.borderColor='#ef4444';return;}
  el.style.borderColor='#22c55e';
  el.value='';
  el.placeholder='✅ You\'re on the list! We\'ll notify you.';
  setTimeout(()=>{el.style.borderColor='rgba(255,255,255,.15)';el.placeholder='Enter your email for early access...'},4000);
}
</script>
</body>
</html>
