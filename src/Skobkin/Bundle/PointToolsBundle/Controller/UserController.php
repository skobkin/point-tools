<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Skobkin\Bundle\PointToolsBundle\DTO\TopUserDTO;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Service\UserApi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @param string $login
     */
    public function showAction($login)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $em->getRepository('SkobkinPointToolsBundle:User')->findUserByLogin($login);

        if (!$user) {
            throw $this->createNotFoundException('User ' . $login . ' not found.');
        }

        $userApi = $this->container->get('skobkin_point_tools.api_user');

        return $this->render('SkobkinPointToolsBundle:User:show.html.twig', [
            'user' => $user,
            'subscribers' => $em->getRepository('SkobkinPointToolsBundle:User')->findUserSubscribersById($user->getId()),
            'log' => $em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->getUserLastSubscribersEventsById($user, 10),
            'avatar_url' => $userApi->getAvatarUrl($user, UserApi::AVATAR_SIZE_LARGE),
        ]);
    }

    public function topAction()
    {
        $topUsers = $this->getDoctrine()->getManager()->getRepository('SkobkinPointToolsBundle:User')->getTopUsers();

        $topChart = $this->createTopUsersGraph($topUsers);

        return $this->render('@SkobkinPointTools/User/top.html.twig', [
            'top_users' => $topUsers,
            'top_chart' => $topChart,
        ]);
    }

    /**
     * @param Request $request
     */
    public function searchUserAction(Request $request)
    {
        $login = $request->request->get('login');

        if (!$login) {
            return $this->redirectToRoute('index');
        }
        return $this->redirectToRoute('user_show', ['login' => $login]);
    }

    /**
     * @param TopUserDTO[] $topUsers
     * @return Highchart
     */
    private function createTopUsersGraph(array $topUsers = [])
    {
        $translator = $this->container->get('translator');

        $chartData = [
            'titles' => [],
            'subscribers' => [],
        ];

        // Preparing chart data
        foreach ($topUsers as $user) {
            $chartData['titles'][] = $user->login;
            $chartData['subscribers'][] = $user->subscribersCount;
        }

        // Chart
        $series = [[
            'name' => $translator->trans('Subscribers'),
            'data' => $chartData['subscribers'],
        ]];

        // Initializing chart
        $ob = new Highchart();
        $ob->chart->renderTo('top-chart');
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
