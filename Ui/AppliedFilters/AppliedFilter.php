<?php

declare(strict_types=1);

namespace M2E\Core\Ui\AppliedFilters;

class AppliedFilter
{
    public const TYPE_SIMPLE = 'simple';
    public const TYPE_SELECT = 'select';
    public const TYPE_RANGE = 'range';

    public string $type;
    public string $field;
    /** @var array|int|string */
    public $value;

    /**
     * @param string|int|array $value
     */
    public function __construct(string $type, string $field, $value)
    {
        $this->type = $type;
        $this->field = $field;
        $this->value = $value;
    }

    public function isTypeSelect(): bool
    {
        return $this->type === self::TYPE_SELECT;
    }

    public function isTypeSimple(): bool
    {
        return $this->type === self::TYPE_SIMPLE;
    }

    public function isTypeRange(): bool
    {
        return $this->type === self::TYPE_RANGE;
    }
}
