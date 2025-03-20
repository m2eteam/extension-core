<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\CoreExtension;

class Extension implements \M2E\Core\Model\ControlPanel\ExtensionInterface
{
    public const NAME = 'm2e_core';

    private \M2E\Core\Model\Module $module;

    public function __construct(
        \M2E\Core\Model\Module $module
    ) {
        $this->module = $module;
    }

    public function getIdentifier(): string
    {
        return \M2E\Core\Helper\Module::IDENTIFIER;
    }

    public function getModule(): \M2E\Core\Model\ModuleInterface
    {
        return $this->module;
    }

    public function getModuleName(): string
    {
        return self::NAME;
    }

    public function getModuleTitle(): string
    {
        return 'M2E Core';
    }
}
