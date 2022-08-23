<?php

namespace Anastasia\Blog\Repositories\PostsRepo;

use Anastasia\Blog\Blogs\{Post, UUID};

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
    public function delete(UUID $uuid): void;
}
