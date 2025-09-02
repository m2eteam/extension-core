<?php

declare(strict_types=1);

namespace M2E\Core\Ui\Component;

class BookmarkWithAppliedFilters extends \Magento\Ui\Component\Bookmark
{
    private \M2E\Core\Ui\AppliedFilters\Manager $appliedFiltersManager;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Ui\Api\BookmarkRepositoryInterface $bookmarkRepository,
        \Magento\Ui\Api\BookmarkManagementInterface $bookmarkManagement,
        \M2E\Core\Ui\AppliedFilters\Manager $appliedFiltersManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $bookmarkRepository, $bookmarkManagement, $components, $data);
        $this->appliedFiltersManager = $appliedFiltersManager;
    }

    public function prepare()
    {
        parent::prepare();

        $config = $this->getData('config');

        $this->appendFiltersByFilter($config);
        $this->setData('config', $config);

        $jsConfig = $this->getConfiguration();
        $this->getContext()->addComponentDefinition($this->getComponentName(), $jsConfig);
    }

    private function appendFiltersByFilter(array &$config)
    {
        $appliedFilters = $this->appliedFiltersManager
            ->getAppliedFiltersFromUiContext($this->getContext());

        if (empty($appliedFilters)) {
            return;
        }

        foreach ($appliedFilters as $appliedFilter) {
            $config['current']['filters']['applied'][$appliedFilter->field] = $appliedFilter->value;
        }
    }
}
