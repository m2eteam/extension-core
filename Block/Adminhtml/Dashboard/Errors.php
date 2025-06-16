<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Dashboard;

class Errors extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    protected $_template = 'M2E_Core::dashboard/errors.phtml';

    private \M2E\Core\Model\Dashboard\Errors\DefinitionInterface $errorsDefinition;

    public function __construct(
        \M2E\Core\Model\Dashboard\Errors\DefinitionInterface $errorsDefinition,
        \Magento\Backend\Block\Template\Context              $context,
        array                                                $data = []
    ) {
        $this->errorsDefinition = $errorsDefinition;

        parent::__construct($context, $data);
    }

    public function getDataProvider(): \M2E\Core\Model\Dashboard\Errors\DataProviderInterface
    {
        return $this->errorsDefinition->getDataProvider();
    }

    public function getInfoProvider(): \M2E\Core\Model\Dashboard\Errors\InfoProviderInterface
    {
        return $this->errorsDefinition->getInfoProvider();
    }
}
