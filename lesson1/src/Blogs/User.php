<?php

namespace Anastasia\Blog\Blogs;

class User
{
    protected ?int $authorId;
    public ?string $firstName;
    public ?string $lastName;

    public function __construct(int $authorId = null, string $firstName = null, string $lastName = null)
    {
        $this->authorId = $authorId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function authorId()
    {
        return $this->authorId;
    }

    public function setAuthorId(int $authorId): void
    {
        $this->authorId = $authorId;
    }

    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName . PHP_EOL;
    }
}
