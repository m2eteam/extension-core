<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database;

class Table extends \Magento\Backend\Block\Widget\Grid\Container
{
    private string $tableName;

    public function __construct(
        \M2E\Core\Block\Adminhtml\Magento\Context\Widget $context,
        string $tableName
    ) {
        $this->tableName = $tableName;
        parent::__construct($context);
    }

    public function _construct(): void
    {
        parent::_construct();

        $this->setData('id', 'controlPanelDatabaseTable');
        $this->_controller = 'adminhtml_controlPanel_tabs_database_table';

        $title = sprintf('Manage Table "%s"', $this->tableName);

        $this->pageConfig->getTitle()->prepend($title);
        $this->_headerText = $title;

        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');

        $this->addButton('back', [
            'label' => 'Back',
            'onclick' => "window.open('{$this->getDatabaseTabUrl()}','_blank')",
            'class' => 'back',
        ]);

        $this->addButton('additional-actions', [
            'label' => 'Additional Actions',
            'onclick' => '',
            'class' => 'action-secondary',
            'sort_order' => 100,
            'class_name' => \M2E\Core\Block\Adminhtml\Magento\Button\DropDown::class,
            'options' => [
                'clear-cache' => [
                    'label' => 'Flush Cache',
                    'onclick' => "window.open('{$this->getFlushCacheUrl()}', '_blank');",
                ],
            ],
        ]);

        $this->addButton('add_row', [
            'label' => 'Append Row',
            'onclick' => 'M2ECoreControlPanelDatabaseGridObj.openTableCellsPopup(\'add\')',
            'class' => 'action-success',
            'sort_order' => 90,
        ]);
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                \M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database\Table\Grid::class,
                '',
                ['tableName' => $this->tableName]
            )
        );

        return parent::_prepareLayout();
    }

    private function getFlushCacheUrl(): string
    {
        return $this->getUrl('*/controlPanel_tools/magento', ['action' => 'clearMagentoCache']);
    }

    private function getDatabaseTabUrl(): string
    {
        return $this->getUrl(
            '*/controlPanel/index',
            ['tab' => \M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database::getTabId()]
        );
    }
}
