<?php

namespace Anastasia\Blog\Repositories\CommentsRepo;

use Anastasia\Blog\Exceptions\{CommentNotFoundException, InvalidArgumentException};
use Anastasia\Blog\Blogs\{Comment, Person\Name, Post, User, UUID};
use Psr\Log\LoggerInterface;

class SqliteCommentsRepo implements CommentsRepositoryInterface
{
    private \PDO $connection;

    public function __construct(\PDO $connection, private LoggerInterface $logger) {
        $this->connection = $connection;
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT *
                    FROM comments
                    LEFT JOIN users ON comments.author_uuid = users.uuid
                    LEFT JOIN posts ON comments.post_uuid = posts.uuid
                    WHERE comments.uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        $this->logger->warning("Comment not found: $uuid");

        return $this->getComment($statement, $uuid);
    }

    public function save(Comment $comment): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, author_uuid, post_uuid, text) VALUES (:uuid, :author_uuid, :post_uuid, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$comment->uuid(),
            ':author_uuid' => $comment->getUser()->uuid(),
            ':post_uuid' => $comment->getPost()->uuid(),
            ':text' => $comment->getText(),
        ]);

        $this->logger->info("Comment created: {$comment->uuid()}");
    }

    /**
     * @throws CommentNotFoundException
     * @throws InvalidArgumentException
     */
    public function getComment(\PDOStatement $statement, string $comment_uuid): Comment
    {
        $result = $statement->fetch (\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot find comment: $comment_uuid"
            );
        }

        $user = new User(
            new UUID($result['author_uuid']),
            $result['userName'],
            $result['password'],
            new Name($result['firstName'], $result['lastName'])
        );

        $post = new Post(
            new UUID($result['post_uuid']),
            $user,
            $result['title'],
            $result['text']
        );

        return new Comment(
            new UUID($result['uuid']),
            $user,
            $post,
            $result['text']
        );
    }
}
