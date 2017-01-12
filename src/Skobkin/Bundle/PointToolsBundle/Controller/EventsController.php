<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventsController extends Controller
{
    public function lastAction(Request $request): Response
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $paginator = $this->get('knp_paginator');

        $eventsPagination = $paginator->paginate(
            $em->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->createLastSubscriptionEventsQuery(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('SkobkinPointToolsBundle:Events:last.html.twig', [
            'last_events' => $eventsPagination,
        ]);
    }
}
