<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Widget;

class VersionInfo extends AbstractWidget
{
    protected $_template = 'M2E_Core::control_panel/widget/versionInfo.phtml';

    private \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver;

    public function __construct(
        \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        ?\Magento\Framework\Json\Helper\Data $jsonHelper = null,
        ?\Magento\Directory\Helper\Data $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);

        $this->currentExtensionResolver = $currentExtensionResolver;
    }

    protected function getBlockId(): string
    {
        return 'controlPanelInspectionVersionInfo';
    }

    protected function getDefaultTitle(): string
    {
        return 'Version Info';
    }

    public function getLatestPublicVersion(): ?string
    {
        $module = $this->currentExtensionResolver->get()->getModule();

        return $module->hasLatestVersion() ? $module->getLatestVersion() : null;
    }
}
