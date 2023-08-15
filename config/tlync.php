<?php

// config for Elshaden/Tlync
return [


    /*
     * You can Force test environment even if you application is in production.
     * This will override the environment settings
     * This is useful for testing.
     *
     */
    'force_test_mode' => env('TLYNC_FORCE_TEST_MODE', false),


    /*
     * You can restrict the IP address that can access the callback url.
     * This is useful for security.
     *
     */
    'restrict_ip' => false,


    ### Test Environment
    /*
     * You can add the IP address that can access the callback url.
     * You need to request the IP's from Tlync Support
     */
    'allowed_ips' => [env('TLYNC_ALLOWED_IPS')],

    /*
     * The url that will be used to send the request to Tlync.
     * This is for testing.
     */
    'tlync_test_url' => 'https://c7drkx2ege.execute-api.eu-west-2.amazonaws.com/',

    /*
     * The Store Id in test environment.
     * You must add this in the .env file,
     * do not add here. as this file can be pushed to git.
     */
    'api_test_key' => env('TLYNC_TEST_STORE_ID'),


    /*
     * The Store token that will be used to send the request to Tlync test environment.
     */
    'tlync_test_token' => env('TLYNC_TEST_TOKEN'),

### Live Environment
    /*
     * Live Production Environment
     * The url that will be used to send the request to Tlync.
     * This is for live production.
     */
    'tlync_live_url' => 'https://wla3xiw497.execute-api.eu-central-1.amazonaws.com/',

    /*
     * The Store Id in live environment.
     * You must add this in the .env file,
     * do not add here. as this file can be pushed to git.
     */
    'api_live_key' => env('TLYNC_LIVE_STORE_ID'),

    /*
     * The Store token that will be used to send the request to Tlync live environment.
     * You must add this in the .env file,
     * do not add here. as this file can be pushed to git.
     */
    'tlync_live_token' => env('TLYNC_LIVE_TOKEN'),



   /*
   * If you want you can direct the call back coming from Tlync to a specific url.
   * This is useful if you want to handle the call back in a different controller.
   * Leave as it is the package will handle the call back.
   */
    'callback_url' => env('APP_URL') . '/api/tlync/callback',

    /*
     * This where the customer will be redirected after payment.
     * if you want to redirect to another page please add this here
     * example back to MyCart page. or My Orders Page
     */
    'frontend_url' => env('APP_URL') ,


#    'required_parameters' => [
#        'intiate' => [
#            'id',
#            'amount',
#            'phone',
#            'email',
#            'backend_url',
#            'frontend_url',
#            'custom_ref'
#        ],
#        'confirm' => [
#            'store_id',
#            'transaction_ref',
#            'custom_ref'
#        ]
#    ],

    /*
     * The model class that should be used to mark the payment as paid or failed.
     * Example class \App\Actions\ConfirmOrder\Class.
     *
     */
    'handel_call_back_class' => env('TLYNC_PAYMENT_CLASS', ''),


    /*
     *  The Method  in the handel_call_back_class  that will be used to mark the payment  as paid.
     *  Example method 'confirm' in \App\Actions\ConfirmOrder\Class.
     *  ```
     *    public function confirm($order, $request){
     *    // Change order from temporary to orders and deliver the order.
     *    }
     * ```
     *  And completes the process if the payment is paid.
     *
     */
    'handel_method' => env('METHOD_TO_CONFIRM_PAYMENT', ''),


];
