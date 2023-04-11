<?php

namespace Lichtner\MockApi\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lichtner\MockApi\MockApiServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Lichtner\\MockApi\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            MockApiServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        $migration1 = include __DIR__.'/../database/migrations/create_mock_api_url_table.php.stub';
        $migration1->up();
        $migration2 = include __DIR__.'/../database/migrations/create_mock_api_url_history_table.php.stub';
        $migration2->up();
    }
}
