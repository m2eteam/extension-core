<?php

declare(strict_types=1);

namespace M2E\Core\Model\Connector\Client;

interface ConfigInterface
{
    public function getHost(): string;
    public function getConnectionTimeout(): int;
    public function getTimeout(): int;
    public function getApplicationKey(): string;
    public function getLicenseKey(): ?string;
}
