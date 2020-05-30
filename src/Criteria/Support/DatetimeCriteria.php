<?php

declare(strict_types=1);

namespace Noitran\Repositories\Criteria\Support;

use Illuminate\Support\Carbon;
use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;

/**
 * Class DatetimeCriteria.
 */
class DatetimeCriteria extends AbstractFilterCriteria
{
    /**
     * @param $value
     *
     * @return FilterCriteriaInterface
     */
    public function setValue($value): FilterCriteriaInterface
    {
        $this->value = Carbon::parse($value)->toDateTimeString();

        return $this;
    }
}
