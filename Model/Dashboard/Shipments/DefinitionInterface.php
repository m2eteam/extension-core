<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\Shipments;

interface DefinitionInterface
{
    public function getDataProvider(): DataProviderInterface;

    public function getInfoProvider(): InfoProviderInterface;
}
