<?php

declare(strict_types=1);

namespace Noitran\Repositories\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Noitran\Repositories\Contracts\Criteria\CriteriaInterface;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;

/**
 * Class ListOfValues.
 */
abstract class ListOfValues implements CriteriaInterface
{
    /**
     * @var array
     */
    protected $values;

    /**
     * ListOfValues constructor.
     *
     * @param string $valueList Comma separated sting of values
     */
    final public function __construct($valueList)
    {
        $this->values = explode(',', $valueList);
    }

    /**
     * Returns field name in schema.
     *
     * @return mixed
     */
    abstract protected function getField(): string;

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param Builder $model
     * @param RepositoryInterface $repository
     *
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository): Builder
    {
        $column = $repository->getColumnName($this->getField(), $model);

        return $model->whereIn($column, $this->getValues());
    }
}
