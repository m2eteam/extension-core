<?php

declare(strict_types=1);

namespace M2E\Core\Setup\Update\y25_m04;

use M2E\Core\Helper\Module\Database\Tables as TablesHelper;
use M2E\Core\Model\ResourceModel\AttributeMapping\Pair as PairResource;
use Magento\Framework\DB\Ddl\Table;

class AddAttributeMappingTable extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $table = $this
            ->getConnection()
            ->newTable($this->getFullTableName(TablesHelper::TABLE_NAME_ATTRIBUTE_MAPPING));

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

        $this->getConnection()->createTable($table);
    }
}
