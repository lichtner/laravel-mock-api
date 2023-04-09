<?php

namespace Lichtner\MockApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MockApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-mock-api')
            ->hasConfigFile()
            // ->hasViews()
            ->hasMigrations([
                'create_mock_api_table',
                'create_mock_api_history_table',
            ]);
    }
}
