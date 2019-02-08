<?php

namespace Noitran\Repositories\Contracts\Criteria;

use Noitran\Repositories\Contracts\Repository\RepositoryInterface;

/**
 * Interface CriteriaInterface
 */
interface CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository);
}
