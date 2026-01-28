<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Xendit Configuration
    |--------------------------------------------------------------------------
    */

    'secret_key' => env('XENDIT_SECRET_KEY', 'xnd_development_xElegYnRBbyNZJANiNp5mUcMCJPh7sbpaLDAJdgkezh6UW8OI5wqGsSVjtlbNKbR'),
    'public_key' => env('XENDIT_PUBLIC_KEY', ''),
    'webhook_token' => env('XENDIT_WEBHOOK_TOKEN', ''),
    
    'key_name' => env('XENDIT_KEY_NAME', 'local-testing'),

    'is_production' => env('XENDIT_IS_PRODUCTION', false),

    'api_url' => env('XENDIT_IS_PRODUCTION', false)
        ? 'https://api.xendit.co'
        : 'https://api.xendit.co', // Xendit uses same URL for sandbox, sandbox mode is determined by key

    'payment_expiry' => env('XENDIT_PAYMENT_EXPIRY', 1440), // in minutes (24 hours)

    // Virtual Account Bank Codes
    'va_bank_codes' => [
        'BCA',
        'BNI',
        'BRI',
        'MANDIRI',
        'PERMATA',
        'BSI',
    ],

    // Invoice settings
    'invoice_duration' => env('XENDIT_INVOICE_DURATION', 86400), // in seconds (24 hours)
    'success_redirect_url' => env('XENDIT_SUCCESS_REDIRECT_URL', null),
    'failure_redirect_url' => env('XENDIT_FAILURE_REDIRECT_URL', null),
];
