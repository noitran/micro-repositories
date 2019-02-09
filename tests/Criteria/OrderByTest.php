<?php

namespace Noitran\Repositories\Tests\Criteria;

use Noitran\Repositories\Criteria\OrderBy;
use Noitran\Repositories\Tests\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Class OrderByTest
 */
class OrderByTest extends TestCase
{
    /**
     * @test
     *
     * @throws \ReflectionException
     */
    public function itShouldSuccessfullyCreateOrderByCriteria(): void
    {
        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder(OrderBy::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Set expectations for constructor calls
        $mock->expects($this->once())
            ->method('setOrderByParameters')
            ->with($this->equalTo('created_at,desc'));

        // Now call the constructor
        $reflectedClass = new ReflectionClass(OrderBy::class);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($mock, 'created_at,desc');
    }

    /**
     * @test
     *
     * @throws ReflectionException
     */
    public function itShouldTestSetOrderByParameters(): void
    {
        $class = new OrderBy('created_at,desc');

        $column = $this->getPrivateProperty(OrderBy::class, 'column')->getValue($class);
        $direction = $this->getPrivateProperty(OrderBy::class, 'direction')->getValue($class);

        $this->assertEquals('created_at', $column);
        $this->assertEquals('desc', $direction);
    }
}
