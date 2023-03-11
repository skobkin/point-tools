<?php

namespace src\PointToolsBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use src\PointToolsBundle\Repository\SubscriptionEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};

class EventsController extends AbstractController
{
    public function lastAction(Request $request, SubscriptionEventRepository $eventRepository, PaginatorInterface $paginator): Response
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
