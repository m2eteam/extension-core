<?php

declare(strict_types=1);

namespace M2E\Core\Ui\AppliedFilters;

use Magento\Backend\Model\UrlInterface;

class Manager
{
    private const QUERY_PARAM_KEY = 'filters';

    private UrlInterface $urlBuilder;
    private \M2E\Core\Ui\AppliedFilters\Serializer $serializer;

    public function __construct(
        Serializer $serializer,
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->serializer = $serializer;
    }

    /**
     * @param \M2E\Core\Ui\AppliedFilters\AppliedFilter[] $appliedFilters
     */
    public function createUrlWithAppliedFilters(
        string $routePath,
        array $appliedFilters,
        array $routeParams = []
    ): string {
        if (empty($appliedFilters)) {
            return $this->urlBuilder->getUrl($routePath, $routeParams);
        }

        $params = array_merge_recursive($routeParams, [
            '_query' => [
                self::QUERY_PARAM_KEY => $this->serializer->encode($appliedFilters),
            ],
        ]);

        return $this->urlBuilder->getUrl($routePath, $params);
    }

    /**
     * @return \M2E\Core\Ui\AppliedFilters\AppliedFilter[]
     */
    public function getAppliedFiltersFromUiContext(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context
    ): array {
        $filters = $context->getRequestParam(self::QUERY_PARAM_KEY);
        if (
            empty($filters)
            || !is_string($filters)
        ) {
            return [];
        }

        return $this->serializer->decode($filters);
    }
}
