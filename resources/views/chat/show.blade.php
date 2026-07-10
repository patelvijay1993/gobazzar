@extends('layouts.app')
@section('title', 'Chat — ' . $conversation->subject_title)

@push('styles')
<style>
.chat-wrap{max-width:720px;margin:20px auto;padding:0 16px;display:flex;flex-direction:column;height:calc(100vh - 160px);min-height:500px}
.chat-header{background:#fff;border:1.5px solid var(--border);border-radius:12px 12px 0 0;padding:14px 18px;display:flex;align-items:center;gap:14px;flex-shrink:0}
.chat-header-back{color:var(--primary);font-size:16px;text-decoration:none;flex-shrink:0}
.chat-header-avatar{width:42px;height:42px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-family:var(--fh);font-size:16px;font-weight:700;flex-shrink:0}
.chat-header-info{flex:1;min-width:0}
.chat-header-name{font-weight:700;font-size:14px}
.chat-header-listing{font-size:11px;color:var(--primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.chat-messages{flex:1;overflow-y:auto;background:#f9fafb;border-left:1.5px solid var(--border);border-right:1.5px solid var(--border);padding:16px;display:flex;flex-direction:column;gap:10px}
.msg{display:flex;flex-direction:column;max-width:75%}
.msg-me{align-self:flex-end;align-items:flex-end}
.msg-other{align-self:flex-start;align-items:flex-start}
.msg-bubble{padding:10px 14px;border-radius:18px;font-size:13.5px;line-height:1.5;word-break:break-word}
.msg-me .msg-bubble{background:var(--primary);color:#fff;border-bottom-right-radius:4px}
.msg-other .msg-bubble{background:#fff;color:var(--text);border:1px solid var(--border);border-bottom-left-radius:4px}
.msg-time{font-size:10px;color:var(--muted);margin-top:3px;padding:0 4px}
.msg-sender{font-size:10px;color:var(--muted);margin-bottom:2px;padding:0 4px}
.chat-input{background:#fff;border:1.5px solid var(--border);border-top:none;border-radius:0 0 12px 12px;padding:12px 16px;display:flex;gap:10px;align-items:center;flex-shrink:0}
.chat-input textarea{flex:1;border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;font-size:14px;font-family:var(--fb);resize:none;height:44px;max-height:120px;overflow-y:auto;line-height:1.4;transition:border-color .15s}
.chat-input textarea:focus{outline:none;border-color:var(--primary)}
.chat-send-btn{background:var(--primary);color:#fff;border:none;border-radius:10px;width:44px;height:44px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;font-size:16px;transition:opacity .15s}
.chat-send-btn:hover{opacity:.85}
.chat-send-btn:disabled{opacity:.5;cursor:not-allowed}
.chat-date-divider{text-align:center;font-size:10px;color:var(--muted);margin:6px 0;position:relative}
.chat-date-divider::before,.chat-date-divider::after{content:'';position:absolute;top:50%;width:40%;height:1px;background:var(--border)}
.chat-date-divider::before{left:0}
.chat-date-divider::after{right:0}
@media(max-width:600px){
  .chat-wrap{height:calc(100vh - 130px);margin:10px auto;padding:0 8px}
}
</style>
@endpush

@section('content')
<div class="chat-wrap">
  {{-- Header --}}
  <div class="chat-header">
    <a href="{{ route('chat.inbox') }}" class="chat-header-back"><i class="fa-solid fa-arrow-left"></i></a>
    @php $other = (int)$conversation->buyer_id === (int)Auth::id() ? $conversation->seller : $conversation->buyer; @endphp
    <div class="chat-header-avatar">{{ strtoupper(substr($other->name, 0, 2)) }}</div>
    @php
      $subject = $conversation->conversable;
      $subjectIcon = $subject instanceof \App\Models\Event ? '🗓️' : ($subject instanceof \App\Models\Business ? '🏢' : ($subject instanceof \App\Models\BusinessPost ? '🛍️' : '📦'));
    @endphp
    <div class="chat-header-info">
      <div class="chat-header-name">{{ $other->name }}</div>
      <div class="chat-header-listing">
        <a href="{{ $conversation->subject_url }}" style="color:inherit">
          {{ $subjectIcon }} {{ Str::limit($conversation->subject_title, 50) }}
        </a>
      </div>
    </div>
  </div>

  {{-- Messages --}}
  <div class="chat-messages" id="chat-messages">
    @php $prevDate = null; @endphp
    @foreach($conversation->messages as $msg)
      @php $msgDate = $msg->created_at->toDateString(); @endphp
      @if($msgDate !== $prevDate)
        <div class="chat-date-divider">{{ $msg->created_at->isToday() ? 'Today' : ($msg->created_at->isYesterday() ? 'Yesterday' : $msg->created_at->format('M d, Y')) }}</div>
        @php $prevDate = $msgDate; @endphp
      @endif
      <div class="msg {{ (int)$msg->sender_id === (int)Auth::id() ? 'msg-me' : 'msg-other' }}" data-id="{{ $msg->id }}">
        @if((int)$msg->sender_id !== (int)Auth::id())
          <div class="msg-sender">{{ $msg->sender->name }}</div>
        @endif
        <div class="msg-bubble">{{ $msg->body }}</div>
        <div class="msg-time" data-ts="{{ $msg->created_at->toISOString() }}"></div>
      </div>
    @endforeach
  </div>

  {{-- Input --}}
  <div class="chat-input">
    <textarea id="msg-input" placeholder="Type a message…" rows="1"></textarea>
    <button class="chat-send-btn" id="send-btn" onclick="sendMessage()">
      <i class="fa-solid fa-paper-plane"></i>
    </button>
  </div>
</div>
@endsection

@push('scripts')
<script>
const CONV_ID  = {{ $conversation->id }};
const MY_ID    = {{ Auth::id() }};
const SEND_URL = '{{ route('chat.send', $conversation) }}';
const READ_URL = '{{ route('chat.read', $conversation) }}';
const POLL_URL = '{{ route('chat.poll', $conversation) }}';
const CSRF     = '{{ csrf_token() }}';

// Track last known message id to poll only new ones
let lastMsgId = {{ $conversation->messages->last()?->id ?? 0 }};

// Convert all server-rendered timestamps to browser local time
document.querySelectorAll('.msg-time[data-ts]').forEach(function(el) {
  el.textContent = new Date(el.dataset.ts).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit', hour12:true});
});

// Mark read on open
fetch(READ_URL, {method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}});

function scrollBottom(force) {
  const el = document.getElementById('chat-messages');
  const nearBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 120;
  if (force || nearBottom) el.scrollTop = el.scrollHeight;
}
scrollBottom(true);

function escHtml(str) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}

function appendMessage(msg, isMine) {
  if (!msg || !msg.body) return;
  const wrap = document.getElementById('chat-messages');
  const div = document.createElement('div');
  div.className = 'msg ' + (isMine ? 'msg-me' : 'msg-other');
  div.dataset.id = msg.id;
  const ts = msg.created_at ? new Date(msg.created_at) : new Date();
  const timeStr = ts.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit', hour12:true});
  div.innerHTML = (isMine ? '' : `<div class="msg-sender">${escHtml(msg.sender_name || '')}</div>`)
    + `<div class="msg-bubble">${escHtml(msg.body)}</div>`
    + `<div class="msg-time">${timeStr}</div>`;
  wrap.appendChild(div);
  if (msg.id && msg.id > lastMsgId) lastMsgId = msg.id;
  scrollBottom(isMine);
}

// Send message
async function sendMessage() {
  const input = document.getElementById('msg-input');
  const body = input.value.trim();
  if (!body) return;
  const btn = document.getElementById('send-btn');

  // Clear input & show message immediately (optimistic)
  input.value = '';
  input.style.height = '44px';
  btn.disabled = true;
  appendMessage({ body: body, sender_name: '{{ Auth::user()->name }}', created_at: new Date().toISOString() }, true);

  try {
    const res = await fetch(SEND_URL, {
      method: 'POST',
      headers: {'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json'},
      body: JSON.stringify({body})
    });
    if (res.status === 401) { window.location.href = '{{ route('login') }}'; return; }
    if (res.ok) {
      const msg = await res.json();
      // Update lastMsgId so poll doesn't re-add this message
      if (msg && msg.id && msg.id > lastMsgId) lastMsgId = msg.id;
    }
  } catch(e) { /* message already shown, will sync on next poll */ }

  btn.disabled = false;
  input.focus();
}

// Enter to send
document.getElementById('msg-input').addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
  setTimeout(() => {
    this.style.height = '44px';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
  }, 0);
});

// Browser notification permission
const SUBJECT_TITLE = '{{ Str::limit($conversation->subject_title, 40) }}';

function requestNotifPermission() {
  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }
}
requestNotifPermission();

const CONV_URL = '{{ route('chat.conversation', $conversation) }}';

function showBrowserNotif(senderName, body) {
  if (!('Notification' in window) || Notification.permission !== 'granted') return;
  if (!document.hidden) return; // only show when tab is not active

  const notif = new Notification(senderName + ' — GoBazaar', {
    body: body,
    icon: '/favicon.ico',
    tag: 'chat-conv-' + CONV_ID,
    renotify: true,
  });

  notif.onclick = function() {
    notif.close();
    window.open(CONV_URL, '_self');
    window.focus();
  };
}

// Polling — fetch new messages every 3 seconds
let polling = true;
let pollErrors = 0;
async function pollMessages() {
  if (!polling) return;
  try {
    const res = await fetch(POLL_URL + '?after=' + lastMsgId, {
      headers: {'Accept':'application/json', 'X-CSRF-TOKEN': CSRF}
    });
    if (res.status === 401) { window.location.href = '{{ route('login') }}'; return; }
    if (!res.ok) { pollErrors++; if (pollErrors > 5) return; setTimeout(pollMessages, 5000); return; }
    pollErrors = 0;
    const msgs = await res.json();
    if (!Array.isArray(msgs)) { setTimeout(pollMessages, 3000); return; }
    msgs.forEach(msg => {
      if (parseInt(msg.sender_id) !== MY_ID && msg.body) {
        appendMessage(msg, false);
        showBrowserNotif(msg.sender_name || '', msg.body);
      }
    });
  } catch(e) {}
  setTimeout(pollMessages, 3000);
}
pollMessages();

// Stop polling when tab hidden, resume when visible
document.addEventListener('visibilitychange', function() {
  if (document.hidden) {
    polling = false;
  } else {
    polling = true;
    pollMessages();
  }
});
</script>
@endpush
