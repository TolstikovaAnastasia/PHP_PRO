<?php

use Anastasia\Blog\Blogs\Person\Name;
use Anastasia\Blog\Blogs\User;
use Anastasia\Blog\Blogs\UUID;
use Anastasia\Blog\Exceptions\UserNotFoundException;
use Anastasia\Blog\Repositories\UsersRepo\SqliteUsersRepo;
use PHPUnit\Framework\TestCase;

class SqliteUsersRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteUsersRepo($connectionMock);
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot find user: Ivan');

        $repository->getByUserName('Ivan');
    }

    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':userName' => 'ivan123',
                ':firstName' => 'Ivan',
                ':lastName' => 'Nikitin',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteUsersRepo($connectionStub);
        $repository->save(
            new User(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                'ivan123',
                new Name('Ivan', 'Nikitin')
            )
        );
    }
}