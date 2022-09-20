<?php

namespace Anastasia\Blog\Http\Actions\Posts;

use Anastasia\Blog\Exceptions\{AuthException, HttpException};
use Anastasia\Blog\Exceptions\InvalidArgumentException;
use Anastasia\Blog\Blogs\{Post, UUID};
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\{Auth\TokenAuthenticationInterface,
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
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newPostUuid = UUID::random();

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