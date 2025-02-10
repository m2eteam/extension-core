<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Widget;

class License extends AbstractWidget
{
    protected $_template = 'M2E_Core::control_panel/widget/license.phtml';

    private \M2E\Core\Model\LicenseService $licenseService;
    private \M2E\Core\Model\License $license;
    private \M2E\Core\Helper\Client $clientHelper;

    public function __construct(
        \M2E\Core\Helper\Client $clientHelper,
        \M2E\Core\Model\LicenseService $licenseService,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        ?\Magento\Framework\Json\Helper\Data $jsonHelper = null,
        ?\Magento\Directory\Helper\Data $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);

        $this->clientHelper = $clientHelper;
        $this->licenseService = $licenseService;
    }

    protected function getBlockId(): string
    {
        return 'controlPanelInfoLicense';
    }

    protected function getDefaultTitle(): string
    {
        return 'License';
    }

    public function getLicense(): \M2E\Core\Model\License
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->license)) {
            $this->license = $this->licenseService->get();
        }

        return $this->license;
    }

    public function getClientHelper(): \M2E\Core\Helper\Client
    {
        return $this->clientHelper;
    }
}
