<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Overview;

interface WidgetProviderInterface
{
    public function getExtensionModuleName(): string;

    /**
     * @return \M2E\Core\Model\ControlPanel\OverviewWidget[]
     */
    public function getWidgets(): array;
}
