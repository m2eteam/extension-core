<?php

declare(strict_types=1);

namespace M2E\Core\Controller\Adminhtml\ControlPanel\Database;

use M2E\Core\Controller\Adminhtml\ControlPanel\AbstractController;

abstract class AbstractTable extends AbstractController
{
    private \M2E\Core\Model\ControlPanel\Database\TableModelFactory $tableModelFactory;
    private \M2E\Core\Model\CacheManager $cacheManager;

    public function __construct(
        \M2E\Core\Model\ControlPanel\Database\TableModelFactory $tableModelFactory,
        \M2E\Core\Model\CacheManager $cacheManager,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($layoutFactory, $resultPageFactory, $resultRawFactory, $context);
        $this->cacheManager = $cacheManager;
        $this->tableModelFactory = $tableModelFactory;
    }

    protected function getTableModel(): \M2E\Core\Model\ControlPanel\Database\TableModel
    {
        return $this->tableModelFactory->createFromRequest();
    }

    protected function prepareCellsValuesArray(): array
    {
        $cells = $this->getRequest()->getParam('cells', []);
        if (is_string($cells)) {
            $cells = [$cells];
        }

        $bindArray = [];
        foreach ($cells as $columnName) {
            $columnValue = $this->getRequest()->getParam('value_' . $columnName);

            if ($columnValue === null) {
                continue;
            }

            if (strtolower($columnValue) === 'null') {
                $columnValue = null;
            }

            $bindArray[$columnName] = $columnValue;
        }

        return $bindArray;
    }

    protected function prepareIds(): array
    {
        $ids = explode(',', $this->getRequest()->getParam('ids'));

        return array_filter(array_map('intval', $ids));
    }

    protected function redirectToTablePage(string $tableName): void
    {
        $this->_redirect('*/*/manageTable', ['table' => $tableName]);
    }

    protected function afterTableAction(string $tableName): void
    {
        if (strpos($tableName, 'config') !== false) {
            $this->cacheManager->removeAllValues();
        }
    }
}
