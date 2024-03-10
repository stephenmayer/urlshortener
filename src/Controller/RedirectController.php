<?php

namespace App\Controller;

use App\Message\RedirectMessage;
use App\Repository\LinkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class RedirectController extends AbstractController
{
    public function __construct(private LinkRepository $shortLinksRepository)
    {
    }

    #[Route('/{shortUrl}', name: 'app_redirect')]
    public function index(string $shortUrl, MessageBusInterface $bus): Response
    {
        if ($shortLink = $this->shortLinksRepository->findByShortUrl($shortUrl)) {
            // async dispatch a message to update the read (click) count
            $message = new RedirectMessage($shortUrl);
            $bus->dispatch($message);

            return $this->redirect($shortLink->getUrl());
        }

        return new Response('Not found', 404);
    }
}
