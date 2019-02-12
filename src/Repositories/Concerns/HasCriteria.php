<?php

namespace Noitran\Repositories\Repositories\Concerns;

use Illuminate\Support\Collection;
use Noitran\Repositories\Contracts\Criteria\CriteriaInterface;
use Noitran\Repositories\Exceptions\RepositoryException;

/**
 * Trait HasCriteria
 */
trait HasCriteria
{
    /**
     * Collection of Criteria
     *
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $disableCriteria = false;

    /**
     * Get Collection of Criteria
     *
     * @return Collection|null
     */
    public function getCriteria(): ?Collection
    {
        return $this->criteria;
    }

    /**
     * Disable/Enable all Criteria
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
     * Push Criteria into Collection
     *
     * @param $criteria
     *
     * @throws RepositoryException
     *
     * @return $this
     */
    public function pushCriteria($criteria): self
    {
        if (is_string($criteria)) {
            $criteria = new $criteria();
        }

        if (!$criteria instanceof CriteriaInterface) {
            throw new RepositoryException(
                'Class ' . get_class($criteria) . ' must be an instance of ' . CriteriaInterface::class
            );
        }

        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Remove Criteria from collection
     *
     * @param $criteria
     *
     * @return mixed
     */
    public function popCriteria($criteria)
    {
        // @todo implement
    }

    /**
     * Apply criteria in current Query
     *
     * @return $this
     */
    protected function applyCriteria(): self
    {
        if ($this->disableCriteria === true) {
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
     * Clear all Criteria
     *
     * @return $this
     */
    public function clearCriteria(): self
    {
        $this->criteria = new Collection();

        return $this;
    }
}
