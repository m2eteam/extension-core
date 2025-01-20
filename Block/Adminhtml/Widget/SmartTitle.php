<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Widget;

class SmartTitle extends \Magento\Theme\Block\Html\Title
{
    private \M2E\Core\Model\Ui\Widget\SmartTitle\DataProviderInterface $dataProvider;
    private \M2E\Core\Model\Ui\Widget\SmartTitle\UrlBuilderInterface $urlBuilder;
    private \Magento\Framework\App\RequestInterface $request;
    private \M2E\Core\Model\Ui\Widget\SmartTitle\Item $currentTitleItem;

    private string $basePrefix;
    /** @var \M2E\Core\Model\Ui\Widget\SmartTitle\Item[] */
    private array $titleItems = [];
    private bool $isTitleItemsInitialized = false;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $scopeConfig, $data);

        $this->basePrefix = $data['base_prefix'] ?? '';
        $this->dataProvider = $data['data_provider'];
        $this->urlBuilder = $data['url_builder'];
        $this->request = $request;
    }

    public function getTitlePrefix(): string
    {
        return $this->basePrefix;
    }

    public function getCurrentTitleItem(): \M2E\Core\Model\Ui\Widget\SmartTitle\Item
    {
        $this->init();

        return $this->currentTitleItem;
    }

    public function getItemUrl(\M2E\Core\Model\Ui\Widget\SmartTitle\Item $item): string
    {
        return $this->urlBuilder->getUrl($item->getId());
    }

    /**
     * @return \M2E\Core\Model\Ui\Widget\SmartTitle\Item[]
     */
    public function getTitleItems(): array
    {
        $this->init();

        return $this->titleItems;
    }

    public function hasMore(): bool
    {
        return count($this->getTitleItems()) > 0;
    }

    protected function _prepareLayout()
    {
        $this->pageConfig->addPageAsset('M2E_Core::css/widget/smart_title.css');

        return parent::_prepareLayout();
    }

    private function init(): void
    {
        if (!$this->isTitleItemsInitialized) {
            $this->isTitleItemsInitialized = true;

            foreach ($this->dataProvider->getItems() as $item) {
                if ($item->getId() === $this->getCurrentItemId()) {
                    $this->currentTitleItem = $item;
                } else {
                    $this->titleItems[] = $item;
                }
            }
        }
    }

    private function getCurrentItemId(): int
    {
        return (int)$this->request->getParam('id');
    }
}
