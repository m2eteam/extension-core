<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\ModuleTools;

interface TabProviderInterface
{
    public function getExtensionModuleName(): string;

    /**
     * @return \M2E\Core\Model\ControlPanel\ModuleToolsTab[]
     */
    public function getTabs(): array;
}
