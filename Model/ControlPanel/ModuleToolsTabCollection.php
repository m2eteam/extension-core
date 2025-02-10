<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class ModuleToolsTabCollection
{
    /** @var \M2E\Core\Model\ControlPanel\ModuleTools\TabProviderInterface[] */
    private array $tabsProviders;

    /**
     * @param \M2E\Core\Model\ControlPanel\ModuleTools\TabProviderInterface[] $tabsProviders
     */
    public function __construct(array $tabsProviders)
    {
        $this->tabsProviders = $tabsProviders;
    }

    /**
     * @param string $extensionModuleName
     *
     * @return \M2E\Core\Model\ControlPanel\ModuleToolsTab[]
     */
    public function getForExtension(string $extensionModuleName): array
    {
        foreach ($this->tabsProviders as $tabsProvider) {
            if ($tabsProvider->getExtensionModuleName() === $extensionModuleName) {
                return $tabsProvider->getTabs();
            }
        }

        return [];
    }
}
