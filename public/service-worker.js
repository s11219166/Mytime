// Service Worker for MyTime Push Notifications
const CACHE_NAME = 'mytime-v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
    '/favicon.ico'
];

// Install event
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Service Worker: Cache opened');
                return cache.addAll(urlsToCache).catch(err => {
                    console.log('Service Worker: Some resources failed to cache', err);
                });
            })
    );
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Service Worker: Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event
self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }

                return fetch(event.request).then(response => {
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }

                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME)
                        .then(cache => {
                            cache.put(event.request, responseToCache);
                        });

                    return response;
                });
            })
            .catch(() => {
                // Return a fallback response if offline
                return new Response('Offline - Service Worker', {
                    status: 503,
                    statusText: 'Service Unavailable',
                    headers: new Headers({
                        'Content-Type': 'text/plain'
                    })
                });
            })
    );
});

// Push event
self.addEventListener('push', event => {
    console.log('Push notification received:', event);

    let notificationData = {
        title: 'MyTime Notification',
        body: 'You have a new notification',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        tag: 'mytime-notification',
        requireInteraction: true,
    };

    if (event.data) {
        try {
            notificationData = event.data.json();
        } catch (e) {
            notificationData.body = event.data.text();
        }
    }

    event.waitUntil(
        self.registration.showNotification(notificationData.title, {
            body: notificationData.body,
            icon: notificationData.icon || '/favicon.ico',
            badge: notificationData.badge || '/favicon.ico',
            tag: notificationData.tag || 'mytime-notification',
            requireInteraction: notificationData.requireInteraction !== false,
            data: notificationData.data || {},
            actions: [
                {
                    action: 'open',
                    title: 'Open',
                    icon: '/favicon.ico'
                },
                {
                    action: 'close',
                    title: 'Close',
                    icon: '/favicon.ico'
                }
            ]
        })
    );
});

// Notification click event
self.addEventListener('notificationclick', event => {
    console.log('Notification clicked:', event);

    event.notification.close();

    const urlToOpen = event.notification.data.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        }).then(clientList => {
            // Check if there's already a window/tab open with the target URL
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            // If not, open a new window/tab with the target URL
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Notification close event
self.addEventListener('notificationclose', event => {
    console.log('Notification closed:', event);
});

// Background sync event (for offline support)
self.addEventListener('sync', event => {
    if (event.tag === 'sync-notifications') {
        event.waitUntil(
            fetch('/api/notifications/sync')
                .then(response => response.json())
                .then(data => {
                    console.log('Notifications synced:', data);
                })
                .catch(err => {
                    console.error('Failed to sync notifications:', err);
                })
        );
    }
});
