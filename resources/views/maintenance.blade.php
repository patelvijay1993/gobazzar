<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Maintenance — GoBazaar</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{min-height:100vh;background:#0d1117;font-family:'Segoe UI',system-ui,sans-serif;display:flex;align-items:center;justify-content:center;overflow-x:hidden;overflow-y:auto;padding:24px 16px}

/* Grid background */
.grid-bg{position:fixed;inset:0;background-image:linear-gradient(rgba(26,58,143,.08) 1px,transparent 1px),linear-gradient(90deg,rgba(26,58,143,.08) 1px,transparent 1px);background-size:50px 50px;z-index:0;pointer-events:none}
.glow{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:min(800px,150vw);height:min(800px,150vw);background:radial-gradient(circle,rgba(26,58,143,.15) 0%,transparent 70%);z-index:0;pointer-events:none}

.container{position:relative;z-index:1;text-align:center;width:100%;max-width:560px}

/* Animated gear */
.gear-wrap{margin-bottom:28px;position:relative;display:inline-block}
.gear{font-size:clamp(56px,12vw,80px);display:inline-block;animation:spin 4s linear infinite;filter:drop-shadow(0 0 20px rgba(240,165,0,.4))}
.gear2{font-size:clamp(32px,7vw,48px);display:inline-block;animation:spin-rev 3s linear infinite;position:absolute;bottom:-8px;right:-16px;filter:drop-shadow(0 0 12px rgba(26,58,143,.5))}
@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
@keyframes spin-rev{from{transform:rotate(0deg)}to{transform:rotate(-360deg)}}

/* Logo */
.logo{display:inline-flex;align-items:center;gap:10px;margin-bottom:24px}
.logo-icon{width:44px;height:44px;background:linear-gradient(135deg,#1a3a8f,#2952c8);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px}
.logo-text{font-size:22px;font-weight:800;color:#fff}
.logo-text span{color:#f0a500}

.badge{display:inline-block;background:rgba(240,165,0,.1);border:1px solid rgba(240,165,0,.25);color:#f0a500;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:6px 16px;border-radius:100px;margin-bottom:16px}

h1{font-size:clamp(24px,5vw,48px);font-weight:900;color:#fff;margin-bottom:10px;line-height:1.2}
.subtitle{font-size:clamp(13px,3.5vw,16px);color:rgba(255,255,255,.5);margin-bottom:32px;line-height:1.7}

/* Status card */
.status-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:20px;margin-bottom:28px}
.status-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.05)}
.status-row:last-child{border-bottom:none;padding-bottom:0}
.status-row:first-child{padding-top:0}
.status-label{font-size:13px;color:rgba(255,255,255,.4);font-weight:500}
.status-val{display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600}
.dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.dot-green{background:#22c55e;box-shadow:0 0 8px rgba(34,197,94,.6);animation:pulse-dot 2s ease infinite}
.dot-yellow{background:#f0a500;box-shadow:0 0 8px rgba(240,165,0,.6);animation:pulse-dot 2s ease infinite}
@keyframes pulse-dot{0%,100%{opacity:1}50%{opacity:.5}}

/* Progress bar */
.maint-progress{margin-bottom:28px}
.maint-progress-label{display:flex;justify-content:space-between;margin-bottom:8px;font-size:12px;color:rgba(255,255,255,.35);font-weight:600}
.maint-bar{height:4px;background:rgba(255,255,255,.06);border-radius:100px;overflow:hidden}
.maint-fill{height:100%;background:linear-gradient(90deg,#1a3a8f,#f0a500);border-radius:100px;width:65%;animation:progress-anim 3s ease-in-out infinite alternate}
@keyframes progress-anim{from{width:55%}to{width:75%}}

/* Contact */
.contact{font-size:13px;color:rgba(255,255,255,.35)}
.contact a{color:#f0a500;text-decoration:none;font-weight:600}

/* Floating particles */
.particle{position:fixed;border-radius:50%;pointer-events:none;animation:rise var(--d) linear infinite var(--delay)}
@keyframes rise{0%{transform:translateY(100vh) scale(0);opacity:0}10%{opacity:.6}90%{opacity:.1}100%{transform:translateY(-10vh) scale(1);opacity:0}}
</style>
</head>
<body>

<div class="grid-bg"></div>
<div class="glow"></div>

<div class="container">
  <div class="logo">
    <div class="logo-icon">🏪</div>
    <div class="logo-text">Go<span>Bazaar</span></div>
  </div>

  <div class="gear-wrap">
    <div class="gear">⚙️</div>
    <div class="gear2">⚙️</div>
  </div>

  <div class="badge">🔧 Scheduled Maintenance</div>
  <h1>We'll Be Back Shortly</h1>
  <p class="subtitle">We're performing scheduled maintenance to improve your experience. Thank you for your patience — it won't take long!</p>

  <div class="status-card">
    <div class="status-row">
      <span class="status-label">Database</span>
      <span class="status-val"><div class="dot dot-green"></div><span style="color:#22c55e">Online</span></span>
    </div>
    <div class="status-row">
      <span class="status-label">Application</span>
      <span class="status-val"><div class="dot dot-yellow"></div><span style="color:#f0a500">Updating</span></span>
    </div>
    <div class="status-row">
      <span class="status-label">CDN / Assets</span>
      <span class="status-val"><div class="dot dot-green"></div><span style="color:#22c55e">Online</span></span>
    </div>
    <div class="status-row">
      <span class="status-label">Estimated Time</span>
      <span class="status-val" style="color:rgba(255,255,255,.6)">⏱ A few minutes</span>
    </div>
  </div>

  <div class="maint-progress">
    <div class="maint-progress-label"><span>Maintenance Progress</span><span>Almost done...</span></div>
    <div class="maint-bar"><div class="maint-fill"></div></div>
  </div>

  <p class="contact">Need urgent help? Email us at <a href="mailto:gobazaar.ca@gmail.com">gobazaar.ca@gmail.com</a></p>
</div>

<script>
// Floating particles
for(let i=0;i<20;i++){
  const p=document.createElement('div');
  p.className='particle';
  const size=Math.random()*4+2;
  p.style.cssText=`width:${size}px;height:${size}px;left:${Math.random()*100}%;background:rgba(${Math.random()>0.5?'26,58,143':'240,165,0'},.3);--d:${8+Math.random()*12}s;--delay:-${Math.random()*12}s`;
  document.body.appendChild(p);
}
</script>
</body>
</html>
