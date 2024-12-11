<?php

declare(strict_types=1);

namespace M2E\Core\Model\Module;

class AdapterFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(
        string $extensionIdentifier,
        \M2E\Core\Model\Registry\Adapter $registry,
        \M2E\Core\Model\Config\Adapter $config
    ): Adapter {
        return $this->objectManager->create(
            Adapter::class,
            [
                'extensionIdentifier' => $extensionIdentifier,
                'registry' => $registry,
                'config' => $config,
            ]
        );
    }
}
