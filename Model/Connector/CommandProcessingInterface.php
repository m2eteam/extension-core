<?php

declare(strict_types=1);

namespace M2E\Core\Model\Connector;

interface CommandProcessingInterface extends \M2E\Core\Model\Connector\CommandInterface
{
    public function parseResponse(
        \M2E\Core\Model\Connector\Response $response
    ): \M2E\Core\Model\Connector\Response\Processing;
}
