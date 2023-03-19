<?php
declare(strict_types=1);

namespace App\Entity\Blog;

use App\Entity\Blog\Post;
use App\Repository\Blog\FileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
#[ORM\Table(name: 'files', schema: 'posts')]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id;

    #[ORM\Column(name: 'remote_url', type: 'text', unique: true)]
    private string $remoteUrl;


    public function __construct($remoteUrl)
    {
        $this->remoteUrl = $remoteUrl;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRemoteUrl(): string
    {
        return $this->remoteUrl;
    }
}
