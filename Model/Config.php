<?php

declare(strict_types=1);

namespace M2E\Core\Model;

use M2E\Core\Model\ResourceModel\Config as ResourceConfig;

class Config extends \M2E\Core\Model\ActiveRecord\AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(\M2E\Core\Model\ResourceModel\Config::class);
    }

    public function create(string $extensionName, string $group, string $key, $value): self
    {
        $this
            ->setData(ResourceConfig::COLUMN_EXTENSION_NAME, $extensionName)
            ->setData(ResourceConfig::COLUMN_GROUP, $group)
            ->setData(ResourceConfig::COLUMN_KEY, $key)
            ->setValue($value);

        return $this;
    }

    public function getExtensionName(): string
    {
        return (string)$this->getData(ResourceConfig::COLUMN_EXTENSION_NAME);
    }

    public function getGroup(): string
    {
        return (string)$this->getData(ResourceConfig::COLUMN_GROUP);
    }

    public function getKey(): string
    {
        return (string)$this->getData(ResourceConfig::COLUMN_KEY);
    }

    public function setValue($value): self
    {
        $this->setData(ResourceConfig::COLUMN_VALUE, $value);

        return $this;
    }

    public function getValue()
    {
        return $this->getData(ResourceConfig::COLUMN_VALUE);
    }
}
