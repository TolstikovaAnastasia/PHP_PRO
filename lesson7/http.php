<?php

use Anastasia\Blog\Http\Actions\Comments\CreateComment;
use Anastasia\Blog\Http\Actions\Posts\{CreatePost, DeletePost};
use Anastasia\Blog\Http\Actions\Users\{CreateUser, FindByUsername};
use Anastasia\Blog\Http\{Actions\Auth\LogIn, Actions\Auth\LogOut, Actions\Likes\CreatePostLike, Request, ErrorResponse};
use Anastasia\Blog\Exceptions\HttpException;
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException $exception) {
    $logger->warning($exception->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $exception) {
    $logger->warning($exception->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
    ],
    'POST' => [
        '/login' => LogIn::class,
        '/logout' => LogOut::class,
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/likesPost/create' => CreatePostLike::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
    ],
];

if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
    $response->send();
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
}

