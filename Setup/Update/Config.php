<?php

declare(strict_types=1);

namespace M2E\Core\Setup\Update;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [
            'y24_m11' => [
                \M2E\Core\Setup\Update\y24_m11\AddConfigRegistry::class,
                \M2E\Core\Setup\Update\y24_m11\AddConnectionConfig::class,
            ]
        ];
    }
}
