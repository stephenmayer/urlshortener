<?php

namespace App\Controller;

use App\Repository\LinkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RedirectController extends AbstractController
{
    public function __construct(private LinkRepository $shortLinksRepository)
    {
    }

    #[Route('/{shortUrl}', name: 'app_redirect')]
    public function index($shortUrl): Response
    {
        if ($shortLink = $this->shortLinksRepository->findByShortUrl($shortUrl)) {
            $this->shortLinksRepository->incrementReadCount($shortLink);

            return $this->redirect($shortLink->getUrl());
        }

        return new Response('Not found', 404);
    }
}
