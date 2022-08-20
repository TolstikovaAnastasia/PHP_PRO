<?php

use Anastasia\Blog\Exceptions\CommentNotFoundException;
use Anastasia\Blog\Blogs\{Article, Comment, Person\Name, User, UUID};
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

        $repository = new SqliteCommentsRepo($connectionMock);
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
        $repository = new SqliteCommentsRepo($connectionStub);

        $repository->save(
            new Comment(
                new UUID('1546058f-5a25-4334-85ae-e68f2a44bbaf'),
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new UUID('94b55fcc-8102-441f-b24c-ae97e8c2d2f7'),
                'As we said at the outset, you have to buckle up for the ride.'
            )
        );
    }
}