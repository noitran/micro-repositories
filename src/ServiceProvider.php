<?php

declare(strict_types=1);

namespace Noitran\Repositories;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Noitran\Repositories\Contracts\Filter\FilterStrategy;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * @return string
     */
    protected function getConfigPath(): string
    {
        return __DIR__ . '/../config/repositories.php';
    }

    public function boot(): void
    {
        $configPath = __DIR__ . '/../config/repositories.php';
        if (\function_exists('config_path')) {
            $publishPath = config_path('repositories.php');
        } else {
            $publishPath = base_path('config/repositories.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $configPath = __DIR__ . '/../config/repositories.php';
        $this->mergeConfigFrom($configPath, 'repositories');

        // $this->registerFilter();
    }

//    protected function registerFilter(): void
//    {
//        $this->app->bind(FilterStrategy::class, )
//    }
}
