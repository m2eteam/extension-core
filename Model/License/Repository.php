<?php

declare(strict_types=1);

namespace M2E\Core\Model\License;

use M2E\Core\Model\ConfigManager;

class Repository
{
    public const CONFIG_LICENSE_GROUP = '/license/';
    public const CONFIG_LICENSE_KEY = 'key';

    public const CONFIG_LICENSE_INFO_DOMAIN_GROUP = '/license/domain/';
    public const CONFIG_LICENSE_INFO_IP_GROUP = '/license/ip/';
    public const CONFIG_LICENSE_INFO_EMAIL_GROUP = '/license/info/';

    private ConfigManager $configManager;

    public function __construct(
        \M2E\Core\Model\ConfigManager $configManager
    ) {
        $this->configManager = $configManager;
    }

    public function set(string $key): void
    {
        $this->configManager->set(self::CONFIG_LICENSE_GROUP, self::CONFIG_LICENSE_KEY, $key);
    }

    public function get(): \M2E\Core\Model\License
    {
        $key = $this->configManager->get(self::CONFIG_LICENSE_GROUP, self::CONFIG_LICENSE_KEY);
        $info = $this->getInfo();

        return new \M2E\Core\Model\License($key, $info);
    }

    private function getInfo(): Info
    {
        return new Info(
            (string)$this->configManager->get(self::CONFIG_LICENSE_INFO_EMAIL_GROUP, 'email'),
            $this->getIdentifier(self::CONFIG_LICENSE_INFO_DOMAIN_GROUP),
            $this->getIdentifier(self::CONFIG_LICENSE_INFO_IP_GROUP),
        );
    }

    private function getIdentifier(string $configGroup): Identifier
    {
        $isValid = $this->configManager->get($configGroup, 'is_valid');

        return new Identifier(
            (string)$this->configManager->get($configGroup, 'real'),
            (string)$this->configManager->get($configGroup, 'valid'),
            $isValid === null || (bool)$isValid,
        );
    }

    public function save(\M2E\Core\Model\License $license): void
    {
        $this->setInfo($license->getInfo());
        $this->set($license->getKey());
    }

    private function setInfo(Info $info): void
    {
        $this->configManager->set(self::CONFIG_LICENSE_INFO_EMAIL_GROUP, 'email', $info->getEmail());

        $this->setIdentifierInfo(self::CONFIG_LICENSE_INFO_DOMAIN_GROUP, $info->getDomainIdentifier());
        $this->setIdentifierInfo(self::CONFIG_LICENSE_INFO_IP_GROUP, $info->getIpIdentifier());
    }

    private function setIdentifierInfo(string $group, Identifier $identifier): void
    {
        $this->configManager->set($group, 'real', $identifier->getRealValue());
        $this->configManager->set($group, 'valid', $identifier->getValidValue());
        $this->configManager->set($group, 'is_valid', (int)$identifier->isValid());
    }
}
