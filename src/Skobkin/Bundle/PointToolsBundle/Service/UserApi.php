<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Service\Client;
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


    public function __construct(Client $httpClient, $https = true, $baseUrl = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($httpClient, $https, $baseUrl);

        $this->em = $entityManager;
        $this->userRepository = $this->em->getRepository('SkobkinPointToolsBundle:User');
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

        // @todo Return ID existance check when @ap-Codkelden will fix this API behaviour
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

            try {
                $this->em->flush($user);
            } catch (\Exception $e) {
                throw new ApiException(sprintf('Error while flushing changes for [%d] %s: %s', $user->getId(), $user->getLogin(), $e->getMessage()), 0, $e);
            }

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

                try {
                    $this->em->flush($user);
                } catch (\Exception $e) {
                    throw new ApiException(sprintf('Error while flushing changes for [%d] %s: %s', $user->getId(), $user->getLogin(), $e->getMessage()), 0, $e);
                }

                $resultUsers[] = $user;
            } else {
                throw new InvalidResponseException('Invalid API response. Mandatory fields do not exist.');
            }
        }

        return $resultUsers;
    }

    /**
     * Creates avatar with specified size URL for user
     *
     * @param User $user
     * @param int $size
     * @return string
     */
    public function getAvatarUrl(User $user, $size)
    {
        if (!in_array($size, [self::AVATAR_SIZE_SMALL, self::AVATAR_SIZE_MEDIUM, self::AVATAR_SIZE_LARGE], true)) {
            throw new \InvalidArgumentException('Avatar size must be one of restricted variants. See UserApi class AVATAR_SIZE_* constants.');
        }

        return $this->avatarsBaseUrl.urlencode($user->getLogin()).'/'.$size;
    }
}
