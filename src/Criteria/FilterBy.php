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
     * @var array
     */
    protected $attributes;

//    /**
//     * @var array
//     */
//    protected $types = [
//        'like',
//        'string',
//        'bool',
//        'int',
//        'date',
//        'datetime',
//    ];
//
//    /**
//     * @var string
//     */
//    protected $defaultType = 'like';
//
//
//    protected $comparisonOperators = [
//        '=',
//        'lte',
//        'lt',
//        'gte',
//        'gt',
//    ];

    /**
     * FilterBy constructor.
     *
     * @param $attributes
     */
    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param Builder $model
     * @param RepositoryInterface $repository
     *
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository): Builder
    {
        if (! empty($this->attributes) && \is_array($this->attributes)) {
            foreach ($this->attributes as $column => $value) {
                $model = $this->applyFilter($model, $column, $value);
            }
        }

        return $model;
    }

    /**
     * @param $model
     * @param $column
     * @param $value
     *
     * @return Builder
     */
    protected function applyFilter($model, $column, $value): Builder
    {


        dd([$column, $value]);
    }
}
