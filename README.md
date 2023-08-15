

# Laravel SKD for Tlync Payment Gateway
# From [Tadawul Digital Solutions Provider , (TDSP)](https://tdsp.ly) 

[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/elshaden/laravel-tlync/run-tests?label=tests)](

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elshaden/laravel-tlync.svg?style=flat-square)](https://packagist.org/packages/elshaden/laravel-tlync)
[![Total Downloads](https://img.shields.io/packagist/dt/elshaden/laravel-tlync.svg?style=flat-square)](https://packagist.org/packages/elshaden/laravel-tlync)

### Overview

This Package is a Laravel SDK for Tlync Payment gateway
you can read more about Tlync API here [Tlync API Documentation](https://dev-merchant.pay.net.ly/apidocs/index.html)

To use this package you must have an account on Tlync and have your API Key and API Secret

You Also need to create a temporary table in your database to store the new orders before sending them to Tlync
This will help in making sure that any completed and paid orders are not sent again to Tlync. 
and also to make sure that Tlync will only receive unique orders.

#### How it works

1. Customer places and order on your website, or cart
2. You create a new order in your temporary table in data database
3. You send the order to Tlync as will be explained below
4. Tlync will send a callback to your website with the order status
5. if the order is paid, you can now create the order in your database and send the customer to the success page
6. and delete the temporary order from your temporary table or mark it as paid
7. if the order is not paid, you can send the customer to the failed page and delete the order from your temporary table



## Installation

You can install the package via composer:

```bash
composer require elshaden/laravel-tlync
```


You must publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-tlync-config"
```

This is the contents of the published config file:

```php


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

```

And Also publishes the Hashids file, if you already have that, please add the custom connections to your file
The hashids config is responsible for providing a hashed id for the temporary order id, so the customer will not see the real order id

## Usage


### Initiate Payment

``` php
use Elshaden\Tlync\Facades\Tlync;

$Response = Tlync::InitiatePayment
    (
    float $Amount, // The amount of the order
    $para_1,  // this can be the real temprory order id or the customer id
    $para_2,  // this can be the value of the order
    int $para_3, // this must the real temprory order id, AND MUST BE UNIQUE & INTEGER
    string $UserPhone,  // Customer Phone Number
    string $UserEmail  // Customer Email Address OPTIONAL
    )


```

### Initiate Payment Response and Redirect

#### Success Response
if the initiate payment is successful, you will get a response in array

``` php
 $Response = [
    'Response' => true,
    'message' => 'redirect to url',
    'url' => 'https://store.pay.net.ly/tdi/xxxxxxxxxxxxx......',
  ];
 
```

You should Redirect the customer to the url provided in the response


if the initiate payment is not successful, you will get a response in array

``` php
 $Response = [
    'Response' => false,
    'message' => 'Error Message',
  ];
 
```


### Call Back
> This is when Tlync sends a call back to your server Backend with the payment status
The route set in the config file will receive the POST call back and will handle it
in the Tlync Controller

>The verification of the call back is done by Tlync Callback Method, so you do not need to worry about that.

>When all is done this package will call the class you set up in the config file 
``` php
    'handel_call_back_class' => env('TLYNC_PAYMENT_CLASS', ''),
    'handel_method' => env('METHOD_TO_CONFIRM_PAYMENT', ''),
```
>passing to it the Parameters you sent to the Initiate and the Tlync Response

>To learn more about the Tlync Response please check the [Tlync Documentation](https://dev-merchant.pay.net.ly/apidocs/index.html)


>You will need to handel the call back in your own class and method, and do what ever you want with it
Normally if success you will need to create the order in your database and send the customer to the success page
and if failed you will need to send the customer to the failed page

You can create your own class and method to handle the call back, and add it to the config file



## Contributing

Please see [CONTRIBUTING](https://github.com/Elshaden/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- 
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
