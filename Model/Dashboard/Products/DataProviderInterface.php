<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\Products;

interface DataProviderInterface
{
    public function getCountOfListedProducts(): int;

    public function getCountOfNotListedProducts(): int;
}
