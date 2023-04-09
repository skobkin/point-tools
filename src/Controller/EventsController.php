<?php
declare(strict_types=1);

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use App\Repository\SubscriptionEventRepository;
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

        return $this->render('Web/Events/last.html.twig', [
            'last_events' => $eventsPagination,
        ]);
    }
}
