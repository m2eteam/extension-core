<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Dashboard;

class Products extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    use \M2E\Core\Block\Adminhtml\Traits\Js;

    protected $_template = 'M2E_Core::dashboard/products.phtml';
    private \M2E\Core\Model\Dashboard\Products\DefinitionInterface $productsDefinition;

    public function __construct(
        \M2E\Core\Model\Dashboard\Products\DefinitionInterface $productsDefinition,
        \Magento\Backend\Block\Template\Context                $context,
        array                                                  $data = []
    ) {
        $this->productsDefinition = $productsDefinition;

        parent::__construct($context, $data);
    }

    protected function _toHtml()
    {
        $dataProvider = $this->productsDefinition->getDataProvider();
        $active = $dataProvider->getCountOfListedProducts();
        $inactive = $dataProvider->getCountOfNotListedProducts();
        $total = $active + $inactive;

        $chartId = 'm2e_sales_products';

        if ($total === 0) {
            $js = $this->prepareRequireJs(
                ['Doughnut' => 'M2ECore/Dashboard/ChartJs/Doughnut'],
                <<<JS
Doughnut.renderWithoutData('#$chartId');
JS
            );

            return parent::_toHtml() . $js;
        }

        $chartDataset = [
            'labels' => [
                __('Active'),
                __('Inactive'),
            ],
            'data' => [
                $active,
                $inactive,
            ],
            'backgroundColor' => [
                '#f1d4b3',
                '#a9b4b8',
            ],
        ];
        $chartDatasetJson = json_encode($chartDataset);

        $js = $this->prepareRequireJs(
            ['Doughnut' => 'M2ECore/Dashboard/ChartJs/Doughnut'],
            <<<JS
Doughnut.renderWithData('#$chartId', $chartDatasetJson);
JS
        );

        return parent::_toHtml() . $js;
    }
}
