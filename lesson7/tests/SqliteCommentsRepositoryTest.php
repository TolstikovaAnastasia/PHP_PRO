<?php

namespace Anastasia\Blogs\UnitTests;

use Anastasia\Blog\Exceptions\CommentNotFoundException;
use PDO;
use PDOStatement;
use Anastasia\Blog\Blogs\{Post, Comment, Person\Name, User, UUID};
use Anastasia\Blog\Repositories\CommentsRepo\SqliteCommentsRepo;
use PHPUnit\Framework\TestCase;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);

        $statementStub->method('fetch')->willReturn(false);

        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteCommentsRepo($connectionMock, new DummyLogger());
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Cannot find comment: 1546058f-5a25-4334-85ae-e68f2a44bbaf');

        $repository->get(new UUID('1546058f-5a25-4334-85ae-e68f2a44bbaf'));
    }

    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => '1546058f-5a25-4334-85ae-e68f2a44bbaf',
                ':author_uuid' => '123e4567-e89b-12d3-a456-426614174000',
                ':post_uuid' => '94b55fcc-8102-441f-b24c-ae97e8c2d2f7',
                ':text' => 'As we said at the outset, you have to buckle up for the ride.',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);
        $repository = new SqliteCommentsRepo($connectionStub, new DummyLogger());

        $user = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            'name',
            new Name('firstName', 'lastName')
        );

        $post = new Post(
            new UUID('94b55fcc-8102-441f-b24c-ae97e8c2d2f7'),
            $user,
            'Sport',
            'Ben Stokes promised he was “not going to throw my toys out of the pram” after the resurgence of England’s Test side under his leadership was emphatically derailed by South Africa at Lord’s.'
        );

        $repository->save(
            new Comment(
                new UUID('1546058f-5a25-4334-85ae-e68f2a44bbaf'),
                $user,
                $post,
                'As we said at the outset, you have to buckle up for the ride.'
            )
        );
    }
}