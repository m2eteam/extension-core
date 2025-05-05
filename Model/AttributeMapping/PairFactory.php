<?php

declare(strict_types=1);

namespace M2E\Core\Model\AttributeMapping;

class PairFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createEmpty(): Pair
    {
        return $this->objectManager->create(Pair::class);
    }

    public function create(
        string $extensionName,
        string $type,
        string $channelAttributeTitle,
        string $channelAttributeCode,
        string $magentoAttributeCode
    ): Pair {
        $pair = $this->createEmpty();
        $pair->create($extensionName, $type, $channelAttributeTitle, $channelAttributeCode, $magentoAttributeCode);

        return $pair;
    }
}
