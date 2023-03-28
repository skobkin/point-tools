<?php
declare(strict_types=1);

namespace App\Factory\Blog;

use App\Factory\AbstractFactory;
use Psr\Log\LoggerInterface;
use App\Entity\Blog\File;
use App\Repository\Blog\FileRepository;

class FileFactory extends AbstractFactory
{
    public function __construct(
        LoggerInterface $logger,
        private readonly FileRepository $fileRepository,
    ) {
        parent::__construct($logger);
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

    public function createFromUrl(string $url): File
    {
        // Replacing HTTP with HTTPS
        $url = str_replace('http://', 'https://', $url);

        if (null === ($file = $this->fileRepository->findOneBy(['remoteUrl' => $url]))) {
            // Creating new file
            $file = new File($url);
            $this->fileRepository->save($file);
        }

        return $file;
    }
}
