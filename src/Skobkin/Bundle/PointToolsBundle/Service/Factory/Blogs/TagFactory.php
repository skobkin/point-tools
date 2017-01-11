<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Tag;
use Skobkin\Bundle\PointToolsBundle\Repository\Blogs\TagRepository;

class TagFactory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TagRepository
     */
    private $tagRepository;


    public function __construct(LoggerInterface $logger, TagRepository $tagRepository)
    {
        $this->logger = $logger;
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param string[] $tagStrings
     *
     * @return Tag[]
     */
    public function createFromStringsArray(array $tagStrings): array
    {
        $tags = [];

        foreach ($tagStrings as $string) {
            try {
                $tag = $this->createFromString($string);
                $tags[] = $tag;
            } catch (\Exception $e) {
                $this->logger->error('Error while creating tag from DTO', ['tag' => $string, 'message' => $e->getMessage()]);
                continue;
            }
        }

        return $tags;
    }

    public function createFromString(string $text): Tag
    {
        if (null === ($tag = $this->tagRepository->findOneByLowerText($text))) {
            // Creating new tag
            $tag = new Tag($text);
            $this->tagRepository->add($tag);
        }

        return $tag;
    }
}