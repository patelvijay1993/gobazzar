<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>GoBazaar — Enable Notifications</title>
<style>
body{font-family:sans-serif;background:#1a3a8f;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
.box{background:#fff;border-radius:16px;padding:32px 28px;max-width:400px;width:90%;text-align:center}
h2{color:#1a3a8f;margin-bottom:8px}
p{color:#555;font-size:14px;margin-bottom:24px}
button{background:#1a3a8f;color:#fff;border:none;border-radius:10px;padding:14px 28px;font-size:15px;font-weight:700;cursor:pointer;width:100%}
button:disabled{opacity:.5}
#status{margin-top:16px;font-size:13px;color:#333;min-height:20px}
.ok{color:#2e7d32;font-weight:700}
.err{color:#c62828;font-weight:700}
</style>
</head>
<body>
<div class="box">
  <div style="font-size:48px;margin-bottom:12px">🔔</div>
  <h2>GoBazaar Notifications</h2>
  <p>Allow notifications to get alerts for new chat messages even when the app is closed.</p>
  <button id="btn" onclick="enablePush()">Enable Notifications</button>
  <div id="status"></div>
</div>

<script>
const VAPID_URL    = '/push/vapid-key';
const SUBSCRIBE_URL = '/push/subscribe';
const CSRF = document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1]
           ? decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1])
           : '';

function log(msg, cls) {
  const el = document.getElementById('status');
  el.innerHTML += '<div class="' + (cls||'') + '">' + msg + '</div>';
}

function urlBase64ToUint8Array(base64String) {
  var padding = '='.repeat((4 - base64String.length % 4) % 4);
  var base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
  var raw     = atob(base64);
  var arr     = new Uint8Array(raw.length);
  for (var i = 0; i < raw.length; i++) arr[i] = raw.charCodeAt(i);
  return arr;
}

async function enablePush() {
  const btn = document.getElementById('btn');
  btn.disabled = true;
  document.getElementById('status').innerHTML = '';

  // 1. Check SW support
  if (!('serviceWorker' in navigator)) { log('❌ Service Worker not supported', 'err'); btn.disabled=false; return; }
  if (!('PushManager' in window))      { log('❌ Push not supported on this browser', 'err'); btn.disabled=false; return; }

  log('⏳ Registering service worker…');
  let reg;
  try {
    reg = await navigator.serviceWorker.register('/sw.js');
    await navigator.serviceWorker.ready;
    log('✅ Service worker ready');
  } catch(e) { log('❌ SW error: ' + e.message, 'err'); btn.disabled=false; return; }

  // 2. Get VAPID key
  log('⏳ Getting VAPID key…');
  let vapidKey;
  try {
    const r = await fetch(VAPID_URL);
    const d = await r.json();
    vapidKey = d.key;
    if (!vapidKey) { log('❌ VAPID key missing from server', 'err'); btn.disabled=false; return; }
    log('✅ VAPID key: ' + vapidKey.substring(0,20) + '…');
  } catch(e) { log('❌ Could not fetch VAPID key: ' + e.message, 'err'); btn.disabled=false; return; }

  // 3. Request notification permission
  log('⏳ Requesting permission…');
  const perm = await Notification.requestPermission();
  log('Permission: ' + perm, perm === 'granted' ? 'ok' : 'err');
  if (perm !== 'granted') { btn.disabled=false; return; }

  // 4. Check existing or create new subscription
  log('⏳ Subscribing to push…');
  let sub;
  try {
    sub = await reg.pushManager.getSubscription();
    if (sub) {
      log('ℹ️ Existing subscription found — re-saving to server…');
    } else {
      sub = await reg.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapidKey),
      });
      log('✅ New subscription created');
    }
  } catch(e) { log('❌ Subscribe error: ' + e.message, 'err'); btn.disabled=false; return; }

  // 5. Save subscription to server
  log('⏳ Saving to server…');
  const json = sub.toJSON();
  log('Endpoint: ' + json.endpoint.substring(0,50) + '…');
  log('p256dh: ' + (json.keys?.p256dh ? json.keys.p256dh.substring(0,20)+'…' : '❌ MISSING'), json.keys?.p256dh ? '' : 'err');
  log('auth: ' + (json.keys?.auth ? json.keys.auth.substring(0,20)+'…' : '❌ MISSING'), json.keys?.auth ? '' : 'err');

  try {
    // CSRF token from cookie (Laravel sets XSRF-TOKEN cookie)
    const csrfToken = decodeURIComponent(
      document.cookie.split(';').map(c=>c.trim())
        .find(c=>c.startsWith('XSRF-TOKEN='))?.split('=')[1] || ''
    );

    const res = await fetch(SUBSCRIBE_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        endpoint:   json.endpoint,
        public_key: json.keys?.p256dh || null,
        auth_token: json.keys?.auth    || null,
      }),
    });

    if (res.status === 401) {
      log('❌ Not logged in! Please login first, then come back here.', 'err');
      btn.disabled = false; return;
    }

    const result = await res.json();
    log('Server response: ' + JSON.stringify(result), result.ok ? 'ok' : 'err');

    if (result.ok) {
      log('🎉 Done! You will now receive notifications.', 'ok');
      btn.textContent = '✅ Notifications Enabled';
      setTimeout(() => { window.location.href = '/'; }, 2000);
    }
  } catch(e) { log('❌ Save error: ' + e.message, 'err'); btn.disabled=false; }
}

// Auto-check status on load
window.addEventListener('load', function() {
  if ('Notification' in window) {
    log('Current permission: <strong>' + Notification.permission + '</strong>');
  }
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.getRegistrations().then(regs => {
      log('Service workers registered: ' + regs.length);
    });
  }
});
</script>
</body>
</html>
