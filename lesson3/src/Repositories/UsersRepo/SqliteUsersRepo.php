<?php

namespace Anastasia\Blog\Repositories\UsersRepo;

use Anastasia\Blog\Exceptions\UserNotFoundException;
use Anastasia\Blog\Blogs\{User, UUID};
use Anastasia\Blog\Blogs\Person\Name;

class SqliteUsersRepo implements UsersRepositoryInterface
{
    private \PDO $connection;

    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new UserNotFoundException(
                "Cannot get user: $uuid"
            );
        }
        return $this->getUser($statement, $uuid);
    }

    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (firstName, lastName, uuid, userName) VALUES (:firstName, :lastName, :uuid, :userName)'
        );

        $statement->execute([
            ':firstName' => $user->name()->getFirstName(),
            ':lastName' => $user->name()->getLastName(),
            ':uuid' => (string)$user->uuid(),
            ':userName' => $user->userName(),
        ]);
    }

    public function getByUserName(string $userName): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE userName = :userName'
        );

        $statement->execute([
            ':userName' => $userName,
        ]);

        return $this->getUser($statement, $userName);
    }

    public function getUser(\PDOStatement $statement, string $userName): User
    {
        $result = $statement->fetch (\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new UserNotFoundException(
                "Cannot find user: $userName"
            );
        }

        return new User(
            new UUID($result['uuid']),
            $result['userName'],
            new Name($result['firstName'], $result['lastName'])
        );
    }
}
