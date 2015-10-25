<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EventsController extends Controller
{
    public function lastAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        return $this->render('SkobkinPointToolsBundle:Events:last.html.twig', [
            'last_events' => $em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->getLastSubscriptionEvents(20),
        ]);
    }
}
