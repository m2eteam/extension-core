<?php

declare(strict_types=1);

namespace M2E\Core\Model;

class ConfigFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createEmpty(): Config
    {
        return $this->objectManager->create(Config::class);
    }

    public function create(string $extensionName, string $group, string $key, $value): Config
    {
        $model = $this->createEmpty();
        $model->create($extensionName, $group, $key, $value);

        return $model;
    }
}
