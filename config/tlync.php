<?php

// config for Egate/Tlync
return [

    'tlync_environment' => env('TLYNC_ENVIRONMENT', 'test'),

    'use_own_tlync_account' => false,


    'tlync_test_url' => 'https://c7drkx2ege.execute-api.eu-west-2.amazonaws.com/',
    'api_test_key' => env('TLYNC_TEST_STORE_ID'),
    'tlync_test_token' => env('TLYNC_TEST_TOKEN'),

    'tlync_live_url' => 'https://wla3xiw497.execute-api.eu-central-1.amazonaws.com/',
    'api_live_key' => env('TLYNC_LIVE_STORE_ID'),
    'tlync_live_token' => env('TLYNC_LIVE_TOKEN'),

    'custom_ref_salt' => env('TLYNC_CUSTOM_REF_SALT', env('APP_KEY')),

    'callback_url' => env('APP_URL') . '/api/tlync/callback',
    'frontend_url' => env('APP_URL') . '/tlync/payment-response/',


    'required_parameters' => [
        'intiate' => [
            'id',
            'amount',
            'phone',
            'email',
            'backend_url',
            'frontend_url',
            'custom_ref'
        ],
        'confirm' => [
            'store_id',
            'transaction_ref',
            'custom_ref'
        ]


    ],

    /*
     * The model class that should be used to mark the payment as paid or failed.
     * Example class \App\Models\Invoice.
     */
    'payment_model' => '',


    /*
     * The field in the payment model  that will be used to mark the payment  as paid.
     *  This should have corresponding observer to listen to changes.
     *   And completes the process if the payment is paid.
     *   Example field 'paid' in \App\Models\Invoice.
     */
    'boolean_field' => 'paid',


];
