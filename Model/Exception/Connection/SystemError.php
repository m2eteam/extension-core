<?php

declare(strict_types=1);

namespace M2E\Core\Model\Exception\Connection;

class SystemError extends \M2E\Core\Model\Exception\Connection
{
    private \M2E\Core\Model\Connector\Response $response;

    public function __construct(
        string $message,
        \M2E\Core\Model\Connector\Response $response,
        array $additionalData = []
    ) {
        parent::__construct($message, $additionalData);
        $this->response = $response;
    }

    public function getResponse(): \M2E\Core\Model\Connector\Response
    {
        return $this->response;
    }
}
