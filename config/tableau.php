<?php

return [
    'server' => env('TABLEAU_SERVER', 'http://103.154.174.60'),
    'api_version' => env('TABLEAU_API_VERSION', '3.8'),
    'site_id' => env('TABLEAU_SITE_ID', ''),  // kosong untuk default site
    'admin_username' => env('TABLEAU_ADMIN_USERNAME', ''),
    'admin_password' => env('TABLEAU_ADMIN_PASSWORD', ''),
    
    // Default viewer untuk Trusted Auth (embed dashboard)
    // Menggunakan admin_username sebagai default untuk embed
    'viewer_username' => env('TABLEAU_ADMIN_USERNAME', 'korlantas'),
    
    // Default view path jika belum ada menu
    'default_view_path' => env('TABLEAU_DEFAULT_VIEW_PATH', '/views/home/01_SummaryDAKGARLANTAS3'),
];
