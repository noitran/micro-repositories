<?php

declare(strict_types=1);

namespace Noitran\Repositories\Criteria\Support;

use Illuminate\Support\Carbon;
use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;

/**
 * Class DateCriteria.
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
