<?php

namespace src\PointToolsBundle\Service\Factory\Blogs;

use Psr\Log\LoggerInterface;
use src\PointToolsBundle\Entity\Blogs\File;
use src\PointToolsBundle\Repository\Blogs\FileRepository;
use src\PointToolsBundle\Exception\Api\InvalidResponseException;
use src\PointToolsBundle\Service\Factory\AbstractFactory;

class FileFactory extends AbstractFactory
{
    /** @var FileRepository */
    private $fileRepository;


    public function __construct(LoggerInterface $logger, FileRepository $fileRepository)
    {
        parent::__construct($logger);
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param string[] $urlStrings
     *
     * @return File[]
     */
    public function createFromUrlsArray(array $urlStrings): array
    {
        $files = [];

        foreach ($urlStrings as $url) {
            try {
                $file = $this->createFromUrl($url);
                $files[] = $file;
            } catch (\Exception $e) {
                $this->logger->error('Error while creating file from DTO', ['file' => $url, 'message' => $e->getMessage()]);
                continue;
            }
        }

        return $files;
    }

    /**
     * @param string $url
     *
     * @return File
     *
     * @throws InvalidResponseException
     */
    public function createFromUrl(string $url): File
    {
        $this->validateData($url);

        // Replacing HTTP with HTTPS
        $url = str_replace('http://', 'https://', $url);

        if (null === ($file = $this->fileRepository->findOneBy(['remoteUrl' => $url]))) {
            // Creating new file
            $file = new File($url);
            $this->fileRepository->add($file);
        }

        return $file;
    }

    /**
     * @param $data
     *
     * @throws InvalidResponseException
     */
    private function validateData($data): void
    {
        if (!is_string($data)) {
            // @todo Change exception
            throw new InvalidResponseException('File data must be a string');
        }
    }
}