<?php

namespace Anastasia\Blog\Blogs\Commands\Posts;

use Anastasia\Blog\Blogs\UUID;
use Anastasia\Blog\Exceptions\{InvalidArgumentException, PostNotFoundException};
use Anastasia\Blog\Repositories\PostsRepo\PostsRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DeletePost extends Command
{
    public function __construct (
        private PostsRepositoryInterface $postsRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('posts:delete')
            ->setDescription('Deletes a post')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a post to delete'
            )
        ->addOption(
            'check-existence',
            'c',
            InputOption::VALUE_NONE,
            'Check if post actually exists'
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $question = new ConfirmationQuestion(
            'Delete post [Y/n]? ',
            false
        );

        if (!$this->getHelper('question')
            ->ask($input, $output, $question)
        ) {
            return Command::SUCCESS;
        }

        $uuid = new UUID($input->getArgument('uuid'));
        if ($input->getOption('check-existence')) {
            try {
                $this->postsRepository->get($uuid);
            } catch (PostNotFoundException $e) {
                $output->writeln($e->getMessage());
                return Command::FAILURE;
            }
        }

        $this->postsRepository->delete($uuid);
        $output->writeln("Post $uuid deleted");
        return Command::SUCCESS;
    }
}