<?php

declare(strict_types=1);

namespace M2E\Core\Model\AttributeMapping;

use M2E\Core\Model\ResourceModel\AttributeMapping\Pair as PairResource;

class Repository
{
    private PairResource $resource;
    private \M2E\Core\Model\ResourceModel\AttributeMapping\Pair\CollectionFactory $collectionFactory;

    public function __construct(
        PairResource $resource,
        \M2E\Core\Model\ResourceModel\AttributeMapping\Pair\CollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
    }

    public function create(Pair $pair): void
    {
        $this->resource->save($pair);
    }

    public function save(Pair $pair): void
    {
        $this->resource->save($pair);
    }

    public function remove(Pair $pair): void
    {
        $this->resource->delete($pair);
    }

    public function removeByChannelCodes(string $extensionName, array $channelCodes): void
    {
        $connection = $this->resource->getConnection();

        $connection->delete(
            $this->resource->getMainTable(),
            [
                sprintf('%s = ?', PairResource::COLUMN_EXTENSION_NAME) => $extensionName,
                sprintf('%s IN (?)', PairResource::COLUMN_CHANNEL_ATTRIBUTE_CODE) => $channelCodes,
            ]
        );
    }

    /**
     * @param string $type
     * @param string $extensionName
     *
     * @return Pair[]
     */
    public function findByType(string $extensionName, string $type): array
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(PairResource::COLUMN_EXTENSION_NAME, $extensionName);
        $collection->addFieldToFilter(PairResource::COLUMN_TYPE, $type);

        return array_values($collection->getItems());
    }

    public function findByChannelCodeAndExtension(string $extensionName, string $channelCode): ?Pair
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(PairResource::COLUMN_EXTENSION_NAME, $extensionName);
        $collection->addFieldToFilter(PairResource::COLUMN_CHANNEL_ATTRIBUTE_CODE, $channelCode);

        $pair = $collection->getFirstItem();
        if ($pair->isObjectNew()) {
            return null;
        }

        return $pair;
    }
}
