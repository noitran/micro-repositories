<?php

namespace Noitran\Repositories\Tests\Stubs\Criteria;

use Noitran\Repositories\Criteria\ListOfValues;

/**
 * Class ByListOfEmails
 */
class ByListOfEmails extends ListOfValues
{
    /**
     * @return string
     */
    protected function getField(): string
    {
        return 'email';
    }
}
