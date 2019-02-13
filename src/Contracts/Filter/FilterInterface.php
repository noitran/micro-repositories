<?php

namespace Noitran\Repositories\Contracts\Filter;

interface FilterInterface
{
    /**
     * @param array $requestAttributes
     *
     * @return mixed
     */
    public function filter(array $requestAttributes);

    /**
     * @return mixed
     */
    public function all();

    /**
     * @param int|null $perPage
     *
     * @return mixed
     */
    public function paginate(int $perPage = null);
}
