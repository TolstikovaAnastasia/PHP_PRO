<?php

namespace Anastasia\Blog\Blogs;

class Comment
{
    public function __construct(
        private ?UUID   $uuid,
        private ?User   $user,
        private ?Post   $post,
        private ?string $text
    )
    {
    }

    public function uuid(): UUID
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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->text . PHP_EOL;
    }
}
