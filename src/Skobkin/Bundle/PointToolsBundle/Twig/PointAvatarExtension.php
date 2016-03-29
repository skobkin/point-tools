<?php

namespace Skobkin\Bundle\PointToolsBundle\Twig;

use Skobkin\Bundle\PointToolsBundle\Service\UserApi;

class PointAvatarExtension extends \Twig_Extension
{
    /**
     * @var UserApi
     */
    private $userApi;

    public function __construct(UserApi $userApi)
    {
        $this->userApi = $userApi;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('point_avatar', [$this, 'pointAvatarFunction']),
            new \Twig_SimpleFunction('point_avatar_small', [$this, 'pointAvatarSmallFunction']),
            new \Twig_SimpleFunction('point_avatar_medium', [$this, 'pointAvatarMediumFunction']),
            new \Twig_SimpleFunction('point_avatar_large', [$this, 'pointAvatarLargeFunction']),
        ];
    }

    public function pointAvatarSmallFunction($login)
    {
        return $this->pointAvatarFunction($login, UserApi::AVATAR_SIZE_SMALL);
    }

    public function pointAvatarMediumFunction($login)
    {
        return $this->pointAvatarFunction($login, UserApi::AVATAR_SIZE_MEDIUM);
    }

    public function pointAvatarLargeFunction($login)
    {
        return $this->pointAvatarFunction($login, UserApi::AVATAR_SIZE_LARGE);
    }

    public function pointAvatarFunction($login, $size)
    {
        return $this->userApi->getAvatarUrlByLogin($login, $size);
    }

    public function getName()
    {
        return 'point_tools_avatars';
    }
}