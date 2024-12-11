<?php

declare(strict_types=1);

namespace M2E\Core\Model\Connector\Client;

class ConfigManager
{
    public const CONFIG_GROUP = '/server/';
    public const CONFIG_KEY_HOST = 'host';
    private \M2E\Core\Model\ConfigManager $configManager;

    public function __construct(\M2E\Core\Model\ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function setHost(string $host): void
    {
        $this->configManager->set(self::CONFIG_GROUP, self::CONFIG_KEY_HOST, $host);
    }

    public function getHost(): string
    {
        return (string)$this->configManager->get(self::CONFIG_GROUP, self::CONFIG_KEY_HOST);
    }
}
