<?php

declare(strict_types=1);

namespace M2E\Core\Model;

class License
{
    private ?string $key;
    /** @var \M2E\Core\Model\License\Info */
    private License\Info $info;

    public function __construct(
        ?string $key,
        License\Info $info
    ) {
        $this->key = $key;
        $this->info = $info;
    }

    public function hasKey(): bool
    {
        return !empty($this->key);
    }

    public function getKey(): string
    {
        return (string)$this->key;
    }

    public function getInfo(): License\Info
    {
        return $this->info;
    }

    public function withInfo(License\Info $info): self
    {
        $new = clone $this;
        $new->info = $info;

        return $new;
    }
}
