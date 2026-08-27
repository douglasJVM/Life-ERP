const CACHE_NAME = 'life-erp-v1';
const STATIC_ASSETS = [
  '/manifest.json',
  '/assets/css/styles.css',
  '/assets/js/script.js',
  '/views/offline.html'
];

// Instalação do Cache
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS);
    })
  );
  self.skipWaiting();
});

// Limpeza de caches antigos
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    })
  );
  self.clients.claim();
});

// Interceptação de requisições (Network first com fallback para offline)
self.addEventListener('fetch', (event) => {
  // Ignora chamadas da API REST (deve sempre buscar dados frescos)
  if (event.request.url.includes(':8000') || event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request).then((response) => {
        if (response) return response;
        if (event.request.headers.get('accept').includes('text/html')) {
          return caches.match('/views/offline.html');
        }
      });
    })
  );
});