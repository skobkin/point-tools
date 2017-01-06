<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Service\Client;
use JMS\Serializer\Serializer;
use Skobkin\Bundle\PointToolsBundle\DTO\Api\Auth;
use Skobkin\Bundle\PointToolsBundle\Entity\User;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\ApiException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Basic Point.im user API functions from /api/user/*
 */
class UserApi extends AbstractApi
{
    const AVATAR_SIZE_SMALL = '24';
    const AVATAR_SIZE_MEDIUM = '40';
    const AVATAR_SIZE_LARGE = '80';

    /**
     * @var string Base URL for user avatars
     */
    protected $avatarsBaseUrl = '//point.im/avatar/';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $userRepository;

    /**
     * @var Serializer
     */
    private $serializer;


    public function __construct(Client $httpClient, $https = true, $baseUrl = null, EntityManager $entityManager, Serializer $serializer)
    {
        parent::__construct($httpClient, $https, $baseUrl);

        $this->em = $entityManager;
        $this->serializer = $serializer;
        $this->userRepository = $this->em->getRepository('SkobkinPointToolsBundle:User');
    }

    public function isAuthDataValid(string $login, string $password): bool
    {
        $auth = $this->authenticate($login, $password);

        if (null === $auth->getError() && null !== $auth->getToken()) {
            $this->logout($auth);

            return true;
        }

        return false;
    }

    public function authenticate(string $login, string $password): Auth
    {
        try {
            $authData = $this->getPostRequestData(
                '/api/login',
                [
                    'login' => $login,
                    'password' => $password,
                ]
            );

            return $this->serializer->deserialize($authData, Auth::class, 'json');
        } catch (ClientErrorResponseException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new InvalidResponseException('API method not found', 0, $e);
            } else {
                throw $e;
            }
        }
    }

    public function logout(Auth $auth): bool
    {
        try {
            $this->getPostRequestData('/api/logout', ['csrf_token' => $auth->getCsRfToken()]);

            return true;
        } catch (ClientErrorResponseException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new InvalidResponseException('API method not found', 0, $e);
            } elseif (Response::HTTP_FORBIDDEN === $e->getResponse()->getStatusCode()) {
                return true;
            } else {
                throw $e;
            }
        }
    }

    /**
     * Get user subscribers by user login
     *
     * @param string $login
     * @return User[]
     * @throws ApiException
     * @throws InvalidResponseException
     * @throws UserNotFoundException
     */
    public function getUserSubscribersByLogin($login)
    {
        try {
            $usersList = $this->getGetRequestData('/api/user/'.urlencode($login).'/subscribers', [], true);
        } catch (ClientErrorResponseException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, null, $login);
            } else {
                throw $e;
            }
        }

        return $this->getUsersFromList($usersList);
    }

    /**
     * Get user subscribers by user id
     *
     * @param $id
     * @return User[]
     * @throws ApiException
     * @throws InvalidResponseException
     * @throws UserNotFoundException
     */
    public function getUserSubscribersById($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('$id must be an integer');
        }

        try {
            $usersList = $this->getGetRequestData('/api/user/id/'.(int) $id.'/subscribers', [], true);
        } catch (ClientErrorResponseException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, $id);
            } else {
                throw $e;
            }
        }

        return $this->getUsersFromList($usersList);
    }

    /**
     * Get user subscriptions by user login
     *
     * @param string $login
     * @return User[]
     * @throws ApiException
     * @throws InvalidResponseException
     * @throws UserNotFoundException
     */
    public function getUserSubscriptionsByLogin($login)
    {
        try {
            $usersList = $this->getGetRequestData('/api/user/'.urlencode($login).'/subscriptions', [], true);
        } catch (ClientErrorResponseException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, null, $login);
            } else {
                throw $e;
            }
        }

        return $this->getUsersFromList($usersList);
    }

    /**
     * Get user subscriptions by user id
     *
     * @param $id
     * @return User[]
     * @throws ApiException
     * @throws InvalidResponseException
     * @throws UserNotFoundException
     */
    public function getUserSubscriptionsById($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('$id must be an integer');
        }

        try {
            $usersList = $this->getGetRequestData('/api/user/id/'.(int) $id.'/subscriptions', [], true);
        } catch (ClientErrorResponseException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, $id);
            } else {
                throw $e;
            }
        }

        return $this->getUsersFromList($usersList);
    }

    /**
     * Get single user by login
     *
     * @param string $login
     * @return User
     * @throws UserNotFoundException
     * @throws ClientErrorResponseException
     */
    public function getUserByLogin($login)
    {
        try {
            $userInfo = $this->getGetRequestData('/api/user/login/'.urlencode($login), [], true);
        } catch (ClientErrorResponseException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, null, $login);
            } else {
                throw $e;
            }
        }

        return $this->getUserFromUserInfo($userInfo);
    }

    /**
     * Get single user by id
     *
     * @param $id
     * @return User
     * @throws UserNotFoundException
     * @throws ClientErrorResponseException
     */
    public function getUserById($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('$id must be an integer');
        }

        try {
            $userInfo = $this->getGetRequestData('/api/user/id/'.(int) $id, [], true);
        } catch (ClientErrorResponseException $e) {
            if (Response::HTTP_NOT_FOUND === $e->getResponse()->getStatusCode()) {
                throw new UserNotFoundException('User not found', 0, $e, $id);
            } else {
                throw $e;
            }
        }

        return $this->getUserFromUserInfo($userInfo);
    }

    /**
     * Finds and updates or create new user from API response data
     *
     * @param array $userInfo
     * @return User
     * @throws ApiException
     * @throws InvalidResponseException
     */
    public function getUserFromUserInfo(array $userInfo)
    {
        if (!is_array($userInfo)) {
            throw new \InvalidArgumentException('$userInfo must be an array');
        }

        // @todo Refactor to UserFactory->createFromArray()
        if (array_key_exists('id', $userInfo) && array_key_exists('login', $userInfo) && array_key_exists('name', $userInfo) && is_numeric($userInfo['id'])) {
            /** @var User $user */
            if (null === ($user = $this->userRepository->find($userInfo['id']))) {
                // Creating new user
                $user = new User($userInfo['id']);
                $this->em->persist($user);
            }

            // Updating data
            $user
                ->setLogin($userInfo['login'])
                ->setName($userInfo['name'])
            ;

            return $user;
        }

        throw new InvalidResponseException('Invalid API response. Mandatory fields do not exist.');
    }

    /**
     * Get array of User objects from API response containing user list
     *
     * @param array $users
     * @return User[]
     * @throws ApiException
     * @throws InvalidResponseException
     */
    private function getUsersFromList(array $users = [])
    {
        if (!is_array($users)) {
            throw new \InvalidArgumentException('$users must be an array');
        }

        /** @var User[] $resultUsers */
        $resultUsers = [];

        foreach ($users as $userInfo) {
            if (array_key_exists('id', $userInfo) && array_key_exists('login', $userInfo) && array_key_exists('name', $userInfo) && is_numeric($userInfo['id'])) {

                // @todo Optimize with prehashed id's list
                if (null === ($user = $this->userRepository->find($userInfo['id']))) {
                    $user = new User((int) $userInfo['id']);
                    $this->em->persist($user);
                }

                // Updating data
                $user
                    ->setLogin($userInfo['login'])
                    ->setName($userInfo['name'])
                ;

                $resultUsers[] = $user;
            } else {
                throw new InvalidResponseException('Invalid API response. Mandatory fields do not exist.');
            }
        }

        return $resultUsers;
    }

    /**
     * Creates URL of avatar with specified size by User object
     *
     * @param User $user
     * @param int $size
     * @return string
     */
    public function getAvatarUrl(User $user, $size)
    {
        return $this->getAvatarUrlByLogin($user->getLogin(), $size);
    }

    /**
     * Creates URL of avatar with specified size by login string
     *
     * @param $login
     * @param $size
     *
     * @return string
     */
    public function getAvatarUrlByLogin($login, $size)
    {
        if (!in_array($size, [self::AVATAR_SIZE_SMALL, self::AVATAR_SIZE_MEDIUM, self::AVATAR_SIZE_LARGE], true)) {
            throw new \InvalidArgumentException('Avatar size must be one of restricted variants. See UserApi class AVATAR_SIZE_* constants.');
        }

        return $this->avatarsBaseUrl.urlencode($login).'/'.$size;
    }
}
