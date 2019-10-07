<?php

declare(strict_types=1);

namespace Noitran\Repositories\Contracts\Criteria;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface FilterCriteriaInterface.
 */
interface FilterCriteriaInterface
{
//    /**
//     * @param $column
//     *
//     * @return FilterCriteriaInterface
//     */
//    public function setColumn($column): self;
//
//    /**
//     * @param $expression
//     *
//     * @return FilterCriteriaInterface
//     */
//    public function setExpression($expression): self;
//
//    /**
//     * @param $value
//     *
//     * @return FilterCriteriaInterface
//     */
//    public function setValue($value): self;
//
//    /**
//     * @return mixed
//     */
//    public function getColumn();
//
//    /**
//     * @return string
//     */
//    public function getExpression(): string;
//
//    /**
//     * @return mixed
//     */
//    public function getValue();

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function apply($builder): Builder;
}
