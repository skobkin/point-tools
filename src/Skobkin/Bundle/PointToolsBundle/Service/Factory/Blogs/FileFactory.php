<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Factory\Blogs;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Skobkin\Bundle\PointToolsBundle\Entity\Blogs\File;
use Skobkin\Bundle\PointToolsBundle\Service\Exceptions\InvalidResponseException;


class FileFactory
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
    private $fileRepository;

    /**
     * @param EntityManager $em
     */
    public function __construct(LoggerInterface $log, EntityManager $em)
    {
        $this->log = $log;
        $this->em = $em;
        $this->fileRepository = $em->getRepository('SkobkinPointToolsBundle:Blogs\File');
    }

    /**
     * @param string[] $urlStrings
     *
     * @return File[]
     */
    public function createFromUrlsArray(array $urlStrings)
    {
        $files = [];

        foreach ($urlStrings as $url) {
            try {
                $file = $this->createFromUrl($url);
                $files[] = $file;
            } catch (\Exception $e) {
                $this->log->error('Error while creating file from DTO', ['file' => $url, 'message' => $e->getMessage()]);
                continue;
            }
        }

        return $files;
    }

    /**
     * @param $url
     *
     * @return File
     * @throws InvalidResponseException
     */
    public function createFromUrl($url)
    {
        $this->validateData($url);

        // Replacing HTTP with HTTPS
        $url = str_replace('http://', 'https://', $url);

        if (null === ($file = $this->fileRepository->findOneBy(['remoteUrl' => $url]))) {
            // Creating new file
            $file = new File($url);
            $this->em->persist($file);
        }

        return $file;
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
            throw new InvalidResponseException('File data must be a string');
        }
    }
}