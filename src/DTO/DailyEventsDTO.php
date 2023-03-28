<?php
declare(strict_types=1);

namespace App\DTO;

/** Events count by day */
class DailyEventsDTO
{
    public readonly \DateTime $date;

    public function __construct(
        string $date,
        public readonly int $eventsCount,
    ) {
        $this->date = new \DateTime($date);
    }
}
