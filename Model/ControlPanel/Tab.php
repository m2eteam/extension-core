<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class Tab
{
    /** @var class-string<\M2E\Core\Block\Adminhtml\ControlPanel\Tab\AbstractTab> */
    private string $className;
    private array $arguments;
    private string $route;
    private bool $isAjax;

    /**
     * @param class-string<\M2E\Core\Block\Adminhtml\ControlPanel\Tab\AbstractTab> $className
     * @param string $route
     * @param array $arguments
     * @param bool $isAjax
     * @throws \Exception
     */
    public function __construct(
        string $className,
        string $route,
        array  $arguments = [],
        bool   $isAjax = false
    ) {
        $this->validateClassName($className);
        $this->className = $className;
        $this->arguments = $arguments;
        $this->route = $route;
        $this->isAjax = $isAjax;
    }

    /**
     * @return class-string<\M2E\Core\Block\Adminhtml\ControlPanel\Tab\AbstractTab>
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function isAjax(): bool
    {
        return $this->isAjax;
    }

    private function validateClassName(string $className): void
    {
        if (!is_subclass_of($className, \M2E\Core\Block\Adminhtml\ControlPanel\Tab\AbstractTab::class)) {
            throw new \LogicException("Class $className is not a subclass of AbstractTab.");
        }
    }
}
