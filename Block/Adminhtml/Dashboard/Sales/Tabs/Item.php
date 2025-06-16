<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Dashboard\Sales\Tabs;

class Item extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    use \M2E\Core\Block\Adminhtml\Traits\Js;

    protected $_template = 'M2E_Core::dashboard/sales/tab.phtml';
    private string $label;
    /** @var \M2E\Core\Model\Dashboard\Sales\Point[] */
    private array $points;
    private bool $isHourlyShowingModeEnabled = false;

    public function __construct(
        string $label,
        array $points,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->label = $label;
        $this->points = $points;

        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        $chartId = $this->getChartIdentifier();

        $dateFormat = $this->isHourlyShowingModeEnabled ? 'g A' : 'j M';
        $dateTimeZone = new \DateTimeZone(\M2E\Core\Helper\Date::getTimezone()->getConfigTimezone());

        $chartData = array_map(function ($point) use ($dateTimeZone, $dateFormat) {
            $date = $point->date;
            $date = $date->setTimezone($dateTimeZone);
            $dateStr = $date->format($dateFormat);

            return [
                'x' => $dateStr,
                'y' => round($point->value, 2),
                'tooltipTitle' => $date->format('M j, Y H:i a'),
            ];
        }, $this->points);

        $dataset = json_encode($chartData);

        $jsCode = $this->prepareRequireJs(
            ['Bar' => 'M2ECore/Dashboard/ChartJs/Bar'],
            <<<JS
Bar.renderChart('#$chartId', $dataset, '{$this->label}');
JS
        );

        return parent::_toHtml() . $jsCode;
    }

    public function getChartIdentifier(): string
    {
        return sprintf('m2e_dashboard_sales_tabs_%s_tab', strtolower($this->label));
    }

    public function enableHourlyShowingMode(): void
    {
        $this->isHourlyShowingModeEnabled = true;
    }
}
