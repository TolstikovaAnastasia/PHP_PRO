<?php

namespace Anastasia\Blogs\UnitTests;

use Anastasia\Blog\Exceptions\PostNotFoundException;
use Anastasia\Blog\Repositories\PostsRepo\SqlitePostsRepo;
use PDO;
use PDOStatement;
use Anastasia\Blog\Blogs\{Post, Person\Name, User, UUID};
use PHPUnit\Framework\TestCase;

class SqlitePostsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenArticleNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqlitePostsRepo($connectionMock, new DummyLogger());
        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage('Cannot find post: 94b55fcc-8102-441f-b24c-ae97e8c2d2f7');

        $repository->get(new UUID('94b55fcc-8102-441f-b24c-ae97e8c2d2f7'));
    }

    public function testItSavesArticleToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '94b55fcc-8102-441f-b24c-ae97e8c2d2f7',
                ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':title' => 'Sport',
                ':text' => 'Ben Stokes promised he was “not going to throw my toys out of the pram” after the resurgence of England’s Test side under his leadership was emphatically derailed by South Africa at Lord’s.',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqlitePostsRepo($connectionStub, new DummyLogger());

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            'name',
            new Name('firstName', 'lastName')
        );

        $repository->save(
            new Post(
                new UUID('94b55fcc-8102-441f-b24c-ae97e8c2d2f7'),
                $user,
                'Sport',
                'Ben Stokes promised he was “not going to throw my toys out of the pram” after the resurgence of England’s Test side under his leadership was emphatically derailed by South Africa at Lord’s.'
            )
        );
    }
}