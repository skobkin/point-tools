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


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return User
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}