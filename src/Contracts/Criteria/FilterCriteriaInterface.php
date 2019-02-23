<?php

namespace Noitran\Repositories\Contracts\Criteria;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface FilterCriteriaInterface
 */
interface FilterCriteriaInterface
{
    /**
     * @param $column
     *
     * @return FilterCriteriaInterface
     */
    public function setColumn($column): FilterCriteriaInterface;

    /**
     * @param $value
     *
     * @return FilterCriteriaInterface
     */
    public function setValue($value): FilterCriteriaInterface;

    /**
     * @return mixed
     */
    public function getColumn();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param Builder $model
     *
     * @return Builder
     */
    public function apply($model): Builder;
}
