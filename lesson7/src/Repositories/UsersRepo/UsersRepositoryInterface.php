<?php

namespace Anastasia\Blog\Repositories\UsersRepo;

use Anastasia\Blog\Blogs\{User, UUID};

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUserName(string $userName): User;
}
