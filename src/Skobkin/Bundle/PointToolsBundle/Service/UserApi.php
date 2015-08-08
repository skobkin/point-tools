<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Guzzle\Service\Client;
use Skobkin\Bundle\PointToolsBundle\Entity\User;

/**
 * Basic Point.im user API functions from /api/user/*
 */
class UserApi extends AbstractApi
{
    const PATH_USER_INFO = '/api/user/%s';
    const PATH_USER_SUBSCRIPTIONS = '/api/user/%s/subscriptions';
    const PATH_USER_SUBSCRIBERS = '/api/user/%s/subscribers';

    const AVATAR_SIZE_SMALL = '24';
    const AVATAR_SIZE_MEDIUM = '40';
    const AVATAR_SIZE_LARGE = '80';

    /**
     * @var string Base URL for user avatars
     */
    protected $avatarsBaseUrl = 'point.im/avatar/';

    /**
     * @var EntityManager
     */
    protected $em;


    public function __construct(Client $httpClient, $https = true, $baseUrl = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($httpClient, $https, $baseUrl);

        $this->em = $entityManager;
    }

    public function getName()
    {
        return 'skobkin_point_tools_api_user';
    }

    /**
     * Get user subscribers by user login
     *
     * @param string $login
     * @return User[]
     */
    public function getUserSubscribersByLogin($login)
    {
        $usersList = $this->getGetRequestData('/api/user/' . $login . '/subscribers', [], true);

        $users = $this->getUsersFromList($usersList);

        return $users;
    }

    /**
     * Get user subscribers by user id
     *
     * @param int $id
     * @return User[]
     */
    public function getUserSubscribersById($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('$id must be an integer');
        }

        $usersList = $this->getGetRequestData('/api/user/id/' . (int) $id . '/subscribers', [], true);

        $users = $this->getUsersFromList($usersList);

        return $users;
    }

    /**
     * Get user subscriptions by user login
     *
     * @param string $login
     * @return User[]
     */
    public function getUserSubscriptionsByLogin($login)
    {
        $usersList = $this->getGetRequestData('/api/user/' . $login . '/subscriptions', [], true);

        $users = $this->getUsersFromList($usersList);

        return $users;
    }

    /**
     * Get user subscriptions by user id
     *
     * @param int $id
     * @return User[]
     */
    public function getUserSubscriptionsById($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('$id must be an integer');
        }

        $usersList = $this->getGetRequestData('/api/user/id/' . (int) $id . '/subscriptions', [], true);

        $users = $this->getUsersFromList($usersList);

        return $users;
    }

    /**
     * @return User[]
     */
    private function getUsersFromList(array $users = [])
    {
        if (!is_array($users)) {
            throw new \InvalidArgumentException('$users must be an array');
        }

        /** @var EntityRepository $userRepo */
        $userRepo = $this->em->getRepository('SkobkinPointToolsBundle:User');

        $resultUsers = [];

        foreach ($users as $userData) {
            if (array_key_exists('id', $userData) && array_key_exists('login', $userData) && array_key_exists('name', $userData) && is_numeric($userData['id'])) {

                // @todo Optimize with prehashed id's list
                $user = $userRepo->findOneBy(['id' => $userData['id']]);

                if (!$user) {
                    $user = new User();
                    $user->setId((int) $userData['id']);
                    $this->em->persist($user);
                }

                // Updating data
                if ($user->getLogin() !== $userData['login']) {
                    $user->setLogin($userData['login']);
                }
                if ($user->getName() !== $userData['name']) {
                    $user->setName($userData['name']);
                }

                $resultUsers[] = $user;
            }
        }

        $this->em->flush();

        return $resultUsers;
    }

    /**
     * @param $login
     */
    public function getAvatarUrl(User $user, $size)
    {
        return ($this->useHttps ? 'https://' : 'http://') . $this->avatarsBaseUrl . $user->getLogin() . '/' . $size;
    }
}
