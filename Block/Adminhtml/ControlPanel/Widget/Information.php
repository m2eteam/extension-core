<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Widget;

class Information extends AbstractWidget
{
    protected $_template = 'M2E_Core::control_panel/widget/information.phtml';

    private \M2E\Core\Model\Module\EnvironmentInterface $environmentModel;
    private bool $isMaintenanceMode;
    private \M2E\Core\Helper\Client $clientHelper;
    private \M2E\Core\Helper\Magento $magentoHelper;
    private \M2E\Core\Helper\Client\MemoryLimit $memoryLimit;
    private \M2E\Core\Model\ModuleInterface $module;
    private \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver;

    public function __construct(
        \M2E\Core\Helper\Client $clientHelper,
        \M2E\Core\Helper\Magento $magentoHelper,
        \M2E\Core\Helper\Client\MemoryLimit $memoryLimit,
        \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        ?\Magento\Framework\Json\Helper\Data $jsonHelper = null,
        ?\Magento\Directory\Helper\Data $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);

        $this->clientHelper = $clientHelper;
        $this->magentoHelper = $magentoHelper;
        $this->memoryLimit = $memoryLimit;
        $this->module = $currentExtensionResolver->get()->getModule();
        $this->currentExtensionResolver = $currentExtensionResolver;
    }

    public function _construct(): void
    {
        parent::_construct();
        $this->isMaintenanceMode = $this->getData('is_maintenance');
        $this->environmentModel = $this->getData('environment');
    }

    protected function getBlockId(): string
    {
        return 'controlPanelSummaryInfo';
    }

    protected function getDefaultTitle(): string
    {
        return 'Information';
    }

    public function getSystemName(): string
    {
        return \M2E\Core\Helper\Client::getSystem();
    }

    public function getMysqlVersion(): ?string
    {
        return $this->clientHelper->getMysqlVersion();
    }

    public function getMemoryLimit(): int
    {
        return $this->memoryLimit->get();
    }

    public function getEnvironment(): string
    {
        return $this->environmentModel->isProductionEnvironment()
            ? \M2E\Core\Model\Module\Environment\Adapter::ENVIRONMENT_PRODUCTION
            : \M2E\Core\Model\Module\Environment\Adapter::ENVIRONMENT_DEVELOPMENT;
    }

    public function getSystemTime(): string
    {
        return \M2E\Core\Helper\Date::createCurrentGmt()->format('Y-m-d H:i:s');
    }

    public function isSetupVersionActual(): bool
    {
        return $this->module->getSetupVersion() == $this->module->getSchemaVersion()
            && $this->module->getSetupVersion() == $this->module->getDataVersion();
    }

    public function getPhpVersion(): string
    {
        return \M2E\Core\Helper\Client::getPhpVersion();
    }

    public function getPhpApiName(): string
    {
        return \M2E\Core\Helper\Client::getPhpApiName();
    }

    public function getMaxExecutionTime(): int
    {
        return (int)ini_get('max_execution_time');
    }

    public function getDatabaseTablesPrefix(): string
    {
        $mySqlPrefix = $this->magentoHelper->getDatabaseTablesPrefix();

        return !empty($mySqlPrefix) ? $mySqlPrefix : (string)__('disabled');
    }

    public function getMagentoHelper(): \M2E\Core\Helper\Magento
    {
        return $this->magentoHelper;
    }

    public function getModule(): \M2E\Core\Model\ModuleInterface
    {
        return $this->currentExtensionResolver->get()->getModule();
    }

    public function isModuleMaintenanceMode(): bool
    {
        return $this->isMaintenanceMode;
    }
}
