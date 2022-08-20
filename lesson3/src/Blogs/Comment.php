<?php

namespace Anastasia\Blog\Blogs;

class Comment
{
    public function __construct(
        private ?UUID $uuid,
        private ?UUID $author_uuid,
        private ?UUID $post_uuid,
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

    public function authorUuid(): ?UUID
    {
        return $this->author_uuid;
    }

    public function setAuthorUuid(?UUID $author_uuid): void
    {
        $this->uuid = $author_uuid;
    }

    public function postUuid(): ?UUID
    {
        return $this->post_uuid;
    }

    public function setPostUuid(?UUID $post_uuid): void
    {
        $this->uuid = $post_uuid;
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
