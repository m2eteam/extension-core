<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Widget;

abstract class AbstractWidget extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    private ?string $widgetTitle;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        ?\Magento\Framework\Json\Helper\Data $jsonHelper = null,
        ?\Magento\Directory\Helper\Data $directoryHelper = null
    ) {
        $this->widgetTitle = $data['title'] ?? null;

        parent::__construct(
            $context,
            $data,
            $jsonHelper,
            $directoryHelper
        );
    }

    public function _construct(): void
    {
        parent::_construct();

        $this->setData('id', $this->getBlockId());
    }

    protected function getBlockId(): string
    {
        return '';
    }

    public function getWidgetTitle(): string
    {
        return $this->widgetTitle ?? $this->getDefaultTitle();
    }

    abstract protected function getDefaultTitle(): string;
}
