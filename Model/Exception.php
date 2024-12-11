<?php

declare(strict_types=1);

namespace M2E\Core\Model;

class Exception extends \Exception
{
    private array $additionalData;

    public function __construct(
        string $message,
        array $additionalData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        $this->additionalData = $additionalData;

        parent::__construct($message, $code, $previous);
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }
}
