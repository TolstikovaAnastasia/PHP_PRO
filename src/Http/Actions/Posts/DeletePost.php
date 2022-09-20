<?php

namespace Anastasia\Blog\Http\Actions\Posts;

use Anastasia\Blog\Blogs\UUID;
use Anastasia\Blog\Exceptions\PostNotFoundException;
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\ErrorResponse;
use Psr\Log\LoggerInterface;
use Anastasia\Blog\Http\{Request, Response};
use Anastasia\Blog\Repositories\PostsRepo\PostsRepositoryInterface;
use Anastasia\Blog\Http\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private LoggerInterface $logger,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query('uuid');
            $this->postsRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->delete(new UUID($postUuid));
        $this->logger->warning("Can't delete post: $postUuid");

        return new SuccessfulResponse([
            'uuid' => $postUuid,
        ]);
    }
}