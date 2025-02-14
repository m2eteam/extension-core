<?php

declare(strict_types=1);

namespace M2E\Core\Model\Cron\Task;

interface PossibleRunInterface
{
    public function isPossibleToRun(): bool;
}
