<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\Shipments;

interface InfoProviderInterface
{
    public function getUrlForLateShipments(): string;

    public function getUrlForShipByToday(): string;

    public function getUrlForShipByTomorrow(): string;

    public function getUrlForTwoAndMoreDays(): string;
}
