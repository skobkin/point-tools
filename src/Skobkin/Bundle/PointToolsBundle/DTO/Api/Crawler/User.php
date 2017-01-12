<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler;

use JMS\Serializer\Annotation as JMSS;

/**
 * @JMSS\ExclusionPolicy("none")
 * @JMSS\AccessType("public_method")
 */
class User
{
    /**
     * @var string
     *
     * @JMSS\SerializedName("id")
     * @JMSS\Type("integer")
     */
    private $id;

    /**
     * @var string
     *
     * @JMSS\SerializedName("login")
     * @JMSS\Type("string")
     */
    private $login;

    /**
     * @var string
     *
     * @JMSS\SerializedName("name")
     * @JMSS\Type("string")
     */
    private $name;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): self
    {
        $this->login = $login;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }
}