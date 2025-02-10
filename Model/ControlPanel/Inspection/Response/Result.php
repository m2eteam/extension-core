<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Inspection\Response;

class Result
{
    private bool $status;
    private ?string $errorMessage;
    /** @var \M2E\Core\Model\ControlPanel\Inspection\Issue[] */
    private array $issues;

    /**
     * @param bool $status
     * @param string|null $errorMessage
     * @param \M2E\Core\Model\ControlPanel\Inspection\Issue[] $issues
     */
    public function __construct(bool $status, ?string $errorMessage, array $issues)
    {
        $this->status = $status;
        $this->errorMessage = $errorMessage;
        $this->issues = $issues;
    }

    public function isSuccess(): bool
    {
        return $this->status;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @return \M2E\Core\Model\ControlPanel\Inspection\Issue[]
     */
    public function getIssues(): array
    {
        return $this->issues;
    }
}
