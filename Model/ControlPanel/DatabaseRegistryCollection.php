<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class DatabaseRegistryCollection
{
    /** @var \M2E\Core\Model\ControlPanel\Database\RegistryInterface[] */
    private array $registry;

    /**
     * @param \M2E\Core\Model\ControlPanel\Database\RegistryInterface[] $registry
     */
    public function __construct(array $registry)
    {
        $this->registry = $registry;
    }

    public function getForExtension(
        string $extensionModuleName
    ): \M2E\Core\Model\ControlPanel\Database\RegistryInterface {
        foreach ($this->registry as $registry) {
            if ($registry->getExtensionModuleName() === $extensionModuleName) {
                return $registry;
            }
        }

        throw new \RuntimeException('Database registry not found for extension ' . $extensionModuleName);
    }
}
