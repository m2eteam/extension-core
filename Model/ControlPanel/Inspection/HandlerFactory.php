<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Inspection;

use M2E\Core\Model\ControlPanel\InspectionTaskDefinition;

class HandlerFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(InspectionTaskDefinition $definition): InspectorInterface
    {
        return $this->objectManager->create($definition->getHandler());
    }
}
