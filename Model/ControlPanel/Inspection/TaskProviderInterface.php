<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Inspection;

interface TaskProviderInterface
{
    public function getExtensionModuleName(): string;
    /**
     * @return \M2E\Core\Model\ControlPanel\InspectionTaskDefinition[]
     */
    public function getTasks(): array;
}
