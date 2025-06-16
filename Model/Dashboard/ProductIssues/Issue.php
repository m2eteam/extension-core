<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\ProductIssues;

class Issue
{
    public int $tagId;
    public string $text;
    public int $total;
    public float $impactRate;

    public function __construct(
        int $tagId,
        string $text,
        int $total,
        float $impactRate
    ) {
        $this->tagId = $tagId;
        $this->text = $text;
        $this->total = $total;
        $this->impactRate = $impactRate;
    }

    public function toArray(): array
    {
        return [
            'tag_id' => $this->tagId,
            'text' => $this->text,
            'total' => $this->total,
            'impact_rate' => $this->impactRate,
        ];
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            $data['tag_id'],
            $data['text'],
            $data['total'],
            $data['impact_rate']
        );
    }
}
