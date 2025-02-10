<?php

declare(strict_types=1);

namespace M2E\Core\Model\ControlPanel\Inspection;

interface InspectorInterface
{
    /**
     * @return \M2E\Core\Model\ControlPanel\Inspection\Issue[]
     */
    public function process(): array;
}
