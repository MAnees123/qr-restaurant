<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SafePay Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | SafePay is a secure credit/debit card payment gateway with 3D Secure
    | verification. Get your keys from: https://www.getsafepay.com
    |
    */
    'safepay' => [
        'environment' => env('SAFEPAY_ENVIRONMENT', 'sandbox'), // sandbox or production
        'api_key'     => env('SAFEPAY_API_KEY'),
        'secret_key'  => env('SAFEPAY_SECRET_KEY'),
        'public_key'  => env('SAFEPAY_PUBLIC_KEY'),
        'webhook_secret' => env('SAFEPAY_WEBHOOK_SECRET'),
        'base_url'    => env('SAFEPAY_ENVIRONMENT', 'sandbox') === 'production'
                            ? 'https://api.getsafepay.com'
                            : 'https://sandbox.api.getsafepay.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bitcoin / Crypto Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Bitcoin Lightning payment gateway for instant crypto transactions.
    | Supports BTC Lightning Network for near-instant confirmations.
    |
    */
    'bitcoin' => [
        'environment'    => env('BITCOIN_ENVIRONMENT', 'testnet'), // testnet or mainnet
        'api_key'        => env('BITCOIN_API_KEY'),
        'secret_key'     => env('BITCOIN_SECRET_KEY'),
        'public_key'     => env('BITCOIN_PUBLIC_KEY'),
        'webhook_secret' => env('BITCOIN_WEBHOOK_SECRET'),
        'wallet_address' => env('BITCOIN_WALLET_ADDRESS'),
        'exchange_rate'  => 18000000, // 1 BTC = 18,000,000 PKR (update regularly)
    ],

    /*
    |--------------------------------------------------------------------------
    | General Payment Settings
    |--------------------------------------------------------------------------
    */
    'currency' => 'PKR',
    'currency_symbol' => 'Rs',

];
