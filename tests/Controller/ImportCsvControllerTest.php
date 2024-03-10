<?php

namespace App\Tests\Controller;

use App\Service\CSVUploadService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportCsvControllerTest extends WebTestCase
{
    private static $file;

    public function testImportCsvNoFile()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/import');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals('No file uploaded', $client->getResponse()->getContent());
    }

    public function testImportCsvWithFile()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $client = static::createClient();
        $this->createMockUploadedCsvFile();
        
        $client->request('POST', '/import', [], ['csv' => self::$file]);
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    private function createMockUploadedCsvFile()
    {
        $filePath = __DIR__ . '/../urls.csv'; // Replace with the actual CSV file path
        self::$file = new UploadedFile(
            $filePath,
            'data.csv',
            'text/csv',
            null,
            true
        );
    }
}