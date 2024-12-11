<?php

declare(strict_types=1);

namespace M2E\Core\Setup\Update\y24_m11;

class AddConnectionConfig extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $configModifier = $this->getConfigModifier(\M2E\Core\Helper\Module::IDENTIFIER);
        $configModifier->insert(
            \M2E\Core\Model\Connector\Client\ConfigManager::CONFIG_GROUP,
            \M2E\Core\Model\Connector\Client\ConfigManager::CONFIG_KEY_HOST,
            'https://api.m2epro.com/'
        );
    }
}
