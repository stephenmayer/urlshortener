<?php

namespace App\Tests\Controller;

use App\Controller\RedirectController;
use App\Entity\Link;
use App\Message\RedirectMessage;
use App\Repository\LinkRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * RedirectControllerTest contains test cases for the RedirectController class
 */
class RedirectControllerTest extends TestCase
{
    private MockObject $repository;

    private RedirectController $redirectController;

    /**
     * The "setUp" method prepares a new instance of RedirectController for test execution.
     */
    public function setUp(): void
    {
        $this->repository = $this->getMockBuilder(LinkRepository::class)->disableOriginalConstructor()->getMock();
        $this->redirectController = new RedirectController($this->repository);
    }

    /**
     * Test "index" method with a valid short url.
     * The method should return a RedirectResponse.
     */
    public function testIndexWithValidShortUrl(): void
    {
        $shortUrl = 'abc123';
        $url = 'http://example.com';

        $link = $this->getMockBuilder(Link::class)->getMock();
        $link->expects($this->once())->method('getUrl')->willReturn($url);

        $this->repository->expects($this->once())->method('findByShortUrl')->with($shortUrl)->willReturn($link);

        $mockMessageBusInterface = $this->createMock(MessageBusInterface::class);
        $mockMessageBusInterface->expects($this->once())->method('dispatch')->with(
            $this->callback(
                fn ($message) => $message instanceof RedirectMessage && $message->getShortUrl() === $shortUrl
            )
        )->willReturn(new Envelope(new RedirectMessage($shortUrl)));

        $response = $this->redirectController->index($shortUrl, $mockMessageBusInterface);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($url, $response->headers->get('Location'));
    }

    /**
     * Test "index" method with a non-existent short url.
     * The method should return a response with a status code 404.
     */
    public function testIndexWithInvalidShortUrl(): void
    {
        $shortUrl = 'non-existent';

        $this->repository->expects($this->once())->method('findByShortUrl')->with($shortUrl)->willReturn(null);
        $mockMessageBusInterface = $this->createMock(MessageBusInterface::class);

        $response = $this->redirectController->index($shortUrl, $mockMessageBusInterface);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not found', $response->getContent());
    }
}