<?php

declare(strict_types=1);

namespace M2E\Core\Setup\InstallHandler;

use M2E\Core\Helper\Module\Database\Tables as TablesHelper;
use M2E\Core\Model\Connector\Client\ConfigManager as ConfigManagerConnection;
use M2E\Core\Model\License\Repository;
use M2E\Core\Model\ResourceModel\Config as ConfigResource;
use M2E\Core\Model\ResourceModel\Registry as RegistryResource;
use Magento\Framework\DB\Ddl\Table;

class CoreHandler implements \M2E\Core\Model\Setup\InstallHandlerInterface
{
    private \M2E\Core\Helper\Module\Database\Tables $tablesHelper;
    private \M2E\Core\Model\Setup\Database\Modifier\ConfigFactory $modifierConfigFactory;

    public function __construct(
        \M2E\Core\Helper\Module\Database\Tables $tablesHelper,
        \M2E\Core\Model\Setup\Database\Modifier\ConfigFactory $modifierConfigFactory
    ) {
        $this->tablesHelper = $tablesHelper;
        $this->modifierConfigFactory = $modifierConfigFactory;
    }

    public function installSchema(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $this->installConfigTable($setup);
        $this->installRegistryTable($setup);
    }

    private function installConfigTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $tableName = $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_CONFIG);

        $table = $setup->getConnection()->newTable($tableName);

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

        $setup->getConnection()->createTable($table);
    }

    private function installRegistryTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $tableName = $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_REGISTRY);

        $table = $setup->getConnection()->newTable($tableName);

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

        $setup->getConnection()->createTable($table);
    }

    // ----------------------------------------

    public function installData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $config = $this->getConfigModifier($setup);

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

    private function getConfigModifier(
        \Magento\Framework\Setup\SetupInterface $setup
    ): \M2E\Core\Model\Setup\Database\Modifier\Config {
        return $this->modifierConfigFactory->create(
            \M2E\Core\Helper\Module::IDENTIFIER,
            $setup
        );
    }
}
