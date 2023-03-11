<?php

namespace src\PointToolsBundle\DTO\Api;

use src\PointToolsBundle\DTO\Api\ValidableInterface;

class User implements ValidableInterface
{
    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $login;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $about;

    /**
     * @var string|null
     */
    private $xmpp;

    /**
     * @var string|null
     */
    private $created;

    /**
     * @var bool|null
     */
    private $gender;

    /**
     * @var bool|null
     */
    private $denyAnonymous;

    /**
     * @var bool|null
     */
    private $private;

    /**
     * @var string|null
     */
    private $birthDate;

    /**
     * @var string|null
     */
    private $homepage;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $location;

    /**
     * @var string|null
     */
    private $error;


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
        if (null === $this->error && null !== $this->id && null !== $this->login) {
            return true;
        }

        return false;
    }
}