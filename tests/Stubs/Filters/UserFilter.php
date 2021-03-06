<?php

declare(strict_types=1);

namespace Noitran\Repositories\Tests\Stubs\Filters;

use Noitran\Repositories\Filters\AbstractFilter;
use Noitran\Repositories\Tests\Stubs\Repositories\UserRepository;

class UserFilter extends AbstractFilter
{
    /**
     * @var array
     */
    protected $queryFilters = [];

    /**
     * UserFilter constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();

        $this->setRepository($userRepository);
    }
}
