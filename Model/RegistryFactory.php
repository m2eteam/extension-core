<?php

declare(strict_types=1);

namespace M2E\Core\Model;

class RegistryFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createEmpty(): Registry
    {
        return $this->objectManager->create(Registry::class);
    }

    public function create(string $extensionName, string $key, $value): Registry
    {
        $model = $this->createEmpty();
        $model->create($extensionName, $key, $value);

        return $model;
    }
}
