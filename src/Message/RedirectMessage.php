<?php

namespace App\Message;

final class RedirectMessage
{
     private string $shortUrl;

     public function __construct(string $shortUrl)
     {
         $this->shortUrl = $shortUrl;
     }

    public function getShortUrl(): string
    {
        return $this->shortUrl;
    }
}
