<?php

declare(strict_types=1);

namespace M2E\Core\Model\Cron;

class TaskHandlerFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(TaskDefinition $taskDefinition): TaskHandlerInterface
    {
        $object = $this->objectManager->create($taskDefinition->getHandlerClass());
        if (!$object instanceof TaskHandlerInterface) {
            throw new \LogicException('Task handler object must implement TaskHandlerInterface');
        }

        return $object;
    }
}
