<?php

namespace Anastasia\Blog\Blogs\Commands;

use Anastasia\Blog\Blogs\Person\Name;
use Anastasia\Blog\Exceptions\{CommandException, UserNotFoundException};
use Anastasia\Blog\Blogs\{User, UUID};
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;

final class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function handle(Arguments $arguments): void
    {
        $userName = $arguments->get('userName');

        if ($this->userExists($userName)) {
            throw new CommandException("User already exists: $userName");
        }

        $this->usersRepository->save(new User(
            UUID::random(),
            $userName,
            new Name($arguments->get('firstName'), $arguments->get('lastName'))
        ));
    }

    private function userExists(string $userName): bool
    {
        try {
            $this->usersRepository->getByUserName($userName);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
