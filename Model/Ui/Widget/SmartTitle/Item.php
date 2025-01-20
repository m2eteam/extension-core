<?php

declare(strict_types=1);

namespace M2E\Core\Model\Ui\Widget\SmartTitle;

class Item
{
    private int $id;
    private string $title;

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
