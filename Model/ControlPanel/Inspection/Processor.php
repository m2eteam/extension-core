<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Inspection;

use M2E\Core\Model\ControlPanel\Inspection\Response\Result;

class Processor
{
    private \M2E\Core\Model\ControlPanel\Inspection\HandlerFactory $handlerFactory;
    private \M2E\Core\Model\ControlPanel\Inspection\Response\ResultFactory $resultFactory;
    private \M2E\Core\Model\ControlPanel\InspectionTaskCollection $inspectionTaskCollection;

    public function __construct(
        \M2E\Core\Model\ControlPanel\InspectionTaskCollection $inspectionTaskCollection,
        \M2E\Core\Model\ControlPanel\Inspection\HandlerFactory $handlerFactory,
        \M2E\Core\Model\ControlPanel\Inspection\Response\ResultFactory $resultFactory
    ) {
        $this->handlerFactory = $handlerFactory;
        $this->resultFactory = $resultFactory;
        $this->inspectionTaskCollection = $inspectionTaskCollection;
    }

    public function process(\M2E\Core\Model\ControlPanel\ExtensionInterface $extension, string $taskName): Result
    {
        $definition = $this->inspectionTaskCollection->findTaskForExtension($extension->getModuleName(), $taskName);
        if ($definition === null) {
            return $this->resultFactory->createFailed('Inspection task not found');
        }

        $handler = $this->handlerFactory->create($definition);

        try {
            $issues = $handler->process();
            $result = $this->resultFactory->createSuccess($issues);
        } catch (\Throwable $e) {
            $result = $this->resultFactory->createFailed($e->getMessage());
        }

        return $result;
    }
}
