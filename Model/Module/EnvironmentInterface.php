<?php

declare(strict_types=1);

namespace M2E\Core\Model\Module;

interface EnvironmentInterface
{
    public function isProductionEnvironment(): bool;

    public function isDevelopmentEnvironment(): bool;

    public function enableProductionEnvironment(): void;

    public function enableDevelopmentEnvironment(): void;
}
