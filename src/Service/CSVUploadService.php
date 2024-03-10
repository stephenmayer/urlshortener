<?php

namespace App\Service;

use App\Entity\Link;
use App\Repository\LinkRepository;

class CSVUploadService
{
    public function __construct(private LinkRepository $shortLinksRepository)
    {
    }

    public function getLinksFromUploadFile(string $file): array
    {
        if (file_exists($file) === false) {
            throw new \Exception('File does not exist');
        }
        return str_getcsv(file_get_contents($file), "\n");
    }

    public function processLinks(array $links): array
    {
        $processedLinks = [];

        foreach ($links as $link) {
            // If the link already exists, add it to the processed links array
            if ($shortLink = $this->shortLinksRepository->findOneByUrl($link)) {
                $processedLinks[] = $shortLink;
                continue;
            }

            $shortLink = new Link();
            $shortLink->setUrl($link);

            $this->shortLinksRepository->save($shortLink);
            $processedLinks[] = $shortLink;
        }

        return $processedLinks;
    }
}