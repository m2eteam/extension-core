<?php

declare(strict_types=1);

namespace M2E\Core\Model\Registry;

use M2E\Core\Model\ResourceModel\Registry as ResourceRegistry;

class Repository
{
    private \M2E\Core\Model\ResourceModel\Registry $resource;
    /** @var \M2E\Core\Model\ResourceModel\Registry\CollectionFactory */
    private ResourceRegistry\CollectionFactory $collectionFactory;

    public function __construct(
        \M2E\Core\Model\ResourceModel\Registry $resource,
        \M2E\Core\Model\ResourceModel\Registry\CollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
    }

    public function save(\M2E\Core\Model\Registry $registry): void
    {
        $this->resource->save($registry);
    }

    public function findByKey(string $extensionName, string $key): ?\M2E\Core\Model\Registry
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(ResourceRegistry::COLUMN_KEY, ['eq' => $key])
            ->addFieldToFilter(ResourceRegistry::COLUMN_EXTENSION_NAME, ['eq' => $extensionName]);

        $record = $collection->getFirstItem();
        if ($record->isObjectNew()) {
            return null;
        }

        return $record;
    }

    public function deleteValueByKey(string $extensionName, string $key): void
    {
        $this->resource
            ->getConnection()
            ->delete(
                $this->resource->getMainTable(),
                sprintf(
                    "`%s` = '$key' and `%s` = '$extensionName'",
                    ResourceRegistry::COLUMN_KEY,
                    ResourceRegistry::COLUMN_EXTENSION_NAME
                )
            );
    }

    public function removeAll(string $extensionName): void
    {
        $collection = $this->collectionFactory->create();
        $collection->getConnection()->delete(
            $collection->getMainTable(),
            [sprintf('%s = ?', ResourceRegistry::COLUMN_EXTENSION_NAME) => $extensionName],
        );
    }
}
