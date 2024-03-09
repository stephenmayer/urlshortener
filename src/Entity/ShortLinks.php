<?php

namespace App\Entity;

use App\Repository\ShortLinksRepository;
use Base62\Base62;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShortLinksRepository::class)]
class ShortLinks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4096)]
    private ?string $url = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?int $readCount = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->readCount = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReadCount(): ?int
    {
        return $this->readCount;
    }

    public function setReadCount(int $readCount): static
    {
        $this->readCount = $readCount;

        return $this;
    }

    public function getShortUrl(): string
    {
        $base62 = new Base62();
        return $_SERVER['HTTP_HOST'] . '/' . $base62->encode($this->id);
    }
}
