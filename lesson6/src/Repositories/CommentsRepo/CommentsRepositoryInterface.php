<?php

namespace Anastasia\Blog\Repositories\CommentsRepo;

use Anastasia\Blog\Blogs\{Comment, UUID};

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;
    public function get(UUID $uuid): Comment;
}
