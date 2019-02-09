<?php

namespace Noitran\Repositories\Tests\Criteria;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Noitran\Repositories\Tests\Stubs\Repositories\UserRepository;
use Noitran\Repositories\Tests\TestCase;
use Mockery;

class RepositoryTest extends TestCase
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
    public function itShouldGetRecordsUsingAllMethod(): void
    {
        $users = $this->repository->all();

        $this->assertCount(5, $users);
        $this->assertInstanceOf(Collection::class, $users);
    }

    /**
     * @test
     */
    public function itShouldGetSingleRecordUsingFindMethod(): void
    {
        $user = $this->repository->find(1);

        $this->assertEquals(1, $user->id);
        $this->assertInstanceOf(Model::class, $user);
    }

    /**
     * @test
     */
    public function itShouldGetMultipleRecordsUsingFindMethod(): void
    {
        $users = $this->repository->find([1, 2]);

        $this->assertCount(2, $users);
        $this->assertInstanceOf(Collection::class, $users);
    }

    /**
     * @test
     */
    public function itShouldGetPaginatedCollectionUsingPaginateMethod(): void
    {
        $users = $this->repository->paginate(3);

        $this->assertCount(3, $users);
        $this->assertInstanceOf(LengthAwarePaginator::class, $users);
    }
}
