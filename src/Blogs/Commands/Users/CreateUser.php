<?php

namespace Anastasia\Blog\Blogs\Commands\Users;

use Anastasia\Blog\Blogs\Person\Name;
use Anastasia\Blog\Blogs\User;
use Anastasia\Blog\Exceptions\{InvalidArgumentException, UserNotFoundException};
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

class CreateUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('users:create')
            ->setDescription('Creates new user')
            ->addArgument('firstName', InputArgument::REQUIRED, 'First name')
            ->addArgument('lastName', InputArgument::REQUIRED, 'Last name')
            ->addArgument('userName', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        $output->writeln('Create user command started');
        $userName = $input->getArgument('userName');
        if ($this->userExists($userName)) {
            $output->writeln("User already exists: $userName");
            return Command::FAILURE;
        }

        $user = User::createFrom(
            $userName,
            $input->getArgument('password'),
            new Name(
                $input->getArgument('firstName'),
                $input->getArgument('lastName')
            )
        );

        $this->usersRepository->save($user);
        $output->writeln('User created: ' . $user->uuid());
        return Command::SUCCESS;
    }

    private function userExists(string $userName): bool
    {
        try {
            $this->usersRepository->getByUsername($userName);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}