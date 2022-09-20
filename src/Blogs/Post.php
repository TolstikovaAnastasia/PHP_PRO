<?php

namespace Anastasia\Blog\Blogs;

class Post
{
    public function __construct(
        private ?UUID $uuid,
        private ?User $user,
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
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
        return $this->user->userName() . 'пишет: ' . $this->title . ' . >>> ' . $this->text . PHP_EOL;
    }
}
