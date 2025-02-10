<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database\Table;

class TableCellsPopup extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    public const MODE_CREATE = 'create';
    public const MODE_UPDATE = 'update';

    protected $_template = 'M2E_Core::control_panel/tab/database/table_cells_popup.phtml';

    private string $tableName;
    private string $mode = self::MODE_UPDATE;
    private array $rowsIds = [];

    public \M2E\Core\Model\ControlPanel\Database\TableModel $tableModel;
    private \M2E\Core\Model\ControlPanel\Database\TableModelFactory $databaseTableFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \M2E\Core\Model\ControlPanel\Database\TableModelFactory $databaseTableFactory,
        array $data = []
    ) {
        $this->databaseTableFactory = $databaseTableFactory;
        parent::__construct($context, $data);
    }

    public function _construct(): void
    {
        parent::_construct();

        $this->setData('id', 'controlPanelDatabaseTableCellsPopup');
        $this->init();
    }

    private function init()
    {
        $this->tableName = $this->getRequest()->getParam('table');
        $this->mode = $this->getRequest()->getParam('mode');
        $this->rowsIds = explode(',', $this->getRequest()->getParam('ids'));

        $this->tableModel = $this->databaseTableFactory->createFromRequest();
    }

    public function isUpdateCellsMode(): bool
    {
        return $this->mode === self::MODE_UPDATE;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getIds(): array
    {
        return $this->rowsIds;
    }
}
