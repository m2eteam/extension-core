<?php

declare(strict_types=1);

namespace M2E\Core\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    private \M2E\Core\Model\VariablesDir $variablesDir;

    private \M2E\Core\Model\Setup\UninstallFactory $uninstallFactory;
    private \M2E\Core\Model\ConfigManager $configManager;
    /** @var \M2E\Core\Setup\MagentoCoreConfigSettings */
    private MagentoCoreConfigSettings $magentoCoreConfigSettings;
    /** @var \M2E\Core\Setup\InstallTablesListResolver */
    private InstallTablesListResolver $installTablesListResolver;

    public function __construct(
        \M2E\Core\Model\Setup\UninstallFactory $uninstallFactory,
        \M2E\Core\Setup\InstallTablesListResolver $installTablesListResolver,
        \M2E\Core\Model\ConfigManager $configManager,
        \M2E\Core\Setup\MagentoCoreConfigSettings $magentoCoreConfigSettings,
        \M2E\Core\Model\VariablesDir $variablesDir
    ) {
        $this->variablesDir = $variablesDir;
        $this->uninstallFactory = $uninstallFactory;
        $this->configManager = $configManager;
        $this->magentoCoreConfigSettings = $magentoCoreConfigSettings;
        $this->installTablesListResolver = $installTablesListResolver;
    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $this->uninstallFactory
            ->create(
                \M2E\Core\Helper\Module::IDENTIFIER,
                $this->installTablesListResolver,
                $this->configManager->getAdapter(),
                $this->variablesDir->getAdapter(),
                $this->magentoCoreConfigSettings,
                $setup,
            )
            ->process();
    }
}
