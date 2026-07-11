@extends('layouts.app')
@section('title', 'My Inbox')


@push('styles')
<style>
.inbox-wrap{max-width:720px;margin:28px auto;padding:0 16px}
.inbox-title{font-family:var(--fh);font-size:22px;font-weight:800;color:var(--primary);margin-bottom:18px;display:flex;align-items:center;gap:10px}
.conv-list{display:flex;flex-direction:column;gap:0}
.conv-item{display:flex;align-items:center;gap:14px;padding:14px 16px;background:#fff;border:1.5px solid var(--border);border-radius:0;cursor:pointer;text-decoration:none;color:var(--text);transition:background .15s;border-bottom:none}
.conv-item:first-child{border-radius:12px 12px 0 0}
.conv-item:last-child{border-radius:0 0 12px 12px;border-bottom:1.5px solid var(--border)}
.conv-item:only-child{border-radius:12px;border-bottom:1.5px solid var(--border)}
.conv-item:hover{background:var(--primary-light)}
.conv-item.unread{background:#fffbeb}
.conv-avatar{width:48px;height:48px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-family:var(--fh);font-size:18px;font-weight:700;flex-shrink:0}
.conv-info{flex:1;min-width:0}
.conv-name{font-weight:700;font-size:14px;margin-bottom:2px}
.conv-listing{font-size:11px;color:var(--primary);font-weight:600;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.conv-preview{font-size:12px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.conv-meta{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0}
.conv-time{font-size:10px;color:var(--muted)}
.conv-badge{background:var(--accent);color:#fff;font-size:10px;font-weight:700;border-radius:20px;padding:2px 8px;min-width:20px;text-align:center}
.empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
.empty-state i{font-size:48px;margin-bottom:16px;display:block;color:#d1d5db}
.empty-state p{font-size:15px;margin-bottom:16px}
.empty-state a{background:var(--primary);color:#fff;padding:10px 22px;border-radius:8px;font-weight:600;font-size:13px;display:inline-block}
</style>
@endpush

@section('content')
<div class="inbox-wrap">
  <div class="inbox-title">
    <i class="fa-solid fa-comments" style="color:var(--accent)"></i>
    My Inbox
  </div>

  @if($conversations->isEmpty())
    <div class="empty-state">
      <i class="fa-regular fa-comment-dots"></i>
      <p>No conversations yet.<br>Browse listings and tap "Chat with Seller" to start.</p>
      <a href="{{ route('classifieds.index') }}">Browse Classifieds</a>
    </div>
  @else
    <div class="conv-list">
      @foreach($conversations as $conv)
        @php
          $other = $conv->buyer_id === Auth::id() ? $conv->seller : $conv->buyer;
          $unread = $conv->unreadCountFor(Auth::id());
          $latest = $conv->latestMessage;
        @endphp
        <a href="{{ route('chat.conversation', $conv) }}" class="conv-item {{ $unread > 0 ? 'unread' : '' }}">
          @if($other->avatar_url)
            <img src="{{ $other->avatar_url }}" class="conv-avatar" style="object-fit:cover;padding:0" alt="{{ $other->name }}">
          @else
            <div class="conv-avatar">{{ strtoupper(substr($other->name, 0, 2)) }}</div>
          @endif
          <div class="conv-info">
            <div class="conv-name">{{ $other->name }}</div>
            <div class="conv-listing">
              @php
                $item = $conv->conversable;
              $icon = $item instanceof \App\Models\Event ? '🗓️' : ($item instanceof \App\Models\Business ? '🏢' : ($item instanceof \App\Models\BusinessPost ? '🛍️' : '📦'));
              @endphp
              {{ $icon }} {{ Str::limit($conv->subject_title, 45) }}
            </div>
            @if($latest)
              <div class="conv-preview">
                {{ $latest->sender_id === Auth::id() ? 'You: ' : '' }}{{ Str::limit($latest->body, 60) }}
              </div>
            @endif
          </div>
          <div class="conv-meta">
            @if($latest)
              <div class="conv-time">{{ $latest->created_at->diffForHumans(null, true, true) }}</div>
            @endif
            @if($unread > 0)
              <div class="conv-badge">{{ $unread }}</div>
            @endif
          </div>
        </a>
      @endforeach
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
const UNREAD_URL = '{{ route('chat.unread') }}';
const INBOX_URL  = '{{ route('chat.inbox') }}';
const CSRF = '{{ csrf_token() }}';

// Request notification permission
if ('Notification' in window && Notification.permission === 'default') {
  Notification.requestPermission();
}

// Poll unread count every 5s — show notification if new messages arrived
let lastUnread = {{ $conversations->sum(fn($c) => $c->unreadCountFor(Auth::id())) }};

async function pollInboxUnread() {
  try {
    const res = await fetch(UNREAD_URL, {headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}});
    const data = await res.json();
    const count = data.count;

    if (count > lastUnread && 'Notification' in window && Notification.permission === 'granted') {
      const notif = new Notification('New message — GoBazaar', {
        body: 'You have ' + count + ' unread message' + (count > 1 ? 's' : ''),
        icon: '/favicon.ico',
        tag: 'gobazaar-inbox',
        renotify: true,
      });
      notif.onclick = function() {
        notif.close();
        window.open(data.conv_url || INBOX_URL, '_self');
        window.focus();
      };
    }

    // Update badge in navbar
    const badge = document.getElementById('nav-chat-badge');
    if (badge) {
      if (count > 0) { badge.textContent = count > 99 ? '99+' : count; badge.style.display = 'flex'; }
      else { badge.style.display = 'none'; }
    }

    // Reload inbox list if new messages arrived
    if (count > lastUnread) setTimeout(() => location.reload(), 500);
    lastUnread = count;
  } catch(e) {}
  setTimeout(pollInboxUnread, 5000);
}
pollInboxUnread();
</script>
@endpush
