<?php

namespace Lichtner\MockApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Lichtner\MockApi\Commands\MockApiCommand;

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
            ->hasViews()
            ->hasMigration('create_laravel-mock-api_table')
            ->hasCommand(MockApiCommand::class);
    }
}
