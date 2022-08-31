<?php

namespace Anastasia\Blog\Repositories\PostsRepo;

use Anastasia\Blog\Exceptions\InvalidArgumentException;
use Anastasia\Blog\Exceptions\PostNotFoundException;
use Anastasia\Blog\Blogs\{Person\Name, Post, User, UUID};
use Psr\Log\LoggerInterface;

class SqlitePostsRepo implements PostsRepositoryInterface
{
    private \PDO $connection;

    public function __construct(\PDO $connection, private LoggerInterface $logger) {
        $this->connection = $connection;
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT *
                    FROM posts LEFT JOIN users
                    ON posts.author_uuid = users.uuid 
                    WHERE posts.uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        $this->logger->warning("Post not found: $uuid");

        return $this->getPost($statement, $uuid);
    }

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$post->uuid(),
            ':author_uuid' => $post->getUser()->uuid(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText()
        ]);

        $this->logger->info("Post created: {$post->uuid()}");
    }

    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare('DELETE FROM posts WHERE uuid = :uuid');

        $statement->execute([
            'uuid' => (string)$uuid,
        ]);
    }

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     */
    public function getPost(\PDOStatement $statement, string $postUuid): Post
    {
        $result = $statement->fetch (\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new PostNotFoundException(
                "Cannot find post: $postUuid"
            );
        }

        $user = new User(
            new UUID($result['author_uuid']),
            $result['userName'],
            new Name($result['firstName'], $result['lastName'])
        );

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
    }
}
