<?php

declare(strict_types=1);

namespace M2E\Core\Model\Connector;

interface ProtocolInterface
{
    public function getComponent(): string;

    public function getComponentVersion(): int;
}
