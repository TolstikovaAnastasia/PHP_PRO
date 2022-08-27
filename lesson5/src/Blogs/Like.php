<?php

namespace Anastasia\Blog\Blogs;

class Like
{
    public function __construct(
        private ?UUID $uuid,
        private ?User $user,
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

    public function __toString(): string
    {
        return $this->uuid();
    }
}