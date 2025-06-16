<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\Errors;

interface DataProviderInterface
{
    public function getCountErrorsForToday(): int;

    public function getCountErrorsForYesterday(): int;

    public function getCountErrorsFor2DaysAgo(): int;

    public function getTotalErrorsCount(): int;
}
