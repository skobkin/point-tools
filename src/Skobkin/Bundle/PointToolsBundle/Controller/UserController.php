<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Skobkin\Bundle\PointToolsBundle\Entity\TopUserDTO;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Service\UserApi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @param User $user
     * @ParamConverter("user", class="SkobkinPointToolsBundle:User", options={"login" = "login"})
     */
    public function showAction(User $user)
    {
        $userApi = $this->container->get('skobkin_point_tools.api_user');

        /** @var QueryBuilder $qb */
        $qb = $this->getDoctrine()->getManager()->getRepository('SkobkinPointToolsBundle:User')->createQueryBuilder('u');

        $subscribers = $qb
            ->select('u')
            ->innerJoin('u.subscriptions', 's')
            ->where('s.author = :author')
            ->setParameter('author', $user->getId())
            ->getQuery()->getResult()
        ;

        $qb = $this->getDoctrine()->getManager()->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->createQueryBuilder('se');

        $subscriptionsEvents = $qb
            ->select()
            ->where('se.author = :author')
            ->orderBy('se.date', 'desc')
            ->setMaxResults(10)
            ->setParameter('author', $user)
            ->getQuery()->getResult()
        ;

        return $this->render('SkobkinPointToolsBundle:User:show.html.twig', [
            'user' => $user,
            'subscribers' => $subscribers,
            'log' => $subscriptionsEvents,
            'avatar_url' => $userApi->getAvatarUrl($user, UserApi::AVATAR_SIZE_LARGE),
        ]);
    }

    public function topAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->getRepository('SkobkinPointToolsBundle:Subscription')->createQueryBuilder('s');

        /** @var TopUserDTO[] $topUsers */
        $topUsers = $qb
            ->select(['COUNT(s.subscriber) as cnt', 'NEW SkobkinPointToolsBundle:TopUserDTO(a.login, COUNT(s.subscriber))'])
            ->innerJoin('s.author', 'a')
            ->orderBy('cnt', 'desc')
            ->groupBy('a.id')
            ->setMaxResults(30)
            ->getQuery()->getResult()
        ;

        return $this->render('@SkobkinPointTools/User/top.html.twig', [
            'top_users' => $topUsers
        ]);
    }

    /**
     * @param Request $request
     */
    public function searchUserAction(Request $request)
    {
        $login = $request->request->get('login');

        if (!$login) {
            throw new \InvalidArgumentException('No login information present');
        }
        return $this->redirectToRoute('user_show', ['login' => $login]);
    }
}
