<?php

declare(strict_types=1);

namespace M2E\Core\Model\Module;

class Adapter
{
    public const CONFIG_GROUP_ROOT = '/';
    public const CONFIG_KEY_DISABLED = 'is_disabled';

    private string $extensionIdentifier;
    private \M2E\Core\Model\Registry\Adapter $registry;
    private \Magento\Framework\Module\PackageInfo $packageInfo;
    private \Magento\Framework\Module\ModuleListInterface $moduleList;
    private \Magento\Framework\Module\ModuleResource $moduleResource;
    private \M2E\Core\Model\Config\Adapter $config;

    public function __construct(
        string $extensionIdentifier,
        \M2E\Core\Model\Registry\Adapter $registry,
        \M2E\Core\Model\Config\Adapter $config,
        \Magento\Framework\Module\PackageInfo $packageInfo,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\ModuleResource $moduleResource
    ) {
        $this->extensionIdentifier = $extensionIdentifier;
        $this->registry = $registry;
        $this->packageInfo = $packageInfo;
        $this->moduleList = $moduleList;
        $this->moduleResource = $moduleResource;
        $this->config = $config;
    }

    public function getPublicVersion(): string
    {
        return $this->packageInfo->getVersion($this->extensionIdentifier);
    }

    public function getSetupVersion(): string
    {
        return $this->moduleList->getOne($this->extensionIdentifier)['setup_version'];
    }

    public function getSchemaVersion(): string
    {
        return $this->moduleResource->getDbVersion($this->extensionIdentifier);
    }

    public function getDataVersion(): string
    {
        return $this->moduleResource->getDataVersion($this->extensionIdentifier);
    }

    public function hasLatestVersion(): bool
    {
        return (bool)$this->getLatestVersion();
    }

    public function setLatestVersion(string $version): void
    {
        $this->registry->set(
            '/module/latest_version/',
            $version
        );
    }

    public function getLatestVersion(): ?string
    {
        return $this->registry->get('/module/latest_version/');
    }

    public function isDisabled(): bool
    {
        return (bool)$this->config->get(self::CONFIG_GROUP_ROOT, self::CONFIG_KEY_DISABLED);
    }

    public function disable(): void
    {
        $this->config->set(self::CONFIG_GROUP_ROOT, self::CONFIG_KEY_DISABLED, 1);
    }

    public function enable(): void
    {
        $this->config->set(self::CONFIG_GROUP_ROOT, self::CONFIG_KEY_DISABLED, 0);
    }
}
