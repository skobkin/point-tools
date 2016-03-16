<?php

namespace Skobkin\Bundle\PointToolsBundle\DTO\Api\Crawler;

use Symfony\Component\Serializer\Annotation as Serializer;

class User
{
    /**
     * @var string
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $id;

    /**
     * @var string
     *
     * @Serializer\Groups({"import_post_page"})
     */
    private $login;

    /**
     * @var string
     *
     * @Serializer\Groups({"import_post_page"})
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