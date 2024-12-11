<?php

declare(strict_types=1);

namespace M2E\Core\Model\Connector\Client;

class SingleFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(
        \M2E\Core\Model\Connector\ProtocolInterface $protocol,
        ConfigInterface $config,
        \M2E\Core\Model\Connector\Client\ModuleInfoInterface $moduleInfo
    ): Single {
        return $this->objectManager->create(
            Single::class,
            [
                'protocol' => $protocol,
                'config' => $config,
                'moduleInfo' => $moduleInfo,
            ]
        );
    }
}
