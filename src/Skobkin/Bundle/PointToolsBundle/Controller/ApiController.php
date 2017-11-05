<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Skobkin\Bundle\PointToolsBundle\Entity\{SubscriptionEvent, User};
use Skobkin\Bundle\PointToolsBundle\Repository\SubscriptionEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /**
     * Returns last user subscribers log
     *
     * @ParamConverter("user", class="SkobkinPointToolsBundle:User")
     */
    public function lastUserSubscribersByIdAction(User $user, SubscriptionEventRepository $subscriptionEventRepository): Response
    {
        $qb = $subscriptionEventRepository->createQueryBuilder('se');
        $qb
            ->select(['se', 'sub'])
            ->innerJoin('se.subscriber', 'sub')
            ->where($qb->expr()->eq('se.author', ':author'))
            ->orderBy('se.date', 'desc')
            ->setParameter('author', $user)
            ->setMaxResults(20)
        ;

        $data = [];

        /** @var SubscriptionEvent $event */
        foreach ($qb->getQuery()->getResult() as $event) {
            $data[] = [
                'user' => $event->getSubscriber()->getLogin(),
                'action' => $event->getAction(),
                'datetime' => $event->getDate()->format('d.m.Y H:i:s'),
            ];
        }

        return $this->json($data);
    }
}
