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
//
// Only GET requests are intercepted. Re-issuing a non-GET Request object
// (e.g. Livewire's polling POSTs to /livewire/update) via fetch() is
// unreliable in some browsers - it silently breaks the request ("Failed
// to fetch") instead of passing it through, which made wire:poll appear
// to work only after a full page reload. Not calling respondWith() lets
// the browser handle those requests natively, bypassing the worker.
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }
    event.respondWith(fetch(event.request));
});