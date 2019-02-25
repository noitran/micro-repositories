<?php

namespace Noitran\Repositories\Criteria;

use Noitran\Repositories\Contracts\Criteria\CriteriaInterface;
use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Noitran\Repositories\Criteria\Support\FilterQueryParser;
use Noitran\Repositories\Exceptions\RepositoryException;
use Noitran\Repositories\Filters\InteractsWithModel;

/**
 * Class FilterBy.
 */
class FilterBy implements CriteriaInterface
{
    use InteractsWithModel;

    /**
     * @var array
     */
    protected $attributes;

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
     * @param $model
     * @param RepositoryInterface $repository
     *
     * @throws RepositoryException
     *
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository): Builder
    {
        if (! empty($this->attributes) && \is_array($this->attributes)) {
            foreach ($this->attributes as $parameter => $value) {
                $model = $this->applyFilter($model, $parameter, $value);
            }
        }

        return $model;
    }

    /**
     * @param $model
     * @param $parameter
     * @param $value
     *
     * @throws RepositoryException
     *
     * @return Builder
     */
    protected function applyFilter($model, $parameter, $value): Builder
    {
        $parser = (new FilterQueryParser($parameter, $value))->parse();
        $relation = $parser->getRelation();

        if ($relation && ! $this->modelHasRelation($model->getModel(), $relation)) {
            throw new RepositoryException('Trying to filter by non existent relation.');
        }

        $column = $parser->getColumn();
        $dataType = $parser->getDataType();
        $expression = $parser->getExpression();
        $valueToSearch = $parser->getValue();

        if ($relation) {
            $model = $model->whereHas(
                $relation,
                function ($query) use ($column, $dataType, $expression, $valueToSearch): void {
                    $this->applyDataTypeFilter($query, $column, $dataType, $expression, $valueToSearch);
                }
            );
        } else {
            $model = $this->applyDataTypeFilter($model, $column, $dataType, $expression, $valueToSearch);
        }

        return $model;
    }

    /**
     * @param $builder
     * @param $column
     * @param $dataType
     * @param $expression
     * @param $valueToSearch
     *
     * @return Builder
     */
    protected function applyDataTypeFilter($builder, $column, $dataType, $expression, $valueToSearch): Builder
    {
        $criteria = $this->createFilterCriteria($dataType);

        $criteria->setColumn($column)
            ->setValue($valueToSearch);

        return $criteria->apply($builder);
    }

    /**
     * @param string $dataType
     *
     * @return FilterCriteriaInterface
     */
    protected function createFilterCriteria(string $dataType): FilterCriteriaInterface
    {
        $namespace = 'Noitran\Repositories\Criteria\Support\\';
        $criteria = $namespace . ucfirst($dataType) . 'Criteria';

        if (! class_exists($criteria)) {
            $defaultCriteria = $namespace . 'DefaultCriteria';

            return new $defaultCriteria();
        }

        return new $criteria();
    }
}
