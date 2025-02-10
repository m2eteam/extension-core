<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab\Inspection;

use M2E\Core\Model\ResourceModel\Collection\Custom;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    use \M2E\Core\Block\Adminhtml\Traits\Js;

    public const NOT_SUCCESS_FILTER = 'not-success';

    private \M2E\Core\Model\ResourceModel\Collection\CustomFactory $customCollectionFactory;
    private \M2E\Core\Model\ControlPanel\InspectionTaskCollection $taskCollection;
    private \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver;

    public function __construct(
        \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver,
        \M2E\Core\Model\ControlPanel\InspectionTaskCollection $taskCollection,
        \M2E\Core\Model\ResourceModel\Collection\CustomFactory $customCollectionFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper
    ) {
        $this->customCollectionFactory = $customCollectionFactory;
        $this->taskCollection = $taskCollection;
        $this->currentExtensionResolver = $currentExtensionResolver;

        parent::__construct($context, $backendHelper);
        $this->setData('id', 'controlPanelInspectionsGrid');
        $this->setSaveParametersInSession(true);
        $this->setData('use_ajax', true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->customCollectionFactory->create();

        $extension = $this->currentExtensionResolver->get();
        foreach ($this->taskCollection->getTasksForExtension($extension->getModuleName()) as $definition) {
            $row = [
                'id' => $definition->getNick(),
                'title' => $definition->getTitle(),
                'description' => $definition->getDescription(),
                'group' => $definition->getGroup(),
            ];
            $collection->addItem(new \Magento\Framework\DataObject($row));
        }

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'title',
            [
                'header' => 'Title',
                'align' => 'left',
                'type' => 'text',
                'width' => '20%',
                'index' => 'title',
                'filter_index' => 'title',
                'filter_condition_callback' => [$this, 'callbackFilterLike'],
                'frame_callback' => [$this, 'callbackColumnTitle'],
            ]
        );

        $this->addColumn(
            'details',
            [
                'header' => 'Details',
                'align' => 'left',
                'type' => 'text',
                'width' => '40%',
                'column_css_class' => 'details',
                'filter_index' => false,
            ]
        );

        $this->addColumn(
            'actions',
            [
                'header' => 'Actions',
                'align' => 'left',
                'width' => '150px',
                'type' => 'action',
                'index' => 'actions',
                'filter' => false,
                'sortable' => false,
                'getter' => 'getId',
                'frame_callback' => [$this, 'prepareActionColumn'],
            ]
        );

        $this->addColumn(
            'id',
            [
                'header' => 'ID',
                'align' => 'right',
                'width' => '100px',
                'type' => 'text',
                'index' => 'id',
                'column_css_class' => 'no-display id',
                'header_css_class' => 'no-display',
            ]
        );

        return parent::_prepareColumns();
    }

    public function callbackColumnTitle($value, \Magento\Framework\DataObject $row)
    {
        $value = '<span style="color: grey;">[' . $row->getData('group') . ']</span> ' . $value;

        if (!$row->getData('description')) {
            return $value;
        }

        return <<<HTML
<style>
    .admin__field-tooltip .admin__field-tooltip-content {
        bottom: 5rem;
    }
</style>
{$value}
<div class="M2E-field-tooltip-to-right admin__field-tooltip">
    <a class="admin__field-tooltip-action"  style="bottom:8px;"></a>
    <div class="admin__field-tooltip-content">
           {$row->getData('description')}
    </div>
</div>
HTML;
    }

    public function prepareActionColumn()
    {
        return '<a field="id" href="javascript:void(0)" onclick="M2ECoreControlPanelInspectionObj.checkAction()">Check</a>';
    }

    public function toHtml()
    {
        $url = $this->getUrl('*/controlPanel_inspection/checkInspection');
        // Set ids to be able to use option "Select All"
        $ids = [];
        $extension = $this->currentExtensionResolver->get();
        foreach ($this->taskCollection->getTasksForExtension($extension->getModuleName()) as $definition) {
            $ids[] = $definition->getNick();
        }
        $allIdsStr = implode(",", $ids);

        return parent::toHtml() . $this->prepareOnReadyJs(
            <<<JS
require(['domReady', 'M2ECore/ControlPanel/Inspection'], function() {
    window.M2ECoreControlPanelInspectionObj = new M2ECoreControlPanelInspection('{$this->getId()}', '{$url}');
    window.M2ECoreControlPanelInspectionObj.afterInitPage();
    window.M2ECoreControlPanelInspectionObj.getGridMassActionObj().setGridIds('{$allIdsStr}');
});
JS
        );
    }

    protected function _prepareMassaction()
    {
        // Set massaction identifiers
        // ---------------------------------------
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        // ---------------------------------------

        $this->getMassactionBlock()->addItem(
            'checkAll',
            [
                'label' => 'Run',
                'url' => '',
            ]
        );

        return parent::_prepareMassaction();
    }

    protected function _addColumnFilterToCollection($column)
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();

        if ($field === 'id') {
            return $this;
        }

        return parent::_addColumnFilterToCollection($column);
    }

    protected function callbackFilterLike($collection, $column)
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
        $value = $column->getFilter()->getValue();
        if ($value == null || empty($field)) {
            return;
        }

        $this->getCollection()->addFilter($field, $value, Custom::CONDITION_LIKE);
    }

    protected function callbackFilterMatch($collection, $column)
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
        $value = $column->getFilter()->getValue();
        if ($value == null || empty($field)) {
            return;
        }

        if ($value == self::NOT_SUCCESS_FILTER) {
            $field = 'need_attention';
            $value = '1';
        }

        $this->getCollection()->addFilter($field, $value, Custom::CONDITION_LIKE);
    }
}
