<?php

namespace Laravie\QueryFilter;

use Illuminate\Database\Query\Expression;

class Column
{
    /**
     * Based on maximum column name length.
     *
     * @var int
     */
    private const MAX_COLUMN_NAME_LENGTH = 64;

    /**
     * Column names are alphanumeric strings that can contain
     * underscores (`_`) but can't start with a number.
     *
     * @var string
     */
    private const VALID_COLUMN_NAME_REGEX = '/^(?![0-9])[A-Za-z0-9_-]*$/';

    /**
     * Field name.
     *
     * @var \Illuminate\Database\Query\Expression|string
     */
    protected $name;

    /**
     * Construct a new Column value object.
     *
     * @param  \Illuminate\Database\Query\Expression|string  $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Make a new Field value object.
     *
     * @param  static|\Illuminate\Database\Query\Expression|string  $name
     *
     * @return static
     */
    public static function make($name)
    {
        return new static($name);
    }

    /**
     * Get original value.
     *
     * @return \Illuminate\Database\Query\Expression|string
     */
    public function getOriginalValue()
    {
        return $this->name;
    }

    /**
     * Get expression value.
     */
    public function getValue(): string
    {
        return (string) $this->name;
    }

    /**
     * Is an instance of Illuminate\Database\Query\Expression.
     */
    public function isExpression(): bool
    {
        return $this->name instanceof Expression;
    }

    /**
     * Validate column.
     */
    public function validate(): bool
    {
        if ($this->isExpression()
            || (! empty($this->name) && static::validateColumnName($this->name))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Get if column is acceptable for filter.
     *
     * @param  array<int, string>  $only
     * @param  array<int, string>  $except
     */
    public function accepted(array $only = [], array $except = []): bool
    {
        if ((! empty($only) && ! \in_array($this->name, $only))
            || (! empty($except) && \in_array($this->name, $except))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Validate column name.
     */
    public static function validateColumnName(?string $column): bool
    {
        if (empty($column) || \strlen($column) > self::MAX_COLUMN_NAME_LENGTH) {
            return false;
        }

        if (! preg_match(self::VALID_COLUMN_NAME_REGEX, $column)) {
            return false;
        }

        return true;
    }
}
