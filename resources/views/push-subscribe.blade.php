@extends('layouts.app')
@section('title', 'Enable Notifications — GoBazaar')

@section('content')
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:20px">
<div style="background:#fff;border-radius:16px;padding:32px 28px;max-width:420px;width:100%;text-align:center;box-shadow:0 4px 24px rgba(0,0,0,.1)">
  <div style="font-size:52px;margin-bottom:12px">🔔</div>
  <h2 style="color:var(--primary);font-family:var(--fh);font-size:22px;margin-bottom:8px">GoBazaar Notifications</h2>
  <p style="color:var(--muted);font-size:14px;margin-bottom:24px">Allow notifications to get alerts for new chat messages even when the app is closed.</p>
  <button id="btn" onclick="enablePush()" style="background:var(--primary);color:#fff;border:none;border-radius:10px;padding:14px 28px;font-size:15px;font-weight:700;cursor:pointer;width:100%">Enable Notifications</button>
  <div id="status" style="margin-top:16px;font-size:13px;color:#333;text-align:left"></div>
</div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

function log(msg, color) {
  document.getElementById('status').innerHTML +=
    '<div style="margin:3px 0;color:' + (color||'#333') + '">' + msg + '</div>';
}

function urlBase64ToUint8Array(b) {
  var pad = '='.repeat((4 - b.length % 4) % 4);
  var base64 = (b + pad).replace(/-/g,'+').replace(/_/g,'/');
  var raw = atob(base64);
  var arr = new Uint8Array(raw.length);
  for (var i=0;i<raw.length;i++) arr[i]=raw.charCodeAt(i);
  return arr;
}

async function enablePush() {
  const btn = document.getElementById('btn');
  btn.disabled = true;
  document.getElementById('status').innerHTML = '';

  if (!('serviceWorker' in navigator)) { log('❌ Service Worker not supported','#c62828'); btn.disabled=false; return; }
  if (!('PushManager' in window))      { log('❌ Push not supported','#c62828'); btn.disabled=false; return; }

  log('⏳ Registering service worker…');
  let reg;
  try {
    reg = await navigator.serviceWorker.register('/sw.js');
    await navigator.serviceWorker.ready;
    log('✅ Service worker ready','#2e7d32');
  } catch(e) { log('❌ SW error: '+e.message,'#c62828'); btn.disabled=false; return; }

  log('⏳ Getting VAPID key…');
  let vapidKey;
  try {
    const r = await fetch('{{ route("push.vapid-key") }}');
    const d = await r.json();
    vapidKey = d.key;
    if (!vapidKey) { log('❌ VAPID key missing','#c62828'); btn.disabled=false; return; }
    log('✅ VAPID key OK','#2e7d32');
  } catch(e) { log('❌ VAPID fetch error: '+e.message,'#c62828'); btn.disabled=false; return; }

  log('⏳ Requesting permission…');
  const perm = await Notification.requestPermission();
  log('Permission: <strong>'+perm+'</strong>', perm==='granted'?'#2e7d32':'#c62828');
  if (perm !== 'granted') { btn.disabled=false; return; }

  log('⏳ Subscribing to push…');
  let sub;
  try {
    sub = await reg.pushManager.getSubscription();
    if (sub) {
      log('ℹ️ Existing subscription — re-saving…','#1a3a8f');
    } else {
      sub = await reg.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapidKey),
      });
      log('✅ New subscription created','#2e7d32');
    }
  } catch(e) { log('❌ Subscribe error: '+e.message,'#c62828'); btn.disabled=false; return; }

  log('⏳ Saving to server…');
  const json = sub.toJSON();
  try {
    const res = await fetch('{{ route("push.subscribe") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        endpoint:   json.endpoint,
        public_key: json.keys?.p256dh || null,
        auth_token: json.keys?.auth    || null,
      }),
    });
    log('HTTP Status: ' + res.status, res.ok ? '#2e7d32' : '#c62828');
    const text = await res.text();
    log('Raw response: ' + text.substring(0, 200));
    let result;
    try { result = JSON.parse(text); } catch(e) { log('❌ Response is not JSON — likely redirect/HTML','#c62828'); btn.disabled=false; return; }
    if (result.ok) {
      log('🎉 <strong>Done! Notifications enabled.</strong>','#2e7d32');
      btn.textContent = '✅ Notifications Enabled';
      btn.style.background = '#2e7d32';
      setTimeout(() => { window.location.href = '{{ route("chat.inbox") }}'; }, 2000);
    } else {
      log('❌ Server error: '+JSON.stringify(result),'#c62828');
      btn.disabled = false;
    }
  } catch(e) { log('❌ Save error: '+e.message,'#c62828'); btn.disabled=false; }
}

// Show current status on load
window.addEventListener('load', function() {
  if ('Notification' in window) {
    const perm = Notification.permission;
    log('Current permission: <strong>'+perm+'</strong>', perm==='granted'?'#2e7d32':perm==='denied'?'#c62828':'#555');
    if (perm === 'denied') {
      log('⚠️ Notifications are blocked. Go to browser settings → Site Settings → Notifications → Allow gobazzarweb.heavendwell.com','#e65100');
      document.getElementById('btn').disabled = true;
      document.getElementById('btn').textContent = 'Blocked in Browser Settings';
    }
  }
});
</script>
@endpush
