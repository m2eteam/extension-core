<?php

declare(strict_types=1);

namespace M2E\Core\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class RecurringData implements InstallDataInterface
{
    private const MINIMUM_REQUIRED_MAGENTO_VERSION = '2.4.0';

    private \M2E\Core\Model\Setup\InstallChecker $installChecker;
    private \Magento\Framework\App\ProductMetadataInterface $productMetadata;
    private \M2E\Core\Model\Setup\InstallerFactory $installerFactory;
    /** @var \M2E\Core\Setup\InstallHandlerCollection */
    private InstallHandlerCollection $installHandlerCollection;
    /** @var \M2E\Core\Setup\InstallTablesListResolver */
    private InstallTablesListResolver $tablesList;
    private \M2E\Core\Model\Setup\UpgraderFactory $upgraderFactory;
    /** @var \M2E\Core\Setup\UpgradeCollection */
    private UpgradeCollection $upgradeCollection;

    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \M2E\Core\Model\Setup\InstallChecker $installChecker,
        \M2E\Core\Model\Setup\InstallerFactory $installerFactory,
        InstallHandlerCollection $installHandlerCollection,
        InstallTablesListResolver $tablesList,
        \M2E\Core\Model\Setup\UpgraderFactory $upgraderFactory,
        \M2E\Core\Setup\UpgradeCollection $upgradeCollection
    ) {
        $this->installChecker = $installChecker;
        $this->installerFactory = $installerFactory;
        $this->productMetadata = $productMetadata;
        $this->installHandlerCollection = $installHandlerCollection;
        $this->tablesList = $tablesList;
        $this->upgraderFactory = $upgraderFactory;
        $this->upgradeCollection = $upgradeCollection;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context): void
    {
        $this->checkMagentoVersion($this->productMetadata->getVersion());

        if (!$this->installChecker->isInstalled(\M2E\Core\Helper\Module::IDENTIFIER)) {
            $this->installerFactory
                ->create(
                    \M2E\Core\Helper\Module::IDENTIFIER,
                    $this->installHandlerCollection,
                    $this->tablesList,
                    $setup
                )
                ->install();

            return;
        }

        $this->upgraderFactory
            ->create(
                \M2E\Core\Helper\Module::IDENTIFIER,
                $this->upgradeCollection,
                $setup
            )
            ->upgrade();
    }

    private function checkMagentoVersion(string $magentoVersion): void
    {
        if (!version_compare($magentoVersion, self::MINIMUM_REQUIRED_MAGENTO_VERSION, '>=')) {
            $message = sprintf(
                'Magento version %s is not compatible with M2E Extension.',
                $magentoVersion,
            );

            $message .= ' Please upgrade your Magento first.';

            throw new \RuntimeException($message);
        }
    }
}
