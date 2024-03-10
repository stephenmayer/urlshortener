<?php

namespace App\Tests\Service;

use App\Service\CSVUploadService;
use App\Repository\LinkRepository;
use App\Entity\Link;
use PHPUnit\Framework\TestCase;

/**
 * CSVUploadServiceTest
 *
 * This class is a test suite for the CSVUploadService.
 */
class CSVUploadServiceTest extends TestCase
{
    private CSVUploadService $CSVUploadService;
    private \PHPUnit\Framework\MockObject\MockObject $shortLinksRepository;
    
    protected function setUp(): void
    {
        $this->shortLinksRepository = $this->createMock(LinkRepository::class);
        $this->CSVUploadService = new CSVUploadService($this->shortLinksRepository);
    }

    /**
     * testGetLinksFromUploadFile
     *
     * This is a test for the getLinksFromUploadFile method in CSVUploadService.
     */
    public function testGetLinksFromUploadFile(): void
    {
        $file = __DIR__ . '/../urls.csv';
        $expectedLinks = ['https://www.google.com', 'https://symfony.com/download'];

        file_put_contents($file, implode(PHP_EOL, $expectedLinks));
        
        $links = $this->CSVUploadService->getLinksFromUploadFile($file);
        
        $this->assertEquals($expectedLinks, $links);

        unlink($file);
    }

    /**
     * testGetLinksFromUploadFileException
     *
     * This is a test for the getLinksFromUploadFile method's exception case in CSVUploadService.
     */
    public function testGetLinksFromUploadFileException(): void
    {
        $nonExistentFile = 'path/to/non-existent/file';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File does not exist');

        $this->CSVUploadService->getLinksFromUploadFile($nonExistentFile);
    }

    public function testProcessLinks(): void
    {
        $existingLink = new Link();
        $existingLink->setUrl('https://www.existing.com');

        $newLink = new Link();
        $newLink->setUrl('https://www.new.com');

        $this->shortLinksRepository->method('findOneByUrl')
            ->will($this->returnCallback(function ($url) use ($existingLink) {
                if ($url === 'https://www.existing.com') {
                    return $existingLink;
                }
                return null;
            }));

        $this->shortLinksRepository->method('save')
            ->will($this->returnCallback(function ($link) use ($newLink) {
                if ($link->getUrl() === 'https://www.new.com') {
                    return $newLink;
                }
                return false;
            }));

        $links = ['https://www.existing.com', 'https://www.new.com'];
        $processedLinks = $this->CSVUploadService->processLinks($links);

        $this->assertCount(2, $processedLinks);
        $this->assertEquals('https://www.existing.com', $processedLinks[0]->getUrl());
        $this->assertEquals('https://www.new.com', $processedLinks[1]->getUrl());
    }
}