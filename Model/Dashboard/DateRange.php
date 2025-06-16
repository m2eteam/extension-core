<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard;

class DateRange
{
    public \DateTime $dateStart;
    public \DateTime $dateEnd;

    public function __construct(\DateTime $dateStart, \DateTime $dateEnd)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
    }
}
