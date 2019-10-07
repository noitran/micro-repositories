<?php

declare(strict_types=1);

namespace Noitran\Repositories\Contracts\Filter;

interface FilterInterface
{
    /**
     * @param mixed $requestAttributes
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
