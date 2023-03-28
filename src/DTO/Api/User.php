<?php
declare(strict_types=1);

namespace App\DTO\Api;

/** TODO: Refactor to public readonly */
class User implements ValidableInterface
{
    private ?string $id;
    private ?string $login;
    private ?string $name;
    private ?string $about;
    private ?string $xmpp;
    private ?string $created;
    private ?bool $gender;
    private ?bool $denyAnonymous;
    private ?bool $private;
    private ?string $birthDate;
    private ?string $homepage;
    private ?string $email;
    private ?string $location;
    private ?string $error;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): void
    {
        $this->login = $login;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): void
    {
        $this->about = $about;
    }

    public function getXmpp(): ?string
    {
        return $this->xmpp;
    }

    public function setXmpp(?string $xmpp): void
    {
        $this->xmpp = $xmpp;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

    public function setCreated(?string $created): void
    {
        $this->created = $created;
    }

    public function getGender(): ?bool
    {
        return $this->gender;
    }

    public function setGender(?bool $gender): void
    {
        $this->gender = $gender;
    }

    public function getDenyAnonymous(): ?bool
    {
        return $this->denyAnonymous;
    }

    public function setDenyAnonymous(?bool $denyAnonymous): void
    {
        $this->denyAnonymous = $denyAnonymous;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(?bool $private): void
    {
        $this->private = $private;
    }

    public function getBirthDate(): ?string
    {
        return $this->birthDate;
    }

    public function setBirthDate(?string $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    public function setHomepage(?string $homepage): void
    {
        $this->homepage = $homepage;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    public function isValid(): bool
    {
        return null === $this->error && null !== $this->id && null !== $this->login;
    }
}
