<?php
declare(strict_types=1);

namespace App\Entity\Blog;

use App\Entity\Blog\Post;
use App\Repository\Blog\TagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'tags', schema: 'posts')]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id;

    #[ORM\Column(name: 'text', type: 'text', unique: true)]
    private string $text;


    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
