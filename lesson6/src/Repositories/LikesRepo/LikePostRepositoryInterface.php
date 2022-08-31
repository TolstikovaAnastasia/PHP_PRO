<?php

namespace Anastasia\Blog\Repositories\LikesRepo;

use Anastasia\Blog\Blogs\LikeToPost;
use Anastasia\Blog\Blogs\UUID;

interface LikePostRepositoryInterface
{
    public function save(LikeToPost $likePost): void;
    public function getByPostUuid(UUID $uuid): array;
}