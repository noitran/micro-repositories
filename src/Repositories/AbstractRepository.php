<?php

namespace Noitran\Repositories\Repositories;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;
use Noitran\Repositories\Exceptions\RepositoryException;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Model
     */
    protected $model;

    /**
     * AbstractRepository constructor.
     *
     * @param Container $app
     *
     * @throws RepositoryException
     */
    public function __construct(Container $app)
    {
        $this->app = $app;

        $this->init();
    }

    /**
     * @throws RepositoryException
     *
     * @return AbstractRepository
     */
    public function init(): self
    {
        $this->setModel();

        return $this;
    }

    /**
     * Returns model's fully qualified class name
     *
     * @return string
     */
    abstract public function getModelClassName(): string;

    /**
     * Fires when repository is created
     */
    abstract public function boot(): void;

    /**
     * @throws RepositoryException
     *
     * @return Model
     */
    public function setModel(): Model
    {
        $model = $this->app->make($this->getModelClassName());

        if (! $model instanceof Model) {
            throw new RepositoryException("Class {$this->getModelClassName()} must be an instance of " . Model::class);
        }

        return $this->model = $model;
    }

    /**
     * @return $this
     */
    public function withTrashed(): self
    {
        $this->model = $this->model->withTrashed();

        return $this;
    }

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
