<?php

namespace Anastasia\Blog\Blogs\Commands\Users;

use Anastasia\Blog\Exceptions\InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Anastasia\Blog\Blogs\{UUID, User};
use Anastasia\Blog\Blogs\Person\Name;

class UpdateUser extends Command
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('users:update')
            ->setDescription('Updates a user')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a user to update'
            )
            ->addOption(
                'firstName',
                'f',
                InputOption::VALUE_OPTIONAL,
                'First name',
            )
            ->addOption(
                'lastName',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name',
            )
            ->addOption(
                'usersNumber',
                'u',
                InputOption::VALUE_REQUIRED,
                'Сколько пользователей создать?',
                1
            )
            ->addOption(
                'postsNumber',
                'p',
                InputOption::VALUE_REQUIRED,
                'Сколько постов создать?',
                1
            );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $firstName = $input->getOption('firstName');
        $lastName = $input->getOption('lastName');

        if (empty($firstName) && empty($lastName)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        $uuid = new UUID($input->getArgument('uuid'));
        $user = $this->usersRepository->get($uuid);

        $updatedName = new Name(
            firstName: empty($firstName)
                ? $user->name()->getFirstName() : $firstName,
            lastName: empty($lastName)
                ? $user->name()->getLastName() : $lastName,
        );

        $updatedUser = new User(
            uuid: $uuid,
            userName: $user->userName(),
            hashedPassword: $user->hashedPassword(),
            name: $updatedName
        );

        $this->usersRepository->save($updatedUser);
        $output->writeln("User updated: $uuid");

        return Command::SUCCESS;
    }
}
