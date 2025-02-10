<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Widget;

class Database extends AbstractWidget
{
    protected $_template = 'M2E_Core::control_panel/widget/database.phtml';

    private \M2E\Core\Helper\Module\Database\Structure $databaseHelper;
    private array $tableList;

    public function __construct(
        \M2E\Core\Helper\Module\Database\Structure $databaseHelper,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        ?\Magento\Framework\Json\Helper\Data $jsonHelper = null,
        ?\Magento\Directory\Helper\Data $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);

        $this->databaseHelper = $databaseHelper;
    }

    public function _construct(): void
    {
        parent::_construct();
        $this->tableList = $this->getData('table_list');
    }

    protected function getBlockId(): string
    {
        return 'controlPanelInfoMysqlTables';
    }

    protected function getDefaultTitle(): string
    {
        return 'Database';
    }

    public function getTablesInfo(): array
    {
        $tablesInfo = [];
        foreach ($this->tableList as $category => $tables) {
            foreach ($tables as $tableName) {
                $tablesInfo[$category][$tableName] = [
                    'count' => 0,
                    'url' => '#',
                ];

                if (!$this->databaseHelper->isTableReady($tableName)) {
                    continue;
                }

                $tablesInfo[$category][$tableName]['count'] = $this->databaseHelper->getCountOfRecords($tableName);
                $tablesInfo[$category][$tableName]['url'] = $this->getUrl(
                    '*/controlPanel_database/manageTable',
                    ['table' => $tableName]
                );
            }
        }

        return $tablesInfo;
    }
}
