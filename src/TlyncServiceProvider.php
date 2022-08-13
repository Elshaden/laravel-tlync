<?php

namespace Elshaden\Tlync;

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Elshaden\Tlync\Commands\TlyncCommand;

class TlyncServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-tlync')
            ->hasConfigFile([
                'tlync',
                'hashids'
            ]);
    }

    public function packageRegistered(): void
    {
        Route::post('/api/tlync/callback', 'Elshaden\Tlync\Http\TlyncController@callback');
    }
}
