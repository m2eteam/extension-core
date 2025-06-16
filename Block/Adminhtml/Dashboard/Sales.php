<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Dashboard;

class Sales extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    use \M2E\Core\Block\Adminhtml\Traits\Js;

    private const DATE_PERIOD_REQUEST_KEY = 'sales_period';

    private const TODAY_PERIOD_VALUE = 'today';
    private const LAST_24_HOURS_PERIOD_VALUE = '24h';
    private const LAST_7_DAYS_PERIOD_VALUE = '7d';

    protected $_template = 'M2E_Core::dashboard/sales.phtml';

    private string $defaultActivePeriodVal = self::TODAY_PERIOD_VALUE;
    private \M2E\Core\Model\Dashboard\Sales\DefinitionInterface $salesDefinition;
    private \M2E\Core\Block\Adminhtml\Dashboard\Sales\TabsFactory $tabsFactory;

    public function __construct(
        \M2E\Core\Model\Dashboard\Sales\DefinitionInterface $salesDefinition,
        \M2E\Core\Block\Adminhtml\Dashboard\Sales\TabsFactory $tabsFactory,
        \Magento\Backend\Block\Template\Context               $context,
        array                                                 $data = []
    ) {
        $this->salesDefinition = $salesDefinition;
        $this->tabsFactory = $tabsFactory;

        parent::__construct($context, $data);
    }

    public function getTabs(): Sales\Tabs
    {
        $activePeriod = $this->getActivePeriod();
        $pointSets = $this->getPointSets($activePeriod);

        $qtyTabItem = $this->tabsFactory->createTabItem(
            (string)__('Quantity'),
            $pointSets['qty'],
            $this->getLayout()
        );

        $amountsTabItem = $this->tabsFactory->createTabItem(
            (string)__('Amounts'),
            $pointSets['amount'],
            $this->getLayout()
        );

        if (
            $activePeriod === self::LAST_24_HOURS_PERIOD_VALUE
            || $activePeriod === self::TODAY_PERIOD_VALUE
        ) {
            $qtyTabItem->enableHourlyShowingMode();
            $amountsTabItem->enableHourlyShowingMode();
        }

        return $this->tabsFactory->createTabs($amountsTabItem, $qtyTabItem, $this->getLayout());
    }

    private function getPointSets(string $period): array
    {
        $dataProvider = $this->salesDefinition->getDataProvider();
        if ($period === self::LAST_24_HOURS_PERIOD_VALUE) {
            return [
                'amount' => $dataProvider->getAmountPointsFor24Hours(),
                'qty' => $dataProvider->getQtyPointsFor24Hours(),
            ];
        }

        if ($period === self::LAST_7_DAYS_PERIOD_VALUE) {
            return [
                'amount' => $dataProvider->getAmountPointsFor7Days(),
                'qty' => $dataProvider->getQtyPointsFor7Days(),
            ];
        }

        return [
            'amount' => $dataProvider->getAmountPointsForToday(),
            'qty' => $dataProvider->getQtyPointsForToday(),
        ];
    }

    private function getActivePeriod(): string
    {
        if ($period = $this->getRequest()->getParam(self::DATE_PERIOD_REQUEST_KEY)) {
            return $period;
        }

        return $this->defaultActivePeriodVal;
    }

    /**
     * @return array<array{value:string, title:string, is_active:bool}>
     */
    public function getDatePeriods(): array
    {
        $periods = [
            ['title' => __('Today'), 'value' => self::TODAY_PERIOD_VALUE,],
            ['title' => __('Last 24 hours'), 'value' => self::LAST_24_HOURS_PERIOD_VALUE],
            ['title' => __('Last 7 days'), 'value' => self::LAST_7_DAYS_PERIOD_VALUE],
        ];

        $activePeriod = $this->getActivePeriod();

        return array_map(function (array $period) use ($activePeriod) {
            $isActive = $period['value'] === $activePeriod;

            return array_merge($period, ['is_active' => $isActive]);
        }, $periods);
    }

    protected function _toHtml()
    {
        $url = $this->getUrl('*/*/*') . self::DATE_PERIOD_REQUEST_KEY;

        $js = $this->prepareOnReadyJs(<<<JS
        jQuery('#m2e_dashboard_chart_period').on('change', function () {
            window.location = '$url/' + this.value;
        });
    JS);

        return parent::_toHtml() . $js;
    }
}
