<?php

namespace src\PointToolsBundle\Entity\Blogs;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tags", schema="posts")
 * @ORM\Entity(repositoryClass="Skobkin\Bundle\PointToolsBundle\Repository\Blogs\TagRepository", readOnly=true)
 */
class Tag
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", unique=true)
     */
    private $text;


    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
