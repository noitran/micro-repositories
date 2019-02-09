<?php

namespace Noitran\Repositories\Contracts\Schema;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface SchemaInterface
 */
interface SchemaInterface
{
    /**
     * @param string $column
     * @param Model $model
     *
     * @return string
     */
    public function getColumnName($column, $model = null): string;

    /**
     * @param array $columns
     * @param Model $model
     *
     * @return array
     */
    public function getColumnNames(array $columns, $model = null): array;

    /**
     * @param Model $model
     *
     * @return string
     */
    public function getSchemaName($model = null): string;
}
