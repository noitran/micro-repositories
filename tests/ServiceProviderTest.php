<?php

declare(strict_types=1);

namespace Noitran\Repositories\Tests;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Noitran\Repositories\ServiceProvider;
use ReflectionClass;

/**
 * Class ServiceProviderTest.
 */
class ServiceProviderTest extends TestCase
{
    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function it_should_sub_class_service_provider_class(): void
    {
        $reflectionClass = new ReflectionClass(ServiceProvider::class);

        $this->assertTrue($reflectionClass->isSubclassOf(IlluminateServiceProvider::class));
    }
}
