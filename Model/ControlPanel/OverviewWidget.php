<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class OverviewWidget
{
    public const FIRST_COLUMN = 1;
    public const SECOND_COLUMN = 2;
    public const THIRD_COLUMN = 3;

    /** @var class-string<\M2E\Core\Block\Adminhtml\ControlPanel\Widget\AbstractWidget> */
    private string $className;
    private array $widgetData;
    private int $column;

    /**
     * @param class-string<\M2E\Core\Block\Adminhtml\ControlPanel\Widget\AbstractWidget> $className
     * @param int $column
     * @param array $widgetData
     */
    public function __construct(
        string $className,
        int $column,
        array $widgetData = []
    ) {
        $this->validateClassName($className);
        $this->className = $className;
        $this->column = $column;
        $this->widgetData = $widgetData;
    }

    /**
     * @return class-string<\M2E\Core\Block\Adminhtml\ControlPanel\Widget\AbstractWidget>
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    public function getWidgetData(): array
    {
        return $this->widgetData;
    }

    public function getColumn(): int
    {
        return $this->column;
    }

    private function validateClassName(string $className): void
    {
        if (!is_subclass_of($className, \M2E\Core\Block\Adminhtml\ControlPanel\Widget\AbstractWidget::class)) {
            throw new \LogicException("Class $className is not a subclass of AbstractWidget.");
        }
    }
}
