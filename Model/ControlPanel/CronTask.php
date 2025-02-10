<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel;

class CronTask
{
    public string $group;
    public string $nick;
    public string $code;

    public function __construct(
        string $group,
        string $nick,
        string $code
    ) {
        $this->group = $group;
        $this->nick = $nick;
        $this->code = $code;
    }
}
