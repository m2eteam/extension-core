<?php

declare(strict_types=1);

namespace M2E\Core\Setup;

class UpgradeCollection extends \M2E\Core\Model\Setup\AbstractUpgradeCollection
{
    public function getMinAllowedVersion(): string
    {
        return '0.0.1';
    }

    protected function getSourceVersionUpgrades(): array
    {
        return [
            '0.0.1' => ['to' => '1.0.0', 'upgrade' => \M2E\Core\Setup\Upgrade\v1_0_0\Config::class],
            '1.0.0' => ['to' => '1.0.1', 'upgrade' => null],
            '1.0.1' => ['to' => '1.1.0', 'upgrade' => null],
            '1.1.0' => ['to' => '1.1.1', 'upgrade' => null],
            '1.1.1' => ['to' => '1.2.0', 'upgrade' => null],
            '1.2.0' => ['to' => '1.3.0', 'upgrade' => null],
            '1.3.0' => ['to' => '1.4.0', 'upgrade' => null],
            '1.4.0' => ['to' => '1.5.0', 'upgrade' => \M2E\Core\Setup\Upgrade\v1_5_0\Config::class],
            '1.5.0' => ['to' => '1.6.0', 'upgrade' => null],
            '1.6.0' => ['to' => '1.6.1', 'upgrade' => null],
            '1.6.1' => ['to' => '1.7.0', 'upgrade' => null],
            '1.7.0' => ['to' => '1.7.1', 'upgrade' => null],
        ];
    }
}
