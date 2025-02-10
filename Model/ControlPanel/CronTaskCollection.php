<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class CronTaskCollection
{
    /** @var \M2E\Core\Model\ControlPanel\Cron\TaskProviderInterface[] */
    private array $taskProviders;

    /**
     * @param \M2E\Core\Model\ControlPanel\Cron\TaskProviderInterface[] $taskProviders
     */
    public function __construct(array $taskProviders)
    {
        $this->taskProviders = $taskProviders;
    }

    /**
     * @param string $extensionModuleName
     *
     * @return \M2E\Core\Model\ControlPanel\CronTask[]
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
}
