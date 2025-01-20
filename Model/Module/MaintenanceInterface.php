<?php

declare(strict_types=1);

namespace M2E\Core\Model\Module;

interface MaintenanceInterface
{
    public function isEnabled(): bool;
    public function isEnabledDueLowMagentoVersion(): bool;

    public function enable(): void;
    public function enableDueLowMagentoVersion(): void;

    public function disable(): void;
}
