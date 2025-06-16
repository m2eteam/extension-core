<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\Sales;

interface DefinitionInterface
{
    public function getDataProvider(): DataProviderInterface;
}
