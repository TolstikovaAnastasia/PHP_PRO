<?php

namespace Anastasia\Blog\Http\Actions\Comments;

use Anastasia\Blog\Exceptions\{AuthException, InvalidArgumentException, HttpException};
use Psr\Log\LoggerInterface;
use Anastasia\Blog\Blogs\{UUID ,Comment};
use Anastasia\Blog\Http\Actions\ActionInterface;
use Anastasia\Blog\Http\{Auth\TokenAuthenticationInterface,
    Request,
    Response,
    SuccessfulResponse,
    ErrorResponse};
use Anastasia\Blog\Repositories\CommentsRepo\CommentsRepositoryInterface;
use Anastasia\Blog\Repositories\PostsRepo\PostsRepositoryInterface;

class CreateComment implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
        private TokenAuthenticationInterface $authentication,
        private LoggerInterface $logger,
    )
    {
    }

    /**
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        $post = $this->postsRepository->get($postUuid);

        $newCommentUuid = UUID::random();

        try {
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