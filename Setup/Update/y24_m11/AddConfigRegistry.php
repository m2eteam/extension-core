<?php

declare(strict_types=1);

namespace M2E\Core\Setup\Update\y24_m11;

use M2E\Core\Helper\Module\Database\Tables as TablesHelper;
use M2E\Core\Model\ResourceModel\Config as ConfigResource;
use M2E\Core\Model\ResourceModel\Registry as RegistryResource;
use M2E\Core\Model\License\Repository;
use M2E\Core\Model\Connector\Client\ConfigManager as ConfigManagerConnection;
use Magento\Framework\DB\Ddl\Table;

class AddConfigRegistry extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $this->installConfigTable();
        $this->installConfigData();

        $this->installRegistryTable();
    }

    private function installConfigTable(): void
    {
        $tableName = $this->getFullTableName(TablesHelper::TABLE_NAME_CONFIG);

        $table = $this->getConnection()->newTable($tableName);

        $table
            ->addColumn(
                ConfigResource::COLUMN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false,
                    'auto_increment' => true,
                ]
            )
            ->addColumn(
                ConfigResource::COLUMN_EXTENSION_NAME,
                Table::TYPE_TEXT,
                255,
                ['default' => null]
            )
            ->addColumn(
                ConfigResource::COLUMN_GROUP,
                Table::TYPE_TEXT,
                255,
                ['default' => null]
            )
            ->addColumn(
                ConfigResource::COLUMN_KEY,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                ConfigResource::COLUMN_VALUE,
                Table::TYPE_TEXT,
                255,
                ['default' => null]
            )
            ->addColumn(
                ConfigResource::COLUMN_UPDATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addColumn(
                ConfigResource::COLUMN_CREATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addIndex('extension_name', ConfigResource::COLUMN_EXTENSION_NAME)
            ->addIndex('group', ConfigResource::COLUMN_GROUP)
            ->addIndex('key', ConfigResource::COLUMN_KEY)
            ->addIndex('value', ConfigResource::COLUMN_VALUE)
            ->setOption('type', 'INNODB')
            ->setOption('charset', 'utf8')
            ->setOption('collate', 'utf8_general_ci')
            ->setOption('row_format', 'dynamic');

        $this->getConnection()->createTable($table);
    }

    public function installConfigData(): void
    {
        $config = $this->getConfigModifier(\M2E\Core\Helper\Module::IDENTIFIER);

        $config->insert(Repository::CONFIG_LICENSE_GROUP, Repository::CONFIG_LICENSE_KEY);
        $config->insert(Repository::CONFIG_LICENSE_INFO_DOMAIN_GROUP, 'real');
        $config->insert(Repository::CONFIG_LICENSE_INFO_DOMAIN_GROUP, 'valid');
        $config->insert(Repository::CONFIG_LICENSE_INFO_DOMAIN_GROUP, 'is_valid');
        $config->insert(Repository::CONFIG_LICENSE_INFO_IP_GROUP, 'real');
        $config->insert(Repository::CONFIG_LICENSE_INFO_IP_GROUP, 'valid');
        $config->insert(Repository::CONFIG_LICENSE_INFO_IP_GROUP, 'is_valid');
        $config->insert(Repository::CONFIG_LICENSE_INFO_EMAIL_GROUP, 'email');

        $config->insert(
            ConfigManagerConnection::CONFIG_GROUP,
            ConfigManagerConnection::CONFIG_KEY_HOST,
            'https://api.m2epro.com/'
        );
    }

    private function installRegistryTable(): void
    {
        $tableName = $this->getFullTableName(TablesHelper::TABLE_NAME_REGISTRY);

        $table = $this->getConnection()->newTable($tableName);

        $table
            ->addColumn(
                RegistryResource::COLUMN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false,
                    'auto_increment' => true,
                ]
            )
            ->addColumn(
                ConfigResource::COLUMN_EXTENSION_NAME,
                Table::TYPE_TEXT,
                255,
                ['default' => null]
            )
            ->addColumn(
                RegistryResource::COLUMN_KEY,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                RegistryResource::COLUMN_VALUE,
                Table::TYPE_TEXT,
                \M2E\Core\Model\ResourceModel\Setup::LONG_COLUMN_SIZE,
                ['default' => null]
            )
            ->addColumn(
                RegistryResource::COLUMN_UPDATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addColumn(
                RegistryResource::COLUMN_CREATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addIndex('extension_name', RegistryResource::COLUMN_EXTENSION_NAME)
            ->addIndex('key', RegistryResource::COLUMN_KEY)
            ->setOption('type', 'INNODB')
            ->setOption('charset', 'utf8')
            ->setOption('collate', 'utf8_general_ci')
            ->setOption('row_format', 'dynamic');

        $this->getConnection()->createTable($table);
    }
}
