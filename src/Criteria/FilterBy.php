<?php

namespace Noitran\Repositories\Criteria;

use Noitran\Repositories\Contracts\Criteria\CriteriaInterface;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class FilterBy.
 */
class FilterBy implements CriteriaInterface
{
    /**
     * @var string
     */
    protected $filter;

    /**
     * FilterBy constructor.
     *
     * @param $filter
     */
    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param Builder $model
     * @param RepositoryInterface $repository
     *
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository): Builder
    {
        return $model;
    }
}
