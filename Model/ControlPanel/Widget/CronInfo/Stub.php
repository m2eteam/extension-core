<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Widget\CronInfo;

class Stub implements \M2E\Core\Model\ControlPanel\Widget\CronInfoInterface
{
    public function isMagentoCronDisabled(): bool
    {
        return true;
    }

    public function isCronWorking(): bool
    {
        return false;
    }

    public function getCronLastRunTime(): ?\DateTimeInterface
    {
        return null;
    }

    public function isRunnerTypeMagento(): bool
    {
        return false;
    }

    public function isRunnerTypeDeveloper(): bool
    {
        return false;
    }

    public function isRunnerTypeServiceController(): bool
    {
        return false;
    }

    public function isRunnerTypeServicePub(): bool
    {
        return false;
    }

    public function isControllerCronDisabled(): bool
    {
        return false;
    }

    public function isServicePubDisabled(): bool
    {
        return false;
    }

    public function getServiceAuthKey(): string
    {
        return '';
    }
}
