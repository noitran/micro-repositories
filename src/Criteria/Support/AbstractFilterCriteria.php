<?php

declare(strict_types=1);

namespace Noitran\Repositories\Criteria\Support;

use Illuminate\Database\Eloquent\Builder;
use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;
use Noitran\RQL\Contracts\Expression\ExprInterface;
use Noitran\RQL\Contracts\Processor\ProcessorInterface;
use Noitran\RQL\Queues\ExprQueue;
use Noitran\RQL\Processors\Eloquent\EloquentProcessor;

/**
 * Class AbstractFilterCriteria.
 */
abstract class AbstractFilterCriteria implements FilterCriteriaInterface
{
    /**
     * @param Builder $builder
     *
     * @return Builder
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @throws \Noitran\RQL\Exceptions\ExpressionException
     */
    public function apply($builder): Builder
    {
        $queue = new ExprQueue();
        $queue->enqueue($this->createExprClass());

        return app()->make(ProcessorInterface::class)->setBuilder($builder)->process($queue);

        // return (new EloquentProcessor($builder))->process($queue);
    }

    /**
     * @return ExprInterface
     */
    protected function createExprClass(): ExprInterface
    {
        $expression = ucfirst($this->getExpression(true));
        $namespace = 'Noitran\RQL\Expressions\\';

        $exprClass = $namespace . $expression . 'Expr';

        return new $exprClass(null, $this->getColumn(), $this->getValue());
    }
}
