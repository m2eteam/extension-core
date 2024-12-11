<?php

declare(strict_types=1);

namespace M2E\Core\Model;

use M2E\Core\Model\ResourceModel\Setup as SetupResource;

class Setup extends \M2E\Core\Model\ActiveRecord\AbstractModel
{
    public function _construct(): void
    {
        parent::_construct();
        $this->_init(\M2E\Core\Model\ResourceModel\Setup::class);
    }

    public function create(string $extensionName, ?string $fromVersion, string $toVersion): self
    {
        $this
            ->setData(SetupResource::COLUMN_EXTENSION_NAME, $extensionName)
            ->setData(SetupResource::COLUMN_VERSION_FROM, $fromVersion)
            ->setData(SetupResource::COLUMN_VERSION_TO, $toVersion)
            ->setData(SetupResource::COLUMN_IS_COMPLETED, 0);

        return $this;
    }

    public function getExtensionName(): string
    {
        return (string)$this->getData(SetupResource::COLUMN_EXTENSION_NAME);
    }

    public function getVersionFrom(): string
    {
        return (string)$this->getData(SetupResource::COLUMN_VERSION_FROM);
    }

    public function getVersionTo(): string
    {
        return $this->getData(SetupResource::COLUMN_VERSION_TO);
    }

    public function isCompleted(): bool
    {
        return (bool)$this->getData(SetupResource::COLUMN_IS_COMPLETED);
    }

    public function markAsCompleted(): self
    {
        $this->setData(SetupResource::COLUMN_IS_COMPLETED, 1);

        return $this;
    }

    public function setProfilerData(string $data): self
    {
        $this->setData(SetupResource::COLUMN_PROFILER_DATA, $data);

        return $this;
    }

    public function getCreateDate(): \DateTime
    {
        $value = $this->getData(SetupResource::COLUMN_CREATE_DATE);

        return \M2E\Core\Helper\Date::createDateGmt($value);
    }
}
