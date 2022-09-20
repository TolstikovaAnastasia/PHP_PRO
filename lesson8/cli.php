<?php

use Anastasia\Blog\Blogs\Commands\{FakeData\PopulateDB, Posts\DeletePost, Users\CreateUser, Users\UpdateUser};
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

$application = new Application();
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    $command = $container->get($commandClass);
    $application->add($command);
}

try {
    $application->run();

    //$command = $container->get(CreateUserCommand::class);
    //$command->handle(Arguments::fromArgv($argv));
} catch (Exception $exception) {
    $logger->error($exception->getMessage(), ['exception' => $exception]);
}
