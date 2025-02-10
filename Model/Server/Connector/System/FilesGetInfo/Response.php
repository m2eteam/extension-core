<?php

declare(strict_types=1);

namespace M2E\Core\Model\Server\Connector\System\FilesGetInfo;

class Response
{
    private array $filesOptions;

    public function __construct(array $filesOptions)
    {
        $this->filesOptions = $filesOptions;
    }

    public function getFilesOptions(): array
    {
        return $this->filesOptions;
    }
}
