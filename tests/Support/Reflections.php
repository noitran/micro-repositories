<?php

declare(strict_types=1);

namespace Noitran\Repositories\Tests\Support;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Trait Reflections.
 */
trait Reflections
{
    /**
     * @param $className
     * @param $methodName
     *
     * @throws ReflectionException
     *
     * @return ReflectionMethod
     */
    public function getPrivateMethod($className, $methodName): ReflectionMethod
    {
        $reflector = new ReflectionClass($className);
        $method = $reflector->getMethod($methodName);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * @param $className
     * @param $propertyName
     *
     * @throws ReflectionException
     *
     * @return ReflectionProperty
     */
    public function getPrivateProperty($className, $propertyName): ReflectionProperty
    {
        $reflector = new ReflectionClass($className);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }
}
