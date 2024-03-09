<?php

namespace App\Controller;

use App\Repository\ShortLinksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RedirectController extends AbstractController
{
    public function __construct(private ShortLinksRepository $shortLinksRepository)
    {
    }

    #[Route('/{shortUrl}', name: 'app_redirect')]
    public function index($shortUrl): Response
    {
        $shortLink = $this->shortLinksRepository->findByShortUrl($shortUrl);
        $this->shortLinksRepository->incrementReadCount($shortLink);

        return $this->redirect($shortLink->getUrl());
    }
}
