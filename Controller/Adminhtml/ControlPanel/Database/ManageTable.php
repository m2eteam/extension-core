<?php

declare(strict_types=1);

namespace M2E\Core\Controller\Adminhtml\ControlPanel\Database;

use Magento\Backend\App\Action;

class ManageTable extends Action
{
    private \Magento\Backend\Model\Url $backendUrlBuilder;
    private \Magento\Framework\View\Result\PageFactory $resultPageFactory;

    public function __construct(
        \Magento\Backend\Model\Url $backendUrlBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->backendUrlBuilder = $backendUrlBuilder;
    }

    public function execute()
    {
        $table = $this->getRequest()->getParam('table');

        if ($table === null) {
            return $this->_redirect($this->getPageUrl(
                ['tab' => \M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database::TAB_ID]
            ));
        }

        $result = $this->resultPageFactory->create();
        $this->_addContent(
            $result->getLayout()->createBlock(
                \M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database\Table::class,
                '',
                ['tableName' => $table],
            ),
        );

        return $result;
    }

    private function getPageUrl(array $params = []): string
    {
        return $this->backendUrlBuilder->getUrl($this->getPageRoute(), $params);
    }

    private function getPageRoute(): string
    {
        return '*/controlPanel/index';
    }
}
