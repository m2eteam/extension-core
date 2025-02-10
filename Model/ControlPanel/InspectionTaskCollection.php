<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class InspectionTaskCollection
{
    /** @var \M2E\Core\Model\ControlPanel\Inspection\TaskProviderInterface[] */
    private array $taskProviders;

    /**
     * @param \M2E\Core\Model\ControlPanel\Inspection\TaskProviderInterface[] $taskProviders
     */
    public function __construct(array $taskProviders)
    {
        $this->taskProviders = $taskProviders;
    }

    /**
     * @param string $extensionModuleName
     *
     * @return \M2E\Core\Model\ControlPanel\InspectionTaskDefinition[]
     */
    public function getTasksForExtension(string $extensionModuleName): array
    {
        foreach ($this->taskProviders as $taskProvider) {
            if ($taskProvider->getExtensionModuleName() !== $extensionModuleName) {
                continue;
            }

            return $taskProvider->getTasks();
        }

        return [];
    }

    public function findTaskForExtension(
        string $extensionName,
        string $taskName
    ): ?\M2E\Core\Model\ControlPanel\InspectionTaskDefinition {
        foreach ($this->getTasksForExtension($extensionName) as $taskDefinition) {
            if ($taskDefinition->getNick() === $taskName) {
                return $taskDefinition;
            }
        }

        return null;
    }
}
