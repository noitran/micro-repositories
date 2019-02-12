<?php

namespace Noitran\Repositories\Contracts\Repository;

use Illuminate\Support\Collection;

/**
 * Interface Criticizable
 */
interface Criticizable
{
    /**
     * Get Collection of Criteria
     *
     * @return Collection|null
     */
    public function getCriteria(): ?Collection;

    /**
     * Disable/Enable all Criteria
     *
     * @param bool $disable
     *
     * @return mixed
     */
    public function disableCriteria($disable = true);

    /**
     * Push Criteria into Collection
     *
     * @param $criteria
     *
     * @return mixed
     */
    public function pushCriteria($criteria);

    /**
     * Remove Criteria from collection
     *
     * @param $criteria
     *
     * @return mixed
     */
    public function popCriteria($criteria);

    /**
     * Clear all Criteria
     *
     * @return mixed
     */
    public function clearCriteria();
}
