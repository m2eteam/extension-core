<?php

declare(strict_types=1);

namespace M2E\Core\Model;

use M2E\Core\Model\ResourceModel\Registry as ResourceRegistry;

class Registry extends \M2E\Core\Model\ActiveRecord\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(\M2E\Core\Model\ResourceModel\Registry::class);
    }

    public function create(string $extensionName, string $key, string $value): self
    {
        $this
            ->setData(ResourceRegistry::COLUMN_EXTENSION_NAME, $extensionName)
            ->setData(ResourceRegistry::COLUMN_KEY, $key)
            ->setValue($value);

        return $this;
    }

    public function getExtensionName(): string
    {
        return (string)$this->getData(ResourceRegistry::COLUMN_EXTENSION_NAME);
    }

    public function getKey(): string
    {
        return $this->getData(ResourceRegistry::COLUMN_KEY);
    }

    public function setValue(string $value): Registry
    {
        return $this->setData(ResourceRegistry::COLUMN_VALUE, $value);
    }

    public function getValue(): ?string
    {
        return $this->getData(ResourceRegistry::COLUMN_VALUE);
    }
}
