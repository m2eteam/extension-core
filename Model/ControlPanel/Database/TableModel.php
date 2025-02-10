<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Database;

class TableModel
{
    private string $tableName;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private \M2E\Core\Helper\Module\Database\Structure $dbStructureHelper;
    private \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection;
    private \M2E\Core\Model\ControlPanel\ExtensionInterface $extension;

    public function __construct(
        string $tableName,
        \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection,
        \M2E\Core\Model\ControlPanel\ExtensionInterface $extension,
        \M2E\Core\Helper\Module\Database\Structure $dbStructureHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->dbStructureHelper = $dbStructureHelper;
        $this->tableName = $tableName;
        $this->collection = $collection;
        $this->extension = $extension;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getCollection(): \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
    {
        return $this->collection;
    }

    public function getColumns(): array
    {
        return $this->dbStructureHelper->getTableInfo($this->createModel()->getResource()->getMainTable()) ?? [];
    }

    /**
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function createModel(): \Magento\Framework\DataObject
    {
        return $this->getCollection()->getNewEmptyItem();
    }

    public function createEntry(array $data): void
    {
        $modelInstance = $this->createModel();

        $idFieldName = $modelInstance->getIdFieldName();
        $isIdAutoIncrement = $this->isIdColumnAutoIncrement();
        if ($isIdAutoIncrement) {
            unset($data[$idFieldName]);
        }

        // add module identifier
        if (
            !isset($data['extension_name'])
            && $this->isNeedModuleIdentifier()
        ) {
            $data['extension_name'] = $this->extension->getIdentifier();
        }

        $modelInstance->setData($data);

        $modelInstance->getResource()->save($modelInstance);
    }

    public function deleteEntries(array $ids): void
    {
        $modelInstance = $this->createModel();
        $collection = $modelInstance->getCollection();
        $collection->addFieldToFilter($modelInstance->getIdFieldName(), ['in' => $ids]);

        foreach ($collection as $item) {
            $item->getResource()->delete($item);
        }
    }

    public function updateEntries(array $ids, array $data): void
    {
        $modelInstance = $this->createModel();

        $collection = $modelInstance->getCollection();
        $collection->addFieldToFilter($modelInstance->getIdFieldName(), ['in' => $ids]);

        $idFieldName = $modelInstance->getIdFieldName();
        $isIdAutoIncrement = $this->isIdColumnAutoIncrement();
        if ($isIdAutoIncrement) {
            unset($data[$idFieldName]);
        }

        if (empty($data)) {
            return;
        }

        foreach ($collection->getItems() as $item) {
            /** @var \M2E\Core\Model\ActiveRecord\AbstractModel $item */
            foreach ($data as $field => $value) {
                if ($field === $idFieldName && !$isIdAutoIncrement) {
                    $this->resourceConnection->getConnection()->update(
                        $this->dbStructureHelper->getTableNameWithPrefix($this->tableName),
                        [$idFieldName => $value],
                        "`$idFieldName` = {$item->getId()}"
                    );
                }

                $item->setData($field, $value);
            }

            $item->getResource()->save($item);
        }
    }

    private function isIdColumnAutoIncrement(): bool
    {
        $list = [
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_SETUP => true,
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_CONFIG => true,
            \M2E\Core\Helper\Module\Database\Tables::TABLE_NAME_REGISTRY => true,
        ];

        if (isset($list[$this->tableName])) {
            return $list[$this->tableName];
        }

        /** @var \M2E\Core\Model\ActiveRecord\AbstractModel $resource */
        $columnId = $this->createModel()->getIdFieldName();

        return $this->dbStructureHelper->isIdColumnAutoIncrement($this->tableName, $columnId);
    }

    private function isNeedModuleIdentifier(): bool
    {
        return in_array($this->tableName, \M2E\Core\Helper\Module\Database\GeneralTables::getAllTables(), true);
    }
}
