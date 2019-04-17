<?php

declare(strict_types=1);

namespace Noitran\Repositories\Tests\Criteria;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Mockery;
use Noitran\Repositories\Events\EntityCreated;
use Noitran\Repositories\Events\EntityDeleted;
use Noitran\Repositories\Events\EntityUpdated;
use Noitran\Repositories\Tests\Stubs\Models\User;
use Noitran\Repositories\Tests\Stubs\Repositories\UserRepository;
use Noitran\Repositories\Tests\TestCase;

/**
 * Class RepositoryTest.
 */
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
    public function it_should_get_records_using_all_method(): void
    {
        $users = $this->repository->all();

        $this->assertCount(5, $users);
        $this->assertInstanceOf(Collection::class, $users);
    }

    /**
     * @test
     */
    public function it_should_get_single_record_using_find_method(): void
    {
        $user = $this->repository->find(1);

        $this->assertEquals(1, $user->id);
        $this->assertInstanceOf(Model::class, $user);
    }

    /**
     * @test
     */
    public function it_should_get_multiple_records_using_find_method(): void
    {
        $users = $this->repository->find([1, 2]);

        $this->assertCount(2, $users);
        $this->assertInstanceOf(Collection::class, $users);
    }

    /**
     * @test
     */
    public function it_should_find_by_field(): void
    {
        $userToFind = User::find(3);

        $users = $this->repository->findByField('email', $userToFind->email);

        $this->assertCount(1, $users);
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertEquals($userToFind->email, $users->first()->email);
    }

    /**
     * @test
     */
    public function it_should_get_paginated_collection_using_paginate_method(): void
    {
        $users = $this->repository->paginate(3);

        $this->assertCount(3, $users);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $users);
    }

    /**
     * @test
     */
    public function it_should_get_paginated_collection_using_simple_paginate_method(): void
    {
        $users = $this->repository->simplePaginate(3);

        $this->assertCount(3, $users);
        $this->assertInstanceOf(\Illuminate\Pagination\Paginator::class, $users);
    }

    /**
     * @test
     */
    public function it_should_get_results_using_first_method(): void
    {
        $user = $this->repository->first();

        $this->assertEquals(1, $user->id);
        $this->assertInstanceOf(Model::class, $user);
    }

    /**
     * @test
     */
    public function it_should_create_new_model(): void
    {
        $this->expectsEvents(EntityCreated::class);

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
    public function it_should_update_model_by_id(): void
    {
        $this->expectsEvents(EntityUpdated::class);

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
    public function it_should_update_model_by_model(): void
    {
        $this->expectsEvents(EntityUpdated::class);

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
    public function it_should_delete_model_by_model_id(): void
    {
        $this->expectsEvents(EntityDeleted::class);

        $user = User::find(3);

        $this->repository->delete($user->id);

        $this->assertDatabaseMissing('users', [
            'id' => 3,
        ]);
    }

    /**
     * @test
     *
     * @throws \Noitran\Repositories\Exceptions\RepositoryException
     */
    public function it_should_delete_model_by_model(): void
    {
        $this->expectsEvents(EntityDeleted::class);

        $user = User::find(3);

        $this->repository->delete($user);

        $this->assertDatabaseMissing('users', [
            'id' => 3,
        ]);
    }
}
