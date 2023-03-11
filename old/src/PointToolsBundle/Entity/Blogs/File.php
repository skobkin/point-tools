<?php

namespace src\PointToolsBundle\Entity\Blogs;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="files", schema="posts")
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\Blogs\FileRepository", readOnly=true)
 */
class File
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="remote_url", type="text", unique=true)
     */
    private $remoteUrl;


    public function __construct($remoteUrl)
    {
        $this->remoteUrl = $remoteUrl;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRemoteUrl(): string
    {
        return $this->remoteUrl;
    }
}
