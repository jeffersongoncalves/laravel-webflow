<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Webflow API Token
    |--------------------------------------------------------------------------
    |
    | Site or Workspace API token generated at Site/Workspace Settings >
    | Apps & Integrations > API access. Sent as a Bearer token on every
    | request to the Webflow API.
    |
    */
    'token' => env('WEBFLOW_API_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Webflow API Base URL
    |--------------------------------------------------------------------------
    */
    'base_url' => env('WEBFLOW_BASE_URL', 'https://api.webflow.com/v2'),

];
