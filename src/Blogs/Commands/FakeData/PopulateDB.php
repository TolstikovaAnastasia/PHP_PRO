<?php

namespace Anastasia\Blog\Blogs\Commands\FakeData;

use Anastasia\Blog\Blogs\Comment;
use Anastasia\Blog\Exceptions\InvalidArgumentException;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;
use Anastasia\Blog\Repositories\PostsRepo\PostsRepositoryInterface;
use Anastasia\Blog\Repositories\CommentsRepo\CommentsRepositoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Anastasia\Blog\Blogs\{User, Post, UUID};
use Anastasia\Blog\Blogs\Person\Name;
use Faker\Generator;

class PopulateDB extends Command
{
    public function __construct(
        private Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data');
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = [];

        for ($i = 0; $i < $input->getOption('usersNumber'); $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->userName());
        }

        foreach ($users as $user) {
            for ($i = 0; $i < $input->getOption('postsNumber'); $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getTitle());
            }
        }

        foreach ($users as $user) {
            for ($i = 0; $i < 15; $i++) {
                $comment = $this->createFakeComment($user, $post);
                $output->writeln('Comment created: ' . $comment->getText());
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createFakeUser(): User
    {
        $user = User::createFrom(
            $this->faker->userName,
            $this->faker->password,
            new Name(
                $this->faker->firstName,
                $this->faker->lastName
            )
        );

        $this->usersRepository->save($user);
        return $user;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            $this->faker->sentence(4, true),
            $this->faker->realText
        );

        $this->postsRepository->save($post);
        return $post;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function createFakeComment(User $author, Post $post): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $author,
            $post,
            $this->faker->realText
        );

        $this->commentsRepository->save($comment);
        return $comment;
    }
}
