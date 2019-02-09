<?php

namespace Noitran\Repositories\Criteria;

use Noitran\Repositories\Contracts\Criteria\CriteriaInterface;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class LimitBy.
 */
class LimitBy implements CriteriaInterface
{
    /**
     * @var string
     */
    protected $count;

    /**
     * LimitBy constructor.
     *
     * @param $count
     */
    public function __construct($count)
    {
        $this->count = $count;
    }

    /**
     * @param Builder $model
     * @param RepositoryInterface $repository
     *
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository): Builder
    {
        if ($this->count) {
            return $model->limit($this->count);
        }

        return $model;
    }
}
