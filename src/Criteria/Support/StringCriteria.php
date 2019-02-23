<?php

namespace Noitran\Repositories\Criteria\Support;

use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;

/**
 * Class StringCriteria
 */
class StringCriteria extends AbstractFilterCriteria
{
    /**
     * @param $value
     *
     * @return FilterCriteriaInterface
     */
    public function setValue($value): FilterCriteriaInterface
    {
        $this->value = filter_var($value, FILTER_SANITIZE_STRING | FILTER_SANITIZE_MAGIC_QUOTES);

        return $this;
    }
}
