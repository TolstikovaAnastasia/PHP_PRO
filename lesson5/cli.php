<?php

use Anastasia\Blog\Blogs\Commands\{Arguments, CreateUserCommand};

$container = require __DIR__ . '/bootstrap.php';

try{
    $command = $container->get(CreateUserCommand::class);
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $exception) {
    echo $exception->getMessage();
}