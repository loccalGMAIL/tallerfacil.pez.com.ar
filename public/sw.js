// Service worker mínimo para que la app sea instalable.
// Sin caché: todo pasa directo a la red (evita servir assets viejos de Vite).
self.addEventListener('install', () => self.skipWaiting());

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
    event.respondWith(fetch(event.request));
});
