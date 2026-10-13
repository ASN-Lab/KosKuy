<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS)
    |--------------------------------------------------------------------------
    | Dibutuhkan aplikasi mobile saat dijalankan sebagai Web (Chrome):
    | browser memblokir fetch ke http://localhost:8000 tanpa header ini.
    | Emulator/HP fisik (native) tidak terpengaruh CORS.
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
