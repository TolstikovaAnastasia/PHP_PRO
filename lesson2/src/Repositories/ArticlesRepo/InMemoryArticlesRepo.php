<?php

namespace Anastasia\Blog\Repositories\ArticlesRepo;

use Anastasia\Blog\Exceptions\ArticleNotFoundException;
use Anastasia\Blog\Blogs\{Article, UUID};

class InMemoryArticlesRepo implements ArticlesRepositoryInterface
{
    private array $articles = [];

    public function save(Article $article): void
    {
        $this->articles[] = $article;
    }

    public function get(UUID $uuid): Article
    {
        foreach ($this->articles as $article) {
            if ((string)$article->uuid() === (string)$uuid) {
                return $article;
            }
        }
        throw new ArticleNotFoundException("Article not found: $uuid");
    }

    public function getByTitle(string $title): Article
    {
        foreach ($this->articles as $article) {
            if ($article->title() === $title) {
                return $article;
            }
        }
        throw new ArticleNotFoundException("Article not found: $title");
    }
}
