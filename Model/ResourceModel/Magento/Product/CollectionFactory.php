<?php

declare(strict_types=1);

namespace M2E\Core\Model\ResourceModel\Magento\Product;

use M2E\Core\Model\ResourceModel\MSI\Magento\Product\Collection as MSICollection;

class CollectionFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;
    private \M2E\Core\Helper\Magento $magentoHelper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \M2E\Core\Helper\Magento $magentoHelper
    ) {
        $this->objectManager = $objectManager;
        $this->magentoHelper = $magentoHelper;
    }

    public function create(array $data = []): Collection
    {
        return $this->magentoHelper->isMSISupportingVersion()
            ? $this->objectManager->create(MSICollection::class, $data)
            : $this->objectManager->create(Collection::class, $data);
    }
}
