<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

class InstallerFactory
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

    /**
     * Module versions from setup_module magento table uses only by magento for run install or upgrade files.
     * We do not use these versions in setup & upgrade logic (only set correct values to it, using domain_setup table).
     * So version, that presented in $context parameter, is not used.
     */
    public function create(
        string $extensionName,
        \M2E\Core\Model\Setup\AbstractInstallHandlerCollection $installHandlersCollection,
        \M2E\Core\Model\Setup\InstallTablesListResolverInterface $tablesList,
        \Magento\Framework\Setup\SetupInterface $setup
    ): Installer {
        return $this->objectManager->create(
            Installer::class,
            [
                'extensionName' => $extensionName,
                'installHandlersCollection' => $installHandlersCollection,
                'installTablesListResolver' => $tablesList,
                'logger' => $this->loggerFactory->create(),
                'setup' => $setup
            ]
        );
    }
}
