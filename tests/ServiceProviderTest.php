<?php

namespace Noitran\Repositories\Tests;

use Noitran\Repositories\ServiceProvider;
use ReflectionClass;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class ServiceProviderTest
 */
class ServiceProviderTest extends TestCase
{
    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function itShouldSubClassServiceProviderClass(): void
    {
        $reflectionClass = new ReflectionClass(ServiceProvider::class);

        $this->assertTrue($reflectionClass->isSubclassOf(IlluminateServiceProvider::class));
    }
}
