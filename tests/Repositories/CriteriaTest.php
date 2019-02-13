<?php

namespace Noitran\Repositories\Tests\Criteria;

use Illuminate\Database\Eloquent\Collection;
use Noitran\Repositories\Criteria\LimitBy;
use Noitran\Repositories\Tests\Stubs\Criteria\ByListOfEmails;
use Noitran\Repositories\Tests\Stubs\Models\User;
use Noitran\Repositories\Tests\Stubs\Repositories\UserRepository;
use Noitran\Repositories\Tests\TestCase;
use Mockery;

/**
 * Class CriteriaTest
 */
class CriteriaTest extends TestCase
{
    /**
     * @var UserRepository
     */
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(UserRepository::class);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function itShouldUseLimitByCriteria(): void
    {
        $users = $this->repository
            ->pushCriteria(new LimitBy(2))
            ->all();

        $this->assertCount(2, $users);
        $this->assertInstanceOf(Collection::class, $users);
    }

    /**
     * @test
     */
    public function itShouldTestByListOfValuesAbstractCriteria(): void
    {
        $users = User::pluck('email');

        $emails = implode(',', $users->toArray());

        $output = $this->repository
            ->pushCriteria(new ByListOfEmails($emails))
            ->all();

        $this->assertCount(count($users), $output);
        $this->assertInstanceOf(Collection::class, $output);
    }
}
