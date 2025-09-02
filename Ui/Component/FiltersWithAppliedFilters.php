<?php

declare(strict_types=1);

namespace M2E\Core\Ui\Component;

class FiltersWithAppliedFilters extends \Magento\Ui\Component\Filters
{
    private \Magento\Framework\Api\FilterBuilder $filterBuilder;
    private \M2E\Core\Ui\AppliedFilters\Manager $appliedFiltersManager;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \M2E\Core\Ui\AppliedFilters\Manager $appliedFiltersManager,
        array $components = [],
        array $data = [],
        ?\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate = null
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data, $localeDate);
        $this->filterBuilder = $filterBuilder;
        $this->appliedFiltersManager = $appliedFiltersManager;
    }

    public function prepare()
    {
        parent::prepare();

        $config = $this->getData('config');

        $this->appendFiltersByFilter($config);

        $this->setData('config', $config);
        parent::prepare();
    }

    private function appendFiltersByFilter(array &$config)
    {
        $appliedFilters = $this->appliedFiltersManager
            ->getAppliedFiltersFromUiContext($this->getContext());

        if (empty($appliedFilters)) {
            return;
        }

        foreach ($appliedFilters as $appliedFilter) {
            $this->applyFiltersInDataProvider($appliedFilter);
            $config['applied'][$appliedFilter->field] = $appliedFilter->value;
        }
    }

    private function applyFiltersInDataProvider(\M2E\Core\Ui\AppliedFilters\AppliedFilter $appliedFilter): void
    {
        if ($appliedFilter->isTypeSelect()) {
            $this->applySelectFilter($appliedFilter);
        }

        if ($appliedFilter->isTypeSimple()) {
            $this->applyTextFilter($appliedFilter);
        }

        if ($appliedFilter->isTypeRange()) {
            $this->applyNumberFilter($appliedFilter);
        }
    }

    private function applyTextFilter(\M2E\Core\Ui\AppliedFilters\AppliedFilter $appliedFilter): void
    {
        $filter = $this->filterBuilder
            ->create()
            ->setValue($appliedFilter->value)
            ->setField($appliedFilter->field)
            ->setConditionType('eq');

        $this->getContext()->getDataProvider()->addFilter($filter);
    }

    private function applySelectFilter(\M2E\Core\Ui\AppliedFilters\AppliedFilter $appliedFilter)
    {
        $filter = $this->filterBuilder
            ->create()
            ->setValue($appliedFilter->value)
            ->setField($appliedFilter->field)
            ->setConditionType('in');

        $this->getContext()->getDataProvider()->addFilter($filter);
    }

    private function applyNumberFilter(\M2E\Core\Ui\AppliedFilters\AppliedFilter $appliedFilter)
    {
        $field = $appliedFilter->field;

        $from = $appliedFilter->value['from'];
        if ($from !== null) {
            $filter = $this->filterBuilder
                ->create()
                ->setField($field)
                ->setValue($from)
                ->setConditionType('gteq');

            $this->getContext()->getDataProvider()->addFilter($filter);
        }

        $to = $appliedFilter->value['to'];
        if ($to !== null) {
            $filter = $this->filterBuilder
                ->create()
                ->setField($field)
                ->setValue($to)
                ->setConditionType('lteq');

            $this->getContext()->getDataProvider()->addFilter($filter);
        }
    }
}
