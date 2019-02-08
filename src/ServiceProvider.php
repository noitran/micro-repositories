<?php

namespace Noitran\Repositories;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProvider
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

    /**
     * @return void
     */
    public function boot(): void
    {
        $configPath = __DIR__ . '/../config/repositories.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('repositories.php');
        } else {
            $publishPath = base_path('config/repositories.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $configPath = __DIR__ . '/../config/repositories.php';
        $this->mergeConfigFrom($configPath, 'repositories');
    }
}
