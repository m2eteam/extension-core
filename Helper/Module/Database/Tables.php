<?php

declare(strict_types=1);

namespace M2E\Core\Helper\Module\Database;

class Tables
{
    public const PREFIX = 'm2e_core_';

    public const TABLE_NAME_SETUP = self::PREFIX . 'setup';

    public const TABLE_NAME_CONFIG = self::PREFIX . 'config';
    public const TABLE_NAME_REGISTRY = self::PREFIX . 'registry';
    public const TABLE_NAME_ATTRIBUTE_MAPPING = self::PREFIX . 'attribute_mapping';

    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    /** @var \M2E\Core\Helper\Module\Database\Structure */
    private Structure $databaseHelper;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \M2E\Core\Helper\Module\Database\Structure $databaseHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->databaseHelper = $databaseHelper;
    }

    public function isExists(string $tableName): bool
    {
        return $this->resourceConnection
            ->getConnection()
            ->isTableExists($this->getFullName($tableName));
    }

    public function getFullName(string $tableName): string
    {
        return $this->databaseHelper->getTableNameWithPrefix($tableName);
    }

    public function renameTable(string $oldTable, string $newTable): bool
    {
        $oldTable = $this->getFullName($oldTable);
        $newTable = $this->getFullName($newTable);

        if (
            $this->resourceConnection->getConnection()->isTableExists($oldTable) &&
            !$this->resourceConnection->getConnection()->isTableExists($newTable)
        ) {
            $this->resourceConnection->getConnection()->renameTable(
                $oldTable,
                $newTable,
            );

            return true;
        }

        return false;
    }

    // ----------------------------------------

    public static function isOperationHistoryTable(string $tableName): bool
    {
        return strpos($tableName, 'operation_history') !== false;
    }

    // ----------------------------------------

    /**
     * @return string[]
     */
    public static function getAllTables(): array
    {
        return array_keys(self::getTablesResourcesModels());
    }

    public static function getTableResourceModel(string $tableName): string
    {
        $tablesModels = self::getTablesResourcesModels();

        return $tablesModels[$tableName];
    }

    private static function getTablesResourcesModels(): array
    {
        return [
            self::TABLE_NAME_SETUP => \M2E\Core\Model\ResourceModel\Setup::class,
            self::TABLE_NAME_CONFIG => \M2E\Core\Model\ResourceModel\Config::class,
            self::TABLE_NAME_REGISTRY => \M2E\Core\Model\ResourceModel\Registry::class,
            self::TABLE_NAME_ATTRIBUTE_MAPPING => \M2E\Core\Model\ResourceModel\AttributeMapping\Pair::class,
        ];
    }

    public static function getTableModel(string $tableName): string
    {
        $tablesModels = self::getTablesModels();

        return $tablesModels[$tableName];
    }

    public static function getTablesModels(): array
    {
        return [
            self::TABLE_NAME_SETUP => \M2E\Core\Model\Setup::class,
            self::TABLE_NAME_CONFIG => \M2E\Core\Model\Config::class,
            self::TABLE_NAME_REGISTRY => \M2E\Core\Model\Registry::class,
            self::TABLE_NAME_ATTRIBUTE_MAPPING => \M2E\Core\Model\AttributeMapping\Pair::class,
        ];
    }

    // ----------------------------------------

    public static function isModuleTable(string $tableName): bool
    {
        return strpos($tableName, self::PREFIX) !== false;
    }
}
