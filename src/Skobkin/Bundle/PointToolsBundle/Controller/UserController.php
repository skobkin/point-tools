<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Skobkin\Bundle\PointToolsBundle\DTO\TopUserDTO;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Service\UserApi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        $userApi = $this->get('app.point.api_user');

        return $this->render('SkobkinPointToolsBundle:User:show.html.twig', [
            'user' => $user,
            'subscribers' => $em->getRepository('SkobkinPointToolsBundle:User')->findUserSubscribersById($user->getId()),
            'subscriptions_log' => $subscriberEventsPagination,
            'rename_log' => $em->getRepository('SkobkinPointToolsBundle:UserRenameEvent')->findBy(['user' => $user], ['date' => 'DESC'], 10),
            'avatar_url' => $userApi->getAvatarUrl($user, UserApi::AVATAR_SIZE_LARGE),
        ]);
    }

    public function topAction(): Response
    {
        $topUsers = $this->getDoctrine()->getManager()->getRepository('SkobkinPointToolsBundle:User')->getTopUsers();

        $topChart = $this->createTopUsersGraph($topUsers);

        return $this->render('@SkobkinPointTools/User/top.html.twig', [
            'top_users' => $topUsers,
            'top_chart' => $topChart,
        ]);
    }

    /**
     * @todo move to the service
     *
     * @param TopUserDTO[] $topUsers
     *
     * @return Highchart
     */
    private function createTopUsersGraph(array $topUsers = []): Highchart
    {
        $translator = $this->get('translator');

        $chartData = [
            'titles' => [],
            'subscribers' => [],
        ];

        // Preparing chart data
        foreach ($topUsers as $user) {
            $chartData['titles'][] = $user->getLogin();
            $chartData['subscribers'][] = $user->getSubscribersCount();
        }

        // Chart
        $series = [[
            'name' => $translator->trans('Subscribers'),
            'data' => $chartData['subscribers'],
        ]];

        // Initializing chart
        $ob = new Highchart();
        $ob->chart->renderTo('topchart');
        $ob->chart->type('bar');
        $ob->title->text($translator->trans('Top users'));
        $ob->xAxis->title(['text' => null]);
        $ob->xAxis->categories($chartData['titles']);
        $ob->yAxis->title(['text' => $translator->trans('amount')]);
        $ob->plotOptions->bar([
            'dataLabels' => [
                'enabled' => true
            ]
        ]);
        $ob->series($series);

        return $ob;
    }
}
