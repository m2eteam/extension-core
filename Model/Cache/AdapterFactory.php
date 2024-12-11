<?php

declare(strict_types=1);

namespace M2E\Core\Model\Cache;

class AdapterFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(string $extensionName): Adapter
    {
        return $this->objectManager->create(Adapter::class, ['extensionName' => $extensionName]);
    }
}
