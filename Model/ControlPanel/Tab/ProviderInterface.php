<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Tab;

interface ProviderInterface
{
    public function getExtensionModuleName(): string;

    /**
     * @return \M2E\Core\Model\ControlPanel\Tab[]
     */
    public function getTabs(): array;
}
