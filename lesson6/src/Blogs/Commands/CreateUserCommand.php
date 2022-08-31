<?php

namespace Anastasia\Blog\Blogs\Commands;

use Anastasia\Blog\Blogs\Person\Name;
use Anastasia\Blog\Exceptions\{ArgumentsException, CommandException, InvalidArgumentException, UserNotFoundException};
use Psr\Log\LoggerInterface;
use Anastasia\Blog\Blogs\{User, UUID};
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;

final class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    )
    {
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     * @throws InvalidArgumentException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");
        $userName = $arguments->get('userName');

        if ($this->userExists($userName)) {
            $this->logger->warning("User already exists: $userName");
            //return;
            throw new CommandException("User already exists: $userName");
        }

        $uuid = UUID::random();

        $this->usersRepository->save(new User(
            $uuid,
            $userName,
            new Name($arguments->get('firstName'), $arguments->get('lastName'))
        ));

        $this->logger->info("User created: $uuid");
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
