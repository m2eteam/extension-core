<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Dashboard\Sales;

class Tabs extends \M2E\Core\Block\Adminhtml\Magento\Tabs\AbstractHorizontalTabs
{
    private \M2E\Core\Block\Adminhtml\Dashboard\Sales\Tabs\Item $amountsTabItem;
    private \M2E\Core\Block\Adminhtml\Dashboard\Sales\Tabs\Item $qtyTabItem;

    public function __construct(
        Tabs\Item $amountsTabItem,
        Tabs\Item $qtyTabItem,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->amountsTabItem = $amountsTabItem;
        $this->qtyTabItem = $qtyTabItem;

        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setDestElementId('sales_tab_container');
    }

    protected function _prepareLayout()
    {
        $this->addTab('orders', [
            'label' => __('Orders'),
            'content' => $this->qtyTabItem->toHtml(),
        ]);

        $this->addTab('amounts', [
            'label' => __('Amounts'),
            'content' => $this->amountsTabItem->toHtml(),
        ]);

        $this->setActiveTab('orders');

        return parent::_prepareLayout();
    }

    public function _toHtml()
    {
        return parent::_toHtml() . '<div id="sales_tab_container"></div>';
    }
}
