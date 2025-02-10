<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab;

class ModuleTools extends AbstractTab
{
    public const TAB_ID = 'tools_module';

    protected $_template = 'M2E_Core::control_panel/tab/module_tools.phtml';

    protected function getBlockId(): string
    {
        return 'controlPanelToolsModule';
    }

    public static function getTabId(): string
    {
        return self::TAB_ID;
    }

    public static function getLabel(): string
    {
        return 'Module Tools';
    }

    protected function _beforeToHtml()
    {
        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock(\M2E\Core\Block\Adminhtml\ControlPanel\Tab\ModuleTools\Tabs::class)
        );

        return parent::_beforeToHtml();
    }
}
