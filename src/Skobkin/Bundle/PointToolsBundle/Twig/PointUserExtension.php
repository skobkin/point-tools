<?php

namespace Skobkin\Bundle\PointToolsBundle\Twig;

use Skobkin\Bundle\PointToolsBundle\Service\UserApi;

class PointUserExtension extends \Twig_Extension
{
    const POINT_HOST = 'point.im';

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
            new \Twig_SimpleFunction('point_user_url', [$this, 'pointUserUrl']),
            new \Twig_SimpleFunction('point_user_blog_url', [$this, 'pointUserBlogUrl']),
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('point_avatar', [$this, 'pointAvatarFunction']),
            new \Twig_SimpleFilter('point_avatar_small', [$this, 'pointAvatarSmallFunction']),
            new \Twig_SimpleFilter('point_avatar_medium', [$this, 'pointAvatarMediumFunction']),
            new \Twig_SimpleFilter('point_avatar_large', [$this, 'pointAvatarLargeFunction']),
            new \Twig_SimpleFilter('point_user_url', [$this, 'pointUserUrl']),
            new \Twig_SimpleFilter('point_user_blog_url', [$this, 'pointUserBlogUrl']),
        ];
    }

    public function pointAvatarSmallFunction(string $login): string
    {
        return $this->pointAvatarFunction($login, UserApi::AVATAR_SIZE_SMALL);
    }

    public function pointAvatarMediumFunction(string $login): string
    {
        return $this->pointAvatarFunction($login, UserApi::AVATAR_SIZE_MEDIUM);
    }

    public function pointAvatarLargeFunction(string $login): string
    {
        return $this->pointAvatarFunction($login, UserApi::AVATAR_SIZE_LARGE);
    }

    public function pointAvatarFunction(string $login, $size): string
    {
        return $this->userApi->getAvatarUrlByLogin($login, $size);
    }

    /**
     * @param string $login
     * @param bool $forceHttps
     *
     * @return string
     */
    public function pointUserUrl(string $login, bool $forceHttps = false): string
    {
        return sprintf('%s//%s.%s/', $forceHttps ? 'https' : '', $login, self::POINT_HOST);
    }

    /**
     * @param string $login
     * @param bool $forceHttps
     *
     * @return string
     */
    public function pointUserBlogUrl(string $login, bool $forceHttps = false): string
    {
        return sprintf('%s//%s.%s/blog/', $forceHttps ? 'https' : '', $login, self::POINT_HOST);
    }
}