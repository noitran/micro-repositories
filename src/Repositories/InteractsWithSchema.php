<?php

namespace Noitran\Repositories\Repositories;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait InteractsWithSchema
 */
trait InteractsWithSchema
{
    /**
     * @param string $column
     * @param Model $model
     *
     * @return string
     */
    public function getColumnName($column, $model = null): string
    {
        return $column;
    }

    /**
     * @param array $columns
     * @param Model $model
     *
     * @return array
     */
    public function getColumnNames(array $columns, $model = null): array
    {
        return array_map(function ($column) use ($model) {
            return $this->getColumnName($column, $model);
        }, $columns);
    }

    /**
     * @param Model $model
     *
     * @return string
     */
    public function getSchemaName($model = null): string
    {
        $model = $model ?? $this->model;

        if ($model instanceof Model) {
            return $model->getTable();
        } elseif ($model instanceof EloquentBuilder) {
            return $model->getModel()->getTable();
        }

        return $model->from;
    }
}
