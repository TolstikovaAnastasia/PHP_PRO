<?php

use Anastasia\Blog\Blogs\Container\DIContainer;
use Anastasia\Blog\Http\Auth\{AuthenticationInterface,
    BearerTokenAuthentication,
    IdentificationInterface,
    JsonBodyUuidIdentification,
    PasswordAuthentication,
    PasswordAuthenticationInterface,
    TokenAuthenticationInterface};
use Anastasia\Blog\Repositories\AuthTokensRepository\{AuthTokensRepositoryInterface, SqliteAuthTokensRepository};
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Anastasia\Blog\Repositories\CommentsRepo\{SqliteCommentsRepo, CommentsRepositoryInterface};
use Anastasia\Blog\Repositories\LikesRepo\{SqliteLikePostRepo, LikePostRepositoryInterface};
use Anastasia\Blog\Repositories\PostsRepo\{SqlitePostsRepo, PostsRepositoryInterface};
use Anastasia\Blog\Repositories\UsersRepo\{SqliteUsersRepo, UsersRepositoryInterface};
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_SERVER['SQLITE_DB_PATH'])
);

$logger = (new Logger('blog'));

if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.error.log',
        level: Logger::ERROR,
        bubble: false,
    ));
}

if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger->pushHandler(
        new StreamHandler("php://stdout")
    );
}

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    LoggerInterface::class,
    $logger
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUuidIdentification::class
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
