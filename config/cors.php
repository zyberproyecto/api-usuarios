<?php

return [

    // Todas las rutas de tu API (prefijo /api) y el preflight de Sanctum
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Habilitamos Landing (5500), Front Socios (5501) y Backoffice (8003)
    'allowed_origins' => [
        'http://127.0.0.1:5500',
        'http://localhost:5500',
        'http://127.0.0.1:5501',
        'http://localhost:5501',
        'http://127.0.0.1:8003',
        'http://localhost:8003',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],
    'exposed_headers'  => [],

    'max_age' => 3600,

    // Usamos token Bearer (no cookies)
    'supports_credentials' => false,
];