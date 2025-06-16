<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\ProductIssues;

interface DataProviderInterface
{
    /**
     *
     * @return Issue[]
     */
    public function getTopIssues(): array;
}
