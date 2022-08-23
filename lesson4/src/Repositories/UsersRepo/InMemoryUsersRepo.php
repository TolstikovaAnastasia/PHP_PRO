<?php

namespace Anastasia\Blog\Repositories\UsersRepo;

use Anastasia\Blog\Exceptions\UserNotFoundException;
use Anastasia\Blog\Blogs\{User, UUID};

class InMemoryUsersRepo implements UsersRepositoryInterface
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if ((string)$user->uuid() === (string)$uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }

    public function getByUserName(string $userName): User
    {
        foreach ($this->users as $user) {
            if ($user->userName() === $userName) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $userName");
    }
}
