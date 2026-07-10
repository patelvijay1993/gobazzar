const CACHE = 'gobazaar-v1';
const OFFLINE_URL = '/offline.html';

// Files to cache for offline
const PRECACHE = [
  '/',
  '/offline.html',
  '/images/pwa-icon-192.png',
];

// ── Install ──────────────────────────────────────────────────────────
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE).then(cache => cache.addAll(PRECACHE)).catch(() => {})
  );
  self.skipWaiting();
});

// ── Activate ─────────────────────────────────────────────────────────
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// ── Fetch — network first, fallback to cache ─────────────────────────
self.addEventListener('fetch', event => {
  // Only handle GET, skip admin/api/livewire
  if (event.request.method !== 'GET') return;
  const url = new URL(event.request.url);
  if (url.pathname.startsWith('/admin') ||
      url.pathname.startsWith('/livewire') ||
      url.pathname.startsWith('/chat')) return;

  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Cache successful HTML responses
        if (response.ok && event.request.headers.get('accept')?.includes('text/html')) {
          const clone = response.clone();
          caches.open(CACHE).then(cache => cache.put(event.request, clone));
        }
        return response;
      })
      .catch(() => {
        // Offline fallback
        return caches.match(event.request) || caches.match(OFFLINE_URL);
      })
  );
});

// ── Message from page → show notification ────────────────────────────
self.addEventListener('message', event => {
  if (event.data?.type === 'SHOW_NOTIFICATION') {
    const { title, body, url } = event.data;
    self.registration.showNotification(title || 'GoBazaar', {
      body:     body || 'You have a new message',
      icon:     '/images/pwa-icon-192.png',
      badge:    '/images/pwa-badge-72.png',
      tag:      'gobazaar-msg',
      renotify: true,
      data:     { url: url || '/chat' },
      actions:  [
        { action: 'open',    title: 'Open Chat' },
        { action: 'dismiss', title: 'Dismiss'   },
      ],
    });
  }
});

// ── Push Notifications ───────────────────────────────────────────────
self.addEventListener('push', event => {
  let data = { title: 'GoBazaar', body: 'You have a new message!', url: '/chat' };
  try { data = { ...data, ...event.data.json() }; } catch (_) {}

  event.waitUntil(
    self.registration.showNotification(data.title, {
      body:    data.body,
      icon:    '/images/pwa-icon-192.png',
      badge:   '/images/pwa-badge-72.png',
      tag:     data.tag || 'gobazaar-msg',
      renotify: true,
      data:    { url: data.url || '/chat' },
      actions: [
        { action: 'open',    title: 'Open Chat' },
        { action: 'dismiss', title: 'Dismiss'   },
      ],
    })
  );
});

// ── Notification Click ───────────────────────────────────────────────
self.addEventListener('notificationclick', event => {
  event.notification.close();
  if (event.action === 'dismiss') return;

  const target = event.notification.data?.url || '/chat';
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
      for (const client of list) {
        if (client.url.includes(self.location.origin) && 'focus' in client) {
          client.navigate(target);
          return client.focus();
        }
      }
      return clients.openWindow(target);
    })
  );
});
