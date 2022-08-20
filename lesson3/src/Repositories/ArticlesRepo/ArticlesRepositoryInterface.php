<?php

namespace Anastasia\Blog\Repositories\ArticlesRepo;

use Anastasia\Blog\Blogs\{Article, UUID};

interface ArticlesRepositoryInterface
{
    public function save(Article $article): void;
    public function get(UUID $uuid): Article;
}
