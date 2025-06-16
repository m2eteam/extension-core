<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\Errors;

interface InfoProviderInterface
{
    public function getUrlForToday(): string;

    public function getUrlForYesterday(): string;

    public function getUrlFor2DaysAgo(): string;

    public function getUrlForTotal(): string;
}
