<?php

namespace Anastasia\Blog\Http\Actions\Posts;

use Anastasia\Blog\Exceptions\HttpException;
use Anastasia\Blog\Repositories\UsersRepo\UsersRepositoryInterface;
use Anastasia\Blog\Blogs\{Post, UUID};
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\{ErrorResponse, Request, Response, SuccessfulResponse};
use Anastasia\Blog\Repositories\PostsRepo\PostsRepositoryInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
            $user = $this->usersRepository->get($authorUuid);

            $newPostUuid = UUID::random();

            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );

            $this->postsRepository->save($post);

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}