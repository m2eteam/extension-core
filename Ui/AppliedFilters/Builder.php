<?php

declare(strict_types=1);

namespace M2E\Core\Ui\AppliedFilters;

class Builder
{
    private array $filters = [];

    /**
     * Use for text input.
     */
    public function addSimpleFilter(string $field, string $value): self
    {
        $this->filters[$field] = new AppliedFilter(AppliedFilter::TYPE_SIMPLE, $field, $value);

        return $this;
    }

    /**
     * Use for select and multiselect filters.
     */
    public function addSelectFilter(string $field, array $value): self
    {
        $value = array_map(fn($item) => (string)$item, $value);

        $this->filters[$field] = new AppliedFilter(AppliedFilter::TYPE_SELECT, $field, $value);

        return $this;
    }

    /**
     * Use for range and date range filters.
     */
    public function addRangeFilter(string $field, ?int $from = null, ?int $to = null): self
    {
        $this->filters[$field] = new AppliedFilter(AppliedFilter::TYPE_RANGE, $field, [
            'from' => $from,
            'to' => $to,
        ]);

        return $this;
    }

    /**
     * @return \M2E\Core\Ui\AppliedFilters\AppliedFilter[]
     */
    public function build(): array
    {
        return array_values($this->filters);
    }
}
