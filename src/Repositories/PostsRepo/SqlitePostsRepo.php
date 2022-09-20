<?php

namespace Anastasia\Blog\Repositories\PostsRepo;

use Anastasia\Blog\Exceptions\{InvalidArgumentException,
    PostNotFoundException,
    PostsRepositoryException,
    UserNotFoundException};
use Anastasia\Blog\Repositories\UsersRepo\SqliteUsersRepo;
use PDOException;
use Anastasia\Blog\Blogs\{Post, UUID};
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
     * @throws UserNotFoundException
     */
    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
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

    /**
     * @throws PostsRepositoryException
     */
    public function delete(UUID $uuid): void
    {
        try {
            $statement = $this->connection->prepare(
                'DELETE FROM posts WHERE uuid = ?'
            );
            $statement->execute([(string)$uuid]);
        } catch (PDOException $e) {
            throw new PostsRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
    }

    /*public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare('DELETE FROM posts WHERE uuid = :uuid');

        $statement->execute([
            'uuid' => (string)$uuid,
        ]);
    }*/

    /**
     * @throws PostNotFoundException
     * @throws InvalidArgumentException
     * @throws UserNotFoundException
     */
    public function getPost(\PDOStatement $statement, string $postUuid): Post
    {
        $result = $statement->fetch (\PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new PostNotFoundException(
                "Cannot find post: $postUuid"
            );
        }

        $userRepository = new SqliteUsersRepo($this->connection, $this->logger);
        $user = $userRepository->get(new UUID($result['author_uuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['title'],
            $result['text']
        );
    }
}
