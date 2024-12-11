<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

class UninstallFactory
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
        \M2E\Core\Model\Setup\InstallTablesListResolverInterface $installTablesListResolver,
        \M2E\Core\Model\Config\Adapter $config,
        \M2E\Core\Model\VariablesDir\Adapter $variablesDir,
        \M2E\Core\Model\Setup\MagentoCoreConfigSettingsInterface $magentoCoreConfigSettings,
        \Magento\Framework\Setup\SchemaSetupInterface $setup
    ): Uninstall {
        return $this->objectManager->create(
            Uninstall::class,
            [
                'extensionName' => $extensionName,
                'installTablesListResolver' => $installTablesListResolver,
                'magentoCoreConfigSettings' => $magentoCoreConfigSettings,
                'config' => $config,
                'variablesDir' => $variablesDir,
                'logger' => $this->loggerFactory->create(),
            ]
        );
    }
}
