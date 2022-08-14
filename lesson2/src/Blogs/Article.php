<?php

namespace Anastasia\Blog\Blogs;

class Article
{
    public function __construct(
        private ?UUID $uuid,
        private ?UUID $author_uuid,
        private ?string $title,
        private ?string $text
    )
    {
    }

    public function uuid(): ?UUID
    {
        return $this->uuid;
    }

    public function setUuid(?UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function authorUuid(): ?UUID
    {
        return $this->author_uuid;
    }

    public function setAuthorUuid(?UUID $author_uuid): void
    {
        $this->uuid = $author_uuid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->author_uuid . 'пишет: ' . $this->title . ' . >>> ' . $this->text . PHP_EOL;
    }
}
