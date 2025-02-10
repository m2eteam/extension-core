<?php

declare(strict_types=1);

namespace M2E\Core\Model\Server\Connector\System;

class TablesGetDiffCommand implements \M2E\Core\Model\Connector\CommandInterface
{
    public const SEVERITY_CRITICAL = 'critical';
    public const SEVERITY_WARNING = 'warning';

    private array $tablesInfo;

    public function __construct(array $tablesInfo)
    {
        $this->tablesInfo = $tablesInfo;
    }

    public function getCommand(): array
    {
        return ['system', 'tables', 'getDiff'];
    }

    public function getRequestData(): array
    {
        return [
            'tables_info' => json_encode($this->tablesInfo),
        ];
    }

    public function parseResponse(
        \M2E\Core\Model\Connector\Response $response
    ): \M2E\Core\Model\Connector\Response {
        return $response;
    }
}
