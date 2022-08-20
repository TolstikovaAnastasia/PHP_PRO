<?php

use Anastasia\Blog\Repositories\ArticlesRepo\SqliteArticlesRepo;
use Anastasia\Blog\Repositories\CommentsRepo\SqliteCommentsRepo;
use Anastasia\Blog\Blogs\Commands\{Arguments, CreateUserCommand};
use Anastasia\Blog\Repositories\UsersRepo\SqliteUsersRepo;

require_once __DIR__ . '/vendor/autoload.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepo($connection);

$command = new CreateUserCommand($usersRepository);

$articlesRepository = new SqliteArticlesRepo($connection);

$commentsRepository = new SqliteCommentsRepo($connection);

try{
    $command->handle(Arguments::fromArgv($argv));

} catch (Exception $exception) {
    echo $exception->getMessage();
}