<?php

namespace Noitran\Repositories\Tests\Criteria;

use Illuminate\Support\Collection;
use Noitran\Repositories\Tests\Stubs\Filters\UserFilter;
use Noitran\Repositories\Tests\Stubs\Models\User;
use Noitran\Repositories\Tests\TestCase;

/**
 * Class FilterByTest
 *
 * https://jsonapi.org/recommendations/#filtering
 *
 * Request for all comments associated with a particular post
 * GET /comments?filter[post]=1 HTTP/1.1
 *
 *
 * Multiple filter values can be combined in a comma-separated list
 * GET /comments?filter[post]=1,2 HTTP/1.1
 *
 * Multiple filters can be applied to a single request:
 * GET /comments?filter[post]=1,2&filter[author]=12 HTTP/1.1
 *
 * https://jsonapi.org/format/1.1/#query-parameters-families
 * ?page[offset]=0&page[limit]=10
 *
 * https://discuss.jsonapi.org/t/filter-on-nested-relationship-attribute/1275/4
 * filter[author.name][eq]=John
 * filter[author.name][not-eq]=John
 *
 * Yes the operator like filter[author.name][eq]=John could be a good extension.
 * With only filter[author.name]=John meaning an implicit [eq].
 *
 * https://discuss.jsonapi.org/t/share-propose-a-filtering-strategy/257
 *
 * RQL implementation for PHP
 * https://github.com/jpcercal/resource-query-language
 */
class FilterByTest extends TestCase
{
    /**
     * @var UserFilter
     */
    protected $userFilter;

    public function setUp(): void
    {
        parent::setUp();

        $this->userFilter = $this->app->make(UserFilter::class);
    }

    public function tearDown(): void
    {
        //
    }

    /**
     * @test
     *
     * Simple filtering request
     *
     * Request: /users?filter[name]=John&filter[surname]=Doe
     */
    public function itShouldUseBasicFilter(): void
    {
        $userToSearch = User::find(3);

        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'name' => $userToSearch->name,
                'surname' => $userToSearch->surname,
            ],
        ])->all();

        $this->assertEquals($userToSearch->name, $users->first()->name);
        $this->assertEquals($userToSearch->surname, $users->first()->surname);
    }

    /**
     * @test
     *
     * Simple filtering request
     *
     * Request: /users?filter[name][$eq]=John&filter[surname]=Doe
     */
    public function itShouldUseFilterWithLogicalOperator(): void
    {
        $userToSearch = User::find(3);

        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'name' => [
                    '$eq' => $userToSearch->name,
                ],
                'surname' => $userToSearch->surname,
            ],
        ])->all();

        $this->assertEquals($userToSearch->name, $users->first()->name);
        $this->assertEquals($userToSearch->surname, $users->first()->surname);
        $this->assertEquals($userToSearch->email, $users->first()->email);
    }
}
