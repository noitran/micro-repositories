<?php

namespace Noitran\Repositories\Criteria\Support;

use Illuminate\Database\Eloquent\Builder;
use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;

/**
 * Class AbstractFilterCriteria
 */
abstract class AbstractFilterCriteria implements FilterCriteriaInterface
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $column
     *
     * @return FilterCriteriaInterface
     */
    public function setColumn($column): FilterCriteriaInterface
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @param Builder $model
     *
     * @return Builder
     */
    public function apply($model): Builder
    {
        return $model->where($this->getColumn(), $this->getValue());
    }
}
