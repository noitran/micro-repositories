<?php

namespace Noitran\Repositories\Tests\Stubs\Repositories;

use Noitran\Repositories\Repositories\SqlRepository;
use Noitran\Repositories\Tests\Stubs\Models\User;

/**
 * Class UserRepositoryEloquent
 */
class UserRepositoryEloquent extends SqlRepository implements UserRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function getModelClassName(): string
    {
        return User::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot(): void
    {
        //
    }
}
