<?php

namespace Skobkin\Bundle\PointToolsBundle\Twig;

use Skobkin\Bundle\PointToolsBundle\Entity\User;

class PointUrlExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $pointDomain;

    /**
     * @var string
     */
    private $pointScheme;

    /**
     * @var string
     */
    private $pointBaseUrl;

    public function __construct(string $pointDomain, string $pointScheme, string $pointBaseUrl)
    {
        $this->pointDomain = $pointDomain;
        $this->pointScheme = $pointScheme;
        $this->pointBaseUrl = $pointBaseUrl;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('point_avatar', [$this, 'avatarFunction']),
            new \Twig_SimpleFunction('point_avatar_small', [$this, 'avatarSmallFunction']),
            new \Twig_SimpleFunction('point_avatar_medium', [$this, 'avatarMediumFunction']),
            new \Twig_SimpleFunction('point_avatar_large', [$this, 'avatarLargeFunction']),
            new \Twig_SimpleFunction('point_user_url', [$this, 'userUrl']),
            new \Twig_SimpleFunction('point_user_blog_url', [$this, 'userBlogUrl']),
            new \Twig_SimpleFunction('point_post_url', [$this, 'postUrl']),
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('point_avatar', [$this, 'avatarFunction']),
            new \Twig_SimpleFilter('point_avatar_small', [$this, 'avatarSmallFunction']),
            new \Twig_SimpleFilter('point_avatar_medium', [$this, 'avatarMediumFunction']),
            new \Twig_SimpleFilter('point_avatar_large', [$this, 'avatarLargeFunction']),
            new \Twig_SimpleFilter('point_user_url', [$this, 'pointUserUrl']),
            new \Twig_SimpleFilter('point_user_blog_url', [$this, 'userBlogUrl']),
            new \Twig_SimpleFilter('point_post_url', [$this, 'postUrl']),
        ];
    }

    public function avatarSmallFunction(string $login): string
    {
        return $this->avatarFunction($login, User::AVATAR_SIZE_SMALL);
    }

    public function avatarMediumFunction(string $login): string
    {
        return $this->avatarFunction($login, User::AVATAR_SIZE_MEDIUM);
    }

    public function avatarLargeFunction(string $login): string
    {
        return $this->avatarFunction($login, User::AVATAR_SIZE_LARGE);
    }

    public function avatarFunction(string $login, $size): string
    {
        return $this->getAvatarUrlByLogin($login, $size);
    }

    public function userUrl(string $login): string
    {
        return sprintf('%s://%s.%s/', $this->pointScheme, $login, $this->pointDomain);
    }

    public function userBlogUrl(string $login): string
    {
        return sprintf('%s://%s.%s/blog/', $this->pointScheme, $login, $this->pointDomain);
    }

    public function postUrl(string $postId): string
    {
        return sprintf('%s://%s/%s', $this->pointScheme, $this->pointDomain, $postId);
    }

    private function getAvatarUrlByLogin(string $login, string $size): string
    {
        if (!in_array($size, [User::AVATAR_SIZE_SMALL, User::AVATAR_SIZE_MEDIUM, User::AVATAR_SIZE_LARGE], true)) {
            throw new \InvalidArgumentException('Avatar size must be one of restricted variants. See User::AVATAR_SIZE_* constants.');
        }

        return sprintf('%s://%s/avatar/%s/%s', $this->pointScheme, $this->pointDomain, urlencode($login), $size);
    }
}