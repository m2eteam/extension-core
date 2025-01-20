<?php

declare(strict_types=1);

namespace M2E\Core\Helper\Module\Database;

class Structure
{
    private array $runtimeCache = [];
    private array $tablesInfo = [];
    private array $tablesDataLength = [];
    private array $tablesCountOfRecords = [];
    private array $tablesStatus = [];

    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private \M2E\Core\Helper\Magento $magentoHelper;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \M2E\Core\Helper\Magento $magentoHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->magentoHelper = $magentoHelper;
    }

    public function isTableReady(string $tableName): bool
    {
        return $this->isTableExists($tableName)
            && $this->isTableStatusOk($tableName);
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

    public function isTableStatusOk(string $tableName): bool
    {
        if (isset($this->tablesStatus[$tableName])) {
            return $this->tablesStatus[$tableName];
        }

        $connection = $this->resourceConnection->getConnection();

        if (!$this->isTableExists($tableName)) {
            throw new \LogicException("Table '$tableName' is not exists.");
        }

        $result = true;

        try {
            $tableNameWithPrefix = $this->getTableNameWithPrefix($tableName);
            $connection->select()
                       ->from($tableNameWithPrefix, new \Zend_Db_Expr('1'))
                       ->limit(1)
                       ->query();
        } catch (\Throwable $e) {
            $result = false;
        }

        return $this->tablesStatus[$tableName] = $result;
    }

    public function getTableInfo(string $tableName): ?array
    {
        if (array_key_exists($tableName, $this->tablesInfo)) {
            return $this->tablesInfo[$tableName];
        }

        if (!$this->isTableExists($this->getTableNameWithoutPrefix($tableName))) {
            return null;
        }

        $moduleTableName = $this->getTableNameWithPrefix($tableName);

        $stmtQuery = $this->resourceConnection->getConnection()->query(
            "SHOW COLUMNS FROM $moduleTableName"
        );

        $result = [];
        while ($row = $stmtQuery->fetch()) {
            $result[strtolower($row['Field'])] = [
                'name' => strtolower($row['Field']),
                'type' => strtolower($row['Type']),
                'null' => strtolower($row['Null']),
                'key' => strtolower($row['Key']),
                'default' => strtolower($row['Default'] ?? ''),
                'extra' => strtolower($row['Extra']),
            ];
        }

        return $this->tablesInfo[$tableName] = $result;
    }

    public function getColumnInfo(string $table, string $columnName): ?array
    {
        $info = $this->getTableInfo($table);

        return $info[$columnName] ?? null;
    }

    public function getDataLengthInMB(string $tableName): float
    {
        if (isset($this->tablesDataLength[$tableName])) {
            return $this->tablesDataLength[$tableName];
        }

        $connection = $this->resourceConnection->getConnection();

        $databaseName = $this->magentoHelper->getDatabaseName();
        $tableNameWithPrefix = $this->getTableNameWithPrefix($tableName);

        $dataLength = $connection->select()
                                 ->from('information_schema.tables', [new \Zend_Db_Expr('data_length + index_length')])
                                 ->where('`table_name` = ?', $tableNameWithPrefix)
                                 ->where('`table_schema` = ?', $databaseName)
                                 ->query()
                                 ->fetchColumn();

        $result = round(((float)$dataLength) / 1024 / 1024, 2); // MB

        return $this->tablesDataLength[$tableName] = $result;
    }

    public function getCountOfRecords(string $tableName): int
    {
        if (isset($this->tablesCountOfRecords[$tableName])) {
            return $this->tablesCountOfRecords[$tableName];
        }

        $connection = $this->resourceConnection->getConnection();
        $tableNameWithPrefix = $this->getTableNameWithPrefix($tableName);

        $result = $connection->select()
                             ->from($tableNameWithPrefix, new \Zend_Db_Expr('COUNT(*)'))
                             ->query()
                             ->fetchColumn();

        return $this->tablesCountOfRecords[$tableName] = (int)$result;
    }

    // ----------------------------------------

    public function getTableNameWithPrefix(string $tableName): string
    {
        return $this->resourceConnection->getTableName($tableName);
    }

    public function getTableNameWithoutPrefix(string $tableName): string
    {
        return str_replace(
            $this->magentoHelper->getDatabaseTablesPrefix(),
            '',
            $this->getTableNameWithPrefix($tableName)
        );
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
