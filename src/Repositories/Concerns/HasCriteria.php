<?php

declare(strict_types=1);

namespace Noitran\Repositories\Repositories\Concerns;

use Illuminate\Support\Collection;
use Noitran\Repositories\Contracts\Criteria\CriteriaInterface;
use Noitran\Repositories\Exceptions\RepositoryException;

/**
 * Trait HasCriteria.
 */
trait HasCriteria
{
    /**
     * Collection of Criteria.
     *
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $disableCriteria = false;

    /**
     * Get Collection of Criteria.
     *
     * @return Collection|null
     */
    public function getCriteria(): ?Collection
    {
        return $this->criteria;
    }

    /**
     * Disable/Enable all Criteria.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableCriteria($disable = true): self
    {
        $this->disableCriteria = $disable;

        return $this;
    }

    /**
     * Push Criteria into Collection.
     *
     * @param $criteria
     *
     * @throws RepositoryException
     *
     * @return $this
     */
    public function pushCriteria($criteria): self
    {
        if (\is_string($criteria)) {
            $criteria = new $criteria();
        }

        if (! $criteria instanceof CriteriaInterface) {
            throw new RepositoryException(
                'Class ' . \get_class($criteria) . ' must be an instance of ' . CriteriaInterface::class
            );
        }

        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Remove Criteria from collection.
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria): self
    {
        $this->criteria = $this->criteria->reject(function ($value) use ($criteria) {
            if (\is_object($value) && \is_string($criteria)) {
                return \get_class($value) === $criteria;
            }

            if (\is_string($value) && \is_object($criteria)) {
                return $value === \get_class($criteria);
            }

            return \get_class($value) === \get_class($criteria);
        });

        return $this;
    }

    /**
     * Apply criteria in current Query.
     *
     * @return $this
     */
    protected function applyCriteria(): self
    {
        if (true === $this->disableCriteria) {
            return $this;
        }

        $criteria = $this->getCriteria();

        if (! $criteria) {
            return $this;
        }

        foreach ($criteria as $value) {
            if ($value instanceof CriteriaInterface) {
                $this->model = $value->apply($this->model, $this);
            }
        }

        return $this;
    }

    /**
     * Clear all Criteria.
     *
     * @return $this
     */
    public function clearCriteria(): self
    {
        $this->criteria = new Collection();

        return $this;
    }
}
