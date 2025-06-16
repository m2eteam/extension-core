<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Dashboard;

class Dashboard extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    protected $_template = 'M2E_Core::dashboard/dashboard.phtml';

    private \M2E\Core\Block\Adminhtml\Dashboard\CurrentDashboardResolver $currentDashboardResolver;

    public function __construct(
        \M2E\Core\Block\Adminhtml\Dashboard\CurrentDashboardResolver $currentDashboardResolver,
        \Magento\Backend\Block\Template\Context                      $context,
        array                                                        $data = []
    ) {
        $this->currentDashboardResolver = $currentDashboardResolver;

        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $dashboardDefinition = $this->currentDashboardResolver->get();

        $this->pageConfig->addPageAsset('M2E_Core::css/dashboard/view.css');
        $this->getLayout()->createBlock(
            \M2E\Core\Block\Adminhtml\Dashboard\PageActions::class,
            'm2e_dashboard_page_actions',
            [
                'dashboardDefinition' => $dashboardDefinition
            ]
        );

        $this->getLayout()->setChild(
            'page.main.actions',
            'm2e_dashboard_page_actions',
            'm2e_dashboard_page_actions'
        );

        $shipmentsBlock = $this->getLayout()->createBlock(
            \M2E\Core\Block\Adminhtml\Dashboard\Shipments::class,
            'dashboard_shipments_block',
            [
                'shipmentDefinition' => $dashboardDefinition->getShipmentDefinition(),
            ]
        );

        $errorsBlock = $this->getLayout()->createBlock(
            \M2E\Core\Block\Adminhtml\Dashboard\Errors::class,
            'dashboard_errors',
            [
                'errorsDefinition' => $dashboardDefinition->getErrorsDefinition(),
            ]
        );

        $productsIssueBlock = $this->getLayout()->createBlock(
            \M2E\Core\Block\Adminhtml\Dashboard\ProductIssues::class,
            'dashboard_products_issues',
            [
                'productIssuesDefinition' => $dashboardDefinition->getProductIssuesDefinition(),
                'moduleTitle' => $dashboardDefinition->getModuleTitle()
            ]
        );

        $salesBlock = $this->getLayout()->createBlock(
            \M2E\Core\Block\Adminhtml\Dashboard\Sales::class,
            'dashboard_sales',
            [
                'salesDefinition' => $dashboardDefinition->getSalesDefinition(),
            ]
        );

        $productsBlock = $this->getLayout()->createBlock(
            \M2E\Core\Block\Adminhtml\Dashboard\Products::class,
            'dashboard_products',
            [
                'productsDefinition' => $dashboardDefinition->getProductsDefinition(),
            ]
        );

        $this->setChild('dashboard_shipments', $shipmentsBlock);
        $this->setChild('dashboard_errors', $errorsBlock);
        $this->setChild('dashboard_products_issues', $productsIssueBlock);
        $this->setChild('dashboard_products', $productsBlock);
        $this->setChild('dashboard_sales', $salesBlock);

        return parent::_prepareLayout();
    }
}
