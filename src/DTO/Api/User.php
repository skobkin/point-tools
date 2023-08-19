<?php
declare(strict_types=1);

namespace App\DTO\Api;

use Symfony\Component\Serializer\Annotation\{Context, Groups, SerializedName};
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class User implements ValidableInterface
{
    public function __construct(
        #[SerializedName('id')]
        #[Groups(['user_short', 'user_full'])]
        public readonly ?int $id,
        #[SerializedName('login')]
        #[Groups(['user_short', 'user_full'])]
        public readonly ?string $login,
        #[SerializedName('name')]
        #[Groups(['user_short', 'user_full'])]
        public readonly ?string $name,
        #[SerializedName('about')]
        #[Groups(['user_full'])]
        public readonly ?string $about,
        #[SerializedName('xmpp')]
        #[Groups(['user_full'])]
        public readonly ?string $xmpp,
        #[SerializedName('created')]
        #[Groups(['user_full'])]
        #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d_H:i:s'])]
        public readonly ?\DateTimeImmutable $created,
        #[SerializedName('gender')]
        #[Groups(['user_full'])]
        public readonly ?bool $gender,
        #[SerializedName('deny_anonymous')]
        #[Groups(['user_full'])]
        public readonly ?bool $denyAnonymous,
        #[SerializedName('private')]
        #[Groups(['user_full'])]
        public readonly ?bool $private,
        #[SerializedName('birthdate')]
        #[Groups(['user_full'])]
        public readonly ?string $birthDate,
        #[SerializedName('homepage')]
        #[Groups(['user_full'])]
        public readonly ?string $homepage,
        #[SerializedName('email')]
        #[Groups(['user_full'])]
        public readonly ?string $email,
        #[SerializedName('location')]
        #[Groups(['user_full'])]
        public readonly ?string $location,
    ) {
    }

    public function isValid(): bool
    {
        return null !== $this->id && null !== $this->login;
    }
}
