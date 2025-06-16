<?php

declare(strict_types=1);

namespace M2E\Core\Block\Adminhtml\Dashboard;

class Shipments extends \M2E\Core\Block\Adminhtml\Magento\AbstractBlock
{
    protected $_template = 'M2E_Core::dashboard/shipments.phtml';

    private \M2E\Core\Model\Dashboard\Shipments\DefinitionInterface $shipmentDefinition;

    public function __construct(
        \M2E\Core\Model\Dashboard\Shipments\DefinitionInterface $shipmentDefinition,
        \Magento\Backend\Block\Template\Context                 $context,
        array                                                   $data = []
    ) {
        $this->shipmentDefinition = $shipmentDefinition;

        parent::__construct($context, $data);
    }

    public function getDataProvider(): \M2E\Core\Model\Dashboard\Shipments\DataProviderInterface
    {
        return $this->shipmentDefinition->getDataProvider();
    }

    public function getInfoProvider(): \M2E\Core\Model\Dashboard\Shipments\InfoProviderInterface
    {
        return $this->shipmentDefinition->getInfoProvider();
    }
}
