<?php

declare(strict_types=1);

namespace M2E\Core\Model\Dashboard;

class DateRangeFactory
{
    public function createForLast7Days(): DateRange
    {
        $dateStart = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateStart->setTime(0, 0);
        $dateStart->modify('-6 days');

        $dateEnd = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateEnd->setTime(23, 59, 59);

        return $this->create($dateStart, $dateEnd);
    }

    public function createForToday(): DateRange
    {
        $dateStart = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateStart->setTime(0, 0);

        $dateEnd = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateEnd->setTime(23, 59, 59);

        return $this->create($dateStart, $dateEnd);
    }

    public function createForTomorrow(): DateRange
    {
        $dateStart = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateStart->setTime(0, 0);
        $dateStart->modify('+1 day');

        $dateEnd = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateEnd->setTime(23, 59, 59);
        $dateEnd->modify('+1 day');

        return $this->create($dateStart, $dateEnd);
    }

    public function createForTwoAndMoreDays(): DateRange
    {
        $dateStart = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateStart->setTime(0, 0);
        $dateStart->modify('+2 day');

        $dateEnd = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateEnd->setTime(23, 59, 59);
        $dateEnd->modify('+1 year');

        return $this->create($dateStart, $dateEnd);
    }

    public function createForYesterday(): DateRange
    {
        $dateStart = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateStart->setTime(0, 0);
        $dateStart->modify('-1 day');

        $dateEnd = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateEnd->setTime(23, 59, 59);
        $dateEnd->modify('-1 day');

        return $this->create($dateStart, $dateEnd);
    }

    public function createFor2DaysAgo(): DateRange
    {
        $dateStart = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateStart->setTime(0, 0);
        $dateStart->modify('-2 days');

        $dateEnd = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateEnd->setTime(23, 59, 59);
        $dateEnd->modify('-2 days');

        return $this->create($dateStart, $dateEnd);
    }

    public function createForLast24Hours(): DateRange
    {
        $dateStart = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateStart->modify('-1 day');

        $dateEnd = \M2E\Core\Helper\Date::createCurrentInCurrentZone();

        return $this->create($dateStart, $dateEnd);
    }

    private function create(\DateTime $dateStart, \DateTime $dateEnd): DateRange
    {
        $gmt = new \DateTimeZone(\M2E\Core\Helper\Date::getTimezone()->getDefaultTimezone());

        $dateStart->setTimezone($gmt);
        $dateEnd->setTimezone($gmt);

        return new DateRange($dateStart, $dateEnd);
    }
}
