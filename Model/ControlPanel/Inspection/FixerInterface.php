<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Inspection;

interface FixerInterface
{
    public function fix(array $data): void;
}
