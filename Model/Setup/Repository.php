<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

use M2E\Core\Model\ResourceModel\Setup as SetupResource;
use Magento\Framework\DB\Ddl\Table;

class Repository
{
    private \M2E\Core\Model\ResourceModel\Setup $setupResource;
    private \M2E\Core\Model\ResourceModel\Setup\CollectionFactory $collectionFactory;
    private \M2E\Core\Helper\Module\Database\Structure $dbHelper;

    public function __construct(
        \M2E\Core\Model\ResourceModel\Setup $setupResource,
        \M2E\Core\Model\ResourceModel\Setup\CollectionFactory $collectionFactory,
        \M2E\Core\Helper\Module\Database\Structure $dbHelper
    ) {
        $this->setupResource = $setupResource;
        $this->collectionFactory = $collectionFactory;
        $this->dbHelper = $dbHelper;
    }

    public function create(string $extensionName, ?string $fromVersion, string $toVersion): \M2E\Core\Model\Setup
    {
        if (!$this->isSetupTableExists()) {
            $this->createTable();
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(SetupResource::COLUMN_EXTENSION_NAME, $extensionName);
        if ($fromVersion === null) {
            $collection->addFieldToFilter(SetupResource::COLUMN_VERSION_FROM, ['null' => true]);
        } else {
            $collection->addFieldToFilter(SetupResource::COLUMN_VERSION_FROM, $fromVersion);
        }

        $collection->addFieldToFilter(SetupResource::COLUMN_VERSION_TO, $toVersion);
        $collection->getSelect()
                   ->limit(1);

        $setupObject = $collection->getFirstItem();

        if ($setupObject->isObjectNew()) {
            $setupObject->create($extensionName, $fromVersion, $toVersion);

            $this->setupResource->save($setupObject);
        }

        return $setupObject;
    }

    public function save(\M2E\Core\Model\Setup $setup): void
    {
        if (!$this->isSetupTableExists()) {
            return;
        }

        $this->setupResource->save($setup);
    }

    public function findLastExecuted(string $extensionName): ?\M2E\Core\Model\Setup
    {
        if (!$this->isSetupTableExists()) {
            return null;
        }

        $collection = $this->collectionFactory->create();
        $collection
            ->addFieldToFilter(SetupResource::COLUMN_EXTENSION_NAME, $extensionName)
            ->addFieldToFilter(SetupResource::COLUMN_IS_COMPLETED, 1);

        /** @var \M2E\Core\Model\Setup $maxCompletedItem */
        $maxCompletedItem = null;
        foreach ($collection->getItems() as $completedItem) {
            if ($maxCompletedItem === null) {
                $maxCompletedItem = $completedItem;
                continue;
            }

            if (version_compare($maxCompletedItem->getVersionTo(), $completedItem->getVersionTo(), '>')) {
                continue;
            }

            $maxCompletedItem = $completedItem;
        }

        return $maxCompletedItem;
    }

    /**
     * @return \M2E\Core\Model\Setup[]
     */
    public function findNotCompletedUpgrades(string $extensionName): array
    {
        if (!$this->isSetupTableExists()) {
            return [];
        }

        $collection = $this->collectionFactory->create();
        $collection
            ->addFieldToFilter(SetupResource::COLUMN_EXTENSION_NAME, $extensionName)
            ->addFieldToFilter(SetupResource::COLUMN_VERSION_FROM, ['notnull' => true])
            ->addFieldToFilter(SetupResource::COLUMN_VERSION_TO, ['notnull' => true])
            ->addFieldToFilter(SetupResource::COLUMN_IS_COMPLETED, 0);

        return array_values($collection->getItems());
    }

    public function findLastUpgrade(string $extensionName): ?\M2E\Core\Model\Setup
    {
        if (!$this->isSetupTableExists()) {
            return null;
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(SetupResource::COLUMN_EXTENSION_NAME, $extensionName);

        $setupObject = $collection->getLastItem();
        if ($setupObject->isObjectNew()) {
            return null;
        }

        return $setupObject;
    }

    /**
     * @param string $extensionName
     *
     * @return \M2E\Core\Model\Setup[]
     */
    public function findAll(string $extensionName): array
    {
        if (!$this->isSetupTableExists()) {
            return [];
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(SetupResource::COLUMN_EXTENSION_NAME, $extensionName);

        return array_values($collection->getItems());
    }

    // ----------------------------------------

    public function removeAll(string $extensionName): void
    {
        if (!$this->isSetupTableExists()) {
            return;
        }

        $collection = $this->collectionFactory->create();
        $collection->getConnection()->delete(
            $collection->getMainTable(),
            [sprintf('%s = ?', SetupResource::COLUMN_EXTENSION_NAME) => $extensionName],
        );
    }

    // ----------------------------------------

    public function isAlreadyInstalled(string $extensionName): bool
    {
        if (!$this->isSetupTableExists()) {
            return false;
        }

        $collection = $this->collectionFactory->create();
        $collection
            ->addFieldToFilter(SetupResource::COLUMN_EXTENSION_NAME, $extensionName)
            ->addFieldToFilter(SetupResource::COLUMN_VERSION_FROM, ['null' => true])
            ->addFieldToFilter(SetupResource::COLUMN_IS_COMPLETED, 1);

        $item = $collection->getFirstItem();

        return !$item->isObjectNew();
    }

    public function createTable(): void
    {
        if ($this->isSetupTableExists()) {
            return;
        }

        $tableName = $this->dbHelper->getTableNameWithPrefix(
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_SETUP,
        );

        $setupTable = $this->setupResource
            ->getConnection()
            ->newTable($tableName)
            ->addColumn(
                SetupResource::COLUMN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false,
                    'auto_increment' => true,
                ],
            )
            ->addColumn(
                SetupResource::COLUMN_EXTENSION_NAME,
                Table::TYPE_TEXT,
                150,
            )
            ->addColumn(
                SetupResource::COLUMN_VERSION_FROM,
                Table::TYPE_TEXT,
                32,
                ['default' => null],
            )
            ->addColumn(
                SetupResource::COLUMN_VERSION_TO,
                Table::TYPE_TEXT,
                32,
                ['default' => null],
            )
            ->addColumn(
                SetupResource::COLUMN_IS_COMPLETED,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
            )
            ->addColumn(
                SetupResource::COLUMN_PROFILER_DATA,
                Table::TYPE_TEXT,
                SetupResource::LONG_COLUMN_SIZE,
                ['default' => null],
            )
            ->addColumn(
                SetupResource::COLUMN_UPDATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null],
            )
            ->addColumn(
                SetupResource::COLUMN_CREATE_DATE,
                Table::TYPE_DATETIME,
                null,
                ['default' => null],
            )
            ->addIndex('extension_name', SetupResource::COLUMN_EXTENSION_NAME)
            ->addIndex('version_from', SetupResource::COLUMN_VERSION_FROM)
            ->addIndex('version_to', SetupResource::COLUMN_VERSION_TO)
            ->addIndex('is_completed', SetupResource::COLUMN_IS_COMPLETED)
            ->setOption('type', 'INNODB')
            ->setOption('charset', 'utf8')
            ->setOption('collate', 'utf8_general_ci');

        $this->setupResource
            ->getConnection()
            ->createTable($setupTable);
    }

    private function isSetupTableExists(): bool
    {
        return $this->dbHelper->isTableExists(\M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_SETUP, true);
    }
}
