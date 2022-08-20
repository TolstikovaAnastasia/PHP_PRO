<?php

namespace Anastasia\Blog\Blogs\Commands;

use Anastasia\Blog\Exceptions\ArgumentsException;
use Anastasia\Blog\Exceptions\CommandException;
use Anastasia\Blog\Exceptions\UserNotFoundException;
use Anastasia\Blog\Repositories\UsersRepo\DummyUsersRepository;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Anastasia\Blog\Blogs\{User, UUID};
use Anastasia\Blog\Blogs\Commands\Arguments;
use Anastasia\Blog\Blogs\Commands\CreateUserCommand;

class CreateUserCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(
            new DummyUsersRepository()
        );

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('User already exists: Ivan');

        $command->handle(new Arguments(['userName' => 'Ivan']));
    }


    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
            }
            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByUserName(string $userName): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

    public function testItRequiresLastName(): void
    {

        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: lastName');
        $command->handle(new Arguments([
            'userName' => 'Ivan',
            'firstName' => 'Ivan',
        ]));
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: firstName');
        $command->handle(new Arguments(['userName' => 'Ivan']));
    }

    public function testItSavesUserToRepository(): void
    {
        $usersRepository = new class implements UsersRepositoryInterface {
            private bool $called = false;

            public function save(User $user): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUserName(string $userName): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $command = new CreateUserCommand($usersRepository);

        $command->handle(new Arguments([
            'userName' => 'Ivan',
            'firstName' => 'Ivan',
            'lastName' => 'Nikitin',
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }
}