<?php

namespace Anastasia\Blog\Blogs;

use Anastasia\Blog\Blogs\{User, Article};

class Comment
{
    protected ?int $commentId;
    protected ?User $authorId;
    protected ?Article $articleId;
    public ?string $commentText;

    public function __construct(int $commentId = null, $author = null, $article = null, string $commentText = null)
    {
        $this->commentId = $commentId;
        $this->authorId = $author->authorId();
        $this->articleId = $article->articleId();
        $this->commentText = $commentText;
    }

    public function commentId()
    {
        return $this->commentId;
    }

    public function setCommentId(int $commentId): void
    {
        $this->commentId = $commentId;
    }

    public function __toString()
    {
        return $this->commentText . PHP_EOL;
    }
}
