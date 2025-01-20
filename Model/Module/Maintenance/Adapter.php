<?php

declare(strict_types=1);

namespace M2E\Core\Model\Module\Maintenance;

class Adapter
{
    private const VALUE_DISABLED = 0;
    private const VALUE_ENABLED = 1;
    private const VALUE_ENABLED_DUE_LOW_MAGENTO_VERSION = 2;

    private array $runtimeCache = [];

    private string $extensionConfigKey;
    private \M2E\Core\Helper\Module\Database\Structure $databaseHelper;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;

    public function __construct(
        string $extensionConfigKey,
        \M2E\Core\Helper\Module\Database\Structure $databaseHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->extensionConfigKey = $extensionConfigKey;
        $this->databaseHelper = $databaseHelper;
        $this->resourceConnection = $resourceConnection;
    }

    public function isEnabled(): bool
    {
        return (bool)$this->getConfig($this->extensionConfigKey);
    }

    public function isEnabledDueLowMagentoVersion(): bool
    {
        return (int)$this->getConfig($this->extensionConfigKey) === self::VALUE_ENABLED_DUE_LOW_MAGENTO_VERSION;
    }

    public function enable(): void
    {
        $this->setConfig($this->extensionConfigKey, self::VALUE_ENABLED);
    }

    public function enableDueLowMagentoVersion(): void
    {
        $this->setConfig($this->extensionConfigKey, self::VALUE_ENABLED_DUE_LOW_MAGENTO_VERSION);
    }

    public function disable(): void
    {
        $this->setConfig($this->extensionConfigKey, self::VALUE_DISABLED);
    }

    // ----------------------------------------

    private function getConfig(string $path)
    {
        if (isset($this->runtimeCache[$path])) {
            return $this->runtimeCache[$path];
        }

        $select = $this->resourceConnection->getConnection()
                                           ->select()
                                           ->from($this->getCoreConfigTableName(), 'value')
                                           ->where('scope = ?', 'default')
                                           ->where('scope_id = ?', 0)
                                           ->where('path = ?', $path);

        return $this->runtimeCache[$path] = $this->resourceConnection->getConnection()->fetchOne($select);
    }

    private function setConfig(string $path, $value): void
    {
        $connection = $this->resourceConnection->getConnection();

        if ($this->getConfig($path) === false) {
            $connection->insert(
                $this->getCoreConfigTableName(),
                [
                    'scope' => 'default',
                    'scope_id' => 0,
                    'path' => $path,
                    'value' => $value,
                ]
            );
        } else {
            $connection->update(
                $this->getCoreConfigTableName(),
                ['value' => $value],
                [
                    'scope = ?' => 'default',
                    'scope_id = ?' => 0,
                    'path = ?' => $path,
                ]
            );
        }

        unset($this->runtimeCache[$path]);
    }

    private function getCoreConfigTableName(): string
    {
        return $this->databaseHelper->getTableNameWithPrefix('core_config_data');
    }
}
