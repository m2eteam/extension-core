<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Inspection\Response;

class ResultFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param \M2E\Core\Model\ControlPanel\Inspection\Issue[] $issues
     *
     * @return Result
     */
    public function createSuccess(array $issues): Result
    {
        return $this->create(true, null, $issues);
    }

    public function createFailed(string $errorMessage): Result
    {
        return $this->create(false, $errorMessage);
    }

    /**
     * @param bool $status
     * @param string|null $errorMessage
     * @param \M2E\Core\Model\ControlPanel\Inspection\Issue[] $issues
     *
     * @return Result
     */
    private function create(bool $status, ?string $errorMessage, array $issues = []): Result
    {
        return $this->objectManager->create(
            Result::class,
            [
                'status' => $status,
                'errorMessage' => $errorMessage,
                'issues' => $issues,
            ]
        );
    }
}
