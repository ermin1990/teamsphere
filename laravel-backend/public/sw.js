// Minimal Service Worker to enable PWA installability WITHOUT caching.
// This service worker does not store responses in Cache Storage.

self.addEventListener('install', (event) => {
    // Activate immediately
    event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

// Network-only fetch handler: forward requests to the network and
// never persist responses in a cache. This ensures the app is not
// cached by the service worker while still allowing a service
// worker to exist (required for some install flows).
self.addEventListener('fetch', (event) => {
    // Only handle same-origin GET navigations and requests
    if (event.request.method !== 'GET') return;
    if (!event.request.url.startsWith(self.location.origin)) return;

    // For navigation requests, try the network and fall back to a minimal response
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => {
                // If offline, return a simple HTML fallback so the browser
                // can still show an installable context without cached app shell.
                return new Response('<!doctype html><title>Offline</title><meta name="viewport" content="width=device-width,initial-scale=1"><h1 style="color:#fff;background:#0f172a;padding:2rem">Offline</h1>', {
                    headers: { 'Content-Type': 'text/html' }
                });
            })
        );
        return;
    }

    // For other GET requests, just perform a normal network fetch.
    event.respondWith(fetch(event.request));
});