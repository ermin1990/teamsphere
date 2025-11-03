// Service Worker for TeamSphere PWA - Beta Version
// Network-first strategy to ensure fresh content during development
const CACHE_NAME = 'teamsphere-beta-v1.0.0';
const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes cache duration for beta

// Minimal static cache - only critical assets
const STATIC_CACHE_URLS = [
    '/favicon.ico',
    '/manifest.json'
];

// Assets that should never be cached during beta
const NEVER_CACHE = [
    '/livewire/',
    '/api/',
    '/sanctum/',
    '/broadcasting/'
];

// Install event - cache only essential static assets
self.addEventListener('install', event => {
    console.log('[SW] Installing service worker (Beta version)');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('[SW] Caching essential assets');
                return cache.addAll(STATIC_CACHE_URLS);
            })
            .then(() => {
                console.log('[SW] Skip waiting');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('[SW] Installation failed:', error);
            })
    );
});

// Activate event - clean old caches immediately
self.addEventListener('activate', event => {
    console.log('[SW] Activating new service worker');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[SW] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            console.log('[SW] Claiming clients');
            return self.clients.claim();
        })
    );
});

// Fetch event - NETWORK FIRST strategy for beta
self.addEventListener('fetch', event => {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;

    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin)) return;

    // Never cache these URLs
    const shouldNeverCache = NEVER_CACHE.some(pattern => 
        event.request.url.includes(pattern)
    );
    
    if (shouldNeverCache) {
        event.respondWith(fetch(event.request));
        return;
    }

    // Network-first strategy with short cache fallback
    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Only cache successful responses
                if (!response || response.status !== 200 || response.type === 'error') {
                    return response;
                }

                // Clone the response
                const responseToCache = response.clone();

                // Cache the response with timestamp
                caches.open(CACHE_NAME).then(cache => {
                    const headers = new Headers(responseToCache.headers);
                    headers.append('sw-cached-at', Date.now().toString());
                    
                    const responseWithHeaders = new Response(responseToCache.body, {
                        status: responseToCache.status,
                        statusText: responseToCache.statusText,
                        headers: headers
                    });
                    
                    cache.put(event.request, responseWithHeaders);
                });

                return response;
            })
            .catch(() => {
                // If network fails, try cache but check freshness
                return caches.match(event.request).then(cachedResponse => {
                    if (!cachedResponse) {
                        return new Response('Offline - No cached version available', {
                            status: 503,
                            statusText: 'Service Unavailable'
                        });
                    }

                    // Check cache age
                    const cachedAt = cachedResponse.headers.get('sw-cached-at');
                    if (cachedAt) {
                        const age = Date.now() - parseInt(cachedAt);
                        if (age > CACHE_DURATION) {
                            console.log('[SW] Cached response expired for:', event.request.url);
                        }
                    }

                    return cachedResponse;
                });
            })
    );
});

// Handle messages from the main app
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            caches.keys().then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => caches.delete(cacheName))
                );
            }).then(() => {
                console.log('[SW] All caches cleared');
            })
        );
    }
});