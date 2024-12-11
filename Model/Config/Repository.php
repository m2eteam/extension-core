<?php

declare(strict_types=1);

namespace M2E\Core\Model\Config;

use M2E\Core\Model\ResourceModel\Config as ResourceConfig;

class Repository
{
    private \M2E\Core\Model\ResourceModel\Config\CollectionFactory $collectionFactory;
    /** @var \M2E\Core\Model\ResourceModel\Config */
    private ResourceConfig $resource;

    public function __construct(
        \M2E\Core\Model\ResourceModel\Config $resourceConfig,
        \M2E\Core\Model\ResourceModel\Config\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->resource = $resourceConfig;
    }

    public function create(\M2E\Core\Model\Config $config): void
    {
        $this->resource->save($config);
    }

    public function save(\M2E\Core\Model\Config $config): void
    {
        $this->resource->save($config);
    }

    /**
     * @return \M2E\Core\Model\Config[]
     */
    public function getAllByExtension(string $extensionName): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(ResourceConfig::COLUMN_EXTENSION_NAME, ['eq' => $extensionName]);

        return array_values($collection->getItems());
    }

    public function findByGroupAndKey(string $extensionName, string $group, string $key): ?\M2E\Core\Model\Config
    {
        $collection = $this->collectionFactory->create();

        $collection
            ->addFieldToFilter(ResourceConfig::COLUMN_EXTENSION_NAME, ['eq' => $extensionName])
            ->addFieldToFilter(ResourceConfig::COLUMN_GROUP, $group)
            ->addFieldToFilter(ResourceConfig::COLUMN_KEY, $key);

        $config = $collection->getFirstItem();
        if ($config->isObjectNew()) {
            return null;
        }

        return $config;
    }

    // ----------------------------------------

    public function removeAll(string $extensionName): void
    {
        $collection = $this->collectionFactory->create();
        $collection->getConnection()->delete(
            $collection->getMainTable(),
            [sprintf('%s = ?', ResourceConfig::COLUMN_EXTENSION_NAME) => $extensionName],
        );
    }
}
