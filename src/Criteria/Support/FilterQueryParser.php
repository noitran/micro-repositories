<?php

namespace Noitran\Repositories\Criteria\Support;

use Noitran\Repositories\Exceptions\RepositoryException;

/**
 * Class FilterQueryParser
 *
 * http://localhost:8104/users?filter[name][eq]=John&filter[surname]=Doe
 */
class FilterQueryParser
{
    /**
     * @var string
     */
    public $filterParameter;

    /**
     * @var mixed
     */
    public $filterValue;

    /**
     * @var
     */
    protected $relation;

    /**
     * @var
     */
    protected $column;

    /**
     * @var
     */
    protected $dataType;

    /**
     * @var
     */
    protected $expression;

    /**
     * @var string
     */
    protected $value;

    /**
     * FilterQueryParser constructor.
     *
     * @param string $filterParameter
     * @param mixed $filterValue
     */
    public function __construct(string $filterParameter, $filterValue)
    {
        $this->filterParameter = $filterParameter;
        $this->filterValue = $filterValue;
    }

    /**
     * @return mixed
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return mixed
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @return mixed
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @throws RepositoryException
     *
     * @return FilterQueryParser
     */
    public function parse(): self
    {
        $this->relation = $this->parseRelation($this->filterParameter);
        $this->column = $this->parseColumn($this->filterParameter);
        $this->expression = $this->parseExpression($this->filterValue);
        $this->dataType = $this->parseDataType($this->filterValue);
        $this->value = $this->parseValue($this->filterValue);

        return $this;
    }

    /**
     * @param $filterParameter
     *
     * @return string|null
     */
    protected function parseRelation($filterParameter): ?string
    {
        if (strpos($filterParameter, '.') !== false) {
            $lastDotPosition = strrpos($filterParameter, '.');

            return substr($filterParameter, 0, $lastDotPosition);
        }

        return null;
    }

    /**
     * @param $filterParameter
     *
     * @return string
     */
    protected function parseColumn($filterParameter): string
    {
        if (strpos($filterParameter, '.') !== false) {
            $lastDotPosition = strrpos($filterParameter, '.');

            return substr($filterParameter, $lastDotPosition + 1);
        }

        return $filterParameter;
    }

    /**
     * @param $filterValue
     *
     * @return string
     */
    protected function parseExpression($filterValue): string
    {
        if (! \is_array($filterValue)) {
            return config('repositories.filtering.default_expression', '$eq');
        }

        return key($filterValue);
    }

    /**
     * @param $filterValue
     *
     * @throws RepositoryException
     *
     * @return string
     */
    protected function parseDataType($filterValue): string
    {
        $value = $this->extractValue($filterValue);

        if (strpos($value, ':') !== false) {
            $lastColonPosition = strpos($value, ':');

            $parsedDataType = substr($value, 0, $lastColonPosition);

            if (! $this->isValidDataType($parsedDataType)) {
                return config('repositories.filtering.default_data_type', '$string');
            }

            return $parsedDataType;
        }

        return config('repositories.filtering.default_data_type', '$string');
    }

    /**
     * @param $filterValue
     *
     * @return string
     */
    protected function parseValue($filterValue): string
    {
        $value = $this->extractValue($filterValue);

        if (strpos($value, ':') !== false) {
            $lastColonPosition = strpos($value, ':');

            return substr($value, $lastColonPosition + 1);
        }

        return $value;
    }

    /**
     * @param $filterValue
     *
     * @return string
     */
    private function extractValue($filterValue): string
    {
        if (! \is_array($filterValue)) {
            return $filterValue;
        }

        return array_shift($filterValue);
    }

    /**
     * @param $dataType
     * @param bool $strict
     *
     * @throws RepositoryException
     *
     * @return bool
     */
    private function isValidDataType($dataType, $strict = false): bool
    {
        if (! in_array($dataType, config('repositories.filtering.allowed_data_types', '$string'), true)) {
            if ($strict) {
                throw new RepositoryException('Invalid/Unallowed data type passed.');
            }

            return false;
        }

        return true;
    }
}
