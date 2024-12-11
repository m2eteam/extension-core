<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

class UpgraderFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;
    /** @var \M2E\Core\Model\Setup\LoggerFactory */
    private LoggerFactory $loggerFactory;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        LoggerFactory $loggerFactory
    ) {
        $this->objectManager = $objectManager;
        $this->loggerFactory = $loggerFactory;
    }

    public function create(
        string $extensionName,
        \M2E\Core\Model\Setup\AbstractUpdateCollection $updateCollection,
        \Magento\Framework\Setup\SetupInterface $setup
    ): Upgrader {
        return $this->objectManager->create(
            Upgrader::class,
            [
                'extensionName' => $extensionName,
                'updateCollection' => $updateCollection,
                'logger' => $this->loggerFactory->create(),
                'setup' => $setup,
            ]
        );
    }
}
