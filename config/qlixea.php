<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Qlixea Payment Hub
    |--------------------------------------------------------------------------
    */
    'project_name' => env('QLIXEA_PROJECT_NAME', 'Mi Proyecto'),
    'api_key' => env('QLIXEA_API_KEY', ''),
    'api_secret' => env('QLIXEA_API_SECRET', ''),
    'dashboard_url' => env('QLIXEA_DASHBOARD_URL', 'https://api.qlixea.com/dashboard'),
    
    // Token maestro para el aprovisionamiento automático (solo se usa durante la instalación)
    'provisioning_master_token' => env('QLIXEA_PROVISIONING_MASTER_TOKEN', 'qlx_prov_8f7a9b2c4d6e1f3a5b7c9d0e2f4a6b8c'),
    'provisioning_api_url' => 'https://api.qlixea.com/api/v1/provisioning',
];