<?php

namespace Anastasia\Blog\Repositories\CommentsRepo;

use Anastasia\Blog\Exceptions\CommentNotFoundException;
use Anastasia\Blog\Blogs\{Comment, UUID};

class InMemoryCommentsRepo implements CommentsRepositoryInterface
{
    private array $comments = [];

    public function save(Comment $comment): void
    {
        $this->comments[] = $comment;
    }

    public function get(UUID $uuid): Comment
    {
        foreach ($this->comments as $comment) {
            if ((string)$comment->uuid() === (string)$uuid) {
                return $comment;
            }
        }
        throw new CommentNotFoundException("Comment not found: $uuid");
    }

    public function getByText(string $text): Comment
    {
        foreach ($this->comments as $comment) {
            if ($comment->text() === $text) {
                return $comment;
            }
        }
        throw new CommentNotFoundException("Comment not found: $text");
    }
}
