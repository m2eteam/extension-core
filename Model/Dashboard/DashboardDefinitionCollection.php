<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard;

class DashboardDefinitionCollection
{
    /** @var \M2E\Core\Model\Dashboard\DashboardDefinitionInterface[]  */
    private array $definitions;

    /**
     * @param \M2E\Core\Model\Dashboard\DashboardDefinitionInterface[] $definitions
     */
    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    public function findByModuleName(string $moduleName): ?\M2E\Core\Model\Dashboard\DashboardDefinitionInterface
    {
        foreach ($this->definitions as $definition) {
            if ($definition->getModuleName() === $moduleName) {
                return $definition;
            }
        }

        return null;
    }
}
