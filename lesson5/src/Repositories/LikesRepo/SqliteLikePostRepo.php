<?php

namespace Anastasia\Blog\Repositories\LikesRepo;

use Anastasia\Blog\Blogs\{UUID, Like};
use Anastasia\Blog\Exceptions\{LikeNotFoundException, LikesIsAlreadyExists};

class SqliteLikePostRepo implements LikePostRepositoryInterface
{
    private \PDO $connection;

    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    public function save(Like $like): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO likesPost (uuid, post_uuid, user_uuid) VALUES (:uuid, :post_uuid, :user_uuid)'
        );

        $statement->execute([
            ':uuid' => (string)$like->uuid(),
            ':post_uuid' => $like->getPost()->uuid(),
            ':user_uuid' => $like->getUser()->uuid()
        ]);
    }

    /**
     * @throws LikeNotFoundException
     */
    public function getByPostUuid(UUID $uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likesPost WHERE post_uuid = :post_uuid'
        );

        $statement->execute([':uuid' => $uuid]);

        $result = $statement->fetchAll();
        if (!$result) {
            $message = 'No likes to this post: ' . $uuid;
            throw new LikeNotFoundException($message);
        }
        return $result;
    }

    /**
     * @throws LikesIsAlreadyExists
     */
    public function likeForPostExists(string $post_uuid, string $user_uuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likesPost WHERE post_uuid = :post_uuid AND user_uuid = :user_uuid'
        );

        $statement->execute(
            [
                ':post_uuid' => $post_uuid,
                ':user_uuid' => $user_uuid
            ]
        );

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikesIsAlreadyExists(
                'The users like for this post already exists'
            );
        }
    }
}