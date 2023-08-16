<?php
declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\{SubscriptionEvent, User};
use App\Repository\SubscriptionEventRepository;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};

class ApiController
{
    /**
     * Returns last user subscribers log
     *
     * @ParamConverter("user", class="SkobkinPointToolsBundle:User")
     */
    public function lastUserSubscribersById(User $user, SubscriptionEventRepository $subscriptionEventRepository): Response
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

        return new JsonResponse($data);
    }
}
