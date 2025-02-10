<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel\Tab;

class Overview extends AbstractTab
{
    public const TAB_ID = 'overview';

    protected $_template = 'M2E_Core::control_panel/tab/overview.phtml';

    /** @var array<int, \M2E\Core\Block\Adminhtml\ControlPanel\Widget\AbstractWidget[]> $widgets */
    private array $widgets = [
        \M2E\Core\Model\ControlPanel\OverviewWidget::FIRST_COLUMN => [],
        \M2E\Core\Model\ControlPanel\OverviewWidget::SECOND_COLUMN => [],
        \M2E\Core\Model\ControlPanel\OverviewWidget::THIRD_COLUMN => [],
    ];
    private \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver;
    private \M2E\Core\Model\ControlPanel\OverviewWidgetCollection $widgetCollection;

    public function __construct(
        \M2E\Core\Model\ControlPanel\CurrentExtensionResolver $currentExtensionResolver,
        \M2E\Core\Model\ControlPanel\OverviewWidgetCollection $widgetCollection,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->currentExtensionResolver = $currentExtensionResolver;
        $this->widgetCollection = $widgetCollection;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function getBlockId(): string
    {
        return 'controlPanelOverview';
    }

    public static function getTabId(): string
    {
        return self::TAB_ID;
    }

    public static function getLabel(): string
    {
        return 'Overview';
    }

    /**
     * @param int $columnNumber
     *
     * @return \M2E\Core\Block\Adminhtml\ControlPanel\Widget\AbstractWidget[]
     */
    public function getColumnWidgets(int $columnNumber): array
    {
        return $this->widgets[$columnNumber] ?? [];
    }

    protected function _beforeToHtml()
    {
        $extension = $this->currentExtensionResolver->get();
        foreach ($this->widgetCollection->getForExtension($extension->getModuleName()) as $widget) {
            $this->widgets[$widget->getColumn()][] = $this->getLayout()
                ->createBlock($widget->getClassName(), '', ['data' => $widget->getWidgetData()]);
        }

        return parent::_beforeToHtml();
    }
}
