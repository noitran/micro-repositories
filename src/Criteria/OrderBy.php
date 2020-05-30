<?php

declare(strict_types=1);

namespace Noitran\Repositories\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Noitran\Repositories\Contracts\Criteria\CriteriaInterface;
use Noitran\Repositories\Contracts\Repository\RepositoryInterface;
use Noitran\Repositories\Exceptions\ValidationException;

class OrderBy implements CriteriaInterface
{
    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $direction;

    /**
     * Allowed characters for order by column.
     *
     * @var string
     */
    protected $allowedToContain = '/^[a-z0-9\.\_\-]+$/i';

    /**
     * OrderBy constructor.
     *
     * @param mixed $orderBy
     */
    public function __construct($orderBy)
    {
        $this->setOrderByParameters($orderBy);
    }

    /**
     * @param $orderBy
     */
    public function setOrderByParameters($orderBy): void
    {
        [$column, $direction] = explode(',', $orderBy);

        $this->column = $column;
        $this->direction = $direction ?? 'asc';

        if (! \in_array($this->direction, ['asc', 'desc'])) {
            $this->direction = 'asc';
        }
    }

    /**
     * @param $model
     * @param RepositoryInterface $repository
     *
     * @throws ValidationException
     *
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository) // : Builder
    {
        if (empty($this->column)) {
            return $model;
        }

        if (! preg_match($this->allowedToContain, $this->column)) {
            throw new ValidationException('OrderBy query parameter contains illegal characters.');
        }

        // @todo Implement

        return $model;
    }
}
