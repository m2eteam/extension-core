<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Widget;

class Location extends AbstractWidget
{
    protected $_template = 'M2E_Core::control_panel/widget/location.phtml';

    private \M2E\Core\Helper\Module $moduleHelper;
    private \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver;
    private \M2E\Core\Helper\Client $clientHelper;

    public function __construct(
        \M2E\Core\Helper\Client $clientHelper,
        \M2E\Core\Helper\Module $moduleHelper,
        \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        ?\Magento\Framework\Json\Helper\Data $jsonHelper = null,
        ?\Magento\Directory\Helper\Data $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);

        $this->clientHelper = $clientHelper;
        $this->moduleHelper = $moduleHelper;
        $this->currentExtensionResolver = $currentExtensionResolver;
    }

    protected function getBlockId(): string
    {
        return 'controlPanelInfoLocation';
    }

    protected function getDefaultTitle(): string
    {
        return 'Location';
    }

    public function getBaseDirectory(): string
    {
        return $this->clientHelper->getBaseDirectory();
    }

    public function getRelativeDirectory(): string
    {
        $extension = $this->currentExtensionResolver->get();

        return $this->moduleHelper->getBaseRelativeDirectory($extension->getIdentifier());
    }
}
