// Minimal Service Worker to enable PWA installability WITHOUT caching.
// This service worker does not store responses in Cache Storage.

self.addEventListener('install', (event) => {
    // Activate immediately
    event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// Network-only fetch handler: forward every request straight to the
// network. No caching, no offline fallback - this worker exists solely
// to make the app installable as a PWA.
self.addEventListener('fetch', (event) => {
    event.respondWith(fetch(event.request));
});