<?php

namespace Anastasia\Blog\Repositories\CommentsRepo;

use Anastasia\Blog\Exceptions\{CommentNotFoundException};
use Anastasia\Blog\Blogs\{Comment, UUID};

class SqliteCommentsRepo implements CommentsRepositoryInterface
{
    private \PDO $connection;

    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getComment($statement, $uuid);
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, author_uuid, post_uuid, text) VALUES (:uuid, :author_uuid, :post_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':author_uuid' => (string)$comment->authorUuid(),
            ':post_uuid' => (string)$comment->postUuid(),
            ':text' => $comment->getText(),
        ]);
    }

    public function getComment(\PDOStatement $statement, string $commentUuid): Comment
    {
        $result = $statement->fetch (\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot find comment: $commentUuid"
            );
        }

        return new Comment(
            new UUID($result['uuid']),
            new UUID($result['author_uuid']),
            new UUID($result['post_uuid']),
            $result['text']
        );
    }
}
