<?php

declare(strict_types=1);

namespace Noitran\Repositories\Criteria\Support;

use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;

/**
 * Class IntCriteria.
 */
class IntCriteria extends AbstractFilterCriteria
{
    /**
     * @param $value
     *
     * @return FilterCriteriaInterface
     */
    public function setValue($value): FilterCriteriaInterface
    {
        $this->value = (int) $value;

        return $this;
    }
}
