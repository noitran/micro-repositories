<?php

declare(strict_types=1);

namespace Noitran\Repositories\Tests\Criteria;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Noitran\Repositories\Tests\Stubs\Filters\UserFilter;
use Noitran\Repositories\Tests\Stubs\Models\User;
use Noitran\Repositories\Tests\TestCase;

/**
 * Class FilterByTest.
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
    }

    /**
     * @test
     *
     * Simple filtering request
     *
     * Request: /users?filter[name]=John&filter[surname]=Doe
     */
    public function it_should_use_basic_filter(): void
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
    public function it_should_use_filter_with_logical_expression_eq(): void
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

    /**
     * @test
     *
     * Request: /users?filter[name][$notEq]=John&filter[surname]=Doe
     */
    public function it_should_use_filter_with_logical_expression_not_eq(): void
    {
        $userToSearch = User::find(3);

        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'name' => [
                    '$notEq' => $userToSearch->name,
                ],
            ],
        ])->all();

        $totalUserCount = User::all()->count();

        $this->assertCount($totalUserCount - 1, $users);
    }

    /**
     * @test
     *
     * '$lt', less than
     */
    public function it_should_test_expression_lt(): void
    {
        $greaterDate = Carbon::create()->addDays(5)->toDateTimeString();

        $excludedUser = User::find(3);
        $excludedUser->last_logged_in_at = $greaterDate;
        $excludedUser->save();

        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'last_logged_in_at' => [
                    '$lt' => $greaterDate,
                ],
            ],
        ])->all();

        $totalUserCount = User::all()->count();

        $this->assertCount($totalUserCount - 1, $users);
    }

    /**
     * @test
     *
     * '$lte', less than or equal
     */
    public function it_should_test_expression_lte(): void
    {
        $greaterDate = Carbon::create()->addDays(5)->toDateTimeString();

        $excludedUser = User::find(3);
        $excludedUser->last_logged_in_at = $greaterDate;
        $excludedUser->save();

        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'last_logged_in_at' => [
                    '$lte' => $greaterDate,
                ],
            ],
        ])->all();

        $totalUserCount = User::all()->count();

        $this->assertCount($totalUserCount, $users);
    }

    /**
     * @test
     *
     * '$gt', greater than
     */
    public function it_should_test_expression_gt(): void
    {
        $greaterDate = Carbon::create()->subDays(5)->toDateTimeString();

        $excludedUser = User::find(3);
        $excludedUser->last_logged_in_at = $greaterDate;
        $excludedUser->save();

        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'last_logged_in_at' => [
                    '$gt' => $greaterDate,
                ],
            ],
        ])->all();

        $totalUserCount = User::all()->count();

        $this->assertCount($totalUserCount - 1, $users);
    }

    /**
     * @test
     *
     * '$gte', greater than or equal
     */
    public function it_should_test_expression_gte(): void
    {
        $greaterDate = Carbon::create()->subDays(5)->toDateTimeString();

        $excludedUser = User::find(3);
        $excludedUser->last_logged_in_at = $greaterDate;
        $excludedUser->save();

        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'last_logged_in_at' => [
                    '$gte' => $greaterDate,
                ],
            ],
        ])->all();

        $totalUserCount = User::all()->count();

        $this->assertCount($totalUserCount, $users);
    }

    /**
     * @test
     *
     * '$like'
     */
    public function it_should_test_expression_like(): void
    {
        $user = new User();
        $user->name = 'SomeRandomString';
        $user->password = bcrypt('random');
        $user->save();

        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'name' => [
                    '$like' => '%Random%',
                ],
            ],
        ])->all();

        $this->assertCount(1, $users);
        $this->assertEquals($user->name, $users->first()->name);
    }

    /**
     * @test
     *
     * '$in'
     */
    public function it_should_test_expression_in(): void
    {
        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'id' => [
                    '$in' => '1,2',
                ],
            ],
        ])->all();

        $this->assertCount(2, $users);
    }

    /**
     * @test
     *
     * '$notIn'
     */
    public function it_should_test_expression_not_in(): void
    {
        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'id' => [
                    '$notIn' => '1,2',
                ],
            ],
        ])->all();

        $this->assertCount(3, $users);
    }

    /**
     * @test
     *
     * '$or'
     */
    public function it_should_test_expression_or(): void
    {
        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'id' => [
                    '$or' => '2|5',
                ],
            ],
        ])->all();

        $this->assertCount(2, $users);
        $this->assertEquals(2, $users->toArray()[0]['id']);
        $this->assertEquals(5, $users->toArray()[1]['id']);
    }

    /**
     * @test
     *
     * '$between'
     */
    public function it_should_test_expression_between(): void
    {
        /** @var Collection $users */
        $users = $this->userFilter->filter([
            'filter' => [
                'id' => [
                    '$between' => '1,3',
                ],
            ],
        ])->all();

        $this->assertCount(3, $users);
    }

    /**
     * @test
     */
    public function it_should_use_default_data_type(): void
    {
        // '$string',
    }

    /**
     * @test
     */
    public function it_should_use_string_data_type(): void
    {
        // '$string',
    }

    /**
     * @test
     */
    public function it_should_use_bool_data_type(): void
    {
        // '$bool',
    }

    /**
     * @test
     */
    public function it_should_use_int_data_type(): void
    {
        // '$int',
    }

    /**
     * @test
     */
    public function it_should_use_date_data_type(): void
    {
        // '$date',
    }

    /**
     * @test
     */
    public function it_should_use_datetime_data_type(): void
    {
        // '$datetime',
    }
}
