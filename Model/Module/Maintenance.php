<?php

declare(strict_types=1);

namespace M2E\Core\Model\Module;

// Maintenance not work for Core
class Maintenance implements MaintenanceInterface
{
    public function isEnabled(): bool
    {
        return false;
    }

    public function isEnabledDueLowMagentoVersion(): bool
    {
        return false;
    }

    public function enable(): void
    {
    }

    public function enableDueLowMagentoVersion(): void
    {
    }

    public function disable(): void
    {
    }
}
