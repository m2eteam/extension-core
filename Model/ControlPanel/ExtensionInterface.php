<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

interface ExtensionInterface
{
    public function getIdentifier(): string;
    public function getModule(): \M2E\Core\Model\ModuleInterface;
    public function getModuleName(): string;
    public function getModuleTitle(): string;
}
