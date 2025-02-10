<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /* The table is excluded because it uses a composite primary key that causes magenta to fail */
    private array $excludedTables = [];

    private \M2E\Core\Helper\Magento $magentoHelper;
    private \M2E\Core\Model\ResourceModel\Collection\CustomFactory $customCollectionFactory;
    private \M2E\Core\Helper\Module\Database\Structure $coreDatabaseHelper;
    private \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver;
    private \M2E\Core\Model\ControlPanel\DatabaseRegistryCollection $databaseRegistryCollection;

    public function __construct(
        \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver,
        \M2E\Core\Model\ControlPanel\DatabaseRegistryCollection $databaseRegistryCollection,
        \M2E\Core\Helper\Magento $magentoHelper,
        \M2E\Core\Model\ResourceModel\Collection\CustomFactory $customCollectionFactory,
        \M2E\Core\Helper\Module\Database\Structure $coreDatabaseHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->customCollectionFactory = $customCollectionFactory;
        $this->magentoHelper = $magentoHelper;
        $this->coreDatabaseHelper = $coreDatabaseHelper;
        $this->currentExtensionResolver = $currentExtensionResolver;
        $this->databaseRegistryCollection = $databaseRegistryCollection;
    }

    public function _construct(): void
    {
        parent::_construct();

        $this->_isExport = true;
        $this->setData('id', 'controlPanelDatabaseGrid');
        $this->setDefaultSort('table_name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setData('use_ajax', true);
        $this->setDefaultLimit(50);
    }

    protected function _prepareCollection()
    {
        $tablesList = $this->magentoHelper->getMySqlTables();
        $databaseTablePrefix = $this->magentoHelper->getDatabaseTablesPrefix();

        $tablesList = array_map(
            static fn (string $tableName) => str_replace($databaseTablePrefix, '', $tableName),
            $tablesList
        );

        $extension = $this->currentExtensionResolver->get();
        $extensionTablesRegistry = $this->databaseRegistryCollection->getForExtension($extension->getModuleName());
        $tablesList = array_unique(
            array_merge(
                $tablesList,
                $extensionTablesRegistry->getAllTables()
            )
        );

        $collection = $this->customCollectionFactory->create();
        foreach ($tablesList as $tableName) {
            if (
                !$extensionTablesRegistry->isModuleTable($tableName)
                || in_array($tableName, $this->excludedTables, true)
            ) {
                continue;
            }

            $tableRow = [
                'table_name' => $tableName,
                'is_exist' => $this->coreDatabaseHelper->isTableExists($tableName),
                'records' => 0,
                'size' => 0,
                'model' => $extensionTablesRegistry->getResourceModelClass($tableName),
            ];

            if ($tableRow['is_exist']) {
                $tableRow['size'] = $this->coreDatabaseHelper->getDataLengthInMB($tableName);
                $tableRow['records'] = $this->coreDatabaseHelper->getCountOfRecords($tableName);
            }

            $collection->addItem(new \Magento\Framework\DataObject($tableRow));
        }

        $this->setCollection($collection);
        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('table_name', [
            'header' => 'Table Name',
            'align' => 'left',
            'index' => 'table_name',
            'filter_index' => 'table_name',
            'frame_callback' => [$this, 'callbackColumnTableName'],
            'filter_condition_callback' => [$this, 'callbackFilterTitle'],
        ]);

        $this->addColumn('records', [
            'header' => 'Records',
            'align' => 'right',
            'width' => '100px',
            'index' => 'records',
            'type' => 'number',
            'filter' => false,
        ]);

        $this->addColumn('size', [
            'header' => 'Size (Mb)',
            'align' => 'right',
            'width' => '100px',
            'index' => 'size',
            'filter' => false,
        ]);

        return parent::_prepareColumns();
    }

    public function callbackColumnTableName($value, $row, $column, $isExport)
    {
        if (!$row->getData('is_exist')) {
            return sprintf(
                '<p style="color: red; font-weight: bold;">%s [table is not exists]</p>',
                $value
            );
        }

        if (!$row->getData('model')) {
            return sprintf('<p style="color: #878787;">%s [resource model not found]</p>', $value);
        }

        return "<p>$value</p>";
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/controlPanel/databaseTab', ['_current' => true]);
    }

    public function getRowUrl($item)
    {
        if (
            !$item->getData('is_exist')
            || !$item->getData('model')
        ) {
            return false;
        }

        return $this->getUrl(
            '*/controlPanel_database/manageTable',
            ['table' => $item->getData('table_name')]
        );
    }

    protected function _prepareMassaction()
    {
        // Set massaction identifiers
        $this->setMassactionIdField('table_name');
        $this->getMassactionBlock()->setFormFieldName('tables');
        $this->getMassactionBlock()->setUseSelectAll(false);

        return parent::_prepareMassaction();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getFilterConditionCallback()) {
            call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
        }

        return $this;
    }

    protected function callbackFilterTitle($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value == null) {
            return;
        }

        $this->getCollection()->addFilter(
            'table_name',
            $value,
            \M2E\Core\Model\ResourceModel\Collection\Custom::CONDITION_LIKE
        );
    }

    protected function callbackFilterMatch($collection, $column)
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex()
            : $column->getIndex();

        $value = $column->getFilter()->getValue();
        if ($value == null || empty($field)) {
            return;
        }

        $this->getCollection()->addFilter(
            $field,
            $value,
            \M2E\Core\Model\ResourceModel\Collection\Custom::CONDITION_MATCH
        );
    }
}
