<?php

declare(strict_types=1);

namespace M2E\Core\Model\Module\Environment;

class Adapter
{
    public const CONFIG_KEY_ENVIRONMENT = 'environment';

    public const ENVIRONMENT_PRODUCTION = 'production';
    public const ENVIRONMENT_DEVELOPMENT = 'development';

    private \M2E\Core\Model\Config\Adapter $config;

    public function __construct(\M2E\Core\Model\Config\Adapter $config)
    {
        $this->config = $config;
    }

    public function isProductionEnvironment(): bool
    {
        return empty($this->getEnvironment())
            || $this->getEnvironment() === self::ENVIRONMENT_PRODUCTION;
    }

    public function isDevelopmentEnvironment(): bool
    {
        return $this->getEnvironment() === self::ENVIRONMENT_DEVELOPMENT;
    }

    public function enableProductionEnvironment(): void
    {
        $this->setEnvironment(self::ENVIRONMENT_PRODUCTION);
    }

    public function enableDevelopmentEnvironment(): void
    {
        $this->setEnvironment(self::ENVIRONMENT_DEVELOPMENT);
    }

    /**
     * @param string $env
     *
     * @return void
     */
    private function setEnvironment(string $env): void
    {
        $this->config->set(\M2E\Core\Model\Module\Adapter::CONFIG_GROUP_ROOT, self::CONFIG_KEY_ENVIRONMENT, $env);
    }

    private function getEnvironment(): string
    {
        return (string)$this->config->get('/', 'environment');
    }
}
