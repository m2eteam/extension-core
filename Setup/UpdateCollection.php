<?php

declare(strict_types=1);

namespace M2E\Core\Setup;

class UpdateCollection extends \M2E\Core\Model\Setup\AbstractUpdateCollection
{
    public function getMinAllowedVersion(): string
    {
        return '0.0.1';
    }

    protected function getSourceVersionUpgrades(): array
    {
        return [
            '0.0.1' => [
                'to' => '1.0.0',
                'upgrade' => \M2E\Core\Setup\Upgrade\v1_0_0\Config::class,
            ],
        ];
    }
}
