<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class ModuleToolsTab
{
    private string $route;
    private string $id;
    private string $label;
    private string $controllerClass;

    public function __construct(
        string $id,
        string $label,
        string $route,
        string $controllerClass
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->route = $route;
        $this->controllerClass = $controllerClass;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }
}
