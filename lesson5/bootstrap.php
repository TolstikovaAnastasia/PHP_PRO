<?php

use Anastasia\Blog\Blogs\Container\DIContainer;
use Anastasia\Blog\Repositories\CommentsRepo\{SqliteCommentsRepo, CommentsRepositoryInterface};
use Anastasia\Blog\Repositories\LikesRepo\{SqliteLikePostRepo, LikePostRepositoryInterface};
use Anastasia\Blog\Repositories\PostsRepo\{SqlitePostsRepo, PostsRepositoryInterface};
use Anastasia\Blog\Repositories\UsersRepo\{SqliteUsersRepo, UsersRepositoryInterface};

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$container->bind(
    LikePostRepositoryInterface::class,
    SqliteLikePostRepo::class
);

$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepo::class
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepo::class
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepo::class
);

return $container;
