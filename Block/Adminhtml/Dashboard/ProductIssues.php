<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Dashboard;

class ProductIssues extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    use \M2E\Core\Block\Adminhtml\Traits\Js;

    protected $_template = 'M2E_Core::dashboard/product_issues.phtml';

    private \Magento\Backend\Model\UrlInterface $urlBuilder;
    private \M2E\Core\Model\Dashboard\ProductIssues\DefinitionInterface $productIssuesDefinition;
    private string $moduleTitle;
    private array $issues;
    private bool $isIssuesTableVisible = false;
    private ?string $notificationMessage = null;

    public function __construct(
        \M2E\Core\Model\Dashboard\ProductIssues\DefinitionInterface $productIssuesDefinition,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        string $moduleTitle,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->moduleTitle = $moduleTitle;
        $this->productIssuesDefinition = $productIssuesDefinition;
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $this->init();

        return parent::_prepareLayout();
    }

    private function init(): void
    {
        $infoProvider = $this->getInfoProvider();
        if (!$infoProvider->hasListedProducts()) {
            $startListingUrl = $infoProvider->getStartListingUrl();

            $message = (string)__(
                "<h2>Currently, there are no items listed on %s. <a href='%s' target='_blank'>Start Listing now</a>
                           to get more sales.</h2>"
            );
            $this->notificationMessage = sprintf($message, $this->moduleTitle, $startListingUrl);

            return;
        }

        if (empty($this->getIssues())) {
            $this->notificationMessage = (string)__('There are no issues at the moment. Keep up the good work!');

            return;
        }

        $this->isIssuesTableVisible = true;
    }

    public function getIssues(): array
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->issues)) {
            return $this->issues;
        }

        $dataProvider = $this->productIssuesDefinition->getDataProvider();

        $issues = array_map(function (\M2E\Core\Model\Dashboard\ProductIssues\Issue $issue) {
            return [
                'url' => $this->getInfoProvider()->getAllItemsViewUrl($issue->tagId),
                'text' => $issue->text,
                'total' => $issue->total,
                'impact_rate' => round($issue->impactRate, 1) . ' %',
            ];
        }, $dataProvider->getTopIssues());

        return $this->issues = $issues;
    }

    public function getInfoProvider(): \M2E\Core\Model\Dashboard\ProductIssues\InfoProviderInterface
    {
        return $this->productIssuesDefinition->getInfoProvider();
    }

    protected function _toHtml()
    {
        $js = $this->prepareOnReadyJs(<<<JS
        jQuery('#m2e_dashboard_listing_product_issues .issues-row').on('click', function () {
            window.open(jQuery(this).data('url'));
        });
    JS);

        return parent::_toHtml() . $js;
    }

    public function isIssuesTableVisible(): bool
    {
        return $this->isIssuesTableVisible;
    }

    public function getNotificationMessage(): ?string
    {
        return $this->notificationMessage;
    }
}
