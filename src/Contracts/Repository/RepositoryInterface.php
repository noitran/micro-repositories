<?php

declare(strict_types=1);

namespace Noitran\Repositories\Contracts\Repository;

use Illuminate\Database\Eloquent\Model;
use Noitran\Repositories\Exceptions\RepositoryException;

/**
 * Interface RepositoryInterface.
 */
interface RepositoryInterface
{
    /**
     * Get list of records.
     *
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * Get collection of paginated records.
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
     * Get single or multiple records by their primary ids.
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
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*']);

    /**
     * Count results of repository.
     *
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return int
     */
    public function count($columns = ['*']): int;

    /**
     * Execute the query and get the first result.
     *
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function first($columns = ['*']): ?Model;

    /**
     * @param array $attributes
     *
     * @throws RepositoryException
     *
     * @return Model|null
     */
    public function create(array $attributes = []): ?Model;

    /**
     * @param mixed $model
     * @param array $attributes
     *
     * @throws RepositoryException
     *
     * @return Model
     */
    public function update($model, array $attributes): Model;

    /**
     * @param $model
     *
     * @throws RepositoryException
     *
     * @return bool|null
     */
    public function delete($model): ?bool;
}
