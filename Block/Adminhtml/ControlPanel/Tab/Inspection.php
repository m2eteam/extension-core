<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab;

class Inspection extends AbstractTab
{
    public const TAB_ID = 'inspection';

    protected $_template = 'M2E_Core::control_panel/tab/inspection.phtml';

    protected function getBlockId(): string
    {
        return 'controlPanelInspection';
    }

    public static function getTabId(): string
    {
        return self::TAB_ID;
    }

    public static function getLabel(): string
    {
        return 'Inspection';
    }

    protected function _beforeToHtml()
    {
        $this->setChild(
            'inspections',
            $this->getLayout()->createBlock(\M2E\Core\Block\Adminhtml\ControlPanel\Tab\Inspection\Grid::class)
        );

        return parent::_beforeToHtml();
    }
}
