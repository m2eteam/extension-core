<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\Sales;

class Point
{
    public float $value;
    public \DateTime $date;

    public function __construct(float $value, \DateTime $date)
    {
        $this->value = $value;
        $this->date = $date;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'date' => $this->date->format('Y-m-d H:i:sP'),
        ];
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            (float)$data['value'],
            \M2E\Core\Helper\Date::createDateGmt($data['date'])
        );
    }
}
