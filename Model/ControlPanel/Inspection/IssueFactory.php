<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Inspection;

class IssueFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $message
     * @param array|string|null $metadata
     *
     * @return \M2E\Core\Model\ControlPanel\Inspection\Issue
     */
    public function create(string $message, $metadata = null): \M2E\Core\Model\ControlPanel\Inspection\Issue
    {
        return $this->objectManager->create(
            \M2E\Core\Model\ControlPanel\Inspection\Issue::class,
            [
                'message' => $message,
                'metadata' => $metadata,
            ]
        );
    }
}
