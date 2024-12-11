<?php

declare(strict_types=1);

namespace M2E\Core\Model\Connector\Response;

class Processing
{
    private string $hash;

    public function __construct(string $hash)
    {
        $this->hash = $hash;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
