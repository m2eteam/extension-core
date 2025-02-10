<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel;

class Tabs extends \M2E\Core\Block\Adminhtml\Magento\Tabs\AbstractHorizontalTabs
{
    private \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver;
    private \M2E\Core\Model\ControlPanel\TabCollection $tabCollection;

    public function __construct(
        \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver,
        \M2E\Core\Model\ControlPanel\TabCollection $tabCollection,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->currentExtensionResolver = $currentExtensionResolver;
        $this->tabCollection = $tabCollection;

        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    public function _construct(): void
    {
        parent::_construct();
        $this->setDestElementId('control_panel_tab_container');
    }

    public function _toHtml(): string
    {
        return parent::_toHtml() . '<div id="control_panel_tab_container"></div>';
    }

    protected function _prepareLayout()
    {
        $this->initPageSmartTitle();
        $this->initTabs();

        $this->pageConfig->addPageAsset('M2E_Core::css/controlpanel/style.css');

        return parent::_prepareLayout();
    }

    private function initPageSmartTitle(): void
    {
        $pageTitleBlock = $this->getLayout()
                               ->createBlock(\M2E\Core\Block\Adminhtml\ControlPanel\SmartTitle::class);
        $this->getLayout()->setBlock('page.title', $pageTitleBlock);
    }

    private function initTabs(): void
    {
        $extension = $this->currentExtensionResolver->get();
        $tabs = $this->tabCollection->getForExtension($extension->getModuleName());

        $activeTab = $this->getRequest()->getParam('tab');
        if (empty($activeTab)) {
            /** @var \M2E\Core\Model\ControlPanel\Tab $firstTab */
            $firstTab = reset($tabs);
            $activeTab = $firstTab->getClassName()::getTabId();
        }

        foreach ($tabs as $tab) {
            $className = $tab->getClassName();
            $tabParams = ['label' => $className::getLabel()];

            if (
                $activeTab !== $className::getTabId()
                && $tab->isAjax()
            ) {
                $tabParams['class'] = 'ajax';
                $tabParams['url'] = $this->getUrl($tab->getRoute());
            } else {
                $tabParams['content'] = $this->getLayout()
                                             ->createBlock($className, '', $tab->getArguments())
                                             ->toHtml();
            }

            $this->addTab($className::getTabId(), $tabParams);
        }

        $this->setActiveTab($activeTab);
    }
}
