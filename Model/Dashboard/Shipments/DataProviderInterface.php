<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\Shipments;

interface DataProviderInterface
{
    public function getCountOfLateShipments(): int;

    public function getCountOfShipByToday(): int;

    public function getCountOfShipByTomorrow(): int;

    public function getCountOfShipForTwoAndMoreDays(): int;
}
