<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Tag;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;

class TagFactory
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var EntityRepository
     */
    private $tagRepository;


    public function __construct(LoggerInterface $log, EntityManagerInterface $em)
    {
        $this->log = $log;
        $this->em = $em;
        $this->tagRepository = $em->getRepository('SkobkinPointToolsBundle:Blogs\Tag');
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
                $this->log->error('Error while creating tag from DTO', ['tag' => $string, 'message' => $e->getMessage()]);
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
            $this->em->persist($tag);
        }

        return $tag;
    }
}