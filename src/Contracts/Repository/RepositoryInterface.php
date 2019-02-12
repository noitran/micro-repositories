<?php

namespace Noitran\Repositories\Contracts\Repository;

use Illuminate\Database\Eloquent\Model;
use Noitran\Repositories\Exceptions\RepositoryException;

/**
 * Interface RepositoryInterface
 */
interface RepositoryInterface
{
    /**
     * Get list of records
     *
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function all($columns = ['*']);

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
    public function paginate(int $perPage = null, $columns = ['*']);

    /**
     * @param int|null $perPage
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function simplePaginate(int $perPage = null, $columns = ['*']);

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
    public function find($id, $columns = ['*']);

    /**
     * @param $field
     * @param null $value
     * @param array $columns
     *
     * @return mixed
     * @throws RepositoryException
     */
    public function findByField($field, $value = null, $columns = ['*']);

    /**
     * Count results of repository
     *
     * @param array $columns
     *
     * @return int
     * @throws RepositoryException
     */
    public function count($columns = ['*']): int;

    /**
     * Execute the query and get the first result.
     *
     * @param array $columns
     *
     * @return mixed
     * @throws RepositoryException
     */
    public function first($columns = ['*']): ?Model;

    /**
     * @param array $attributes
     *
     * @return Model|null
     * @throws RepositoryException
     */
    public function create(array $attributes = []): ?Model;

    /**
     * @param mixed $model
     * @param array $attributes
     *
     * @return Model
     * @throws RepositoryException
     */
    public function update($model, array $attributes): Model;

    /**
     * @param $model
     *
     * @return bool|null
     * @throws RepositoryException
     */
    public function delete($model): ?bool;
}
