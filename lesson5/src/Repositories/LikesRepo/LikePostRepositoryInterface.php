<?php

namespace Anastasia\Blog\Repositories\LikesRepo;

use Anastasia\Blog\Blogs\Like;
use Anastasia\Blog\Blogs\UUID;

interface LikePostRepositoryInterface
{
    public function save(Like $like): void;
    public function getByPostUuid(UUID $uuid): array;
}