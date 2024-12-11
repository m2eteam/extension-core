<?php

declare(strict_types=1);

namespace M2E\Core\Setup\Upgrade\v1_0_0;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [
            \M2E\Core\Setup\Update\y24_m11\AddConfigRegistry::class,
            \M2E\Core\Setup\Update\y24_m11\AddConnectionConfig::class,
        ];
    }
}
