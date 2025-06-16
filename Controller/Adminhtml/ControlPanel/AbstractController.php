<?php

namespace M2E\Core\Controller\Adminhtml\ControlPanel;

use Magento\Backend\App\Action;

abstract class AbstractController extends Action
{
    public const LAYOUT_ONE_COLUMN = '1column';

    private \Magento\Framework\View\LayoutInterface $emptyLayout;
    private \Magento\Framework\View\LayoutFactory $layoutFactory;
    private \Magento\Framework\View\Result\Page $resultPage;
    private \Magento\Framework\View\Result\PageFactory $resultPageFactory;
    private \Magento\Framework\Controller\Result\Raw $rawResult;
    private \Magento\Framework\Controller\Result\RawFactory $resultRawFactory;

    public function __construct(
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    protected function isAjax(): bool
    {
        $request = $this->getRequest();

        return $request->isXmlHttpRequest() || $request->getParam('isAjax');
    }

    protected function getLayout()
    {
        if ($this->isAjax()) {
            $this->initEmptyLayout();

            return $this->emptyLayout;
        }

        return $this->getResultPage()->getLayout();
    }

    protected function initEmptyLayout(): void
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->emptyLayout)) {
            return;
        }

        $this->emptyLayout = $this->layoutFactory->create();
    }

    protected function getResultPage(): \Magento\Framework\View\Result\Page
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->resultPage)) {
            $this->initResultPage();
        }

        return $this->resultPage;
    }

    protected function initResultPage(): void
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->resultPage)) {
            return;
        }

        $this->resultPage = $this->resultPageFactory->create();
        $this->resultPage->addHandle(self::LAYOUT_ONE_COLUMN);

        $this->resultPage->getConfig()->getTitle()->set(__('Core'));
    }

    protected function addContent(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        $this->initResultPage();

        return $this->_addContent($block);
    }

    protected function getResult()
    {
        if ($this->isAjax()) {
            return $this->getRawResult();
        }

        return $this->getResultPage();
    }

    protected function getRawResult(): \Magento\Framework\Controller\Result\Raw
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->rawResult)) {
            $this->initRawResult();
        }

        return $this->rawResult;
    }

    protected function initRawResult(): void
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->rawResult)) {
            return;
        }

        $this->rawResult = $this->resultRawFactory->create();
    }

    protected function setAjaxContent($blockData): void
    {
        if ($blockData instanceof \Magento\Framework\View\Element\AbstractBlock) {
            $blockData = $blockData->toHtml();
        }

        $this->getRawResult()->setContents($blockData);
    }
}
