<?php

namespace App\Service;

use App\Entity\ShortLinks;
use App\Repository\ShortLinksRepository;

class CSVUploadService
{
    public function __construct(private ShortLinksRepository $shortLinksRepository)
    {
    }

    public function getLinksFromUploadFile(string $file): array
    {
        $links = str_getcsv(file_get_contents($file), "\n");

        return $links;
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

            $shortLink = new ShortLinks();
            $shortLink->setUrl($link);

            $this->shortLinksRepository->save($shortLink);
            $processedLinks[] = $shortLink;
        }

        return $processedLinks;
    }
}