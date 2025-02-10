<?php

declare(strict_types=1);

namespace M2E\Core\Helper\Module\Database;

class GeneralTables
{
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
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_SETUP => \M2E\Core\Model\ResourceModel\Setup::class,
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_CONFIG => \M2E\Core\Model\ResourceModel\Config::class,
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_REGISTRY => \M2E\Core\Model\ResourceModel\Registry::class,
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
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_SETUP => \M2E\Core\Model\Setup::class,
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_CONFIG => \M2E\Core\Model\Config::class,
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_REGISTRY => \M2E\Core\Model\Registry::class,
        ];
    }
}
