<?php

namespace Anastasia\Blog\Http\Actions\Comments;

use Psr\Log\LoggerInterface;
use Anastasia\Blog\Blogs\{UUID ,Comment};
use Anastasia\Blog\Exceptions\{HttpException};
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\{Auth\IdentificationInterface, Request, Response, SuccessfulResponse, ErrorResponse};
use Anastasia\Blog\Repositories\CommentsRepo\CommentsRepositoryInterface;
use Anastasia\Blog\Repositories\PostsRepo\PostsRepositoryInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
        private IdentificationInterface $identification,
        private LoggerInterface $logger,
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->identification->user($request);

            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
            $post = $this->postsRepository->get($postUuid);

            $newCommentUuid = UUID::random();

            $comment = new Comment(
                $newCommentUuid,
                $user,
                $post,
                $request->jsonBodyField('text'),
            );

            $this->commentsRepository->save($comment);
            $this->logger->info("Comment created: $newCommentUuid");

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}