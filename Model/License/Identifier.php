<?php

declare(strict_types=1);

namespace M2E\Core\Model\License;

class Identifier
{
    private string $realValue;
    private string $validValue;
    private bool $isValid;

    public function __construct(
        string $realValue,
        string $validValue,
        bool $isValid
    ) {
        $this->realValue = $realValue;
        $this->validValue = $validValue;
        $this->isValid = $isValid;
    }

    public function getRealValue(): string
    {
        return $this->realValue;
    }

    public function withRealValue(string $realValue): self
    {
        $new = clone $this;
        $new->realValue = $realValue;

        return $new;
    }

    public function getValidValue(): string
    {
        return $this->validValue;
    }

    public function withValidValue(string $validValue): self
    {
        $new = clone $this;
        $new->validValue = $validValue;

        return $new;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function withValid(bool $isValid): self
    {
        $new = clone $this;
        $new->isValid = $isValid;

        return $new;
    }
}
