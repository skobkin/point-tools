<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->getRepository('SkobkinPointToolsBundle:User')->createQueryBuilder('u');

        // All users in the system count
        $usersCount = $qb->select('COUNT(u)')->getQuery()->getSingleScalarResult();

        $qb = $em->getRepository('SkobkinPointToolsBundle:Subscription')->createQueryBuilder('s');

        // Service subscribers count
        $subscribersCount = $qb
            ->select('COUNT(s)')
            ->innerJoin('s.author', 'a')
            ->where('a.login = :login')
            ->setParameter('login', $this->container->getParameter('point_login'))
            ->getQuery()->getSingleScalarResult()
        ;

        $qb = $em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->createQueryBuilder('se');

        $now = new \DateTime();

        $eventsCount = $qb
            ->select('COUNT(se)')
            ->where('se.date > :time')
            ->setParameter('time', $now->sub(new \DateInterval('PT24H')))
            ->getQuery()->getSingleScalarResult()
        ;

        return $this->render('SkobkinPointToolsBundle:Main:index.html.twig', [
            'users_count' => $usersCount,
            'subscribers_count' => $subscribersCount,
            'events_count' => $eventsCount,
            'service_login' => $this->container->getParameter('point_login'),
        ]);
    }

}
