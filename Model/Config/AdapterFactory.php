<?php

declare(strict_types=1);

namespace M2E\Core\Model\Config;

class AdapterFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(string $extensionName, \M2E\Core\Model\Cache\Adapter $cache): Adapter
    {
        return $this->objectManager->create(Adapter::class, ['extensionName' => $extensionName, 'cache' => $cache]);
    }
}
