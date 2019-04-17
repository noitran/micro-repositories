<?php

declare(strict_types=1);

namespace Noitran\Repositories\Filters;

/**
 * @todo Add Fractal as Adapter !!!
 * https://github.com/iocaste/microservice-foundation/blob/master/src/Repository/Preloadable.php
 *
 * Trait Preloadable
 */
trait Preloadable
{
    /**
     * @return array
     */
    public function getWith(): array
    {
        return $this->getPreloadables()['with'] ?? [];
    }

    /**
     * @return array
     */
    public function getWithCount(): array
    {
        return $this->getPreloadables()['withCount'] ?? [];
    }
}
