<?php

declare(strict_types=1);

namespace M2E\Core\Ui\AppliedFilters;

class Serializer
{
    /**
     * @param \M2E\Core\Ui\AppliedFilters\AppliedFilter[] $appliedFilters
     * @return string
     */
    public function encode(array $appliedFilters): string
    {
        $result = [];
        foreach ($appliedFilters as $filter) {
            $result[] = [
                'type' => $filter->type,
                'field' => $filter->field,
                'value' => $filter->value,
            ];
        }

        $jsonString = json_encode($result);

        return base64_encode($jsonString);
    }

    /**
     * @return \M2E\Core\Ui\AppliedFilters\AppliedFilter[]
     */
    public function decode(string $encodedString): array
    {
        try {
            $jsonString = base64_decode($encodedString, true);
            if ($jsonString === false) {
                return [];
            }

            $filtersData = json_decode($jsonString, true);
            $result = [];
            foreach ($filtersData as $fieldData) {
                $result[] = new AppliedFilter(
                    $fieldData['type'],
                    $fieldData['field'],
                    $fieldData['value']
                );
            }

            return $result;
        } catch (\Throwable $e) {
            return [];
        }
    }
}
