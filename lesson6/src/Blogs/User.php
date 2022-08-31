<?php

namespace Anastasia\Blog\Blogs;

use Anastasia\Blog\Blogs\Person\Name;
use Anastasia\Blog\Blogs\UUID;

class User
{
    public function __construct(
        private UUID $uuid,
        private string $userName,
        private Name $name
    )
    {
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function userName(): string
    {
        return $this->userName;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function __toString(): string
    {
        $firstName = $this->name()->getFirstName();
        $lastName = $this->name()->getLastName();
        return "Пользователь $firstName $lastName" . PHP_EOL;
    }
}
