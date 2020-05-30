<?php

declare(strict_types=1);

namespace Noitran\Repositories\Tests;

use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Noitran\Repositories\ServiceProvider;
use Noitran\Repositories\Tests\Stubs\Repositories\UserRepository;
use Noitran\Repositories\Tests\Stubs\Repositories\UserRepositoryEloquent;
use Noitran\Repositories\Tests\Support\Reflections;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * Class TestCase.
 */
abstract class TestCase extends OrchestraTestCase
{
    use Reflections;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->registerEloquentFactoriesFrom(__DIR__ . '/database/factories');

        $this->artisan('db:seed', [
            '--class' => PostTestSeeder::class,
        ]);

        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
    }

    /**
     * @param \Laravel\Lumen\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            \Noitran\RQL\ServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    /**
     * Register factories.
     *
     * @param string $path
     */
    protected function registerEloquentFactoriesFrom($path): void
    {
        $this->app->make(EloquentFactory::class)->load($path);
    }
}
