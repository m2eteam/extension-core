<?php

declare(strict_types=1);

namespace M2E\Core\Model\Setup;

abstract class AbstractInstallHandlerCollection
{
    /** @var \M2E\Core\Model\Setup\InstallHandlerInterface[] */
    private array $handlers = [];

    public function __construct(array $handlers)
    {
        foreach ($handlers as $handler) {
            if (!$handler instanceof \M2E\Core\Model\Setup\InstallHandlerInterface) {
                throw new \LogicException('Install handler must implement InstallHandlerInterface');
            }

            $this->handlers[] = $handler;
        }
    }

    public function getAll(): array
    {
        return $this->handlers;
    }
}
