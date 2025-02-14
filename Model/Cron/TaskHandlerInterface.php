<?php

declare(strict_types=1);

namespace M2E\Core\Model\Cron;

interface TaskHandlerInterface
{
    public function process($context): void;
}
