<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Widget;

interface CronInfoInterface
{
    // ----------------------------------------

    public function isCronWorking(): bool;

    public function getCronLastRunTime(): ?\DateTimeInterface;

    // ----------------------------------------

    public function isRunnerTypeMagento(): bool;

    public function isRunnerTypeDeveloper(): bool;

    public function isRunnerTypeServiceController(): bool;

    public function isRunnerTypeServicePub(): bool;

    // ----------------------------------------

    public function isMagentoCronDisabled(): bool;

    public function isControllerCronDisabled(): bool;
    public function isServicePubDisabled(): bool;

    // ----------------------------------------

    public function getServiceAuthKey(): string;
}
