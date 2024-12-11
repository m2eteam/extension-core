<?php

declare(strict_types=1);

namespace M2E\Core\Model;

use M2E\Core\Helper\Module\Database\Tables as ModuleTablesHelper;

class Module implements \M2E\Core\Model\ModuleInterface
{
    private bool $areImportantTablesExist;
    private \M2E\Core\Model\RegistryManager $registryManager;
    private \M2E\Core\Model\Module\AdapterFactory $adapterFactory;

    private \M2E\Core\Model\Module\Adapter $adapter;
    /** @var \M2E\Core\Model\ConfigManager */
    private ConfigManager $configManager;
    private \M2E\Core\Helper\Module\Database\Structure $moduleDatabaseHelper;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;

    public function __construct(
        \M2E\Core\Model\Module\AdapterFactory $adapterFactory,
        RegistryManager $registryManager,
        ConfigManager $configManager,
        \M2E\Core\Helper\Module\Database\Structure $moduleDatabaseHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->registryManager = $registryManager;
        $this->adapterFactory = $adapterFactory;
        $this->configManager = $configManager;
        $this->moduleDatabaseHelper = $moduleDatabaseHelper;
        $this->resourceConnection = $resourceConnection;
    }

    public function getName(): string
    {
        return 'core';
    }

    public function getPublicVersion(): string
    {
        return $this->getAdapter()->getPublicVersion();
    }

    public function getSetupVersion(): string
    {
        return $this->getAdapter()->getSetupVersion();
    }

    public function getSchemaVersion(): string
    {
        return $this->getAdapter()->getSchemaVersion();
    }

    public function getDataVersion(): string
    {
        return $this->getAdapter()->getDataVersion();
    }

    public function hasLatestVersion(): bool
    {
        return $this->getAdapter()->hasLatestVersion();
    }

    public function setLatestVersion(string $version): void
    {
        $this->getAdapter()->setLatestVersion($version);
    }

    public function getLatestVersion(): ?string
    {
        return $this->getAdapter()->getLatestVersion();
    }

    public function isDisabled(): bool
    {
        return false;
    }

    public function disable(): void
    {
        throw new \LogicException('Unable to change Core Extension mode.');
    }

    public function enable(): void
    {
        throw new \LogicException('Unable to change Core Extension mode.');
    }

    public function isReadyToWork(): bool
    {
        return $this->areImportantTablesExist();
    }

    public function areImportantTablesExist(): bool
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->areImportantTablesExist)) {
            return $this->areImportantTablesExist;
        }

        $result = true;
        foreach ([ModuleTablesHelper::TABLE_NAME_CONFIG, ModuleTablesHelper::TABLE_NAME_REGISTRY] as $table) {
            $tableName = $this->moduleDatabaseHelper->getTableNameWithPrefix($table);
            if (!$this->resourceConnection->getConnection()->isTableExists($tableName)) {
                $result = false;
                break;
            }
        }

        return $this->areImportantTablesExist = $result;
    }

    public function getAdapter(): \M2E\Core\Model\Module\Adapter
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->adapter)) {
            $this->adapter = $this->adapterFactory->create(
                \M2E\Core\Helper\Module::IDENTIFIER,
                $this->registryManager->getAdapter(),
                $this->configManager->getAdapter()
            );
        }

        return $this->adapter;
    }
}
