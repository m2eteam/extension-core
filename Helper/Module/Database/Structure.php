<?php

declare(strict_types=1);

namespace M2E\Core\Helper\Module\Database;

class Structure
{
    private array $runtimeCache = [];

    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private \M2E\Core\Helper\Magento $magentoHelper;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \M2E\Core\Helper\Magento $magentoHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->magentoHelper = $magentoHelper;
    }

    public function isTableExists(string $tableName, bool $force = false): bool
    {
        $cacheKey = __METHOD__ . $tableName;
        $cacheData = $this->getFromRuntimeCache($cacheKey);

        if (null !== $cacheData && !$force) {
            return $cacheData !== false;
        }

        $connection = $this->resourceConnection->getConnection();

        $databaseName = $this->magentoHelper->getDatabaseName();
        $tableName = $this->getTableNameWithPrefix($tableName);

        $result = $connection->query("SHOW TABLE STATUS FROM `$databaseName` WHERE `name` = '$tableName'")
                             ->fetch();

        $this->setToRuntimeCache($cacheKey, $result);

        return $result !== false;
    }

    public function getTableNameWithPrefix(string $tableName): string
    {
        return $this->resourceConnection->getTableName($tableName);
    }

    // ----------------------------------------

    private function setToRuntimeCache(string $key, $value): void
    {
        $this->runtimeCache[$key] = $value;
    }

    private function getFromRuntimeCache(string $key)
    {
        return $this->runtimeCache[$key] ?? null;
    }
}
