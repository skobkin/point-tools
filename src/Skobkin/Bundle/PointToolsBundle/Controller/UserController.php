<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Service\UserApi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @param
     * @ParamConverter("user", class="SkobkinPointToolsBundle:User", options={"login" = "login"})
     */
    public function showAction(User $user)
    {
        $userApi = $this->container->get('skobkin_point_tools.api_user');

        /** @var QueryBuilder $qb */
        $qb = $this->getDoctrine()->getManager()->getRepository('SkobkinPointToolsBundle:SubscriptionEvent')->createQueryBuilder('se');

        $subscriptionsEvents = $qb
            ->select()
            ->where('se.author = :author')
            ->orderBy('se.date', 'desc')
            ->setMaxResults(30)
            ->setParameter('author', $user)
            ->getQuery()->getResult()
        ;

        return $this->render('SkobkinPointToolsBundle:User:show.html.twig', [
            'user' => $user,
            'log' => $subscriptionsEvents,
            'avatar_url' => $userApi->getAvatarUrl($user, UserApi::AVATAR_SIZE_LARGE),
        ]);
    }

    public function topAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->getRepository('SkobkinPointToolsBundle:Subscription')->createQueryBuilder('us');


    }
}
