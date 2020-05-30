<?php

declare(strict_types=1);

namespace Noitran\Repositories\Repositories\Concerns;

/**
 * Trait BuildsQueries.
 */
trait BuildsQueries
{
    /**
     * Eager load relations.
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations): self
    {
        $this->model = $this->model->with($relations);

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
     * Add sub-select queries to count the relations.
     *
     * @param mixed $relations
     *
     * @return $this
     */
    public function withCount($relations): self
    {
        $this->model = $this->model->withCount($relations);

        return $this;
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param $column
     * @param string $direction
     *
     * @return BuildsQueries
     */
    public function orderBy($column, $direction = 'asc'): self
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }
}
