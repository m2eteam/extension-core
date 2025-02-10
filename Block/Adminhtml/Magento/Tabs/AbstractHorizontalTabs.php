<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Magento\Tabs;

abstract class AbstractHorizontalTabs extends AbstractTabs
{
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

    protected function _prepareLayout()
    {
        $this->pageConfig->addPageAsset('M2E_Core::css/tabs/horizontal.css');

        return parent::_prepareLayout();
    }

    protected function _toHtml()
    {
        return
            '<div class="M2E-tabs-horizontal">' .
            parent::_toHtml() .
            '</div>';
    }
}
