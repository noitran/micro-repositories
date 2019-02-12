<?php

namespace Noitran\Repositories\Tests\Criteria;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Noitran\Repositories\Tests\Stubs\Models\User;
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
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $users);
    }

    /**
     * @test
     */
    public function itShouldGetPaginatedCollectionUsingSimplePaginateMethod(): void
    {
        $users = $this->repository->simplePaginate(3);

        $this->assertCount(3, $users);
        $this->assertInstanceOf(\Illuminate\Pagination\Paginator::class, $users);
    }

    /**
     * @test
     */
    public function itShouldGetResultsUsingFirstMethod(): void
    {
        $user = $this->repository->first();

        $this->assertEquals(1, $user->id);
        $this->assertInstanceOf(Model::class, $user);
    }

    /**
     * @test
     */
    public function itShouldCreateNewModel(): void
    {
        $attributes = [
            'name' => 'Stub',
            'email' => 'stub@stub.com',
        ];

        $count = $this->repository->count();
        $user = $this->repository->create(array_merge($attributes, ['password' => '12345']));

        $this->assertEquals($count + 1, $user->id);
        $this->assertDatabaseHas('users', $attributes);
    }

    /**
     * @test
     */
    public function itShouldUpdateModelById(): void
    {
        $user = User::find(3);
        $attributes = [
            'name' => 'Updated.Name',
            'email' => 'updated.stub@stub.com',
        ];

        $this->repository->update($user->id, $attributes);

        $this->assertDatabaseHas('users', $attributes);
    }

    /**
     * @test
     */
    public function itShouldUpdateModelByModel(): void
    {
        $user = User::find(3);
        $attributes = [
            'name' => 'Updated.Name',
            'email' => 'updated.stub@stub.com',
        ];

        $this->repository->update($user, $attributes);

        $this->assertDatabaseHas('users', $attributes);
    }

    /**
     * @test
     */
    public function itShouldDeleteModelByModelId(): void
    {
        $user = User::find(3);

        $this->repository->delete($user->id);

        $this->assertDatabaseMissing('users', [
            'id' => 3,
        ]);
    }

    /**
     * @test
     */
    public function itShouldDeleteModelByModel(): void
    {
        $user = User::find(3);

        $this->repository->delete($user);

        $this->assertDatabaseMissing('users', [
            'id' => 3,
        ]);
    }
}
