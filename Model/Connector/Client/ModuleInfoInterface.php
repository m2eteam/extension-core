<?php

declare(strict_types=1);

namespace M2E\Core\Model\Connector\Client;

interface ModuleInfoInterface
{
    public function getName(): string;
    public function getVersion(): string;
}
