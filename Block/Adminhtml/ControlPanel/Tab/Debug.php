<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab;

class Debug extends AbstractTab
{
    public const TAB_ID = 'debug';

    protected $_template = 'M2E_Core::control_panel/tab/debug.phtml';

    protected function getBlockId(): string
    {
        return 'controlPanelDebug';
    }

    public static function getTabId(): string
    {
        return self::TAB_ID;
    }

    public static function getLabel(): string
    {
        return 'Debug';
    }
}
