<?php

namespace Anastasia\Blog\Repositories\PostsRepo;

use Anastasia\Blog\Exceptions\PostNotFoundException;
use Anastasia\Blog\Blogs\{Post, UUID};

class InMemoryPostsRepo implements PostsRepositoryInterface
{
    private array $posts = [];

    public function save(Post $post): void
    {
        $this->posts[] = $post;
    }

    /**
     * @throws PostNotFoundException
     */
    public function get(UUID $uuid): Post
    {
        foreach ($this->posts as $post) {
            if ((string)$post->uuid() === (string)$uuid) {
                return $post;
            }
        }
        throw new PostNotFoundException("Post not found: $uuid");
    }

    /**
     * @throws PostNotFoundException
     */
    public function delete(UUID $uuid): void
    {
        foreach ($this->posts as $post) {
            if ((string)$post->uuid() === (string)$uuid) {
                unset($post);
            }
            return;
        }
        throw new PostNotFoundException("Post not found: $uuid");
    }

    /**
     * @throws PostNotFoundException
     */
    public function getByTitle(string $title): Post
    {
        foreach ($this->posts as $post) {
            if ($post->title() === $title) {
                return $post;
            }
        }
        throw new PostNotFoundException("Post not found: $title");
    }
}
