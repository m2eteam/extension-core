<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab;

class Database extends AbstractTab
{
    public const TAB_ID = 'database';

    protected $_template = 'M2E_Core::control_panel/tab/database.phtml';

    protected function getBlockId(): string
    {
        return 'controlPanelDatabaseTab';
    }

    public static function getTabId(): string
    {
        return self::TAB_ID;
    }

    public static function getLabel(): string
    {
        return 'Database';
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                \M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database\Grid::class,
            )
        );

        return parent::_prepareLayout();
    }
}
