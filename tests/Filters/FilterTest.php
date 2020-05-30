<?php

declare(strict_types=1);

namespace Noitran\Repositories\Tests\Criteria;

use Illuminate\Support\Fluent;
use Mockery;
use Noitran\Repositories\Tests\Stubs\Filters\UserFilter;
use Noitran\Repositories\Tests\TestCase;

/**
 * Class FilterTest.
 */
class FilterTest extends TestCase
{
    /**
     * @var UserFilter
     */
    protected $filter;

    public function setUp(): void
    {
        parent::setUp();

        $this->filter = $this->app->make(UserFilter::class);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function it_should_test_abstract_get_input_method(): void
    {
        $queryFilters = [
            [
                'queryParameter' => 'name',
                'uses' => '\Noitran\Repositories\Criteria\ByName',
            ],
            [
                'queryParameter' => 'surname',
                'uses' => '\Noitran\Repositories\Criteria\BySurname',
            ],
            [
                'queryParameter' => 'from_date',
                'uses' => '\Noitran\Repositories\Criteria\RegisteredFrom',
            ],
        ];

        $requestAttributes = [
            'name' => 'John',
            'surname' => 'Doe',
            'from_date' => '2019-02-15T12:07:07+00:00',
        ];

        $input = $this->filter->getInput(
            $queryFilters,
            $requestAttributes
        );

        $expected = new Fluent($requestAttributes);

        $this->assertEquals($expected->get('name'), $input['name']);
        $this->assertEquals($expected->get('surname'), $input['surname']);
        $this->assertEquals($expected->get('from_date'), $input['from_date']);

        $this->assertFalse($input['paginate']);
        $this->assertEquals('created_at,desc', $input['order_by']);
        $this->assertEquals(1, $input['page']);
    }

    /**
     * @test
     */
    public function it_should_override_default_query_settings(): void
    {
        $queryFilters = [
            [
                'queryParameter' => 'name',
                'uses' => '\Noitran\Repositories\Criteria\ByName',
            ],
            [
                'queryParameter' => 'surname',
                'uses' => '\Noitran\Repositories\Criteria\BySurname',
            ],
        ];

        $requestAttributes = [
            'name' => 'John',
            'surname' => 'Doe',
            'paginate' => 'true',
            'order_by' => 'updated_at,asc',
            'page' => 2,
            'per_page' => 100,
        ];

        $input = $this->filter->getInput(
            $queryFilters,
            $requestAttributes
        );

        $expected = new Fluent($requestAttributes);

        $this->assertEquals($expected->get('name'), $input['name']);
        $this->assertEquals($expected->get('surname'), $input['surname']);

        $this->assertEquals($expected->get('paginate'), $input['paginate']);
        $this->assertEquals($expected->get('order_by'), $input['order_by']);
        $this->assertEquals($expected->get('page'), $input['page']);
        $this->assertEquals($expected->get('per_page'), $input['per_page']);
    }
}
