<?php

namespace Anastasia\Blog\Repositories\UsersRepo;

use Anastasia\Blog\Blogs\Person\Name;
use Anastasia\Blog\Blogs\User;
use Anastasia\Blog\Blogs\UUID;
use Anastasia\Blog\Exceptions\UserNotFoundException;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;

class DummyUsersRepository implements UsersRepositoryInterface
{

    public function save(User $user): void
    {
        // TODO: Implement save() method.
    }

    public function get(UUID $uuid): User
    {
        throw new UserNotFoundException("Not found");
    }

    public function getByUserName(string $userName): User
    {
        return new User(UUID::random(), "user123", new Name("first", "last"));
    }
}