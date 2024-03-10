<?php

namespace App\MessageHandler;

use App\Message\RedirectMessage;
use App\Repository\LinkRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RedirectMessageHandler
{
    public function __construct(private LinkRepository $shortLinksRepository)
    {
    }

    public function __invoke(RedirectMessage $message)
    {
        $link = $this->shortLinksRepository->findByShortUrl($message->getShortUrl());
        $this->shortLinksRepository->incrementReadCount($link);
    }
}
