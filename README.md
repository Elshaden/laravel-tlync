

# Laravel SKD for Tlync Payment Gateway

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elshaden/laravel-tlync.svg?style=flat-square)](https://packagist.org/packages/elshaden/laravel-tlync)
[![Total Downloads](https://img.shields.io/packagist/dt/elshaden/laravel-tlync.svg?style=flat-square)](https://packagist.org/packages/elshaden/laravel-tlync)


## Installation

You can install the package via composer:

```bash
composer require elshaden/laravel-tlync
```

You can publish and run the migrations with:


You must publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-tlync-config"
```

This is the contents of the published config file:

And Also publishes the Hashids file, if you already have that, please add the custom connections to your file



## Usage

```php
$tlync = new Elshaden\Tlync();

Doc will be updated soon

```

## Contributing

Please see [CONTRIBUTING](https://github.com/Elshaden/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- 
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
