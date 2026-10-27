<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VGT Core Platform Connection
    |--------------------------------------------------------------------------
    |
    | Defines the URL and identity parameters for connecting this independent
    | Website Application with the VGT Core Control Plane.
    |
    */

    'core_url' => env('VGT_CORE_URL', 'http://127.0.0.1:8000'),

    /*
    |--------------------------------------------------------------------------
    | Website Unique Identity
    |--------------------------------------------------------------------------
    |
    | Every independent website application receives a unique UUID, Project Key,
    | and Installation ID generated during the 19-step installation process.
    |
    */

    'project_uuid' => env('VGT_PROJECT_UUID', null),

    'project_key' => env('VGT_PROJECT_KEY', null),

    'installation_id' => env('VGT_INSTALLATION_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Project Secret Token
    |--------------------------------------------------------------------------
    |
    | Cryptographic Bearer token issued by VGT Core to authenticate telemetry,
    | heartbeats, and remote status checks.
    |
    */

    'project_token' => env('VGT_PROJECT_TOKEN', null),

    /*
    |--------------------------------------------------------------------------
    | Telemetry & Heartbeat Settings
    |--------------------------------------------------------------------------
    |
    | Interval in seconds between background heartbeat signals sent to Core.
    | Timeout ensures that even if Core is offline, website execution is never blocked.
    |
    */

    'heartbeat_interval' => (int) env('VGT_HEARTBEAT_INTERVAL', 300),

    'heartbeat_timeout' => (int) env('VGT_HEARTBEAT_TIMEOUT', 3),

    /*
    |--------------------------------------------------------------------------
    | Standalone / Offline Mode
    |--------------------------------------------------------------------------
    |
    | If enabled, website operates in full air-gapped isolation without
    | attempting any outgoing telemetry calls to VGT Core.
    |
    */

    'offline_mode' => (bool) env('VGT_OFFLINE_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Runtime Version
    |--------------------------------------------------------------------------
    */

    'cms_version' => '2.0.0',

];
