<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO;

/**
 * Events count by day
 */
class DailyEvents
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var int
     */
    private $eventsCount;

    public function __construct(string $date, int $eventsCount)
    {
        $this->date = new \DateTime($date);
        $this->eventsCount = $eventsCount;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getEventsCount(): int
    {
        return $this->eventsCount;
    }
}