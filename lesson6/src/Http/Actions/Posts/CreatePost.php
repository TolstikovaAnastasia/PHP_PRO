<?php

namespace Anastasia\Blog\Http\Actions\Posts;

use Anastasia\Blog\Exceptions\HttpException;
use Anastasia\Blog\Blogs\{Post, UUID};
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\{Auth\JsonBodyUsernameIdentification,
    ErrorResponse,
    Request,
    Response,
    SuccessfulResponse};
use Anastasia\Blog\Repositories\PostsRepo\PostsRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private JsonBodyUsernameIdentification $identification,
        private LoggerInterface $logger,
    )
    {
    }

    public function handle(Request $request): Response
    {
        $newPostUuid = UUID::random();
        $user = $this->identification->user($request);

        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );

            $this->postsRepository->save($post);

            $this->logger->info("Post created: $newPostUuid");

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}