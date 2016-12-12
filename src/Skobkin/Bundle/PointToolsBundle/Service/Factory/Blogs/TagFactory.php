<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Tag;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;


class TagFactory
{
    /**
     * @var EntityManager
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

    /**
     * @param EntityManager $em
     */
    public function __construct(LoggerInterface $log, EntityManager $em)
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
    public function createFromStringsArray(array $tagStrings)
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

    /**
     * @param $text
     *
     * @return Tag
     * @throws InvalidResponseException
     */
    public function createFromString($text)
    {
        $this->validateData($text);

        if (null === ($tag = $this->tagRepository->findOneByLowerText($text))) {
            // Creating new tag
            $tag = new Tag($text);
            $this->em->persist($tag);
        }

        $this->em->flush($tag);

        return $tag;
    }

    /**
     * @param $data
     *
     * @throws InvalidResponseException
     */
    private function validateData($data)
    {
        if (!is_string($data)) {
            // @todo Change exception
            throw new InvalidResponseException('Tag data must be a string');
        }
    }
}