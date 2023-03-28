<?php
declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PointUrlExtension extends AbstractExtension
{
    public function __construct(
        private readonly string $pointDomain,
        private readonly string $pointScheme,
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('point_avatar', [$this, 'avatarFunction']),
            new TwigFunction('point_avatar_small', [$this, 'avatarSmallFunction']),
            new TwigFunction('point_avatar_medium', [$this, 'avatarMediumFunction']),
            new TwigFunction('point_avatar_large', [$this, 'avatarLargeFunction']),
            new TwigFunction('point_user_url', [$this, 'userUrl']),
            new TwigFunction('point_user_blog_url', [$this, 'userBlogUrl']),
            new TwigFunction('point_post_url', [$this, 'postUrl']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('point_avatar', [$this, 'avatarFunction']),
            new TwigFilter('point_avatar_small', [$this, 'avatarSmallFunction']),
            new TwigFilter('point_avatar_medium', [$this, 'avatarMediumFunction']),
            new TwigFilter('point_avatar_large', [$this, 'avatarLargeFunction']),
            new TwigFilter('point_user_url', [$this, 'userUrl']),
            new TwigFilter('point_user_blog_url', [$this, 'userBlogUrl']),
            new TwigFilter('point_post_url', [$this, 'postUrl']),
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
