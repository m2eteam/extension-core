<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab\ModuleTools;

class Tabs extends \M2E\Core\Block\Adminhtml\Magento\Tabs\AbstractTabs
{
    private \M2E\Core\Model\ControlPanel\ModuleToolsTabCollection $tabsCollection;
    private \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver;

    public function __construct(
        \M2E\Core\Model\ControlPanel\ModuleToolsTabCollection $tabsCollection,
        \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->tabsCollection = $tabsCollection;
        $this->currentExtensionResolver = $currentExtensionResolver;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    public function _construct(): void
    {
        parent::_construct();

        $this->setData('id', 'controlPanelToolsModuleTabs');
        $this->setDestElementId('tools_module_tabs');
    }

    protected function _beforeToHtml()
    {
        $extension = $this->currentExtensionResolver->get();
        foreach ($this->tabsCollection->getForExtension($extension->getModuleName()) as $tab) {
            $this->addTab(
                $tab->getId(),
                [
                    'label' => $tab->getLabel(),
                    'content' => $this->getLayout()->createBlock(
                        \M2E\Core\Block\Adminhtml\ControlPanel\Tab\ModuleTools\Tab\Commands::class,
                        '',
                        ['controllerClass' => $tab->getControllerClass(), 'route' => $tab->getRoute()],
                    )->toHtml(),
                ],
            );
        }

        return parent::_beforeToHtml();
    }
}
