<?php

namespace Noitran\Repositories\Contracts\Repository;

/**
 * Interface RepositoryInterface
 */
interface RepositoryInterface
{
    /**
     * Get collection
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * Get paginated collection
     *
     * @param null $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*']);

    /**
     * Find by id
     *
     * @param $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*']);
}
