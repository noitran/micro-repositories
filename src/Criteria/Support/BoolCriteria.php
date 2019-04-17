<?php

declare(strict_types=1);

namespace Noitran\Repositories\Criteria\Support;

use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;

/**
 * Class BoolCriteria.
 */
class BoolCriteria extends AbstractFilterCriteria
{
    /**
     * @param $value
     *
     * @return FilterCriteriaInterface
     */
    public function setValue($value): FilterCriteriaInterface
    {
        $this->value = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        return $this;
    }
}
