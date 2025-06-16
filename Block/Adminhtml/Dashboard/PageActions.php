<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Dashboard;

class PageActions extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    private \M2E\Core\Model\Dashboard\DashboardDefinitionInterface $dashboardDefinition;

    public function __construct(
        \M2E\Core\Model\Dashboard\DashboardDefinitionInterface $dashboardDefinition,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->dashboardDefinition = $dashboardDefinition;

        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $this->pageConfig->addPageAsset('M2E_Core::css/dashboard/page-actions.css');

        return parent::_prepareLayout();
    }

    protected function _toHtml()
    {
        $button = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class);

        $url =  $this->getLoginUrl() . '?source=' . $this->dashboardDefinition->getModuleName();
        $button->setData(
            [
                'label' => __('Go to Analytics'),
                'onclick' => sprintf("window.open('%s', '_blank')", $url),
                'class' => 'action-btn primary external-link',
            ]
        );

        $tooltipText = __('M2E Analytics offers easy tracking and analysis of sales
        and orders from multiple marketplaces, along with product performance and customer data monitoring.');

        $button->setAfterHtml('<div class="go_to_analytics_tooltip_content">' . $tooltipText . '</div>');

        return '<div style="display: inline-block; float: right; position: relative;">' . $button->toHtml() . '</div>';
    }

    private function getLoginUrl(): string
    {
        return 'https://analytics.m2e.cloud/login';
    }
}
