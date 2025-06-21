<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your payment settings for LiqPay and WayForPay.
    |
    */

    'default' => env('PAYMENT_PROVIDER', 'liqpay'),

    'liqpay' => [
        'public_key' => env('LIQPAY_PUBLIC_KEY'),
        'private_key' => env('LIQPAY_PRIVATE_KEY'),
        'sandbox' => env('LIQPAY_SANDBOX', false),
        'callback_url' => env('LIQPAY_CALLBACK_URL', '/payment/liqpay/callback'),
        'redirect_url' => env('LIQPAY_REDIRECT_URL', '/payment/liqpay/redirect'),
        'merchant_domain' => env('LIQPAY_MERCHANT_DOMAIN', 'https://your-domain.com'),
        'currency' => env('LIQPAY_CURRENCY', 'UAH'),
        'language' => env('LIQPAY_LANGUAGE', 'uk'),
        'debug' => env('APP_DEBUG', false),
    ],

    'wayforpay' => [
        'merchant_account' => env('WAYFORPAY_MERCHANT_ACCOUNT'),
        'merchant_secret_key' => env('WAYFORPAY_MERCHANT_SECRET_KEY'),
        'sandbox' => env('WAYFORPAY_SANDBOX', false),
        'callback_url' => env('WAYFORPAY_CALLBACK_URL', '/payment/wayforpay/callback'),
        'redirect_url' => env('WAYFORPAY_REDIRECT_URL', '/payment/wayforpay/redirect'),
        'merchant_domain' => env('WAYFORPAY_MERCHANT_DOMAIN', 'https://your-domain.com'),
        'currency' => env('WAYFORPAY_CURRENCY', 'UAH'),
        'language' => env('WAYFORPAY_LANGUAGE', 'uk'),
        'debug' => env('APP_DEBUG', false),
    ],
];