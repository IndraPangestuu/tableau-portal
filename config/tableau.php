<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tableau Server Configuration
    |--------------------------------------------------------------------------
    */
    'server' => env('TABLEAU_SERVER', 'http://localhost'),
    'api_version' => env('TABLEAU_API_VERSION', '3.8'),
    'site_id' => env('TABLEAU_SITE_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Tableau REST API Credentials
    |--------------------------------------------------------------------------
    | Digunakan untuk mengambil daftar workbooks dan views dari Tableau Server
    */
    'admin_username' => env('TABLEAU_ADMIN_USERNAME', ''),
    'admin_password' => env('TABLEAU_ADMIN_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Tableau Trusted Authentication
    |--------------------------------------------------------------------------
    | Digunakan untuk embed dashboard dengan Trusted Auth
    */
    'viewer_username' => env('TABLEAU_VIEWER_USERNAME', env('TABLEAU_ADMIN_USERNAME', '')),
    'default_view_path' => env('TABLEAU_DEFAULT_VIEW_PATH', ''),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache_ttl' => env('TABLEAU_CACHE_TTL', 300), // 5 minutes
];
