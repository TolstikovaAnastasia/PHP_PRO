<?php

namespace Anastasia\Blog\Blogs;

class LikeToPost extends Like
{
    private Post $post;

    public function __construct(UUID $uuid, Post $post, User $user)
    {
        parent::__construct($uuid, $user);
        $this->post = $post;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): void
    {
        $this->post = $post;
    }
}