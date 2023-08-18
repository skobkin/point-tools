<?php
declare(strict_types=1);

namespace App\Controller;

use App\Chart\ChartGenerator;
use App\Repository\{SubscriptionEventRepository, UserRepository};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class StatsController extends AbstractController
{
    public function show(
        UserRepository $userRepository,
        SubscriptionEventRepository $subscriptionEventRepository,
        ChartGenerator $chartGenerator,
    ): Response {
        $topUsers = $userRepository->getTopUsersBySubscribersCount();
        $eventsByDay = $subscriptionEventRepository->getLastEventsByDay();

        return $this->render('Web/User/top.html.twig', [
            'events_dynamic_chat' => $chartGenerator->eventsDynamicChart($eventsByDay),
            'top_chart' => $chartGenerator->topUsersChart($topUsers),
        ]);
    }
}
