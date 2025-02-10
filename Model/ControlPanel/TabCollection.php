<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class TabCollection
{
    /** @var \M2E\Core\Model\ControlPanel\Tab\ProviderInterface[] */
    private array $providers;

    /**
     * @param \M2E\Core\Model\ControlPanel\Tab\ProviderInterface[] $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @param string $extensionModuleName
     *
     * @return \M2E\Core\Model\ControlPanel\Tab[]
     */
    public function getForExtension(string $extensionModuleName): array
    {
        foreach ($this->providers as $provider) {
            if ($provider->getExtensionModuleName() === $extensionModuleName) {
                return $provider->getTabs();
            }
        }

        return [];
    }
}
