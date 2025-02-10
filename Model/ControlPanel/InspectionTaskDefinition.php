<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class InspectionTaskDefinition
{
    public const GROUP_PRODUCTS = 'products';
    public const GROUP_STRUCTURE = 'structure';
    public const GROUP_GENERAL = 'general';

    public const EXECUTION_SPEED_SLOW = 'slow';
    public const EXECUTION_SPEED_FAST = 'fast';

    private string $nick;
    private string $title;
    private string $description;
    private string $group;
    private string $executionSpeedGroup;
    private string $handler;

    public function __construct(
        string $nick,
        string $title,
        string $description,
        string $group,
        string $executionSpeedGroup,
        string $handler
    ) {
        $this->nick = $nick;
        $this->title = $title;
        $this->description = $description;
        $this->group = $group;
        $this->executionSpeedGroup = $executionSpeedGroup;
        $this->handler = $handler;
    }

    public function getNick(): string
    {
        return $this->nick;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getExecutionSpeedGroup(): string
    {
        return $this->executionSpeedGroup;
    }

    public function getHandler(): string
    {
        return $this->handler;
    }
}
