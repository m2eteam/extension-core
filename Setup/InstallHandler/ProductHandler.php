<?php

declare(strict_types=1);

namespace M2E\Core\Setup\InstallHandler;

use M2E\Core\Helper\Module\Database\Tables as TablesHelper;
use M2E\Core\Model\ResourceModel\AttributeMapping\Pair as PairResource;
use Magento\Framework\DB\Ddl\Table;

class ProductHandler implements \M2E\Core\Model\Setup\InstallHandlerInterface
{
    private TablesHelper $tablesHelper;

    public function __construct(\M2E\Core\Helper\Module\Database\Tables $tablesHelper)
    {
        $this->tablesHelper = $tablesHelper;
    }

    public function installSchema(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $this->installAttributesMappingTable($setup);
    }

    private function installAttributesMappingTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $tableName = $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_ATTRIBUTE_MAPPING);
        $table = $setup->getConnection()->newTable($tableName);

        $table
            ->addColumn(
                PairResource::COLUMN_ID,
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
                PairResource::COLUMN_EXTENSION_NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                PairResource::COLUMN_TYPE,
                Table::TYPE_TEXT,
                100,
                ['nullable' => false]
            )
            ->addColumn(
                PairResource::COLUMN_CHANNEL_ATTRIBUTE_TITLE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                PairResource::COLUMN_CHANNEL_ATTRIBUTE_CODE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                PairResource::COLUMN_MAGENTO_ATTRIBUTE_CODE,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                PairResource::COLUMN_UPDATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addColumn(
                PairResource::COLUMN_CREATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null]
            )
            ->addIndex('extension_name', PairResource::COLUMN_EXTENSION_NAME)
            ->addIndex('type', PairResource::COLUMN_TYPE)
            ->addIndex('create_date', PairResource::COLUMN_CREATE_DATE)
            ->setOption('type', 'INNODB')
            ->setOption('charset', 'utf8')
            ->setOption('collate', 'utf8_general_ci')
            ->setOption('row_format', 'dynamic');

        $setup->getConnection()->createTable($table);
    }

    // ----------------------------------------

    public function installData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
    }
}
