<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\{SubscriptionEventRepository, UserRenameEventRepository, UserRepository};
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};

class UserController extends AbstractController
{
    public function show(
        Request $request,
        string $login,
        SubscriptionEventRepository $subscriptionEventRepository,
        UserRepository $userRepository,
        UserRenameEventRepository $renameEventRepository,
        PaginatorInterface $paginator,
    ): Response {
        /** @var User $user */
        $user = $userRepository->findUserByLogin($login);

        if (!$user) {
            throw $this->createNotFoundException('User ' . $login . ' not found.');
        }

        $subscriberEventsPagination = $paginator->paginate(
            $subscriptionEventRepository->createUserLastSubscribersEventsQuery($user),
            $request->query->getInt('page', 1),
            10,
        );

        return $this->render('Web/User/show.html.twig', [
            'user' => $user,
            'subscribers' => $userRepository->findUserSubscribersById($user->getId()),
            'subscriptions_log' => $subscriberEventsPagination,
            'rename_log' => $renameEventRepository->findBy(['user' => $user], ['date' => 'DESC'], 10),
        ]);
    }
}
