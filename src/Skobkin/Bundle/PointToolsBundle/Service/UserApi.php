<?php

namespace Skobkin\Bundle\PointToolsBundle\Service;

use Skobkin\Bundle\PointToolsBundle\Entity\User;

/**
 * Basic Point.im user API functions from /api/user/*
 */
class UserApi extends AbstractApi
{
    const PATH_USER_INFO = '/api/user/%s';
    const PATH_USER_SUBSCRIPTIONS = '/api/user/%s/subscriptions';
    const PATH_USER_SUBSCRIBERS = '/api/user/%s/subscribers';

    /**
     * @var string Base URL for user avatars
     */
    protected $avatarsBaseUrl = '//i.point.im/a/';

    public function getName()
    {
        return 'skobkin_point_tools_api_user';
    }

    /**
     * Get user subscribers by his/her name
     *
     * @param string $login
     * @return User[]
     */
    public function getUserSubscribersByLogin($login)
    {
        $response = $this->sendGetRequest(self::PATH_USER_SUBSCRIBERS, [$login]);

        $body = $response->getBody(true);

        // @todo use JMSSerializer
        $data = json_decode($body);

        $users = [];

        if (is_array($data)) {
            foreach ($data as $apiUser) {
                $user = new User();
                $user->setId($apiUser->id);
                $user->setLogin($apiUser->login);
                $user->setName($apiUser->name);

                $users[] = $user;
            }
        }

        return $users;
    }
}
