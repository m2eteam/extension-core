<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Cron;

interface TaskProviderInterface
{
    public function getExtensionModuleName(): string;

    /**
     * @return \M2E\Core\Model\ControlPanel\CronTask[]
     */
    public function getTasks(): array;
}
