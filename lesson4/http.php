<?php

require_once __DIR__ . '/vendor/autoload.php';

use Anastasia\Blog\Http\Actions\Comments\CreateComment;
use Anastasia\Blog\Http\Actions\Posts\{CreatePost, DeletePost};
use Anastasia\Blog\Http\Actions\Users\{CreateUser, FindByUsername};
use Anastasia\Blog\Http\{ErrorResponse, Request};
use Anastasia\Blog\Repositories\PostsRepo\SqlitePostsRepo;
use Anastasia\Blog\Repositories\UsersRepo\SqliteUsersRepo;
use Anastasia\Blog\Repositories\CommentsRepo\SqliteCommentsRepo;
use Anastasia\Blog\Exceptions\HttpException;

$request = new Request($_GET,
    $_SERVER,
    file_get_contents('php://input')
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => new FindByUsername(
            new SqliteUsersRepo(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        )
    ],

    'POST' => [
        '/users/create' => new CreateUser(
            new SqliteUsersRepo(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),

        '/posts/create' => new CreatePost(
            new SqliteUsersRepo(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqlitePostsRepo(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),

        '/posts/comment' => new CreateComment(
            new SqliteUsersRepo(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqlitePostsRepo(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteCommentsRepo(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],

    'DELETE' => [
        'delete' => new DeletePost(
            new SqlitePostsRepo(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
];

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

$action = $routes[$method][$path];

try{
    $response = $action->handle($request);
    $response->send();

} catch (Exception $exception) {
    (new ErrorResponse($exception->getMessage()))->send();
}


