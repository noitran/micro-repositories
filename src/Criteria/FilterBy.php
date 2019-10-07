<?php

declare(strict_types=1);

namespace Noitran\Repositories\Criteria;

use Noitran\RQL\Parsers\Model;
use Noitran\RQL\Parsers\Request\Illuminate\RequestParser;
use Noitran\RQL\Parsers\Simple\SimpleParser;
use function is_array;

use Illuminate\Database\Eloquent\Builder;
use Noitran\Repositories\Contracts\Criteria\CriteriaInterface;
use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;
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
    public function apply($model, RepositoryInterface $repository) //: Builder
    {
        if (empty($this->attributes) || ! is_array($this->attributes)) {
            return $model;
        }

        $collection = (new SimpleParser($this->attributes))->parse();

        foreach ($collection as $item) {
            $model = $this->applyFilter($model, $item);
        }

        return $model;
    }

    /**
     * @param $builderModel
     * @param $queryModel
     * @return Builder
     * @throws RepositoryException
     */
    protected function applyFilter($builderModel, Model $queryModel): Builder
    {
        $relation = $queryModel->getRelation();

        if ($relation && ! $this->modelHasRelation($builderModel->getModel(), $relation)) {
            throw new RepositoryException('Trying to filter by non existent relation.');
        }

        $column = $queryModel->getField();
        $dataType = $queryModel->getDataType();
        $expression = $queryModel->getExpression();
        $valueToSearch = $queryModel->getValue();

        if ($relation) {
            $builderModel = $builderModel->whereHas(
                $relation,
                function ($query) use ($column, $dataType, $expression, $valueToSearch): void {
                    $this->applyDataTypeFilter($query, $column, $dataType, $expression, $valueToSearch);
                }
            );
        } else {
            $builderModel = $this->applyDataTypeFilter($builderModel, $column, $dataType, $expression, $valueToSearch);
        }

        return $builderModel;
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
            ->setExpression($expression)
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
