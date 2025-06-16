<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard\Sales;

interface DataProviderInterface
{
    /**
     * @return Point[]
     */
    public function getAmountPointsFor24Hours(): array;

    /**
     * @return Point[]
     */
    public function getQtyPointsFor24Hours(): array;

    /**
     * @return Point[]
     */
    public function getAmountPointsFor7Days(): array;

    /**
     * @return Point[]
     */
    public function getQtyPointsFor7Days(): array;

    /**
     * @return Point[]
     */
    public function getAmountPointsForToday(): array;

    /**
     * @return Point[]
     */
    public function getQtyPointsForToday(): array;
}
