<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Widget;

class Cron extends AbstractWidget
{
    protected $_template = 'M2E_Core::control_panel/widget/cron.phtml';

    private \M2E\Core\Model\ControlPanel\Widget\CronInfoInterface $cronInfo;

    public function _construct(): void
    {
        parent::_construct();

        $this->cronInfo = $this->getData('cron_info') ?? new \M2E\Core\Model\ControlPanel\Widget\CronInfo\Stub();
    }

    protected function getBlockId(): string
    {
        return 'controlPanelInspectionCron';
    }

    protected function getDefaultTitle(): string
    {
        return 'Cron';
    }

    // ----------------------------------------

    public function getRunnerType(): string
    {
        if ($this->cronInfo->isRunnerTypeDeveloper()) {
            return 'Developer';
        }

        if ($this->cronInfo->isRunnerTypeMagento()) {
            return 'Magento';
        }

        if ($this->cronInfo->isRunnerTypeServiceController()) {
            return 'Service Controller';
        }

        if ($this->cronInfo->isRunnerTypeServicePub()) {
            return 'Service Pub';
        }

        return 'N/A';
    }

    public function isRunnerTypeService(): bool
    {
        return $this->cronInfo->isRunnerTypeServiceController() || $this->cronInfo->isRunnerTypeServicePub();
    }

    public function getServiceAuthKey(): string
    {
        return $this->cronInfo->getServiceAuthKey();
    }

    public function isMagentoCronDisabled(): bool
    {
        return $this->cronInfo->isMagentoCronDisabled();
    }

    public function isCronNotWorking(): bool
    {
        return !$this->cronInfo->isCronWorking();
    }

    public function isControllerCronDisabled(): bool
    {
        return $this->cronInfo->isControllerCronDisabled();
    }

    public function isPubCronDisabled(): bool
    {
        return $this->cronInfo->isServicePubDisabled();
    }

    public function getCronLastRunTime(): ?string
    {
        $lastRun = $this->cronInfo->getCronLastRunTime();

        return $lastRun !== null ? $lastRun->format('Y-m-d H:i:s') : 'N/A';
    }
}
