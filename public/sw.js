/**
 * Service Worker - Sistema de Agendamentos PWA
 * 
 * Estratégias de cache:
 * - Cache First: Assets estáticos (CSS, JS, imagens)
 * - Network First: API e páginas dinâmicas
 * - Stale While Revalidate: Fontes e ícones
 */

const CACHE_NAME = 'agendamentos-v1.0.0';
const STATIC_CACHE = 'agendamentos-static-v1.0.0';
const DYNAMIC_CACHE = 'agendamentos-dynamic-v1.0.0';

// Arquivos para cache inicial (App Shell)
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/front/css/bulma.min.css',
    '/front/css/style.css',
    '/front/js/app.js',
    '/back/vendor/fontawesome-free/css/all.min.css',
    '/offline.html',
];

// Arquivos que devem ser sempre buscados da rede
const NETWORK_ONLY = [
    '/api/',
    '/login',
    '/logout',
    '/super/',
];

// Padrões de URL para diferentes estratégias
const CACHE_STRATEGIES = {
    cacheFirst: [
        /\.css$/,
        /\.js$/,
        /\.woff2?$/,
        /\.ttf$/,
        /\.eot$/,
        /\/img\//,
        /\/icons\//,
    ],
    networkFirst: [
        /\/api\//,
        /\/schedule/,
        /\/my-schedules/,
    ],
    staleWhileRevalidate: [
        /fonts\.googleapis\.com/,
        /fonts\.gstatic\.com/,
        /cdnjs\.cloudflare\.com/,
    ],
};

// =========================================================================
// EVENTOS DO SERVICE WORKER
// =========================================================================

/**
 * Instalação - Cache dos assets estáticos
 */
self.addEventListener('install', (event) => {
    console.log('[SW] Instalando Service Worker...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                console.log('[SW] Cacheando App Shell');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => self.skipWaiting())
            .catch((error) => {
                console.error('[SW] Erro ao cachear:', error);
            })
    );
});

/**
 * Ativação - Limpeza de caches antigos
 */
self.addEventListener('activate', (event) => {
    console.log('[SW] Ativando Service Worker...');
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((name) => {
                            return name !== STATIC_CACHE && 
                                   name !== DYNAMIC_CACHE &&
                                   name.startsWith('agendamentos-');
                        })
                        .map((name) => {
                            console.log('[SW] Removendo cache antigo:', name);
                            return caches.delete(name);
                        })
                );
            })
            .then(() => self.clients.claim())
    );
});

/**
 * Fetch - Intercepta requisições
 */
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignorar requisições não-GET
    if (request.method !== 'GET') {
        return;
    }

    // Ignorar requisições para outros domínios (exceto CDNs)
    if (url.origin !== location.origin && !isAllowedOrigin(url.origin)) {
        return;
    }

    // Verificar se deve ser apenas network
    if (shouldNetworkOnly(url.pathname)) {
        event.respondWith(networkOnly(request));
        return;
    }

    // Determinar estratégia
    const strategy = getStrategy(url);
    
    switch (strategy) {
        case 'cacheFirst':
            event.respondWith(cacheFirst(request));
            break;
        case 'networkFirst':
            event.respondWith(networkFirst(request));
            break;
        case 'staleWhileRevalidate':
            event.respondWith(staleWhileRevalidate(request));
            break;
        default:
            event.respondWith(networkFirst(request));
    }
});

/**
 * Push Notifications
 */
self.addEventListener('push', (event) => {
    console.log('[SW] Push recebido:', event);

    let data = {
        title: 'Sistema de Agendamentos',
        body: 'Você tem uma nova notificação',
        icon: '/front/img/icons/icon-192x192.png',
        badge: '/front/img/icons/badge-72x72.png',
        tag: 'notification',
        requireInteraction: true,
        data: {},
    };

    if (event.data) {
        try {
            const payload = event.data.json();
            data = { ...data, ...payload };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon,
            badge: data.badge,
            tag: data.tag,
            requireInteraction: data.requireInteraction,
            data: data.data,
            actions: data.actions || [
                { action: 'open', title: 'Abrir' },
                { action: 'dismiss', title: 'Dispensar' },
            ],
        })
    );
});

/**
 * Clique na notificação
 */
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notificação clicada:', event.action);

    event.notification.close();

    const data = event.notification.data || {};
    let url = '/';

    if (event.action === 'open' || event.action === '') {
        url = data.url || '/my-schedules';
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Verificar se já há uma janela aberta
                for (const client of clientList) {
                    if (client.url.includes(location.origin) && 'focus' in client) {
                        client.navigate(url);
                        return client.focus();
                    }
                }
                // Abrir nova janela
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});

/**
 * Sincronização em background
 */
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync:', event.tag);

    if (event.tag === 'sync-appointments') {
        event.waitUntil(syncAppointments());
    }
});

// =========================================================================
// ESTRATÉGIAS DE CACHE
// =========================================================================

/**
 * Cache First - Retorna do cache, se não existir busca da rede
 */
async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        return cachedResponse;
    }

    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        return caches.match('/offline.html');
    }
}

/**
 * Network First - Busca da rede, fallback para cache
 */
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Retornar página offline para navegação
        if (request.mode === 'navigate') {
            return caches.match('/offline.html');
        }
        
        return new Response('Offline', { status: 503 });
    }
}

/**
 * Stale While Revalidate - Retorna cache imediatamente e atualiza em background
 */
async function staleWhileRevalidate(request) {
    const cache = await caches.open(DYNAMIC_CACHE);
    const cachedResponse = await cache.match(request);

    const fetchPromise = fetch(request)
        .then((networkResponse) => {
            if (networkResponse.ok) {
                cache.put(request, networkResponse.clone());
            }
            return networkResponse;
        })
        .catch(() => cachedResponse);

    return cachedResponse || fetchPromise;
}

/**
 * Network Only - Sempre busca da rede
 */
async function networkOnly(request) {
    try {
        return await fetch(request);
    } catch (error) {
        if (request.mode === 'navigate') {
            return caches.match('/offline.html');
        }
        return new Response('Network error', { status: 503 });
    }
}

// =========================================================================
// HELPERS
// =========================================================================

/**
 * Determina estratégia baseada na URL
 */
function getStrategy(url) {
    const pathname = url.pathname + url.href;

    for (const [strategy, patterns] of Object.entries(CACHE_STRATEGIES)) {
        for (const pattern of patterns) {
            if (pattern.test(pathname)) {
                return strategy;
            }
        }
    }

    return 'networkFirst';
}

/**
 * Verifica se deve usar apenas network
 */
function shouldNetworkOnly(pathname) {
    return NETWORK_ONLY.some((pattern) => pathname.startsWith(pattern));
}

/**
 * Verifica se a origem é permitida para cache
 */
function isAllowedOrigin(origin) {
    const allowed = [
        'fonts.googleapis.com',
        'fonts.gstatic.com',
        'cdnjs.cloudflare.com',
        'cdn.jsdelivr.net',
        'unpkg.com',
    ];
    
    return allowed.some((domain) => origin.includes(domain));
}

/**
 * Sincroniza agendamentos offline
 */
async function syncAppointments() {
    try {
        // Buscar dados pendentes do IndexedDB
        // e enviar para o servidor
        console.log('[SW] Sincronizando agendamentos...');
    } catch (error) {
        console.error('[SW] Erro na sincronização:', error);
    }
}

/**
 * Limita tamanho do cache dinâmico
 */
async function trimCache(cacheName, maxItems) {
    const cache = await caches.open(cacheName);
    const keys = await cache.keys();
    
    if (keys.length > maxItems) {
        await cache.delete(keys[0]);
        return trimCache(cacheName, maxItems);
    }
}

// Limpar cache periodicamente
setInterval(() => {
    trimCache(DYNAMIC_CACHE, 50);
}, 1000 * 60 * 60); // A cada hora
