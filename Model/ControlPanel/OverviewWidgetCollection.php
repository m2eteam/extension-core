<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class OverviewWidgetCollection
{
    /** @var \M2E\Core\Model\ControlPanel\Overview\WidgetProviderInterface[] */
    private array $widgetProviders;

    /**
     * @param \M2E\Core\Model\ControlPanel\Overview\WidgetProviderInterface[] $widgetProviders
     */
    public function __construct(
        array $widgetProviders
    ) {
        $this->widgetProviders = $widgetProviders;
    }

    /**
     * @param string $extensionModuleName
     *
     * @return \M2E\Core\Model\ControlPanel\OverviewWidget[]
     */
    public function getForExtension(string $extensionModuleName): array
    {
        foreach ($this->widgetProviders as $widgetProvider) {
            if ($widgetProvider->getExtensionModuleName() === $extensionModuleName) {
                return $widgetProvider->getWidgets();
            }
        }

        return [];
    }
}
