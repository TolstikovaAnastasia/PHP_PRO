<?php

namespace Anastasia\Blogs\UnitTests\Commands;

use Anastasia\Blog\Exceptions\{ArgumentsException, CommandException, InvalidArgumentException, UserNotFoundException};
use Anastasia\Blog\Repositories\UsersRepo\{DummyUsersRepository, UsersRepositoryInterface};
use Anastasia\Blogs\UnitTests\DummyLogger;
use PHPUnit\Framework\TestCase;
use Anastasia\Blog\Blogs\{Commands\Users\CreateUser, User, UUID};
use Anastasia\Blog\Blogs\Commands\{Arguments, CreateUserCommand};
use Symfony\Component\Console\Exception\{ExceptionInterface, RuntimeException};
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class CreateUserCommandTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     */
    public function testItRequiresPassword(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "firstName, lastName, password"');
        $command->run(
            new ArrayInput([
                'userName' => 'user12345678',
            ]),
            new NullOutput()
        );
    }

     /*public function testItRequiresPassword(): void
    {
        $command = new CreateUserCommand(
            $this->makeUsersRepository(),
            new DummyLogger()
        );

        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');
        $command->handle(new Arguments([
            'userName' => 'Ivan',
        ]));
    }*/

    /**
     * @throws InvalidArgumentException
     * @throws ArgumentsException
     */
    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());

        $this->expectException(CommandException::class);
        $this->expectExceptionMessage('User already exists: Ivan');

        $command->handle(new Arguments(
            [
                'userName' => 'Ivan', 'password' => '216512'
            ]
        ));
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

    /**
     * @throws ExceptionInterface
     */
    public function testItRequiresLastName(): void
    {
        $command = new CreateUser($this->makeUsersRepository());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "lastName")');
        $command->run(
            new ArrayInput([
                'userName' => 'user12345678',
                'firstName' => 'Nikolay',
                'password' => '216512',
            ]),
            new NullOutput()
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser($this->makeUsersRepository());
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "firstName")');
        $command->run(
            new ArrayInput([
                'userName' => 'user12345678',
                'lastName' => 'Fedorov',
                'password' => '216512',
            ]),
            new NullOutput()
        );
    }

    /*public function testItRequiresLastName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: lastName');
        $command->handle(new Arguments([
            'userName' => 'Ivan',
            'firstName' => 'Ivan',
            'password' => '216512',
        ]));
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: firstName');
        $command->handle(new Arguments(['userName' => 'Ivan']));
    }*/

    /**
     * @throws CommandException
     * @throws InvalidArgumentException
     * @throws ArgumentsException
     */
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

        $command = new CreateUserCommand($usersRepository, new DummyLogger());

        $command->handle(new Arguments([
            'userName' => 'Ivan',
            'password' => '216512',
            'firstName' => 'Ivan',
            'lastName' => 'Nikitin',
        ]));

        $this->assertTrue($usersRepository->wasCalled());
    }
}