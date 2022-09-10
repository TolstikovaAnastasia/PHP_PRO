<?php

namespace Anastasia\Blog\Blogs;

use Anastasia\Blog\Blogs\Person\Name;
use Anastasia\Blog\Blogs\UUID;
use Anastasia\Blog\Exceptions\InvalidArgumentException;

class User
{
    public function __construct(
        private UUID $uuid,
        private string $userName,
        private string $hashedPassword,
        private Name $name
    )
    {
    }

    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $uuid . $password);
    }

    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function createFrom(
        string $userName,
        string $password,
        Name $name
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $userName,
            self::hash($password, $uuid),
            $name
        );
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
