<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Force HTTPS
    |--------------------------------------------------------------------------
    |
    | Redirect HTTP requests to HTTPS and generate secure URLs. Enable in
    | production behind TLS termination.
    |
    */

    'force_https' => (bool) env('APP_FORCE_HTTPS', env('APP_ENV') === 'production'),

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Comma-separated proxy IPs or CIDR ranges. Use "*" when behind a trusted
    | load balancer (e.g. AWS ALB, Cloudflare).
    |
    */

    'trusted_proxies' => env('TRUSTED_PROXIES'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Security Headers
    |--------------------------------------------------------------------------
    */

    'headers' => [
        'x_frame_options' => 'SAMEORIGIN',
        'x_content_type_options' => 'nosniff',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'permissions_policy' => 'camera=(self), microphone=(), geolocation=()',
        'x_xss_protection' => '0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Strict Transport Security
    |--------------------------------------------------------------------------
    |
    | Only sent over HTTPS when enabled. max-age is in seconds (1 year).
    |
    */

    'hsts' => [
        'enabled' => (bool) env('SECURITY_HSTS_ENABLED', env('APP_ENV') === 'production'),
        'max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
        'include_subdomains' => (bool) env('SECURITY_HSTS_SUBDOMAINS', true),
    ],

];
