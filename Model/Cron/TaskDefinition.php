<?php

declare(strict_types=1);

namespace M2E\Core\Model\Cron;

class TaskDefinition
{
    private string $nick;
    private int $intervalInSeconds;
    /** @var class-string<\M2E\Core\Model\Cron\TaskHandlerInterface> */
    private string $handlerClass;
    private string $group;

    /**
     * @param string $group
     * @param string $nick
     * @param int $intervalInSeconds
     * @param class-string<\M2E\Core\Model\Cron\TaskHandlerInterface> $handlerClass
     */
    public function __construct(
        string $group,
        string $nick,
        int $intervalInSeconds,
        string $handlerClass
    ) {
        $this->validateHandlerClass($handlerClass);

        $this->group = $group;
        $this->nick = $nick;
        $this->intervalInSeconds = $intervalInSeconds;
        $this->handlerClass = $handlerClass;
    }

    private function validateHandlerClass(string $handlerClass): void
    {
        if (!is_a($handlerClass, \M2E\Core\Model\Cron\TaskHandlerInterface::class, true)) {
            throw new \LogicException('Cron task handler must implement TaskHandlerInterface');
        }
    }

    // ----------------------------------------

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getNick(): string
    {
        return $this->nick;
    }

    public function getIntervalInSeconds(): int
    {
        return $this->intervalInSeconds;
    }

    /**
     * @return class-string<\M2E\Core\Model\Cron\TaskHandlerInterface>
     */
    public function getHandlerClass(): string
    {
        return $this->handlerClass;
    }
}
