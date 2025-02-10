<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class ExtensionCollection
{
    /** @var \M2E\Core\Model\ControlPanel\ExtensionInterface[]  */
    private array $extensions;

    /**
     * @param \M2E\Core\Model\ControlPanel\ExtensionInterface[] $extensions
     */
    public function __construct(array $extensions)
    {
        $this->extensions = [];
        foreach ($extensions as $extension) {
            $this->extensions[$extension->getModuleName()] = $extension;
        }
    }

    /**
     * @return \M2E\Core\Model\ControlPanel\ExtensionInterface[]
     */
    public function getAll(): array
    {
        return array_values($this->extensions);
    }

    public function findByModuleName(string $moduleName): ?\M2E\Core\Model\ControlPanel\ExtensionInterface
    {
        return $this->extensions[$moduleName] ?? null;
    }
}
