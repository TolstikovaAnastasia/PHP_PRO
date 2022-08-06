<?php

namespace Anastasia\Blog\Blogs;

use Anastasia\Blog\Blogs\User;

class Article
{
    protected ?int $articleId;
    protected ?User $authorId;
    public ?string $articleHeader;
    public ?string $articleText;

    public function __construct(int $articleId = null, $author = null, string $articleHeader = null, string $articleText = null)
    {
        $this->articleId = $articleId;
        $this->authorId = $author->authorId();
        $this->articleHeader = $articleHeader;
        $this->articleText = $articleText;
    }

    public function articleId()
    {
        return $this->articleId;
    }

    public function setArticleId(int $articleId): void
    {
        $this->articleId = $articleId;
    }

    public function __toString()
    {
        return $this->articleHeader . ' . >>> ' . $this->articleText . PHP_EOL;
    }
}
