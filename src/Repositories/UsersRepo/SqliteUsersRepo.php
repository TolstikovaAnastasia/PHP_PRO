<?php

namespace Anastasia\Blog\Repositories\UsersRepo;

use Anastasia\Blog\Exceptions\InvalidArgumentException;
use Anastasia\Blog\Exceptions\UserNotFoundException;
use Psr\Log\LoggerInterface;
use Anastasia\Blog\Blogs\{User, UUID};
use Anastasia\Blog\Blogs\Person\Name;

class SqliteUsersRepo implements UsersRepositoryInterface
{
    private \PDO $connection;

    public function __construct(\PDO $connection, private LoggerInterface $logger)
    {
        $this->connection = $connection;
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        $this->logger->warning("User not found: $uuid");

        return $this->getUser($statement, $uuid);
    }

    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, userName, password, firstName, lastName)
            VALUES (:uuid, :userName, :password, :firstName, :lastName)
            ON CONFLICT (uuid) DO UPDATE SET
            firstName = :firstName,
            lastName = :lastName'
        );

        $statement->execute([
            ':firstName' => $user->name()->getFirstName(),
            ':lastName' => $user->name()->getLastName(),
            ':password' => $user->hashedPassword(),
            ':uuid' => (string)$user->uuid(),
            ':userName' => $user->userName(),
        ]);

        $this->logger->info("User created: {$user->uuid()}");
    }

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
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

    /**
     * @throws UserNotFoundException
     * @throws InvalidArgumentException
     */
    public function getUser(\PDOStatement $statement, string $userName): User
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new UserNotFoundException(
                "Cannot find user: $userName"
            );
        }

        return new User(
            new UUID($result['uuid']),
            $result['userName'],
            $result['password'],
            new Name($result['firstName'], $result['lastName'])
        );
    }
}
