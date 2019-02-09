<?php

namespace Noitran\Repositories\Repositories;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Noitran\Repositories\Contracts\Criteria\CriteriaInterface;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;
use Noitran\Repositories\Contracts\Schema\SchemaInterface;
use Noitran\Repositories\Exceptions\RepositoryException;
use Illuminate\Support\Collection;
use Closure;

/**
 * Class AbstractRepository
 */
abstract class AbstractRepository implements RepositoryInterface, SchemaInterface
{
    use InteractsWithSchema;

    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Model
     */
    protected $model;

    /**
     * Collection of Criteria
     *
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * @var Closure
     */
    protected $scopeQuery;

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
     * Get Collection of Criteria
     *
     * @return Collection|null
     */
    public function getCriteria(): ?Collection
    {
        return $this->criteria;
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
     * Clear all Criteria
     *
     * @return $this
     */
    public function clearCriteria(): self
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * Clears query scope
     *
     * @return $this
     */
    public function clearScope(): self
    {
        $this->scopeQuery = null;

        return $this;
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
     * Apply criteria in current Query
     *
     * @return $this
     */
    protected function applyCriteria(): self
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        $criteria = $this->getCriteria();

        if (! $criteria) {
            return $this;
        }

        foreach ($criteria as $c) {
            if ($c instanceof CriteriaInterface) {
                $this->model = $c->apply($this->model, $this);
            }
        }

        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope(): self
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
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
     * Get collection of paginated records
     *
     * @param null $perPage
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function paginate($perPage = null, $columns = ['*'])
    {
        $this->applyCriteria()
            ->applyScope();

        $perPage = $perPage ?? config('repositories.pagination.per_page', $this->model->getPerPage());

        $results = $this->model
            ->paginate($perPage, $columns)
            ->appends(app('request')->query());

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

        $model = $this->model->findOrFail($id, $columns);

        $this->clearModel();

        return $model;
    }
}
