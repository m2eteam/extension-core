<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

interface MagentoCoreConfigSettingsInterface
{
    public function getConfigKeyPrefix(): string;
}
