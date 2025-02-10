<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\ControlPanel;

class SmartTitle extends \Magento\Theme\Block\Html\Title implements \M2E\Core\Block\Adminhtml\Widget\SmartTitleInterface
{
    protected $_template = 'M2E_Core::widget/smart_title.phtml';

    private \M2E\Core\Model\Ui\Widget\SmartTitle\DataProviderInterface $dataProvider;
    private \Magento\Framework\App\RequestInterface $request;
    private \M2E\Core\Model\Ui\Widget\SmartTitle\Item $currentTitleItem;

    /** @var \M2E\Core\Model\Ui\Widget\SmartTitle\Item[] */
    private array $titleItems = [];
    private bool $isTitleItemsInitialized = false;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \M2E\Core\Model\ControlPanel\Ui\SmartTitle\DataProvider $dataProvider,
        array $data = []
    ) {
        parent::__construct($context, $scopeConfig, $data);

        $this->request = $request;
        $this->dataProvider = $dataProvider;
    }

    public function getTitlePrefix(): string
    {
        return 'Control Panel';
    }

    public function getCurrentTitleItem(): \M2E\Core\Model\Ui\Widget\SmartTitle\Item
    {
        $this->init();

        return $this->currentTitleItem;
    }

    public function getItemUrl(\M2E\Core\Model\Ui\Widget\SmartTitle\Item $item): string
    {
        return $this->getUrl($item->getCode() . '/controlPanel/index');
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
                if ($item->getCode() === $this->request->getModuleName()) {
                    $this->currentTitleItem = $item;
                } else {
                    $this->titleItems[] = $item;
                }
            }
        }
    }
}
