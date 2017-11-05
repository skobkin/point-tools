<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Knp\Component\Pager\Paginator;
use Skobkin\Bundle\PointToolsBundle\Repository\SubscriptionEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\{Request, Response};

class EventsController extends Controller
{
    public function lastAction(Request $request, SubscriptionEventRepository $eventRepository, Paginator $paginator): Response
    {
        $eventsPagination = $paginator->paginate(
            $eventRepository->createLastSubscriptionEventsQuery(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('SkobkinPointToolsBundle:Events:last.html.twig', [
            'last_events' => $eventsPagination,
        ]);
    }
}
