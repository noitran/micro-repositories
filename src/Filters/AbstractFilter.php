<?php

namespace Noitran\Repositories\Filters;

use Noitran\Repositories\Repositories\AbstractRepository;

abstract class AbstractFilter
{
    /**
     * @var AbstractRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $queryFilters = [];

    /**
     * @var array
     */
    protected $defaultQueryFilters = [
        [
            'queryParameter' => 'order_by',
            'criteria' => \Iocaste\Filter\OrderBy::class,
        ],
        [
            'queryParameter' => 'filter',
            'criteria' => \Iocaste\Filter\FilterBy::class,
        ],
    ];

    /**
     * @var array
     */
    protected $defaultQuerySettings = [
        'paginate' => false,
        'per_page' => self::ITEMS_PER_PAGE,
        'page' => 1,
        'order_by' => 'created_at,desc',
    ];

    /**
     * @param AbstractRepository $repository
     *
     * @return $this
     */
    public function setRepository(AbstractRepository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }
}
