<?php

namespace Noitran\Repositories\Contracts\Repository;

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
     * @param null $perPage
     * @param array $columns
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function paginate($perPage = null, $columns = ['*']);

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
}
