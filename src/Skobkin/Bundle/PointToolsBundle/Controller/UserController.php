<?php

namespace Skobkin\Bundle\PointToolsBundle\Controller;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function showAction($login)
    {
        return $this->render('SkobkinPointToolsBundle:User:show.html.twig', []);
    }

    public function topAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var QueryBuilder $qb */
        $qb = $em->getRepository('SkobkinPointToolsBundle:Subscription')->createQueryBuilder('us');


    }
}
