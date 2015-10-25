<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Skobkin\Bundle\PointToolsBundle\Entity\SubscriptionEvent;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    /**
     * Returns last user subscribers log
     *
     * @param $login
     * @ParamConverter("user", class="SkobkinPointToolsBundle:User")
     * @return Response
     */
    public function lastUserSubscribersByIdAction(User $user)
    {
        $qb = $this->getDoctrine()->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->createQueryBuilder('se');
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
