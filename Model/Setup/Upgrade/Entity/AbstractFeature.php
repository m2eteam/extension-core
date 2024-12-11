<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup\Upgrade\Entity;

use M2E\Core\Model\Setup\Database\Modifier\Table;
use M2E\Core\Model\Setup\Database\Modifier\Config;

abstract class AbstractFeature
{
    private \Magento\Framework\Module\Setup $installer;
    private \M2E\Core\Model\Setup\Database\Modifier\TableFactory $modifierTableFactory;
    private \M2E\Core\Model\Setup\Database\Modifier\ConfigFactory $modifierConfigFactory;
    private \M2E\Core\Helper\Module\Database\Tables $tablesHelper;

    public function __construct(
        \M2E\Core\Model\Setup\Database\Modifier\ConfigFactory $modifierConfigFactory,
        \M2E\Core\Model\Setup\Database\Modifier\TableFactory $modifierTableFactory,
        \M2E\Core\Helper\Module\Database\Tables $tablesHelper,
        \Magento\Framework\Module\Setup $installer
    ) {
        $this->installer = $installer;
        $this->modifierTableFactory = $modifierTableFactory;
        $this->modifierConfigFactory = $modifierConfigFactory;
        $this->tablesHelper = $tablesHelper;
    }

    // ----------------------------------------

    abstract public function execute(): void;

    // ----------------------------------------

    protected function createTableModifier(string $tableName): Table
    {
        return $this->modifierTableFactory->create(
            $tableName,
            $this->installer
        );
    }

    protected function getConfigModifier(string $extensionName): Config
    {
        return $this->modifierConfigFactory->create($extensionName, $this->installer);
    }

    // ----------------------------------------

    protected function getConnection(): \Magento\Framework\DB\Adapter\AdapterInterface
    {
        return $this->installer->getConnection();
    }

    protected function getFullTableName(string $tableName): string
    {
        return $this->tablesHelper->getFullName($tableName);
    }

    // ----------------------------------------

    protected function renameTable(string $oldTable, string $newTable): bool
    {
        return $this->tablesHelper->renameTable($oldTable, $newTable);
    }
}
