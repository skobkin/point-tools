<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Tag;
use Skobkin\Bundle\PointToolsBundle\Repository\Blogs\TagRepository;
use Skobkin\Bundle\PointToolsBundle\Service\Factory\AbstractFactory;

class TagFactory extends AbstractFactory
{
    /** @var TagRepository */
    private $tagRepository;


    public function __construct(LoggerInterface $logger, TagRepository $tagRepository)
    {
        parent::__construct($logger);

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

    private function createFromString(string $text): Tag
    {
        if (null === ($tag = $this->tagRepository->findOneByLowerText($text))) {
            // Creating new tag
            $tag = new Tag($text);
            $this->tagRepository->add($tag);
        }

        return $tag;
    }
}