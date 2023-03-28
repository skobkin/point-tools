<?php
declare(strict_types=1);

namespace App\Factory\Blog;

use App\Factory\AbstractFactory;
use Psr\Log\LoggerInterface;
use App\Entity\Blog\Tag;
use App\Repository\Blog\TagRepository;

class TagFactory extends AbstractFactory
{
    public function __construct(
        LoggerInterface $logger,
        private readonly TagRepository $tagRepository,
    ) {
        parent::__construct($logger);
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
            $this->tagRepository->save($tag);
        }

        return $tag;
    }
}