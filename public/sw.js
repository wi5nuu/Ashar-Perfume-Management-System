const CACHE = 'apms-v1';
const URLS = ['/','/css/app.css','/js/app.js','/offline'];

self.addEventListener('install', e => { e.waitUntil(caches.open(CACHE).then(c => c.addAll(URLS))); });
self.addEventListener('fetch', e => { e.respondWith(caches.match(e.request).then(r => r||fetch(e.request).catch(()=>caches.match('/offline')))); });
self.addEventListener('activate', e => { e.waitUntil(caches.keys().then(ks => Promise.all(ks.filter(k=>k!==CACHE).map(k=>caches.delete(k))))); });
