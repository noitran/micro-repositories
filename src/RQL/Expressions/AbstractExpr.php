<?php

namespace Noitran\Repositories\RQL\Expressions;

use Noitran\Repositories\Contracts\Expression\ExprInterface;

/**
 * Class AbstractExpr
 */
abstract class AbstractExpr implements ExprInterface
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
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $operator;

    /**
     * AbstractExpr constructor.
     *
     * @param string|null $relation
     * @param string $column
     * @param mixed $value
     */
    public function __construct(?string $relation, string $column, $value)
    {
        $this->relation = $relation;
        $this->column = $column;
        $this->value = $value;
    }

    /**
     * @param string|null $expression
     *
     * @return AbstractExpr
     */
    public function setExpression(string $expression = null): self
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @param string|null $operator
     *
     * @return AbstractExpr
     */
    public function setOperator(string $operator = null): self
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }
}
