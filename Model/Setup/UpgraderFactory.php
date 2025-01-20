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
        \M2E\Core\Model\Setup\AbstractUpgradeCollection $upgradeCollection,
        \Magento\Framework\Setup\SetupInterface $setup,
        \M2E\Core\Model\Module\MaintenanceInterface $maintenance = null
    ): Upgrader {
        if ($maintenance === null) {
            $maintenance = new \M2E\Core\Model\Module\Maintenance\Stub();
        }

        return $this->objectManager->create(
            Upgrader::class,
            [
                'extensionName' => $extensionName,
                'upgradeCollection' => $upgradeCollection,
                'logger' => $this->loggerFactory->create(),
                'setup' => $setup,
                'maintenance' => $maintenance,
            ]
        );
    }
}
