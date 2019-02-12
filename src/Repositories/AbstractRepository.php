<?php

namespace Noitran\Repositories\Repositories;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Noitran\Repositories\Contracts\Repository\Criticizable;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;
use Noitran\Repositories\Contracts\Schema\SchemaInterface;
use Noitran\Repositories\Events\EntityCreated;
use Noitran\Repositories\Events\EntityDeleted;
use Noitran\Repositories\Events\EntityUpdated;
use Noitran\Repositories\Exceptions\RepositoryException;
use Closure;

/**
 * Class AbstractRepository
 */
abstract class AbstractRepository implements RepositoryInterface, SchemaInterface, Criticizable
{
    use Concerns\InteractsWithSchema,
        Concerns\HasCriteria,
        Concerns\BuildsQueries;

    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Closure
     */
    protected $scope;

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
            throw new RepositoryException(
                "Class {$this->getModelClassName()} must be an instance of " . Model::class
            );
        }

        return $this->model = $model;
    }

    /**
     * Clears model
     *
     * @throws RepositoryException
     */
    public function clearModel(): self
    {
        $this->setModel();

        return $this;
    }

    /**
     * @param Closure $scope
     *
     * @return $this
     */
    public function setScope(Closure $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Clears query scope
     *
     * @return $this
     */
    public function clearScope(): self
    {
        $this->scope = null;

        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope(): self
    {
        if (isset($this->scope) && is_callable($this->scope)) {
            $callback = $this->scope;
            $this->model = $callback($this->model);
        }

        return $this;
    }

    /**
     * Get list of records
     *
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return EloquentBuilder[]|\Illuminate\Database\Eloquent\Collection|Model[]|mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria()
            ->applyScope();

        if ($this->model instanceof EloquentBuilder) {
            $output = $this->model->get($columns);
        } else {
            $output = $this->model::all($columns);
        }

        $this->clearModel()
            ->clearScope();

        return $output;
    }

    /**
     * Alias of all()
     *
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return EloquentBuilder[]|\Illuminate\Database\Eloquent\Collection|Model[]|mixed
     */
    public function get($columns = ['*'])
    {
        return $this->all($columns);
    }

    /**
     * Get collection of paginated records
     *
     * @param int|null $perPage
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function paginate(int $perPage = null, $columns = ['*'])
    {
        return $this->getPaginator($perPage, $columns, function ($perPage, $columns) {
            return $this->model
                ->paginate($perPage, $columns)
                ->appends(app('request')->query());
        });
    }

    /**
     * @param int|null $perPage
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function simplePaginate(int $perPage = null, $columns = ['*'])
    {
        return $this->getPaginator($perPage, $columns, function ($perPage, $columns) {
            return $this->model
                ->simplePaginate($perPage, $columns)
                ->appends(app('request')->query());
        });
    }

    /**
     * @param int|null $perPage
     * @param array $columns
     * @param callable|null $callback
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    protected function getPaginator(int $perPage = null, $columns = ['*'], callable $callback = null)
    {
        $this->applyCriteria()
            ->applyScope();

        $perPage = $perPage ?? config('repositories.pagination.per_page', $this->model->getPerPage());

        $results = $callback($perPage, $columns);
        $this->clearModel();

        return $results;
    }

    /**
     * Get single or multiple records by their primary ids
     *
     * @param mixed $id
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        $this->applyCriteria()
            ->applyScope();

        $model = $this->model->find($id, $columns);

        $this->clearModel();

        return $model;
    }

    /**
     * @param $field
     * @param null $value
     * @param array $columns
     *
     * @return mixed
     * @throws RepositoryException
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        $this->applyCriteria()
            ->applyScope();

        $results = $this->model
            ->where($field, '=', $value)
            ->get($columns);

        $this->clearModel();

        return $results;
    }

    /**
     * Count results of repository
     *
     * @param array $columns
     *
     * @return int
     * @throws RepositoryException
     */
    public function count($columns = ['*']): int
    {
        $this->applyCriteria()
            ->applyScope();

        $count = $this->model->count($columns);

        $this->clearModel();

        return $count;
    }

    /**
     * Execute the query and get the first result.
     *
     * @param array $columns
     *
     * @return mixed
     * @throws RepositoryException
     */
    public function first($columns = ['*']): ?Model
    {
        $this->applyCriteria()
            ->applyScope();

        $model = $this->model->first($columns);

        $this->clearModel();

        return $model;
    }

    /**
     * @param array $attributes
     *
     * @return Model|null
     * @throws RepositoryException
     */
    public function create(array $attributes = []): ?Model
    {
        $model = $this->model->create($attributes);

        $this->clearModel();

        event(new EntityCreated($this, $model));

        return $model;
    }

    /**
     * @param mixed $model
     * @param array $attributes
     *
     * @return Model
     * @throws RepositoryException
     */
    public function update($model, array $attributes): Model
    {
        $this->applyScope();

        if (! $model instanceof Model) {
            $model = $this->model->findOrFail($model);
        }

        $model->fill($attributes)->save();

        $this->clearModel();

        event(new EntityUpdated($this, $model));

        return $model;
    }

    /**
     * @param $model
     *
     * @return bool|null
     * @throws RepositoryException
     */
    public function delete($model): ?bool
    {
        $this->applyScope();

        if (! $model instanceof Model) {
            $model = $this->find($model);
        }

        $clonedModel = clone $model;
        $deleted = $model->delete();

        event(new EntityDeleted($this, $clonedModel));

        return $deleted;
    }
}
