<?php

declare(strict_types=1);

namespace M2E\Core\Helper\Magento;

class Country
{
    /**
     * @psalm-suppress UndefinedClass
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    private \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory;

    /**
     * @psalm-suppress UndefinedClass
     */
    public function __construct(
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
    ) {
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    public function asOptions(): array
    {
        /**
         * @psalm-suppress UndefinedClass
         * @var \Magento\Directory\Model\ResourceModel\Country\Collection $collection
         */
        $collection = $this->countryCollectionFactory->create();

        return $collection->toOptionArray();
    }
}
