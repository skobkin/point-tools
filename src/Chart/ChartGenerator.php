<?php
declare(strict_types=1);

namespace App\Chart;

use App\DTO\DailyEventsDTO;
use App\DTO\TopUserDTO;
use Ghunti\HighchartsPHP\Highchart;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChartGenerator
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param DailyEventsDTO[] $events
     */
    public function eventsDynamicChart(array $events): Highchart
    {
        $data = [];

        foreach ($events as $event) {
            $data[$event->date->format('d.m')] = $event->eventsCount;
        }

        return $this->createChart('line', $data, 'Events by day', 'amount');
    }

    /**
     * @param TopUserDTO[] $topUsers
     */
    public function topUsersChart(array $topUsers): Highchart
    {
        $data = [];

        foreach ($topUsers as $topUser) {
            $data[$topUser->login] = $topUser->subscribersCount;
        }

        return $this->createChart('bar', $data, 'Top users', 'amount');
    }

    /** @see https://github.com/ghunti/HighchartsPHP/blob/master/demos/highcharts/line/basic_line.php */
    private function createChart(
        string $type,
        array $data,
        string $bottomLabel,
        string $amountLabel,
    ): Highchart {
        $c = new Highchart();

        $c->chart->type = $type;

        $c->title->text = $this->translator->trans($bottomLabel);

        // Preparing chart data
        foreach ($data as $key => $value) {
            $chartData['keys'][] = $key;
            $chartData['values'][] = $value;
        }

        $c->xAxis->title = ['text' => null];
        $c->xAxis->categories = $chartData['keys'] ?? [];

        $c->yAxis->title->text = $this->translator->trans($amountLabel);
        $c->yAxis->plotOptions->bar->dataLabels->enabled = true;

        $c->series[] = [
            'name' => $this->translator->trans($amountLabel),
            'data' => $chartData['values'] ?? [],
        ];

        return $c;
    }
}