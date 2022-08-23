<?php

use Anastasia\Blog\Blogs\Post;
use Anastasia\Blog\Blogs\UUID;
use Anastasia\Blog\Repositories\PostsRepo\SqlitePostsRepo;

require_once __DIR__ . '/vendor/autoload.php';

try{
    $connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

    $postRepository = new SqlitePostsRepo($connection);

    $post = $postRepository->get(new UUID('d02eef69-1a06-460f-b859-202b84164734'));
    echo $post;
    echo $post->getUser();

    //$command->handle(Arguments::fromArgv($argv));

} catch (Exception $exception) {
    echo $exception->getMessage();
}