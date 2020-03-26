<?php

declare(strict_types=1);

namespace Robwasripped\Charliewfh\DateGenerator;

class DateGenerator
{
    public function getDatesForRange(\DateTimeImmutable $from, \DateTimeImmutable $to, bool $includeWeekends): iterable
    {
        $oneDay = new \DateInterval('P1D');

        for($rangeDate = \DateTime::createFromImmutable($from); $rangeDate <= $to; $rangeDate->add($oneDay)) {
            if(!$includeWeekends && $rangeDate->format('N') > 5) {
                continue;
            }
            $date = \DateTimeImmutable::createFromMutable($rangeDate);
            $date->setTime(0,0,0,0);
            yield $date;
        }
    }
}
