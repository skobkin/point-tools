<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\Tag;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\ApiException;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;


class TagFactory
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $tagRepository;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->tagRepository = $em->getRepository('SkobkinPointToolsBundle:Blogs\Tag');
    }

    /**
     * @param $data
     *
     * @return Tag
     * @throws ApiException
     * @throws InvalidResponseException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createFromArray($data)
    {
        $this->validateData($data);

        $qb = $this->tagRepository->createQueryBuilder('t');
        $qb
            ->select()
            ->where($qb->expr()->eq('lower(t.text)', 'lower(:text)'))
            ->setParameter('text', $data)
        ;

        if (null === ($tag = $qb->getQuery()->getOneOrNullResult())) {
            $tag = new Tag($data);
            $this->em->persist($tag);
        }

        try {
            $this->em->flush($tag);
        } catch (\Exception $e) {
            throw new ApiException(sprintf('Error while flushing changes for [%d] %s: %s', $tag->getId(), $tag->getText(), $e->getMessage()), 0, $e);
        }

        return $tag;
    }

    /**
     * @param array $data
     *
     * @return Tag[]
     * @throws ApiException
     */
    public function createFromListArray(array $data)
    {
        $tags = [];

        foreach ($data as $text) {
            $tags[] = $this->createFromArray($text);
        }

        return $tags;
    }

    /**
     * @param $data
     *
     * @throws InvalidResponseException
     */
    private function validateData($data)
    {
        if (!is_string($data)) {
            throw new InvalidResponseException('Tag data must be a string');
        }
    }
}