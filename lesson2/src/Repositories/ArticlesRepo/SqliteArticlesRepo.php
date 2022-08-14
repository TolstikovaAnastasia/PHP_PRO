<?php

namespace Anastasia\Blog\Repositories\ArticlesRepo;

use Anastasia\Blog\Exceptions\ArticleNotFoundException;
use Anastasia\Blog\Exceptions\UserNotFoundException;
use Anastasia\Blog\Blogs\{Article, User, UUID};

class SqliteArticlesRepo implements ArticlesRepositoryInterface
{
    private \PDO $connection;

    public function __construct(\PDO $connection) {
        $this->connection = $connection;
    }

    public function get(UUID $uuid): Article
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM articles WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getArticle($statement, $uuid);
    }

    public function save(Article $article): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO articles (uuid, author_uuid, title, text) VALUES (:uuid, :author_uuid, :title, :text)'
        );

        $statement->execute([
            ':uuid' => (string)$article->uuid(),
            ':author_uuid' => $article->authorUuid(),
            ':title' => $article->getTitle(),
            ':text' => $article->getText(),
        ]);
    }

    public function getArticle(\PDOStatement $statement, string $articleUuid): Article
    {
        $result = $statement->fetch (\PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new ArticleNotFoundException(
                "Cannot find article: $articleUuid"
            );
        }

        return new Article(
            new UUID($result['uuid']),
            new UUID($result['author_uuid']),
            $result['title'],
            $result['text']
        );
    }
}
