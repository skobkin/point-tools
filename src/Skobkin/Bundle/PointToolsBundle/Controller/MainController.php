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

        return $this->render('SkobkinPointToolsBundle:Main:index.html.twig', [
            'users_count' => $em->getRepository('SkobkinPointToolsBundle:User')->getUsersCount(),
            'subscribers_count' => $em->getRepository('SkobkinPointToolsBundle:Subscription')->getUserSubscribersCountById($this->container->getParameter('point_id')),
            'events_count' => $em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->getLastDayEventsCount(),
            'service_login' => $this->container->getParameter('point_login'),
        ]);
    }
}
