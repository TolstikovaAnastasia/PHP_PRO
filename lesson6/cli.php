<?php

use Anastasia\Blog\Blogs\Commands\{Arguments, CreateUserCommand};
use Psr\Log\LoggerInterface;

$container = require __DIR__ . '/bootstrap.php';

$command = $container->get(CreateUserCommand::class);
$logger = $container->get(LoggerInterface::class);

try{
    $command->handle(Arguments::fromArgv($argv));
} catch (Exception $exception) {
    $logger->error($exception->getMessage(), ['exception' => $exception]);
}