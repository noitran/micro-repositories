<?php

namespace Noitran\Repositories\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SqlRepository
 */
abstract class SqlRepository extends AbstractRepository
{
    /**
     * {@inheritDoc}
     */
    public function all($columns = ['*'])
    {
        return parent::all(
            $this->getColumnNames($columns)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(int $perPage = null, $columns = ['*'])
    {
        return parent::paginate(
            $perPage,
            $this->getColumnNames($columns)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function simplePaginate(int $perPage = null, $columns = ['*'])
    {
        return parent::simplePaginate(
            $perPage,
            $this->getColumnNames($columns)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function find($id, $columns = ['*'])
    {
        return parent::find(
            $id,
            $this->getColumnNames($columns)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        return parent::findByField(
            $this->getColumnName($field),
            $value,
            $this->getColumnNames($columns)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function first($columns = ['*']): ?Model
    {
        return parent::first(
            $this->getColumnNames($columns)
        );
    }

    /**
     * @param string $column
     * @param mixed $model
     *
     * @return string
     */
    public function getColumnName($column, $model = null): string
    {
        $model = $model ?? $this->model;

        return ! strpos($column, '.')
            ? $this->getSchemaName($model) . '.' . $column
            : $column;
    }
}
