<?php

declare(strict_types=1);

namespace Noitran\Repositories\Events;

use Illuminate\Database\Eloquent\Model;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;

abstract class AbstractEvent
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var Model
     */
    protected $model;

    /**
     * AbstractEvent constructor.
     *
     * @param RepositoryInterface $repository
     * @param Model $model
     */
    public function __construct(RepositoryInterface $repository, Model $model)
    {
        $this->repository = $repository;
        $this->model = $model;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
