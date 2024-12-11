<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

class UpdateDefinition
{
    private string $fromVersion;
    private string $toVersion;
    private ?string $upgradeConfig;

    public function __construct(
        string $fromVersion,
        string $toVersion,
        ?string $upgradeConfig
    ) {
        $this->fromVersion = $fromVersion;
        $this->toVersion = $toVersion;
        $this->upgradeConfig = $upgradeConfig;
    }

    public function getFromVersion(): string
    {
        return $this->fromVersion;
    }

    public function getToVersion(): string
    {
        return $this->toVersion;
    }

    public function hasUpgradeConfig(): bool
    {
        return $this->upgradeConfig !== null;
    }

    public function getUpgradeConfig(): string
    {
        if (!$this->hasUpgradeConfig()) {
            throw new \M2E\Core\Model\Exception\Setup('Upgrade configuration not found');
        }

        return (string)$this->upgradeConfig;
    }
}
