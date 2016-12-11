<?php

namespace Skobkin\Bundle\PointToolsBundle\Entity\Blogs;

use Doctrine\ORM\Mapping as ORM;

/**
 * File
 *
 * @ORM\Table(name="files", schema="posts")
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\Blogs\FileRepository")
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


    public function __construct($remoteUrl = null)
    {
        $this->remoteUrl = $remoteUrl;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set remoteUrl
     *
     * @param string $remoteUrl
     * @return File
     */
    public function setRemoteUrl($remoteUrl)
    {
        $this->remoteUrl = $remoteUrl;

        return $this;
    }

    /**
     * Get remoteUrl
     *
     * @return string 
     */
    public function getRemoteUrl()
    {
        return $this->remoteUrl;
    }
}
