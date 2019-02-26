<?php

namespace Noitran\Repositories\Criteria\Support;

use Illuminate\Database\Eloquent\Builder;
use Noitran\Repositories\Contracts\Criteria\FilterCriteriaInterface;
use Noitran\RQL\Contracts\Expression\ExprInterface;
use Noitran\RQL\ExprQueue;
use Noitran\RQL\Processors\EloquentProcessor;

/**
 * Class AbstractFilterCriteria
 */
abstract class AbstractFilterCriteria implements FilterCriteriaInterface
{
    /**
     * @var string|null
     */
    protected $relation;

    /**
     * @var string
     */
    protected $column;

    /**
     * @var
     */
    protected $expression;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @return string|null
     */
    public function getRelation(): ?string
    {
        return $this->relation;
    }

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @param bool $replace
     *
     * @return string
     */
    public function getExpression(bool $replace = false): string
    {
        return $replace ? str_replace('$', '', $this->expression) : $this->expression;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $column
     *
     * @return FilterCriteriaInterface
     */
    public function setColumn($column): FilterCriteriaInterface
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @param $expression
     *
     * @return FilterCriteriaInterface
     */
    public function setExpression($expression): FilterCriteriaInterface
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     * @throws \Noitran\RQL\Exceptions\ExpressionException
     */
    public function apply($builder): Builder
    {
        $queue = new ExprQueue();
        $queue->enqueue($this->createExprClass());

        return (new EloquentProcessor($builder))->process($queue);
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
