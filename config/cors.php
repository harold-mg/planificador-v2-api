<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    /* 'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, */
    'paths' => ['api/*','api/centros_salud/*', 'sanctum/csrf-cookie', 'reporte-mensual/*', 
        'reporte-mensual-con-vehiculo/*', 
        'reporte-mensual-sin-vehiculo/*', 
        'reporte-mensual-auditorio/*',
        'reporte-mensual-virtual/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:4200'],  // Reemplaza con el dominio de tu Angular
    //'allowed_origins' => ['https://n4m3zpnb-4200.brs.devtunnels.ms'],
    //'allowed_origins' => ['https://m8jfxbh1-4200.brs.devtunnels.ms'],  // Reemplaza con el dominio de tu Angular
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,


];
