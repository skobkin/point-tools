<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\{TopUserDTO, DailyEventsDTO};
use Knp\Component\Pager\PaginatorInterface;
use Ob\HighchartsBundle\Highcharts\Highchart;
use App\Entity\User;
use App\Repository\{SubscriptionEventRepository, UserRenameEventRepository, UserRepository};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function showAction(
        Request $request,
        string $login,
        SubscriptionEventRepository $subscriptionEventRepository,
        UserRepository $userRepository,
        UserRenameEventRepository $renameEventRepository,
        PaginatorInterface $paginator
    ): Response {
        /** @var User $user */
        $user = $userRepository->findUserByLogin($login);

        if (!$user) {
            throw $this->createNotFoundException('User ' . $login . ' not found.');
        }

        $subscriberEventsPagination = $paginator->paginate(
            $subscriptionEventRepository->createUserLastSubscribersEventsQuery($user),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('SkobkinPointToolsBundle:User:show.html.twig', [
            'user' => $user,
            'subscribers' => $userRepository->findUserSubscribersById($user->getId()),
            'subscriptions_log' => $subscriberEventsPagination,
            'rename_log' => $renameEventRepository->findBy(['user' => $user], ['date' => 'DESC'], 10),
        ]);
    }

    public function topAction(UserRepository $userRepository, SubscriptionEventRepository $subscriptionEventRepository): Response
    {
        $topUsers = $userRepository->getTopUsers();
        $eventsByDay = $subscriptionEventRepository->getLastEventsByDay();

        return $this->render('@SkobkinPointTools/User/top.html.twig', [
            'events_dynamic_chat' => $this->createEventsDynamicChart($eventsByDay),
            'top_chart' => $this->createTopUsersGraph($topUsers),
        ]);
    }

    /**
     * @param DailyEventsDTO[] $eventsByDay
     *@todo move to the service
     *
     */
    private function createEventsDynamicChart(array $eventsByDay = []): Highchart
    {
        $data = [];

        foreach ($eventsByDay as $dailyEvents) {
            $data[$dailyEvents->date->format('d.m')] = $dailyEvents->eventsCount;
        }

        return $this->createChart('eventschart', 'line', $data, 'Events by day', 'amount');
    }

    /**
     * @todo move to the service
     *
     * @param TopUserDTO[] $topUsers
     */
    private function createTopUsersGraph(array $topUsers = []): Highchart
    {
        $data = [];

        foreach ($topUsers as $topUser) {
            $data[$topUser->login] = $topUser->subscribersCount;
        }

        return $this->createChart('topchart', 'bar', $data, 'Top users', 'amount');
    }

    private function createChart(string $blockId, string $type, array $data, string $bottomLabel, string $amountLabel): Highchart
    {
        $chartData = [
            'keys' => [],
            'values' => [],
        ];

        // Preparing chart data
        foreach ($data as $key => $value) {
            $chartData['keys'][] = $key;
            $chartData['values'][] = $value;
        }

        // Chart
        $series = [[
            'name' => $this->translator->trans($amountLabel),
            'data' => $chartData['values'],
        ]];

        // Initializing chart
        $ob = new Highchart();
        $ob->chart->renderTo($blockId);
        $ob->chart->type($type);
        $ob->title->text($this->translator->trans($bottomLabel));
        $ob->xAxis->title(['text' => null]);
        $ob->xAxis->categories($chartData['keys']);
        $ob->yAxis->title(['text' => $this->translator->trans($amountLabel)]);
        $ob->plotOptions->bar([
            'dataLabels' => [
                'enabled' => true
            ]
        ]);
        $ob->series($series);

        return $ob;
    }
}
