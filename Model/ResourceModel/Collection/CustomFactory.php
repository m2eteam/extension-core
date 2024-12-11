<?php

declare(strict_types=1);

namespace M2E\Core\Model\ResourceModel\Collection;

class CustomFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Custom
    {
        return $this->objectManager->create(Custom::class);
    }
}
