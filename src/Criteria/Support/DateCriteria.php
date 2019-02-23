<?php

namespace Noitran\Repositories\Criteria\Support;

use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;
use Illuminate\Support\Carbon;

/**
 * Class DateCriteria
 */
class DateCriteria extends AbstractFilterCriteria
{
    /**
     * @param $value
     *
     * @return FilterCriteriaInterface
     */
    public function setValue($value): FilterCriteriaInterface
    {
        $this->value = Carbon::parse($value)->toDateString();

        return $this;
    }
}
