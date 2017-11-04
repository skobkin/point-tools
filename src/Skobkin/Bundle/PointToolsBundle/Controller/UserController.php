<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Skobkin\Bundle\PointToolsBundle\DTO\{DailyEvents, TopUserDTO};
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\HttpFoundation\{Request, Response};

class UserController extends Controller
{
    /**
     * @param string $login
     */
    public function showAction(Request $request, string $login): Response
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository('SkobkinPointToolsBundle:User')->findUserByLogin($login);

        if (!$user) {
            throw $this->createNotFoundException('User ' . $login . ' not found.');
        }

        $paginator = $this->get('knp_paginator');

        $subscriberEventsPagination = $paginator->paginate(
            $em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->createUserLastSubscribersEventsQuery($user),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('SkobkinPointToolsBundle:User:show.html.twig', [
            'user' => $user,
            'subscribers' => $em->getRepository('SkobkinPointToolsBundle:User')->findUserSubscribersById($user->getId()),
            'subscriptions_log' => $subscriberEventsPagination,
            'rename_log' => $em->getRepository('SkobkinPointToolsBundle:UserRenameEvent')->findBy(['user' => $user], ['date' => 'DESC'], 10),
        ]);
    }

    public function topAction(): Response
    {
        $userRepo = $this->getDoctrine()->getManager()->getRepository('SkobkinPointToolsBundle:User');
        $subscriptionsRecordsRepo = $this->getDoctrine()->getManager()->getRepository('SkobkinPointToolsBundle:SubscriptionEvent');
        $topUsers = $userRepo->getTopUsers();
        $eventsByDay = $subscriptionsRecordsRepo->getLastEventsByDay();

        return $this->render('@SkobkinPointTools/User/top.html.twig', [
            'events_dynamic_chat' => $this->createEventsDynamicChart($eventsByDay),
            'top_chart' => $this->createTopUsersGraph($topUsers),
        ]);
    }

    /**
     * @todo move to the service
     *
     * @param DailyEvents[] $eventsByDay
     */
    private function createEventsDynamicChart(array $eventsByDay = []): Highchart
    {
        $data = [];

        foreach ($eventsByDay as $dailyEvents) {
            $data[$dailyEvents->getDate()->format('d.m')] = $dailyEvents->getEventsCount();
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
            $data[$topUser->getLogin()] = $topUser->getSubscribersCount();
        }

        return $this->createChart('topchart', 'bar', $data, 'Top users', 'amount');
    }

    private function createChart(string $blockId, string $type, array $data, string $bottomLabel, string $amountLabel): Highchart
    {
        $translator = $this->get('translator');

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
            'name' => $translator->trans($amountLabel),
            'data' => $chartData['values'],
        ]];

        // Initializing chart
        $ob = new Highchart();
        $ob->chart->renderTo($blockId);
        $ob->chart->type($type);
        $ob->title->text($translator->trans($bottomLabel));
        $ob->xAxis->title(['text' => null]);
        $ob->xAxis->categories($chartData['keys']);
        $ob->yAxis->title(['text' => $translator->trans($amountLabel)]);
        $ob->plotOptions->bar([
            'dataLabels' => [
                'enabled' => true
            ]
        ]);
        $ob->series($series);

        return $ob;
    }
}
