<?php

declare(strict_types=1);

namespace M2E\Core\Helper\Module\Database;

class Tables
{
    public const PREFIX = 'm2e_core_';

    public const TABLE_NAME_SETUP = self::PREFIX . 'setup';

    public const TABLE_NAME_CONFIG = self::PREFIX . 'config';
    public const TABLE_NAME_REGISTRY = self::PREFIX . 'registry';
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
}
